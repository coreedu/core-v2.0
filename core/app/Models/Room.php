<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    protected $table = 'room';

    protected $fillable = [
        'name',
        'number',
        'img',
        'type',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'type');
    }

    public function equipments()
    {
        return $this->belongsToMany(\App\Models\Inventory\Equipment::class, 'equipment_room', 'room_id', 'equipment_id')
            ->withTimestamps();
    }

    // --- NOVO MÉTODO PARA O GRÁFICO 1 ---

    /**
     * Retorna a contagem total de salas agrupada por categoria.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getCountByCategory()
    {
        // 1. Começa na tabela 'room'
        // 2. Faz o JOIN com 'category' (usando 'room.type' como chave)
        // 3. Seleciona o nome da categoria e conta os IDs das salas
        // 4. Agrupa pelo nome da categoria
        
        return self::query() // 'self' aqui se refere ao Model 'Room'
            ->join('category', 'room.type', '=', 'category.id')
            ->select('category.name', DB::raw('COUNT(room.id) as room_count'))
            ->groupBy('category.name')
            ->orderBy('room_count', 'desc') // Mais salas primeiro
            ->get();
    }
}
