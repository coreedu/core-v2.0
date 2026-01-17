<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use App\Models\Time\Context;
use App\Models\Time\Shift;
use App\Models\Time\TimeSlots;
use App\Models\Time\Day;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeConfig extends Model
{
    protected $table = 'time_config';

    protected $fillable = ['context_id', 'shift_id'];

    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class, 'context_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(TimeSlots::class, 'time_config_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
}
