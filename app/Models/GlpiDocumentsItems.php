<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiDocumentsItems extends Model
{
           // Especificamos la conexión que usará este modelo
           protected $connection = 'mysql_second';

           // Especificamos la tabla asociada
           protected $table = 'glpi_documents_items';
       
           // Desactivamos timestamps
           public $timestamps = false;
}
