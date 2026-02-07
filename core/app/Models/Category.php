<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Category extends Model
{
    use Auditable;
    protected $table = 'category';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function categories()
    {
        return self::count();
    }
}
