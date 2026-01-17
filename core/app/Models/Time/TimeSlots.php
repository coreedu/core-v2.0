<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Time\TimeConfig;
use App\Models\Time\LessonTime;
use App\Models\Time\Day;

class TimeSlots extends Model
{
    protected $table = 'time_slots';

    protected $fillable = ['time_config_id','lesson_time_id','day_id'];

    public function timeConfig(): BelongsTo
    {
        return $this->belongsTo(TimeConfig::class, 'time_config_id');
    }

    public function lessonTime(): BelongsTo
    {
        return $this->belongsTo(LessonTime::class, 'lesson_time_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
}
