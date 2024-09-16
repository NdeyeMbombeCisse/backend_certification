<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Créer une variable qui va contenir le résultat de la validation
        $validator = Validator::make(
            $request->all(),
            // Les règles de validation
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]
        );

        // Condition pour vérifier s'il y a des erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Récupération des données de l'authentification
        $credentials = $request->only('email', 'password');
        // Utilisation de la fonction auth pour gérer l'authentification
        $token = auth()->attempt($credentials);

        if (!$token) {
            return response()->json(["message" => "Informations de connexion incorrectes"], 401);
        }

        return response()->json([
            "access_token" => $token,
            "token_type" => "bearer",
            "user" => auth()->user(),
            "expires_in" => env("JWT_TTL") * 60 . " secondes"
        ]);
    }

    public function logout()
    {

        auth()->logout();
        return response()->json(["message" => "deconnexion reussie"]);
    }

   public function refresh(){
    $token = auth()->refresh();
    return response()->json([
        "access_token" => $token,
        "token_type" => "bearer",
        "user" => auth()->user(),
        "expires_in" => env("JWT_TTL") * 60 . " secondes"

    ]);
   }
}