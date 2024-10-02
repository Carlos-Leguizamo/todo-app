<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    // Login de usuario
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json(['token' => $token], 200);
    }


    // Información del usuario autenticado
    public function user()
    {
        return response()->json(Auth::user());
    }

    public function validateToken(Request $request)
    {
        // Obtener el usuario autenticado usando el token de la cabecera Authorization
        $user = Auth::guard('api')->user();

        // Verificar si el usuario está autenticado (si el token es válido)
        if ($user) {
            return response()->json(['valid' => true, 'user' => $user], 200);
        } else {
            return response()->json(['valid' => false, 'message' => 'Invalid token'], 401);
        }
    }
}
