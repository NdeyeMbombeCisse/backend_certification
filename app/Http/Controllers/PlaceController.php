<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Models\Place;
use Illuminate\Http\Request;

use App\Models\Bateau;
use App\Models\Trajet;



class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $places = Place::all();
        return response()->json($places);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlaceRequest $request)
    {
        // Pas de validation, on crée directement la place
    $place = Place::create($request->all());

    return response()->json($place, 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        return response()->json($place);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $place->update($request->all());

    return response()->json($place);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();
        return response()->json(['message' => 'place supprimer avec succes supprimée avec succès'], 200);
    }


    public function getPlacesByTrajetId($trajetId) {
        $places = Place::where('trajet_id', $trajetId)->get();
        return response()->json($places);
    }

    // places par categorie

    // public function getPlacesByCategorie($categorieId)
    // {
    //     return Place::where('categorie_id', $categorieId)->get();
    // }


    public function getPlacesByCategorie($trajetId, $categorieId)
{
    // Récupérer le trajet pour vérifier son existence
    $trajet = Trajet::find($trajetId);
    if (!$trajet) {
        return response()->json(['message' => 'Trajet non trouvé.'], 404);
    }

    // Récupérer les places associées au trajet et à la catégorie
    $places = Place::where('categorie_id', $categorieId)
                    ->where('id_bateau', $trajet->bateau_id) // Utilisez id_bateau ici
                    ->get();

    return response()->json($places);
}

    


    // places restantes
    public function getPlacesRestantes()
    {
        $trajets = Trajet::with('reservations')->where('statut', 1)->get(); // Récupère uniquement les trajets avec le statut 1
        $placesRestantes = [];
    
        foreach ($trajets as $trajet) {
            $totalReservations = $trajet->reservations->count();
            $placesRestantes[$trajet->id] = $trajet->total_places - $totalReservations; // Ajustez 'total_places' selon votre modèle
        }
    
        return response()->json(['data' => $placesRestantes]);
    }

    // places disponibles

    public function  getAvailablePlacesForTrajet($trajetId)
{
    // Récupérer les réservations pour le trajet donné
    $reservedPlaces = Reservation::where('trajet_id', $trajetId)
        ->pluck('place_id'); // Récupérer uniquement les IDs des places réservées

    return response()->json(['reserved_places' => $reservedPlaces]);
}



    // chnager le statut de la place
    // public function updatePlaceReservation(Request $request, $id)
    // {
    //     // Valider la requête
    //     $request->validate([
    //         'is_reserved' => 'required|boolean',
    //     ]);

    //     // Trouver la place par ID
    //     $place = Place::find($id);

    //     if (!$place) {
    //         return response()->json(['message' => 'Place non trouvée'], 404);
    //     }

    //     // Mettre à jour le statut de réservation
    //     $place->is_reserved = $request->input('is_reserved');
    //     $place->save();

    //     return response()->json(['message' => 'Statut de réservation mis à jour avec succès', 'place' => $place], 200);
    // }




    public function updatePlaceReservation(Request $request, $id)
    {
        // Valider la requête
        $request->validate([
            'is_reserved' => 'required|boolean',
        ]);
    
        // Trouver la place par ID
        $place = Place::find($id);
    
        if (!$place) {
            return response()->json(['message' => 'Place non trouvée'], 404);
        }
    
        // Optionnel : Vérifier que la place appartient à un trajet spécifique
        // Si vous avez un ID de trajet, vous pouvez le passer en paramètre de la requête
        $trajetId = $request->input('trajet_id');
    
        // Vérifiez que la place appartient au trajet spécifié
        if ($place->trajet_id != $trajetId) {
            return response()->json(['message' => 'La place ne correspond pas au trajet spécifié'], 400);
        }
    
        // Mettre à jour le statut de réservation
        $place->is_reserved = $request->input('is_reserved');
        $place->save();
    
        return response()->json(['message' => 'Statut de réservation mis à jour avec succès', 'place' => $place], 200);
    }
    

    // PlaceController.php

    public function getPlacesByTrajet($trajetId)
    {
        try {
            // Récupérer le trajet
            $trajet = Trajet::with('bateau')->findOrFail($trajetId);
    
            // Vérifiez si le bateau est associé au trajet
            if (!$trajet->bateau) {
                return response()->json(['message' => 'Aucun bateau associé à ce trajet.'], 404);
            }
    
            // Récupérer les places associées au bateau
            $places = Place::where('id_bateau', $trajet->bateau_id)
                           ->with('categorie') // Récupérer les catégories en même temps
                           ->get();
    
            return response()->json($places);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

}




