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
        'closedate',
        'solvedate',
        'takeintoaccountdate',
        'date_mod',
        'users_id_lastupdater',
        'status',
        'users_id_recipient',
        'requesttypes_id',
        'content',
        'urgency',
        'impact',
        'priority',
        'itilcategories_id',
        'type',
        'global_validation',
        'slas_id_ttr',
        'slas_id_tto',
        'slalevels_id_ttr',
        'ola_waiting_duration',
        'olas_id_tto',
        'olas_id_ttr',
        'olalevels_id_ttr',
        'waiting_duration',
        'close_delay_stat',
        'solve_delay_stat', 
        'takeintoaccount_delay_stat',       
        'locations_id',
        'validation_percent',
        'date_creation',
        'ola_tto_begin_date',
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

    /**
     * Relation with GlpiTicketsUser
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketsUser()
    {
        return $this->hasMany(GlpiTicketsUsers::class, 'tickets_id', 'id');
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
