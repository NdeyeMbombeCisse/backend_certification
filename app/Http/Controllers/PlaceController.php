<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Models\Place;

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

    public function getPlacesByCategorie($categorieId)
    {
        return Place::where('categorie_id', $categorieId)->get();
    }
}



