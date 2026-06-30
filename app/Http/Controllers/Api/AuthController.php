<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::with('ventanilla')
            ->where('username', $request->username)
            ->where('activo', true)
            ->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'username' => $usuario->username,
                'ventanilla' => [
                    'id' => $usuario->ventanilla->id,
                    'numero' => $usuario->ventanilla->numero,
                    'responsable' => $usuario->ventanilla->responsable,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()->load('ventanilla')
        );
    }
}