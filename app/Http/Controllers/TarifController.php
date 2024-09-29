<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifRequest;
use App\Http\Requests\UpdateTarifRequest;
use App\Models\Tarif;
use Illuminate\Http\Request;


class TarifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tarifs = Tarif::all();
        return response()->json($tarifs);
    }

   

    // tarif par categorie
    // public function getTarifsByCategorie($categorieId) {
    //     // Récupérer la nationalité de l'utilisateur connecté
    //     $nationalite = Auth::user()->nationalite;

    //     // Récupérer les tarifs correspondant à la catégorie et à la nationalité
    //     $tarifs = Tarif::where('categorie_id', $categorieId)
    //                    ->where('nationnalite', $nationalite)
    //                    ->get();

    //     return response()->json($tarifs);
    // }


    public function getTarifsByCategorie($placeId, $userNationality)
{
    // Trouver la place par son ID
    $place = Place::find($placeId);

    // Vérifier si la place existe
    if (!$place) {
        return response()->json(['message' => 'Place non trouvée.'], 404);
    }

    // Récupérer la catégorie de la place
    $categorie = $place->categorie;

    // Récupérer le tarif correspondant à la catégorie et à la nationalité
    $tarif = Tarif::where('categorie_id', $categorie->id)
                  ->where('nationnalite', $userNationality)
                  ->first();

    // Vérifier si un tarif a été trouvé
    if (!$tarif) {
        return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
    }

    return response()->json([
        'tarif' => $tarif,
    ], 200);
}


    public function tarifparcat(Request $request)
{
    $categorieId = $request->input('categorie_id');
    
    if ($categorieId) {
        $tarifs = Tarif::where('categorie_id', $categorieId)->get();
    } else {
        $tarifs = Tarif::all();
    }
    
    return response()->json($tarifs);
}

}
