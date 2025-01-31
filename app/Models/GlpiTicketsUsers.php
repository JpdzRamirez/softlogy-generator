<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiTicketsUsers extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_tickets_users';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * Relaciones que siempre deben cargarse.
     *
     * @var array
    */
    protected $with = ['ticketContent'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tickets_id',
        'users_id',
        'type',
        'use_notification',
        'alternative_email'
    ];


    /*  
    ***************************************************
               👚 Complementary Data info
    ***************************************************                    
    */

    /**
     * Relation with GlpiTickets (BelongsTo)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticketContent()
    {
        return $this->belongsTo(GlpiTickets::class, 'tickets_id', 'id')
            ->where('is_deleted', '!=', 1); // Filtra tickets no eliminados
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
