<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    // Especificamos la tabla asociada
    protected $table = 'store';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'entitie_id',
        'entitie',
        'token_User',
        'token_endpoint',
        'token_pass',
        'endPoint',
        'endPoint_solicitudes',
        'endPoint_online',
        'endPoint_RPD',
        'sqlConfig_id',
    ];
}
