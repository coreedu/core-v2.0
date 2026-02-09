<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use App\Models\Time\Context;
use App\Models\Time\Shift;
use App\Models\Time\TimeSlots;
use App\Models\Time\Day;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class TimeConfig extends Model
{
    use Auditable;
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

    public function getFullNameAttribute(): string
    {
        return "{$this->context->name} - {$this->shift->name}";
    }

    public function schedules(): HasMany
    {
        // Uma configuração pode ter vários registros de grade (versões)
        return $this->hasMany(\App\Models\Schedule::class, 'time_config_id');
    }
}
