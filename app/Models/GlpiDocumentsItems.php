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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'documents_id',
        'items_id',
        'itemtype',
        'entities_id',
        'is_recursive',
        'date_mod',
        'users_id',
        'timeline_position',
        'date_creation',
        'date',
    ];
}
