<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ventanilla extends Model
{
    public $timestamps = false;
    protected $fillable = ['numero', 'responsable', 'activa'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenPago::class);
    }
}