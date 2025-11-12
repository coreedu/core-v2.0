<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Time\Shift;
use App\Models\Curso;
use App\Models\Modality;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\DB;

class Schedule extends Model
{
    protected $table = 'schedule';

    protected $fillable = ['version', 'shift_cod', 'course_id', 'modality_id', 'module_id', 'status'];

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_cod', 'cod');
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
