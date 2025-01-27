<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiTicket extends Model
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

            /*  
    ***************************************************
               👚 Complementary Data info
    ***************************************************                    
    */
    /**
     * location Name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function itilfollowups()
    {
        return $this->belongsTo(GlpiUserLocation::class, 'locations_id');
    }
}
