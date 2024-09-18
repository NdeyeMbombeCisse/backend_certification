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
    public function store(StoreTarifRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarif $tarif)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarif $tarif)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTarifRequest $request, Tarif $tarif)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarif $tarif)
    {
        //
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
