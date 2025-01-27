<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiItilFollowups extends Model
{
        // Especificamos la conexión que usará este modelo
        protected $connection = 'mysql_second';

        // Especificamos la tabla asociada
        protected $table = 'glpi_itilfollowups';
    
        // Desactivamos timestamps
        public $timestamps = false;
}
