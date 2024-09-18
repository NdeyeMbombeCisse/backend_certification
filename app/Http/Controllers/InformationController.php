<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateInformationRequest;
use App\Models\Information;
use Illuminate\Http\Request;


class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informations = Information::all();
         return response()->json($informations);
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
    // Créer une nouvelle information sans validation
    $information = Information::create($request->all());

    // Retourner la réponse avec le status 201 (Créé)
    return response()->json($information, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Information $information)
    {
        return response()->json($information);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Information $information)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        

        $information = Information::findOrFail($id);
    
        // Updateinformationwith the new data
        $information->update($request->all());
    
        // Return a JSON response with the updated Bateau
        return response()->json($information, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $information = Information::findOrFail($id);
        $information->delete();
        return response()->json(['message' => 'Information supprimée avec succès'], 200);
    }
}
