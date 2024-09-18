<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrajetRequest;
use App\Http\Requests\UpdateTrajetRequest;
use App\Models\Trajet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trajets = Trajet::all();
        return response()->json($trajets);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $trajet = Trajet::findOrFail($id);

        $validatedData = $request->validate([
            'date_depart' => 'sometimes|date',
            'date_arrivee' => 'sometimes|date',
            'lieu_depart' => 'sometimes|string|max:255',
            'lieu_arrive' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'statut' => 'sometimes|boolean',
            'heure_embarquement' => 'sometimes|string|max:255',
            'heure_depart' => 'sometimes|string|max:255',
            'bateau_id' => 'sometimes|exists:bateaus,id'
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($trajet->image) {
                Storage::disk('public')->delete($trajet->image);
            }

            $imagePath = $request->file('image')->store('trajet_images', 'public');
            $trajet->image = $imagePath;
        }

        // Mise à jour des champs
        $trajet->update($validatedData);

        return response()->json(['message' => 'Trajet mis à jour avec succès', 'trajet' => $trajet], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trajet = Trajet::findOrFail($id);

        // Supprimer l'image associée
        if ($trajet->image) {
            Storage::disk('public')->delete($trajet->image);
        }

        $trajet->delete();

        return response()->json(['message' => 'Trajet supprimé avec succès'], 200);
    }
}
