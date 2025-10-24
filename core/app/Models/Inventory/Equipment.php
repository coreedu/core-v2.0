<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ClassSchedule;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'brand_id',
        'type_id',
        'patrimony',
        'status',
        'observation',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Models\Room::class, 'equipment_room', 'equipment_id', 'room_id')
            ->withTimestamps();
    }

    // --- NOVO MÉTODO PARA O GRÁFICO 2 ---

    /**
     * Retorna a contagem de uso (aulas) agrupada por equipamento.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getUsageByEquipment()
    {
        // Consulta ligando 4 tabelas:
        // T1 = class_schedule (A aula)
        // T2 = room (A sala)
        // T3 = equipment_room (Pivô: sala <-> equipamento)
        // T4 = equipment (O equipamento)
        
        return ClassSchedule::query()
            // T1 -> T2 (Liga a Aula na Sala, usando a coluna 'room')
            ->join('room as T2', 'class_schedule.room', '=', 'T2.id')
            
            // T2 -> T3 (Liga a Sala na Tabela Pivô 'equipment_room')
            // (Verifique os nomes 'room_id' e 'equipment_id' na sua tabela pivô)
            ->join('equipment_room as T3', 'T3.room_id', '=', 'T2.id') 
            
            // T3 -> T4 (Liga a Pivô ao Equipamento)
            ->join('equipment as T4', 'T4.id', '=', 'T3.equipment_id')
            
            // Agrupa e conta
            ->select('T4.name', DB::raw('COUNT(class_schedule.id) as usage_count'))
            ->groupBy('T4.name')
            ->orderBy('usage_count', 'desc')
            ->take(10) // Limita aos 10 equipamentos mais usados
            ->get();
    }
}
