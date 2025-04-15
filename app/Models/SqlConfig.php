<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlConfig extends Model
{
    // Especificamos la tabla asociada
    protected $table = 'sql_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
    */
    protected $fillable = [
        'nombre',
        'store_id',
        'direccion',
        'telefono',
        'marca',
        'numResolucion',
        'prefijo',
        'fechaFacIni',
        'fechaFacFin',
        'rangoInicial',
        'rangoFinal',
        'Folio',
        'Tipo',
        'NumCertificado',
    ];
}
