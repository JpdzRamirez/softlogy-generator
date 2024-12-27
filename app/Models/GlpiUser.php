<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUser extends Model
{   
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_users';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'phone2',
        'mobile',
        'realname',
        'realname',
        'firstname',
        'picture',
        'password',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'personal_token',
        'personal_token_date',
        'cookie_token',
        'cookie_token_date',
    ];

}
