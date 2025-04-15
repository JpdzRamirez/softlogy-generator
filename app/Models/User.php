<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'realname',
        'firstname',
        'email',
        'password',
        'phone',
        'mobile',
        'entiti',
        'entities_id',
        'title',
        'location',
        'glpi_id',
        'profile',
        'profile_id',
        'is_active',
        'picture',
    ];

    /**
     * Relaciones que siempre deben cargarse.
     *
     * @var array
    */
    protected $with = ['entiti'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /*💱💱💱💱
    *************************************
    ----------------MUTATORS-------------
    *************************************
     */
    /**
     * 
     * @param mixed $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value)); // Convierte la primera letra de cada palabra a mayúscula
    }

    /**
     * Entitie name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entiti()
    {
        return $this->belongsTo(GlpiUserEntiti::class, 'entities_id');
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
