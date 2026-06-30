<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    // AGREGA ESTA LÍNEA AQUÍ PARA EVITAR EL ERROR DE UPDATED_AT:
    public $timestamps = false;
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'username', 'password_hash', 'ventanilla_id', 'activo'];
    protected $hidden = ['password_hash'];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function ventanilla()
    {
        return $this->belongsTo(Ventanilla::class);
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenPago::class);
    }
}