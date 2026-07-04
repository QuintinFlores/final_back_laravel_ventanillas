<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPago extends Model
{
    protected $table = 'ordenes_pago';
    protected $fillable = [
        'empresa_id',
        'arancel_id',
        'ventanilla_id',
        'usuario_id',
        'cantidad',
        'monto_unitario',
        'codigo_misa',
        'descripcion',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto_unitario' => 'decimal:2',
        'monto_total' => 'decimal:2',
    ];

    // monto_total es GENERATED en PG, nunca se escribe
    protected $guarded = ['monto_total', 'numero_orden'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function arancel()
    {
        return $this->belongsTo(Arancel::class);
    }

    public function ventanilla()
    {
        return $this->belongsTo(Ventanilla::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}