<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUserEmail extends Model
{
       // Especificamos la conexión que usará este modelo
       protected $connection = 'mysql_second';

       // Especificamos la tabla asociada
       protected $table = 'glpi_useremails';
   
       // Desactivamos timestamps
       public $timestamps = false;
   
           /**
        * The attributes that should be hidden for serialization.
        *
        * @var list<string>
        */
       protected $hidden = [
           'is_dynamic',
           'is_default',
       ];
}
