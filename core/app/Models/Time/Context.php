<?php

namespace App\Models\Time;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Time\TimeConfig;

class Context extends Model
{
    protected $table = 'context';

    protected $fillable = ['name'];

    public function timeConfigs(): HasMany
    {
        return $this->hasMany(TimeConfig::class, 'context_id');
    }
}
