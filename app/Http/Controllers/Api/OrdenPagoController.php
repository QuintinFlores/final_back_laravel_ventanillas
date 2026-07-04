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

        $arancel = Arancel::findOrFail($request->arancel_id);
        $anioActual = now()->year;

        // 1. Buscamos y actualizamos el secuencial fuera de la transacción para asegurar el número inmediato
        $registroSecuencial = \DB::table('arancel_secuenciales')
            ->where('arancel_id', $arancel->id)
            ->where('anio', $anioActual)
            ->first();

        if (!$registroSecuencial) {
            \DB::table('arancel_secuenciales')->insert([
                'arancel_id' => $arancel->id,
                'anio' => $anioActual,
                'secuencial' => 1,
                'updated_at' => now()
            ]);
            $nuevoSecuencial = 1;
        } else {
            $nuevoSecuencial = $registroSecuencial->secuencial + 1;
            \DB::table('arancel_secuenciales')
                ->where('id', $registroSecuencial->id)
                ->update([
                    'secuencial' => $nuevoSecuencial,
                    'updated_at' => now()
                ]);
        }

        // 2. Construimos el código MISA garantizado
        $codigoGenerado = "{$arancel->codigo_arancel}-{$nuevoSecuencial}-{$anioActual}";

        // 3. Forzamos la inserción usando DB::table para saltar cualquier restricción oculta del Modelo ($fillable)
        $idOrden = \DB::table('ordenes_pago')->insertGetId([
            'empresa_id' => $request->empresa_id,
            'arancel_id' => $request->arancel_id,
            'ventanilla_id' => $request->user()->ventanilla_id,
            'usuario_id' => $request->user()->id,
            'cantidad' => $request->cantidad,
            'monto_unitario' => $arancel->monto,

            // Aquí inyectamos el código directamente en PostgreSQL
            'codigo_misa' => $codigoGenerado,

            'descripcion' => $request->descripcion,
            'estado' => 'ENTREGADO',
            'fecha' => now()->toDateTimeString(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 4. Retornamos la orden fresca con sus relaciones para la respuesta JSON de Postman y Angular
        $ordenFinal = OrdenPago::with(['empresa', 'arancel', 'ventanilla', 'usuario'])->findOrFail($idOrden);

        return response()->json($ordenFinal, 201);
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

    // MÉTODO PARA PREVISUALIZAR EL CÓDIGO MISA EN POSTMAN / ANGULAR
    public function obtenerSiguienteSecuencial($id)
    {
        // 1. Buscamos que el arancel exista en Postgres
        $arancel = Arancel::find($id);

        if (!$arancel) {
            return response()->json(['error' => 'Arancel no encontrado'], 404);
        }

        // 2. Obtenemos el año actual en base al servidor
        $anioActual = now()->year;

        // 3. Consultamos el estado actual del secuencial en tu tabla arancel_secuenciales
        $registro = \DB::table('arancel_secuenciales')
            ->where('arancel_id', $id)
            ->where('anio', $anioActual)
            ->first();

        // 4. Si existe registro, el siguiente será +1. Si no existe, iniciará en 1.
        $siguienteSecuencial = $registro ? ($registro->secuencial + 1) : 1;

        // 5. Armamos la estructura exacta que me pediste: [CODIGO_ARANCEL]-[SECUENCIAL]-[AÑO]
        $codigoMisaPrevisualizado = "{$arancel->codigo_arancel}-{$siguienteSecuencial}-{$anioActual}";

        // 6. Retornamos la respuesta limpia en formato JSON
        return response()->json([
            'codigo_misa' => $codigoMisaPrevisualizado
        ]);
    }



}


