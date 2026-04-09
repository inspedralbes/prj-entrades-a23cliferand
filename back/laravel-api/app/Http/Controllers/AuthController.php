<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usuari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar un usuari nou
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuaris,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = Usuari::create([
            'nom' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuari registrat correctament',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * Iniciar sessió i retornar token
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = Usuari::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credencials incorrectes.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login correcte',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Retornar l'usuari autenticat
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Tancar sessió eliminant el token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout correcte'
        ]);
    }
}
