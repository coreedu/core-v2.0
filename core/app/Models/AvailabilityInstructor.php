<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AvailabilityInstructor extends Model
{
    use Auditable;
    protected $table = 'availability_instructor';

    protected $fillable = [
        'user_id',
        'day_id',
        'time_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }

    public function time()
    {
        return $this->belongsTo(LessonTime::class, 'time_id');
    }
}
