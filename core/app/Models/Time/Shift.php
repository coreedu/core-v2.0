<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Time\TimeConfig;
use App\Traits\Auditable;

class Shift extends Model
{
    use Auditable;
    protected $table = 'shift';

    // Campos que podem ser preenchidos em mass-assignment
    protected $fillable = ['name', 'description']; 

    // public function lessonTimes()
    // {
    //     return $this->belongsToMany(
    //         \App\Models\Time\LessonTime::class,
    //         'time_shift',
    //         'shift_id',
    //         'lesson_time_id',
    //         'cod',
    //         'id'
    //     );
    // }

    public static function listCodAndName(): array
    {
        return self::query()
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function timeConfigs(): HasMany
    {
        return $this->hasMany(TimeConfig::class, 'shift_id');
    }
}
