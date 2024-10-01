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

    // Validar token
    public function validateToken(Request $request)
    {
        // Validar que se envíe el token
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        dd($request->input('token')); // Muestra el token y detiene la ejecución

        // Verificar si el token es válido
        $user = Auth::guard('api')->user(); // Verifica si el token está asociado a un usuario


        if ($user) {
            return response()->json(['valid' => true, 'user' => $user], 200);
        } else {
            return response()->json(['valid' => false], 401);
        }
    }
}
