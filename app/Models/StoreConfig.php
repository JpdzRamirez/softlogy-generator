<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreConfig extends Model
{
    // Especificamos la tabla asociada
    protected $table = 'store_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_entitie',
        'entitie',
        'token_User',
        'token_endpoint',
        'token_pass',
        'endPoint',
        'endPoint_solicitudes',
        'endPoint_online',
        'endPoint_RPD'        
    ];
}
