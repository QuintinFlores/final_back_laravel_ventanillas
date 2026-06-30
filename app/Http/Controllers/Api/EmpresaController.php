<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

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
    }

    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:300',
            'nit' => 'nullable|string|max:20|unique:empresas,nit',
        ]);

        $empresa = Empresa::create($request->only('razon_social', 'nit'));

        return response()->json($empresa, 201);
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