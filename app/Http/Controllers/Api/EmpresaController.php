<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(
            Empresa::orderBy('razon_social')->get()
        );
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        return response()->json(
            Empresa::where('razon_social', 'ilike', "%{$q}%")
                ->orderBy('razon_social')
                ->limit(15)
                ->get()
        );
    } // <- CORREGIDO: Aquí se cerró correctamente el método search

    public function store(Request $request)
    {
        try {
            // Mapeo: Si Angular envía 'empresa', lo asignamos a 'razon_social'
            $razonSocial = $request->input('empresa') ?? $request->input('razon_social');
            $nit = $request->input('nit');

            // Validación manual rápida
            if (empty($razonSocial)) {
                return response()->json(['status' => 'error', 'message' => 'La razón social es obligatoria.'], 422);
            }

            // Validar unicidad del NIT manualmente si es que se envía
            if (!empty($nit)) {
                $existe = DB::table('empresas')->where('nit', $nit)->exists();
                if ($existe) {
                    return response()->json(['status' => 'error', 'message' => 'Este número de NIT ya está registrado.'], 422);
                }
            }

            // Inserción directa en PostgreSQL usando Query Builder (obtenemos el ID generado)
            $id = DB::table('empresas')->insertGetId([
                'razon_social' => $razonSocial,
                'nit' => $nit
            ]);

            // Retornamos el objeto estructurado exactamente como lo espera Angular para pre-seleccionarlo
            return response()->json([
                'id' => $id,
                'razon_social' => $razonSocial,
                'nit' => $nit
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error crítico en Postgres: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cambia el método show para que use el $id directo:
    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json($empresa, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'razon_social' => 'sometimes|required|string|max:300',
            'nit' => 'nullable|string|max:20|unique:empresas,nit,' . $id,
        ]);

        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->only('razon_social', 'nit'));

        return response()->json($empresa, 200);
    }

    // Cambia el método destroy para que use el $id directo:
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();
        return response()->json(null, 204);
    }
}
