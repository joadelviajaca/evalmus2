<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $user = $request->user();

        // Solo evaluadores pueden usar el frontend
        if (!$user->hasRole('evaluador')) {
            abort(403, 'No autorizado.');
        }

        $token = $user->createToken('react-frontend')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'email'=> $user->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
