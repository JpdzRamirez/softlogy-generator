<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiDocuments extends Model
{
    // Especificamos la conexión que usará este modelo
    protected $connection = 'mysql_second';

    // Especificamos la tabla asociada
    protected $table = 'glpi_documents';

    // Desactivamos timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'entities_id',
        'is_recursive',
        'name',
        'filename',
        'filepath',
        'documentcategories_id',
        'mime',
        'date_mod',
        'comment',
        'is_deleted',
        'link',
        'users_id',
        'tickets_id',
        'sha1sum',
        'is_blacklisted',
        'tag',
        'date_creation',
    ];
}
