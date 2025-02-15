<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiItilCategories extends Model
{
       // Especificamos la conexión que usará este modelo
       protected $connection = 'mysql_second';

       // Especificamos la tabla asociada
       protected $table = 'glpi_itilcategories';
   
       // Desactivamos timestamps
       public $timestamps = false;
   
   
       /**
        * The attributes that are mass assignable.
        *
        * @var list<string>
        */
       protected $fillable = [
           'entities_id',
           'is_recursive',
           'itilcategories_id',
           'name',
           'completename',
       ];
   
}
