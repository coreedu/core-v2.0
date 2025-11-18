<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';

    // Campos que podem ser preenchidos em mass-assignment
    protected $fillable = ['cod', 'name', 'description']; 

    public function lessonTimes()
    {
        return $this->belongsToMany(
            \App\Models\Time\LessonTime::class,
            'time_shift',
            'shift_cod',
            'lesson_time_id',
            'cod',
            'id'
        );
    }

    public static function listCodAndName(): array
    {
        return self::query()
            ->orderBy('cod')
            ->pluck('name', 'cod')
            ->toArray();
    }
}
