<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Place;
use App\Models\User;
use App\Models\Trajet;
use Illuminate\Http\Request;
use App\Notifications\PaymentNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


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

  


// public function store(Request $request)
// {
//     // Trouver la place
//     $place = Place::find($request->input('place_id'));

//     // Vérifier si la place est déjà réservée
//     if ($place->is_reserved) {
//         return response()->json(['message' => 'Place déjà réservée.'], 400); // 400 Bad Request
//     }

//     // Trouver le trajet
//     $trajet = Trajet::find($request->input('trajet_id'));

//     // Vérifier si le trajet existe (pas nécessaire si la validation 'exists' est déjà utilisée)
//     if (!$trajet) {
//         return response()->json(['message' => 'Trajet non trouvé.'], 404); // 404 Not Found
//     }

//     // Créer la réservation
//     $reservation = Reservation::create([
//         'statut' => false,
//         'place_id' => $request->input('place_id'),
//         'trajet_id' => $trajet->id, // Utiliser l'ID du trajet trouvé
//         'user_id' => $request->input('user_id'),
//     ]);

//     // Marquer la place comme réservée
//     $place->update(['is_reserved' => true]);

//     // Trouver l'utilisateur concerné
//     $user = User::find($request->input('user_id'));

//     $qrCodeContent = [
//         'reservation_id' => $reservation->id,
//         'user' => [
//             'id' => $user->id,
//             'name' => $user->name, // Assurez-vous d'avoir un champ 'name' ou autre
//             'email' => $user->email,
//         ],
//         'place' => [
//             'id' => $place->id,
//             'name' => $place->name, // Assurez-vous d'avoir un champ 'name' ou autre
//             'location' => $place->location, // Exemple d'autre champ
//         ],
//         'trajet' => [
//             'id' => $trajet->id,
//             'destination' => $trajet->destination, // Assurez-vous d'avoir un champ 'destination' ou autre
//             'date' => $trajet->date, // Assurez-vous d'avoir un champ 'date' ou autre
//         ],
//     ];

//     // Générer le QR code pour la réservation
//     $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent)); // Ajustez la taille au besoin
//     // Enregistrer le QR code en tant qu'image
//     $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg'; // Path to save the QR code image
//     file_put_contents(public_path($qrCodePath), $qrCode); // Save QR code to public directory

//     // Mettre à jour la réservation pour inclure le QR code
//     $reservation->update(['qr_code' => url($qrCodePath)]); // Stocker l'URL du QR code dans la base de données

//     // Envoyer la notification à l'utilisateur
//     // $user->notify(new PaimentNotifcation($reservation));

//     return response()->json([
//         'message' => 'Réservation créée avec succès et notification envoyée.',
//         'reservation' => $reservation,
//         'qr_code' => url($qrCodePath), // Return the URL of the QR code
//     ], 201); // 201 Created
// }


public function store(Request $request)
{
    // Trouver la place
    $place = Place::find($request->input('place_id'));

    // Vérifier si la place est déjà réservée
    if ($place->is_reserved) {
        return response()->json(['message' => 'Place déjà réservée.'], 400); // 400 Bad Request
    }

    // Trouver le trajet
    $trajet = Trajet::find($request->input('trajet_id'));

    // Vérifier si le trajet existe
    if (!$trajet) {
        return response()->json(['message' => 'Trajet non trouvé.'], 404); // 404 Not Found
    }

    // Récupérer la nationalité de l'utilisateur
    $user = User::find($request->input('user_id'));
    $userNationality = $user->nationnalite; // Assurez-vous que 'nationalite' est un champ de l'utilisateur

    // Trouver la catégorie de la place
    $categorie = $place->categorie; // Assurez-vous que la relation est définie

    // Récupérer le tarif correspondant à la catégorie et à la nationalité
    $tarif = Tarif::where('categorie_id', $categorie->id)
                  ->where('nationnalite', $userNationality)
                  ->first();

    // Vérifier si un tarif a été trouvé
    if (!$tarif) {
        return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
    }

    // Créer la réservation
    $reservation = Reservation::create([
        'statut' => false,
        'place_id' => $request->input('place_id'),
        'trajet_id' => $trajet->id,
        'user_id' => $request->input('user_id'),
        'tarif_id' => $tarif->id, // Enregistrer le tarif de la réservation
    ]);

    // Marquer la place comme réservée
    $place->update(['is_reserved' => true]);

    // Contenu du QR Code
    $qrCodeContent = [
        'reservation_id' => $reservation->id,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'place' => [
            'id' => $place->id,
            'name' => $place->name,
            'location' => $place->location,
        ],
        'trajet' => [
            'id' => $trajet->id,
            'destination' => $trajet->destination,
            'date' => $trajet->date,
        ],
        'tarif' => [
            'montant' => $tarif->montant, // Assurez-vous que 'montant' est un champ de tarif
            'nationnalite' => $tarif->nationnalite,
        ],
    ];

    // Générer le QR code pour la réservation
    $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
    $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
    file_put_contents(public_path($qrCodePath), $qrCode);

    // Mettre à jour la réservation pour inclure le QR code
    $reservation->update(['qr_code' => url($qrCodePath)]);

    return response()->json([
        'message' => 'Réservation créée avec succès.',
        'reservation' => $reservation,
        'qr_code' => url($qrCodePath),
    ], 201); // 201 Created
}







// detail
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
            'user_details' => [
                'name' => $reservation->user->prenom,
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


    // les reservation de l'utilisateur connecte


public function getUserReservations()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
    }

    $reservations = Reservation::where('user_id', $user->id)
                                ->with(['place.categorie.tarifs', 'trajet', 'user']) // Ajoutez 'user' ici
                                ->get();

    foreach ($reservations as $reservation) {
        $categorie = $reservation->place->categorie;
        $tarif = $categorie->tarifs()->where('nationnalite', $user->nationnalite)->first();
        $reservation->tarif = $tarif; // Ajoutez le tarif complet ici
    }

    return response()->json($reservations); // Assurez-vous de retourner les réservations
}





// App\Models\Reservation.php

public function getTarifAttribute()
{
    // Récupérer la catégorie de la place associée
    $categorie = $this->place->categorie;

    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Vérifier si la catégorie existe et récupérer le tarif correspondant à la nationalité de l'utilisateur
    if ($categorie) {
        $tarif = Tarif::where('categorie_id', $categorie->id)
                      ->where('nationnalite', $user->nationnalite)
                      ->first();

        // Retourner le tarif ou null si aucun tarif n'est trouvé
        return $tarif ? $tarif->tarif : null;
    }

    // Si la catégorie n'existe pas, retourner null
    return null;
}






    // qr code

    public function generateQrCode(Request $request)
    {
        // Récupérer les données pour le QR code (ces données viennent de la requête du frontend)
        $reservationData = $request->input('reservation');
        $trajetData = $request->input('trajet');
        $userData = $request->input('user');

        // Créer une chaîne de texte avec les informations nécessaires pour le QR code
        $qrContent = "Réservation pour le trajet ID: " . $trajetData['id'] . "\n" .
                     "Utilisateur ID: " . $userData['id'] . "\n" .
                     "Détails de la réservation:\n" . json_encode($reservationData);

        // Générer le QR code
        $qrCode = QrCode::format('png')->size(300)->generate($qrContent);

        // Sauvegarder le QR code dans le système de fichiers (par exemple, dans le dossier 'public/qrcodes')
        $fileName = 'qrcode_' . time() . '.png';
        Storage::put('public/qrcodes/' . $fileName, $qrCode);

        // Retourner l'URL du fichier QR code généré
        $qrCodeUrl = asset('storage/qrcodes/' . $fileName);

        return response()->json(['qrCodeUrl' => $qrCodeUrl], 200);
    }
    
}

