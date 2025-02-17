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

    /**
     * Relaciones que siempre deben cargarse.
     *
     * @var array
     */
    protected $with = ['documents'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'itemtype',
        'items_id',
        'date',
        'users_id',
        'users_id_editor',
        'content',
        'is_private',
        'requesttypes_id',
        'date_mod',
        'date_creation',
        'timeline_position',
        'sourceitems_id',
        'sourceof_items_id',
    ];

    /**
     * Relation with GlpiDocumentsItems
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function documents()
    {
        return $this->hasManyThrough(
            GlpiDocuments::class,         // Modelo final (documentos)
            GlpiDocumentsItems::class,    // Modelo intermedio (relación documentos-items)
            'items_id',                   // Clave foránea en GlpiDocumentsItems que apunta a GlpiItilFollowups
            'id',                         // Clave primaria en GlpiDocuments
            'id',                         // Clave primaria en GlpiItilFollowups
            'documents_id'                // Clave foránea en GlpiDocumentsItems que apunta a GlpiDocuments
        )
        ->where('glpi_documents_items.itemtype', '=', 'ITILFollowup') // Validar que los documentos pertenezcan a ITILFollowup
        ->select(
            'glpi_documents.id',
            'glpi_documents.name',
            'glpi_documents.filename',
            'glpi_documents.filepath',
            'glpi_documents.mime',
            'glpi_documents.sha1sum',
            'glpi_documents.tag'
        );
    }

    /**
     * Relation with GlpiUsers (BelongsTo)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(GlpiUser::class, 'users_id', 'id');
    }
}
