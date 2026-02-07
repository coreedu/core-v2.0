<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Auditable;
// use Bezhansalleh\FilamentShield\Traits\HasFilamentShield;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use Auditable;
    // use HasFilamentShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rm',
        'name',
        'email',
        'birthdate',
        'password',
        'photo',
        'course',
        'semester',
        'shift',
        'is_determined',
        'contract_end_at',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_determined' => 'boolean',
        'contract_end_at' => 'date',
        'settings' => 'array',
    ];

    // Relacionamento com curso
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function components()
    {
        return $this->belongsToMany(Componente::class, 'component_instructor', 'instructor', 'component');
    }

    public function availabilities()
    {
        return $this->belongsToMany(\App\Models\Time\LessonTime::class, 'availability_instructor', 'user_id', 'time_id')
            ->withPivot('day_id')
            ->withTimestamps();
    }

    public function shift()
    {
        return $this->belongsTo(\App\Models\Time\Shift::class, 'turno', 'cod');
    }
}
