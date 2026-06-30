<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arancel;
use Illuminate\Http\Request;

class ArancelController extends Controller
{
    public function index()
    {
        return response()->json(
            Arancel::orderBy('codigo_arancel')->get()
        );
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        return response()->json(
            Arancel::where('nombre_arancel', 'ilike', "%{$q}%")
                ->orWhere('codigo_arancel', 'ilike', "%{$q}%")
                ->orderBy('codigo_arancel')
                ->limit(15)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_arancel' => 'required|string|max:20|unique:aranceles,codigo_arancel',
            'nombre_arancel' => 'required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'monto' => 'required|numeric|min:0',
            'codigo_misa' => 'nullable|string|max:50|unique:aranceles,codigo_misa',
        ]);

        $arancel = Arancel::create($request->all());

        return response()->json($arancel, 201);
    }

    public function show(Arancel $arancel)
    {
        return response()->json($arancel);
    }

    public function update(Request $request, Arancel $arancel)
    {
        $request->validate([
            'codigo_arancel' => 'sometimes|required|string|max:20|unique:aranceles,codigo_arancel,' . $arancel->id,
            'nombre_arancel' => 'sometimes|required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'monto' => 'sometimes|required|numeric|min:0',
            'codigo_misa' => 'nullable|string|max:50|unique:aranceles,codigo_misa,' . $arancel->id,
        ]);

        $arancel->update($request->all());

        return response()->json($arancel);
    }

    public function destroy(Arancel $arancel)
    {
        $arancel->delete();
        return response()->json(null, 204);
    }
}