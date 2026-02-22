<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Time\Shift;
use App\Models\Time\LessonTime;
use App\Models\Time\Day;
use App\Models\Curso;
use App\Models\TimeShift;
use App\Models\Componente;
use App\Models\Room;
use App\Models\User;
use App\Models\Modality;
use App\Models\ClassSchedule;
use App\Models\Time\TimeConfig;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\Auditable;

class Schedule extends Model
{
    use Auditable;
    protected $table = 'schedule';

    protected $fillable = ['version', 'time_config_id', 'course_id', 'modality_id', 'module_id', 'status'];

    public function timeConfig() {
        return $this->belongsTo(TimeConfig::class, 'time_config_id', 'id');
    }

    public function course() {
        return $this->belongsTo(Curso::class, 'course_id', 'id');
    }

    public function modality() {
        return $this->belongsTo(Modality::class, 'modality_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ClassSchedule::class, 'schedule_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function getActiveConfig()
    {
        $now = now()->format('H:i');
        $todayId = (now()->dayOfWeek === 0) ? 7 : now()->dayOfWeek;

        // 1. Descobrir o turno pelo horário atual
        $currentShiftId = TimeShift::getCurrentShiftCodByHour($now);

        // 2. Buscar as configurações que pertencem a esse turno
        // Aqui pegamos as configurações e verificamos se existe ClassSchedule (horário publicado)
        return TimeConfig::where('shift_id', $currentShiftId)
        ->whereHas('timeConfig', function ($query) {
             $query->has('items'); 
        })
        ->with(['context', 'shift', 'slots.lessonTime'])
        ->get();
    }

    public static function getPublished()
    {
        $now = now()->format('H:i');
        $dayOfWeek = (now()->dayOfWeek % 7) + 1; 
        $todayId = $todayId = in_array($dayOfWeek, [6, 7]) ? 2 : $dayOfWeek; // converte sabado e domingo para segunda (2)
        
        $currentShiftId = TimeShift::getCurrentShiftCodByHour($now);
        $shiftName = Shift::find($currentShiftId)?->name ?? '';

        $configsAtivas = TimeConfig::where('shift_id', $currentShiftId)
            ->whereHas('schedules', function ($q) use ($todayId) {
                $q->where('status', 1)
                ->whereHas('items.timeSlots', fn($querySlot) => $querySlot->where('day_id', $todayId));
            })
            ->with(['context', 'shift', 'slots.lessonTime'])
            ->get();
        $register = [];

        foreach ($configsAtivas as $config) {
            $category = $config->context?->id;
            
            if (!isset($register['context'][$category]['shifts'][$currentShiftId])) {
                $register['context'][$category]['shifts'][$currentShiftId] = [
                    'name' => $shiftName,
                    'times' => [],
                    'courses' => [],
                    'days' => [$todayId => Day::find($todayId)?->name ?? '-']
                ];
                $register['context'][$category]['name'] = $config->context?->name ?? '';
            }

            $horarios = $config->slots
                ->where('day_id', $todayId)
                ->sortBy('lessonTime.start')
                ->mapWithKeys(fn($slot) => [
                    $slot->lesson_time_id => substr($slot->lessonTime->start, 0, 5) . " - " . substr($slot->lessonTime->end, 0, 5)
                ])->toArray();

            $register['context'][$category]['shifts'][$currentShiftId]['times'] = array_replace($register['context'][$category]['shifts'][$currentShiftId]['times'], $horarios);

            $schedulesAtivos = $config->schedules()->where('status', 1)->get();

            foreach ($schedulesAtivos as $schedule) {
                $existingItems = $schedule->items()
                    ->whereHas('timeSlots', fn($q) => $q->where('day_id', $todayId))
                    ->with(['instructor', 'component', 'sala', 'timeSlots'])
                    ->get();

                $scheduleCourse = self::mountScheduleArray($schedule->course_id, $schedule->module_id, $existingItems);
                
                // Merge recursivo dentro da categoria e turno específicos
                $register['context'][$category]['shifts'][$currentShiftId]['courses'] = array_replace_recursive(
                    $register['context'][$category]['shifts'][$currentShiftId]['courses'], 
                    $scheduleCourse
                );
            }
        }
        return $register;
    }

    public static function getScheduleByShift($day, $shift){
        return self::where('status', true)
            ->whereHas('items.timeSlots', function ($query) use ($day) {
                $query->where('day_id', $day);
            })
            ->whereHas('timeConfig', function ($query) use ($shift) {
                $query->where('shift_id', $shift);
            })
            ->with(['items' => function ($query) use ($day) {
                $query->whereHas('timeSlots', fn($q) => $q->where('day_id', $day))
                    ->with(['timeSlots.lessonTime', 'instructor', 'sala', 'component']);
            }])
            ->get();
    }

    public static function mountScheduleArray($courseId, $moduleId, $itens)
    {
        $courseName = Curso::find($courseId)?->nome ?? '-';
        $scheduleData[$courseId] = [
            'name' => $courseName,
            'days' => []
        ];

        foreach ($itens as $item) {
            $day = $item->timeSlots->day_id;
            $time = $item->timeSlots->lesson_time_id;
            $group = $item->group ?? 'A';

            // Estrutura interna: Dia > Modulo > Horario > Grupo
            $scheduleData[$courseId]['days'][$day]['modules'][$moduleId]['times'][$time]['groups'][$group] = [
                'subject' => $item->getRelation('component')?->abreviacao ?? '-', // Ideal usar o relacionamento carregado
                'teacher' => $item->getRelation('instructor')?->name ?? '-',
                'room'    => $item->room ?? '-',
            ];
        }

        return $scheduleData;
    }

    public static function mountSchedulePdf($schedules) 
    {
        $timeSlots = TimeShift::getTimesMap(); 
        $shifts = Shift::listCodAndName(); 
        $days = Day::getWeekDays(); 

        $register = [
            'context' => [],
            'times' => $timeSlots,
            'days' => $days,
            'shifts' => $shifts
        ];

        foreach($schedules as $schedule) {
            $context = $schedule->timeConfig->context?->id;
            $shift = $schedule->timeConfig->shift->id;

            $existingItems = $schedule->items()
                ->select('slot_id', 'component', 'instructor', 'room', 'group')
                ->with(['timeSlots']) // Importante para pegar o day_id e lesson_time_id
                ->get();

            $scheduleCourse = self::mountScheduleArrayPdf(
                $schedule->course_id, 
                $schedule->module_id,
                $shift,
                $existingItems
            );
            
            if (!isset($register['context'][$context])) {
                $register['context'][$context]['courses'] = [];
                $register['context'][$context]['name'] = $schedule->timeConfig->context?->name ?? '';
            }

            $register['context'][$context]['courses'] = array_replace_recursive(
                $register['context'][$context]['courses'],
                $scheduleCourse
            );
        }

        return $register;
    }

    public static function mountScheduleArrayPdf($course, $module, $shift, $itens){
        $scheduleData[$course] = [];
        foreach ($itens as $item) {
            $day = $item->timeSlots->day_id;
            $time = $item->timeSlots->lesson_time_id;
            
            $group = $item->group ?? 'A';

            // $shift = TimeShift::getShiftCodById($item->time);

            $scheduleData[$course]['shifts'][$shift]['modules'][$module]['days'][$day]['times'][$time]['groups'][$group] = [
                'subject' => Componente::find($item->component)?->nome ?? '-',
                'teacher' => User::find($item->instructor)?->name ?? '-',
                'room' => Room::find($item->room)?->number ?? '-',
            ];


            if(!isset($scheduleData[$course]['name'])) $scheduleData[$course]['name'] = Curso::find($course)?->nome ?? '-';
            if(!isset($scheduleData[$course]['abreviacao'])) $scheduleData[$course]['abreviacao'] = Curso::find($course)?->abreviacao ?? '-';
        }

        return $scheduleData;
    }

    // --- NOVO MÉTODO PARA O GRÁFICO 3 (PREDITIVO) ---

    /**
     * Retorna a tendência de uso (contagem de aulas) por categoria
     * ao longo das versões de horário.
     *
     * @return array
     */
    public static function getUsageTrendByCategory()
    {
        // Esta consulta é o coração da análise preditiva.
        // T1 = schedule (O Tempo, Eixo X)
        // T2 = class_schedule (O Fato, a Aula)
        // T3 = room (A Sala)
        // T4 = category (A Categoria da Sala, Nossas Linhas)

        // $data = self::query() // Começa do Schedule (T1)
        //     ->join('class_schedule as T2', function ($join) {
        //         // A "cola" de chave composta
        //         $join->on('schedule.course_id', '=', 'T2.course')
        //              ->on('schedule.shift_cod', '=', 'T2.shift')
        //              ->on('schedule.modality_id', '=', 'T2.modality')
        //              ->on('schedule.module_id', '=', 'T2.module');
        //     })
        //     // Liga a Aula (T2) na Sala (T3)
        //     ->join('room as T3', 'T2.room', '=', 'T3.id')
        //     // Liga a Sala (T3) na Categoria (T4)
        //     ->join('category as T4', 'T3.type', '=', 'T4.id')
            
        //     // Agrupa pelo eixo X (versão) e pelas linhas (categoria)
        //     ->groupBy('schedule.version', 'T4.name')
        //     // Seleciona os dados que queremos
        //     ->select(
        //         'schedule.version', // O Eixo X
        //         'T4.name as category_name', // O nome da Linha
        //         DB::raw('COUNT(T2.id) as usage_count') // O Eixo Y
        //     )
        //     ->orderBy('schedule.version')
        //     ->get();

        // // Agora, precisamos formatar os dados para o Chart.js
        // // O formato final deve ser:
        // // 'labels' => ['2024.1', '2024.2', '2025.1']
        // // 'datasets' => [
        // //   ['label' => 'Laboratório', 'data' => [100, 110, 130]],
        // //   ['label' => 'Sala Comum', 'data' => [50, 50, 45]],
        // // ]
        
        // $labels = $data->pluck('version')->unique()->sort()->values();
        // $categories = $data->pluck('category_name')->unique()->values();
        // $datasets = [];

        // // Prepara as cores para cada linha
        // $colors = [
        //     'rgba(54, 162, 235, 1)',  // Azul
        //     'rgba(255, 99, 132, 1)',  // Vermelho
        //     'rgba(75, 192, 192, 1)',  // Verde
        //     'rgba(255, 159, 64, 1)',  // Laranja
        //     'rgba(153, 102, 255, 1)', // Roxo
        // ];
        
        // foreach ($categories as $index => $categoryName) {
        //     $color = $colors[$index % count($colors)];
            
        //     $datasetData = $labels->map(function ($version) use ($data, $categoryName) {
        //         // Encontra a contagem para esta categoria e esta versão
        //         return $data->where('version', $version)
        //                    ->where('category_name', $categoryName)
        //                    ->first()
        //                    ->usage_count ?? 0;
        //     });

        //     $datasets[] = [
        //         'label' => $categoryName,
        //         'data' => $datasetData,
        //         'borderColor' => $color,
        //         'backgroundColor' => str_replace('1)', '0.1)', $color), // Cor com transparência
        //         'fill' => true,
        //         'tension' => 0.1, // Deixa a linha levemente curvada
        //     ];
        // }

        // return [
        //     'datasets' => $datasets,
        //     'labels' => $labels,
        // ];
        return [];
    }

    protected static function booted()
    {
        static::deleting(function ($schedule) {
            // Apaga todos os itens relacionados na tabela class_schedule
            // Usamos delete() em vez de query->delete() para disparar observers dos filhos, se houver.
            $schedule->items()->each(function ($item) {
                $item->delete();
            });
        });
    }
}
