<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenPago;
use App\Models\Arancel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenPagoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenPago::with(['empresa', 'arancel', 'ventanilla', 'usuario']);

        if ($request->has('ventanilla_id')) {
            $query->where('ventanilla_id', $request->ventanilla_id);
        }
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        return response()->json(
            $query->orderBy('numero_orden', 'desc')->paginate(50)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'arancel_id' => 'required|exists:aranceles,id',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
        ]);

        // Jalar datos del arancel automáticamente para proteger los montos
        $arancel = Arancel::findOrFail($request->arancel_id);

        $orden = OrdenPago::create([
            'empresa_id' => $request->empresa_id,
            'arancel_id' => $request->arancel_id,
            'ventanilla_id' => $request->user()->ventanilla_id,
            'usuario_id' => $request->user()->id,
            'cantidad' => $request->cantidad,
            'monto_unitario' => $arancel->monto,

            // CAMBIA ÚNICAMENTE ESTA LÍNEA AQUÍ:
            'codigo_misa' => $arancel->codigo_arancel,

            'descripcion' => $request->descripcion,
            'estado' => 'ENTREGADO',
            'fecha' => now()->toDateString(),
        ]);



        return response()->json(
            $orden->load(['empresa', 'arancel', 'ventanilla', 'usuario']),
            201
        );
    }

    // Cambiado a $id manual para asegurar compatibilidad en Laravel 12
    public function show($id)
    {
        $ordenPago = OrdenPago::with(['empresa', 'arancel', 'ventanilla', 'usuario'])->findOrFail($id);
        return response()->json($ordenPago);
    }

    // Cambiado a $id manual para asegurar compatibilidad en Laravel 12
    public function update(Request $request, $id)
    {
        $request->validate([
            // Ajustado estrictamente a tus nuevas reglas de Postgres
            'estado' => 'sometimes|required|in:PENDIENTE,ENTREGADO,ANULADO',
            'descripcion' => 'nullable|string|max:500',
            'cantidad' => 'sometimes|required|integer|min:1',
        ]);

        $ordenPago = OrdenPago::findOrFail($id);

        $ordenPago->update($request->only('estado', 'descripcion', 'cantidad'));

        return response()->json(
            $ordenPago->fresh()->load(['empresa', 'arancel', 'ventanilla', 'usuario'])
        );
    }

    // Cambiado a $id manual para asegurar compatibilidad en Laravel 12
    public function destroy($id)
    {
        $ordenPago = OrdenPago::findOrFail($id);
        $ordenPago->delete();
        return response()->json(null, 204);
    }
    // MÉTODO PARA FABRICAR EL REPORTE OFICIAL DE AGEMED DESDE POSTGRES
    public function generarPdf($id)
    {
        // Buscamos la orden con sus relaciones cargadas desde la base de datos
        $orden = \App\Models\OrdenPago::with(['empresa', 'arancel'])->find($id);

        if (!$orden) {
            return response()->json(['mensaje' => 'Orden de pago no encontrada'], 404);
        }

        // Convertimos el monto numérico a texto legal de forma automática
        $textoLiteral = $this->convertirNumeroALetras($orden->monto_total);

        // Cargamos la plantilla Blade pasándole todas las variables dinámicas
        $pdf = Pdf::loadView('reportes.orden_pago', compact('orden', 'textoLiteral'))
            ->setPaper('letter', 'portrait'); // Forzamos tamaño Carta vertical estricto

        // Abre el documento en modo previsualización en la pestaña del navegador
        return $pdf->stream("Orden_Pago_{$orden->numero_orden}.pdf");
    }

    // Convertidor interno a Bolivianos en mayúsculas
    // Convertidor nativo independiente para Bolivia (No requiere extensiones de servidor)
    private function convertirNumeroALetras($numero)
    {
        $entero = floor($numero);
        $centavos = str_pad(round(($numero - $entero) * 100), 2, '0', STR_PAD_LEFT);

        if ($entero == 0) {
            return "SON: CERO $centavos/100 BOLIVIANOS";
        }

        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        $convertirCifras = function ($n) use ($unidades, $decenas, $especiales, $centenas) {
            $res = '';
            $c = floor($n / 100);
            $d = floor(($n % 100) / 10);
            $u = $n % 10;

            if ($c > 0)
                $res .= ($c == 1 && $d == 0 && $u == 0 ? 'CIEN' : $centenas[$c]) . ' ';
            if ($d == 1) {
                $res .= $especiales[$u] . ' ';
            } else {
                if ($d > 0)
                    $res .= $decenas[$d] . ($u > 0 ? ' Y ' : ' ');
                if ($u > 0)
                    $res .= $unidades[$u] . ' ';
            }
            return trim($res);
        };

        $texto = '';
        $millones = floor($entero / 1000000);
        $miles = floor(($entero % 1000000) / 1000);
        $cientos = $entero % 1000;

        if ($millones > 0)
            $texto .= ($millones == 1 ? 'UN MILLON' : $convertirCifras($millones) . ' MILLONES') . ' ';
        if ($miles > 0)
            $texto .= ($miles == 1 ? 'MIL' : $convertirCifras($miles) . ' MIL') . ' ';
        if ($cientos > 0 || $texto == '')
            $texto .= $convertirCifras($cientos);

        return "SON: " . trim($texto) . " $centavos/100 BOLIVIANOS";
    }


}


