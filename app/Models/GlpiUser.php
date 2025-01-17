<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiUser extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_users';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'phone2',
        'mobile',
        'realname',
        'realname',
        'firstname',
        'picture',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'personal_token',
        'personal_token_date',
        'cookie_token',
        'cookie_token_date',
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
    public function location()
    {
        return $this->belongsTo(GlpiUserLocation::class, 'locations_id');
    }

    
    /**
     * Entitie name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entiti()
    {
        return $this->belongsTo(GlpiUserEntiti::class, 'entities_id');
    }

    /**
     * Title name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function title()
    {
        return $this->belongsTo(GlpiUserTitle::class, 'usertitles_id');
    }

    public function email()
    {
        return $this->hasOne(GlpiUserEmail::class, 'users_id');
    }
}
