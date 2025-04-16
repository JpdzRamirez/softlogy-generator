<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    // Especificamos la tabla asociada
    protected $table = 'store';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'entities_id',        
        'tienda',
        'id_tienda',
        'id_brand',
        'token_user',
        'token_endpoint',
        'token_pass',
        'endPoint',
        'endPoint_solicitudes',
        'endPoint_online',
        'endPoint_RPD'        
    ];

    protected $with = ['sqlConfigs'];

    
    /**
     * Summary of sqlConfigs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SqlConfig, Store>
     */
    public function sqlConfigs(): HasMany
    {
        return $this->hasMany(SqlConfig::class, 'store_id', 'id');
    }

    /**
     * Entitie name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entitie()
    {
        return $this->belongsTo(GlpiUserEntiti::class, 'entities_id');
    }
}
