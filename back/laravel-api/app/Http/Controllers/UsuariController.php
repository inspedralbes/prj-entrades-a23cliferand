<?php

namespace App\Http\Controllers;

use App\Models\Usuari;
use Illuminate\Http\Request;

class UsuariController extends Controller
{
    /**
     * Mostra una llista de tots els usuaris.
     */
    public function index()
    {
        return response()->json(Usuari::all(), 200);
    }

    /**
     * Emmagatzema un nou usuari a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:usuaris,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $usuari = Usuari::create($validated);
            return response()->json($usuari, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra l'usuari especificat.
     */
    public function show($id)
    {
        $usuari = Usuari::find($id);

        if (!$usuari) {
            return response()->json(['error' => 'Usuari no trobat'], 404);
        }

        return response()->json($usuari, 200);
    }

    /**
     * Actualitza l'usuari especificat a la base de dades.
     */
    public function update(Request $request, $id)
    {
        $usuari = Usuari::find($id);

        if (!$usuari) {
            return response()->json(['error' => 'Usuari no trobat'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:usuaris,email,' . $id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        try {
            $usuari->update($validated);
            return response()->json($usuari, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Elimina l'usuari especificat de la base de dades.
     */
    public function destroy($id)
    {
        $usuari = Usuari::find($id);

        if (!$usuari) {
            return response()->json(['error' => 'Usuari no trobat'], 404);
        }

        $usuari->delete();
        return response()->json(['message' => 'Usuari eliminat correctament'], 200);
    }
}
