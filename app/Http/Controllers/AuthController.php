<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;



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


//    affichage des users
public function afficher_user(Request $request): JsonResponse
    {
        // Récupérer tous les utilisateurs
        $users = User::all();

        // Retourner les utilisateurs en tant que réponse JSON
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        // Initialiser le chemin de l'image à null
        $imagePath = null;
    
        // Gestion du téléchargement de l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trajet_images', 'public');
        }
    
        // Création de l'utilisateur sans validation
        $user = User::create([
            'email' => $request->input('email'),
            'prenom' => $request->input('prenom'),
            'nom' => $request->input('nom'),
            'telephone' => $request->input('telephone'),
            'numero_identite' => $request->input('numero_identite'),
            'nationnalite' => $request->input('nationnalite'),
            'password' => Hash::make($request->input('password')),
            'image' => $imagePath, // Enregistrer le chemin de l'image dans la base de données
        ]);
    
        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        // Trouver l'utilisateur par ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        // Gérer le téléchargement de l'image si elle est présente
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('user_images', 'public');
            $user->image = $imagePath;
        }

        // Mettre à jour les informations de l'utilisateur
        $user->email = $request->input('email', $user->email);
        $user->prenom = $request->input('prenom', $user->prenom);
        $user->nom = $request->input('nom', $user->nom);
        $user->telephone = $request->input('telephone', $user->telephone);
        $user->numero_identite = $request->input('numero_identite', $user->numero_identite);
        $user->nationnalite = $request->input('nationnalite', $user->nationnalite);

        // Mettre à jour le mot de passe uniquement s'il est fourni
        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Sauvegarder les modifications
        $user->save();

        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user,
        ], 200);
    }
}
    
