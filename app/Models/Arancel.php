<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arancel extends Model
{
    // AGREGA ESTA LÍNEA AQUÍ PARA DETENER EL ERROR:
    protected $table = 'aranceles';

    public $timestamps = false;

    protected $fillable = [
        'codigo_arancel',
        'nombre_arancel',
        'descripcion',
        'monto',
        'codigo_misa'
    ];

    public function ordenes()
    {
        return $this->hasMany(OrdenPago::class);
    }
}
