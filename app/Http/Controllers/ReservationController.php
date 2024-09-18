<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Place;
use Illuminate\Http\Request;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return response()->json($reservations);
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
    // public function store(StoreReservationRequest $request)
    // {
    //     //
    // }

    public function store(Request $request)
    {
        // Validation des données de réservation
        $request->validate([
            'date_reservation' => 'required|date',
            'place_id' => 'required|exists:places,id',
            'user_id' => 'required|exists:users,id',
        ]);
    
        // Trouver la place
        $place = Place::find($request->input('place_id'));
    
        // Vérifier si la place est déjà réservée
        if ($place->is_reserved) {
            return response()->json(['message' => 'Place déjà réservée.'], 400); // 400 Bad Request
        }
    
        // Créer la réservation
        $reservation = Reservation::create([
            'date_reservation' => $request->input('date_reservation'),
            'statut' => false,
            'place_id' => $request->input('place_id'),
            'trajet_id' => $request->input('trajet_id'),
            'user_id' => $request->input('user_id'),
            // 'is_paid' => false, // Par défaut, la réservation n'est pas payée
        ]);
    
        // Marquer la place comme réservée
        $place->update(['is_reserved' => true]);
    
        return response()->json([
            'message' => 'Réservation créée avec succès.',
            'reservation' => $reservation,
        ], 201); // 201 Created
    }
    
    /**
     * Display the specified resource.
     */
    // public function show(Reservation $reservation)
    // {
    //     //
    // }


    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update($request->all());
        return response()->json($reservation, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return response()->json(['message' => 'Reservation supprimée avec succès'], 200);
    }


    // verifier si une reservation est deja payee
    public function markAsPaid($id)
    {
        // Trouver la réservation par son ID
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée.'], 404); // 404 Not Found
        }

        // Mettre à jour le statut de paiement
        $reservation->update(['is_paid' => true]);

        // Préparer les données pour le code QR
        $reservationDetails = [
            'reservation_id' => $reservation->id,
            'place_id' => $reservation->place_id,
            'user_id' => $reservation->user_id,
            'date_reservation' => $reservation->date_reservation,
            'user_details' => [
                'name' => $reservation->user->name,
                'email' => $reservation->user->email,
            ],
        ];

        // Générer le code QR et le sauvegarder en tant qu'image
        $qrCode = QrCode::format('png')->size(200)->generate(json_encode($reservationDetails));
        $fileName = 'qr_codes/reservation_' . $reservation->id . '.png';
        Storage::put($fileName, $qrCode);

        // Retourner l'URL de l'image QR Code
        $qrCodeUrl = Storage::url($fileName);

        return response()->json([
            'reservation' => $reservation,
            'qr_code_url' => $qrCodeUrl // Inclure l'URL du code QR dans la réponse JSON
        ], 200); // Retourner un statut 200 (OK)
    }
}

