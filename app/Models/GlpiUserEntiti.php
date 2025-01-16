<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUserEntiti extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_entities';

    // Desactivamos timestamps
    public $timestamps = false;

        /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'completename',
        'level',
        'sons_cache',
        'ancestors_cache',
    ];
}
