<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Modality extends Model
{
    use Auditable;
    protected $table = 'modality';

    protected $fillable = ['name', 'description'];
}
