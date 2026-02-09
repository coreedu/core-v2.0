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
        $todayId = (now()->dayOfWeek % 7) + 1;

        $currentShiftId = TimeShift::getCurrentShiftCodByHour($now);

        $register['courses'] = [];
        $register['weight'] = 1;
        $register['days'] = [$todayId => Day::find($todayId)?->name ?? '-'];

        $configsAtivas = TimeConfig::where('shift_id', $currentShiftId)
            ->whereHas('schedules', function ($query) {
                $query->has('items'); 
            })
            ->with(['context', 'shift', 'slots.lessonTime'])
            ->get();

        
        $register['times'] = $configsAtivas->flatMap->slots
            ->where('day_id', $todayId)
            ->sortBy('lessonTime.start')
            ->mapWithKeys(function ($slot) {
                $label = substr($slot->lessonTime->start, 0, 5) . " - " . substr($slot->lessonTime->end, 0, 5);
                return [$slot->lesson_time_id => $label];
            })
            ->toArray();

        foreach ($configsAtivas as $config) {
            
            $schedules = $config->schedules()->whereHas('items')->get();
            foreach ($schedules as $schedule) {
                $existingItems = $schedule->items()
                    ->whereHas('timeSlots', fn($q) => $q->where('day_id', $todayId))
                    ->with(['instructor', 'component', 'sala'])
                    ->get();

                $scheduleCourse = [];
                
                $scheduleCourse = self::mountScheduleArray($schedule->course_id, $schedule->module_id, $existingItems);

                $register['courses'] = array_replace_recursive(
                    $register['courses'],
                    $scheduleCourse
                );
                }
            return $register;
        }
    }

    // public static function getPublished()
    // {
    //     $today = (now()->dayOfWeek % 7) + 1;

    //     $timeSlots = [];
    //     $currentShift = 0;
    //     // if($today == 7){
    //     //     $timeSlots = TimeDay::getTimesByDay(7);
    //     // }else{
    //         $now = Carbon::now()->format('H:i');
    //         $currentShift = TimeShift::getCurrentShiftCodByHour($now);
    
    //         $timeSlots = TimeShift::getTimesByShift($currentShift);
    //     // }

    //     $days = [$today => Day::find($today)?->name ?? '-'];
        
    //     $rooms = Room::getRoomsArray();

    //     $register = [];
    //     $weight = 1;

    //     $register['courses'] = [];

    //     foreach($days as $idxDay => $day){
    //         $schedules = self::getScheduleByShift($idxDay, $currentShift);

    //         if($schedules){
    //             foreach($schedules as $schedule){
    //                 $existingItems = $schedule->items()
    //                     ->select('slot_id', 'component', 'instructor', 'room', 'group')
    //                     ->get();

    //                 $scheduleCourse = self::mountScheduleArray($schedule->course_id, $schedule->module_id, $existingItems);
                    
    //                 $weight += $scheduleCourse['weight'];
    //                 unset($scheduleCourse['weight']);
                    
    //                 $register['courses'] = array_replace_recursive(
    //                     $register['courses'],
    //                     $scheduleCourse
    //                 );
    //             }
    //         }

    //     }

    //     $register['times'] = $timeSlots;
    //     $register['days'] = $days;
    //     $register['weight'] = $weight;

    //     return $register;
    // }

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

    public static function mountScheduleArray($course, $module, $itens){
        $scheduleData[$course] = [];

        foreach ($itens as $item) {
            $day = $item->timeSlots->day_id;
            $time = $item->timeSlots->lesson_time_id;
            
            $group = $item->group ?? 'A';

            $scheduleData[$course]['days'][$day]['modules'][$module]['times'][$time]['groups'][$group] = [
                'subject' => Componente::find($item->component)?->nome ?? '-',
                'teacher' => User::find($item->instructor)?->name ?? '-',
                'room' => Room::find($item->room)?->number ?? '-',
            ];


            if(!isset($scheduleData[$course]['name'])) $scheduleData[$course]['name'] = Curso::find($course)?->nome ?? '-';
            // if(!isset($scheduleData[$course]['categoria'])) $scheduleData[$course]['categoria'] = Curso::find($course)?-> ?? '-';
            // $scheduleData['weight'] = isset([$course]['days'][$day]['modules'][$module]) ? 0 : 1;
        }

        return $scheduleData;
    }

    public static function mountSchedulePdf($schedules){
        
        // $satSlots = TimeDay::getTimesByDay(7); // sat times
        $timeSlots = TimeShift::getTimesMap(); // mapear periodo -> horarios
        $shifts = Shift::listCodAndName(); // nome dos turnos
        $days = Day::getWeekDays(); // week days

        $register['courses'] = [];

        foreach($schedules as $schedule){
            $existingItems = $schedule->items()
                ->select('slot_id', 'component', 'instructor', 'room', 'group')
                ->get();

            $scheduleCourse = self::mountScheduleArrayPdf($schedule->course_id, $schedule->module_id, $schedule->timeConfig->shift_id, $existingItems);
            
            $register['courses'] = array_replace_recursive(
                $register['courses'],
                $scheduleCourse
            );
        }

        $register['times'] = $timeSlots;
        $register['days'] = $days;
        $register['shifts'] = $shifts;
        // $register['satSlots'] = $satSlots;
        
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
}
