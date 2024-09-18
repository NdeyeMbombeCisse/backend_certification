<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBateauRequest;
use App\Http\Requests\UpdateBateauRequest;
use App\Models\Bateau;
use Illuminate\Http\Request;


class BateauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bateaux = Bateau::all();
        return response()->json($bateaux);
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
        $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|boolean',
        ]);

        $bateau = Bateau::create($request->all());

        return response()->json($bateau, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Bateau $bateau)
    {
        return response()->json($bateau);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bateau $bateau)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        // Find the Bateau by ID
        $bateau = Bateau::findOrFail($id);
    
        // Update the Bateau with the new data
        $bateau->update($request->all());
    
        // Return a JSON response with the updated Bateau
        return response()->json($bateau, 200);
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $bateau = Bateau::findOrFail($id);
        $bateau->delete();

        return response()->json(['message' => 'Trajet supprimé avec succès'], 200);
    }

    

}
