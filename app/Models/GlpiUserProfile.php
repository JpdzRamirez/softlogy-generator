<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUserProfile extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_profiles';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'is_default',
        'helpdesk_hardware',
        'helpdesk_item_type',
        'ticket_status',
        'comment',
        'problem_status',
        'create_ticket_on_login',
        'tickettemplates_id',
        'changetemplates_id',
        'problemtemplates_id',
        'change_status',
        'managed_domainrecordtypes',
        'date_creation',
    ];
}
