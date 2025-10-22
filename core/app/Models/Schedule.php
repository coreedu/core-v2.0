<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Time\Shift;
use App\Models\Curso;
use App\Models\Modality;

class Schedule extends Model
{
    protected $table = 'schedule';

    protected $fillable = ['version', 'shift_cod', 'course_id', 'modality_id', 'module_id', 'status'];

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_cod', 'cod');
    }

    public function course() {
        return $this->belongsTo(Curso::class, 'course_id', 'id');
    }

    public function modality() {
        return $this->belongsTo(Modality::class, 'modality_id', 'id');
    }
}
