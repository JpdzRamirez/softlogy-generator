<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiTickets extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_tickets';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'entities_id',
        'name',
        'date',
        'user_id_lastupdater',
        'status',
        'user_id_recipient',
        'requesttypes_id',
        'content',
        'urgency',
        'impact',
        'priority',
        'itilcategories_id',
        'type',
        'global_validation',
    ];

    /**
     * Relaciones que siempre deben cargarse.
     *
     * @var array
    */
    protected $with = ['documents'];

    /*  
    ***************************************************
               👚 Complementary Data info
    ***************************************************                    
    */

    /**
     * Relation with GlpiItilFollowups
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itilfollowups()
    {
        return $this->hasMany(GlpiItilFollowups::class, 'items_id', 'id')
        ->select('id','itemtype','items_id','users_id','users_id_editor','content');
    }

    /**
     * Relation with GlpiDocumentsItems
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function documents()
    {
        return $this->hasManyThrough(
            GlpiDocuments::class,            // Modelo final
            GlpiDocumentsItems::class,       // Modelo intermedio
            'items_id',                      // Clave foránea en GlpiDocumentsItems
            'id',                            // Clave foránea en GlpiDocuments
            'id',                            // Clave local en GlpiTicket
            'documents_id'                   // Clave local en GlpiDocumentsItems
        )->where('glpi_documents_items.itemtype', 'Ticket') // Filtra por itemtype = Ticket
        ->select('glpi_documents.id', 'glpi_documents.name',
        'glpi_documents.filename','glpi_documents.filepath','glpi_documents.mime','glpi_documents.sha1sum'); 
    }
}
