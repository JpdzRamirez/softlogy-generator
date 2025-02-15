<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUserCategory extends Model
{
       // Especificamos la conexión que usará este modelo
       protected $connection = 'mysql_second';

       // Especificamos la tabla asociada
       protected $table = 'glpi_usercategories';
   
       // Desactivamos timestamps
       public $timestamps = false;
   
       /**
        * The attributes that are mass assignable.
        *
        * @var list<string>
        */
       protected $fillable = [
           'name',
           'comment',
           'date_mod',
           'date_creation',
       ];
   
}
