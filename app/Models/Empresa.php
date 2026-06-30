<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    // Campos que el controlador puede llenar
    protected $fillable = ['razon_social', 'nit'];

    public function ordenes()
    {
        return $this->hasMany(OrdenPago::class);
    }
}
