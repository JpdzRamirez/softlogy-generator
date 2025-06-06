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
        'profiles_id',
        'entities_id',
        'usertitles_id',
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

    /**
     * Relaciones que siempre deben cargarse.
     *
     * @var array
    */
    protected $with = ['location','entiti','profile','title','email','usercategory'];

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
     * Entitie name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(GlpiUserProfile::class, 'profiles_id','id');
    }

    /**
     * Title name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function title()
    {
        return $this->belongsTo(GlpiUserTitle::class, 'usertitles_id');
    }

    /**
     * Summary of email
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<GlpiUserEmail, GlpiUser>
     */
    public function email()
    {
        return $this->hasOne(GlpiUserEmail::class, 'users_id');
    }

    /**
     * Summary of usercategory
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<GlpiUserCategory, GlpiUser>
    */
    public function usercategory()
    {
        return $this->belongsTo(GlpiUserCategory::class, 'usercategories_id');
    }
    /*  
    ***************************************************
               👚 Complementary Data info
    ***************************************************                    
    */
    /**
     * location Name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tickets()
    {
        return $this->belongsTo(GlpiTickets::class, 'users_id_recipient');
    }

}
