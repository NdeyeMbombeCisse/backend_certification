<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrajetRequest;
use App\Http\Requests\UpdateTrajetRequest;
use App\Models\Trajet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Place; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class TrajetController extends Controller
{
   

    public function index()
{
    $trajets = Trajet::with(['bateau', 'reservations.place'])->get(); // Eager Loading

    foreach ($trajets as $trajet) {
        // Vérifiez que la colonne existe bien
        $totalPlaces = Place::where('id_bateau', $trajet->bateau_id)->count(); // Utilisez 'id_bateau' pour la condition
        $placesReservees = $trajet->reservations->count(); // Nombre de réservations pour ce trajet
        $trajet->placesRestantes = $totalPlaces - $placesReservees; // Calcul des places restantes
    }

    return response()->json(['data' => $trajets]);
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
    public function store(Request $request)
    {
        // Gestion de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trajet_images', 'public');
        }
    
        // Création du trajet
        $trajet = Trajet::create([
            'date_depart' => $request->input('date_depart'),
            'date_arrivee' => $request->input('date_arrivee'),
            'lieu_depart' => $request->input('lieu_depart'),
            'lieu_arrive' => $request->input('lieu_arrive'),
            'image' => $imagePath, // Chemin de l'image stockée
            'statut' => $request->input('statut'),
            'heure_embarquement' => $request->input('heure_embarquement'),
            'heure_depart' => $request->input('heure_depart'),
            'bateau_id' => $request->input('bateau_id')
        ]);
    
        return response()->json(['message' => 'Trajet créé avec succès', 'trajet' => $trajet], 201);
    }
    
    // compter le nombre d etrajet en cours

    public function countTrajets()
{
    $count = Trajet::where('statut', 1)->count(); // Compte les trajets avec le statut 1
    return response()->json(['count' => $count]);
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $trajet = Trajet::findOrFail($id);
        return response()->json($trajet);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trajet $trajet)
    {
        //
    }

  


public function update(Request $request, $id)
{
    // Log des données de la requête
    \Log::info('Requête de mise à jour reçue:', $request->all());
    
    $trajet = Trajet::findOrFail($id);

    // Gestion de l'image
    if ($request->hasFile('image')) {
        \Log::info('Image reçue pour le trajet:', ['image' => $request->file('image')]);
    
        // Supprimer l'ancienne image
        if ($trajet->image) {
            \Log::info('Suppression de l\'ancienne image:', ['image' => $trajet->image]);
            Storage::disk('public')->delete($trajet->image);
        }
    
        $imagePath = $request->file('image')->store('trajet_images', 'public');
        \Log::info('Nouvelle image enregistrée à:', ['path' => $imagePath]);
    
        $trajet->image = $imagePath;
    }

    // Mise à jour des champs
    $trajet->date_depart = $request->input('date_depart', $trajet->date_depart);
    $trajet->date_arrivee = $request->input('date_arrivee', $trajet->date_arrivee);
    $trajet->lieu_depart = $request->input('lieu_depart', $trajet->lieu_depart);
    $trajet->lieu_arrive = $request->input('lieu_arrive', $trajet->lieu_arrive);
    $trajet->statut = $request->input('statut', $trajet->statut);
    $trajet->heure_embarquement = $request->input('heure_embarquement', $trajet->heure_embarquement);
    $trajet->heure_depart = $request->input('heure_depart', $trajet->heure_depart);
    $trajet->bateau_id = $request->input('bateau_id', $trajet->bateau_id);

    // Enregistrement des modifications
    try {
        $trajet->save();
        return response()->json(['message' => 'Trajet mis à jour avec succès', 'trajet' => $trajet], 200);
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la mise à jour du trajet:', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Erreur lors de la mise à jour du trajet'], 500);
    }
}



    // suppression

    public function destroy($id)
{
    // Trouver le trajet ou échouer si non trouvé
    $trajet = Trajet::findOrFail($id);

    // Supprimer toutes les réservations associées au trajet
    Reservation::where('trajet_id', $id)->delete();

    // Supprimer l'image associée s'il y en a une
    if ($trajet->image) {
        Storage::disk('public')->delete($trajet->image);
    }

    // Supprimer le trajet
    $trajet->delete();

    // Retourner une réponse JSON
    return response()->json(['message' => 'Trajet supprimé avec succès'], 200);
}


// afficher un trajet en fontion de son id
public function getTrajetById($id)
    {
        // Chercher le trajet par son identifiant
        $trajet = Trajet::find($id);

        // Vérifier si le trajet existe
        if (!$trajet) {
            return response()->json([
                'success' => false,
                'message' => 'Trajet non trouvé'
            ], 404);
        }

        // Retourner les données du trajet si trouvé
        return response()->json([
            'success' => true,
            'data' => $trajet
        ], 200);
    }

    // places restante pour tous les trajets
    public function getPlacesRestantes()
    {
        $trajets = Trajet::with('reservations')->get(); // Récupère tous les trajets avec leurs réservations
        $placesRestantes = [];
    
        foreach ($trajets as $trajet) {
            $totalReservations = $trajet->reservations->count();
            $placesRestantes[$trajet->id] = $trajet->total_places - $totalReservations; // Ajustez 'total_places' selon votre modèle
        }
    
        return response()->json(['data'=>$placesRestantes]);
    }

    // update statut

    public function updateStatut(Request $request, $id)
{
    // Validation des données
    $request->validate([
        'statut' => 'required|boolean', // Assurez-vous que le statut est un boolean
    ]);

    // Trouver le trajet par ID
    $trajet = Trajet::findOrFail($id);
    
    // Mettre à jour le statut
    $trajet->statut = $request->statut;
    $trajet->save();

    return response()->json(['message' => 'Statut du trajet mis à jour avec succès.', 'data' => $trajet], 200);
}

// afficher les trajets en cours



public function getTrajetsEnCours()
{
    $trajets = Trajet::with(['bateau', 'reservations.place'])
    ->where('statut', 1) // Filter where 'statut' is 1
    ->get();

foreach ($trajets as $trajet) {
    // Calculate total places and reserved places
    $totalPlaces = Place::where('id_bateau', $trajet->bateau_id)->count(); 
    $placesReservees = $trajet->reservations->count(); 
    $trajet->placesRestantes = $totalPlaces - $placesReservees; 
}

// Log to ensure correct data is being fetched
\Log::info($trajets);

return response()->json(['data' => $trajets]);
}


// les trajet enregistrer par semaine
public function getTrajetsByWeek()
{
    // Récupérer les trajets publiés (statut = true) pour le mois en cours
    $trajets = Trajet::where('statut', true)
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->get();

    // Grouper les trajets par semaine
    $trajetsByWeek = $trajets->groupBy(function($trajet) {
        return Carbon::parse($trajet->created_at)->weekOfYear;
    });

    // Compter le nombre de trajets par semaine
    $weeklyData = $trajetsByWeek->map(function($week) {
        return $week->count(); // Compte le nombre de trajets dans chaque semaine
    });

    return response()->json([
        'labels' => $weeklyData->keys()->map(function($week) {
            return 'Semaine ' . $week; // Optionnel : formatage des labels
        }),
        'trajets' => $weeklyData->values(),
    ]);
}





}
