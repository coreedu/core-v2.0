<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Time\TimeConfig;
use App\Traits\Auditable;

class Context extends Model
{
    use Auditable;
    protected $table = 'context';

    protected $fillable = ['name'];

    public function timeConfigs(): HasMany
    {
        return $this->hasMany(TimeConfig::class, 'context_id');
    }
}
