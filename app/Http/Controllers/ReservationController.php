<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Tarif;
use App\Models\Trajet;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Reservation;
use Illuminate\Http\Request;

use App\Notifications\ReservationCreated;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        

        $reservations = Reservation::with(['user', 'trajet', 'place.categorie.tarifs'])->get();

        // Parcourir chaque réservation pour associer le tarif basé sur la nationalité de l'utilisateur
        foreach ($reservations as $reservation) {
            $categorie = $reservation->place->categorie;
    
            // Filtrer les tarifs selon la nationalité de l'utilisateur
            $tarif = $categorie->tarifs()->where('nationnalite', $reservation->user->nationnalite)->first();
    
            // Ajouter le tarif à l'objet réservation
            $reservation->tarif = $tarif;
        }
    
        // Retourner les réservations avec tous les détails nécessaires
        return response()->json(['data' => $reservations]);
    }
    

 
    

    public function getReservationsByTrajet($trajetId)
{
    // Récupérer les réservations pour le trajet donné avec les relations nécessaires
    $reservations = Reservation::where('trajet_id', $trajetId)
        ->with(['user', 'place.categorie.tarifs','trajet']) // Inclure la place, sa catégorie et ses tarifs
        ->get();

    // Parcourir chaque réservation pour associer le tarif basé sur la nationalité de l'utilisateur
    foreach ($reservations as $reservation) {
        $categorie = $reservation->place->categorie;

        if ($categorie) {
            // Filtrer les tarifs selon la nationalité de l'utilisateur
            $tarif = $categorie->tarifs()->where('nationnalite', $reservation->user->nationnalite)->first();
            $reservation->tarif = $tarif; // Ajouter le tarif à l'objet réservation
        } else {
            $reservation->tarif = null; // Gérer les cas où la catégorie est null
        }
    }

    // Retourner les réservations avec tous les détails nécessaires
    return response()->json(['data' => $reservations]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

  


    public function store(Request $request)
    {
        // Trouver la place
        $place = Place::find($request->input('place_id'));
    
        // Vérifier si la place existe
        if (!$place) {
            return response()->json(['message' => 'Place non trouvée.'], 404); // 404 Not Found
        }
    
        // Trouver le trajet
        $trajet = Trajet::find($request->input('trajet_id'));
    
        // Vérifier si le trajet existe
        if (!$trajet) {
            return response()->json(['message' => 'Trajet non trouvé.'], 404); // 404 Not Found
        }
    
        // Vérifier si la place est déjà réservée pour ce trajet
        $existingReservation = Reservation::where('place_id', $place->id)
                                           ->where('trajet_id', $trajet->id)
                                           ->first();
    
        if ($existingReservation) {
            return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400); // 400 Bad Request
        }
    
        // Récupérer la nationalité de l'utilisateur
        $user = User::find($request->input('user_id'));
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404); // 404 Not Found
        }
        $userNationality = $user->nationnalite; 
    
        // Trouver la catégorie de la place
        $categorie = $place->categorie; 
    
        
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
            'place_id' => $place->id,
            'trajet_id' => $trajet->id,
            'user_id' => $user->id,
        ]);
    
        // Contenu du QR Code
        $qrCodeContent = [
            'reservation_id' => $reservation->id,
            'user' => [
                'id' => $user->id,
                'prenom' => $user->prenom,
                'nom' => $user->nom,
                'nationnalite' => $user->nationnalite,
                'CNI'=>$user->numero_identite
            ],
            'place' => [
                'id' => $place->id,
                'libelle' => $place->libelle,
                // 'location' => $place->location,
            ],
            'trajet' => [
                'id' => $trajet->id,
                'destination' => $trajet->lieu_arrive,
                'lieu_depart' => $trajet->lieu_depart,
                'lieu_arrive' => $trajet->lieu_arrive,
            ],
            'tarif' => [
                'tarif' => $tarif->tarif, // Assurez-vous que 'montant' est un champ de tarif
                'nationnalite' => $tarif->nationnalite,
            ],
        ];
    
        // Générer le QR code pour la réservation
        $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
        $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
        file_put_contents(public_path($qrCodePath), $qrCode);
    
        // Mettre à jour la réservation pour inclure le QR code
        $reservation->update(['qr_code' => url($qrCodePath)]);

        $ticketUrl = $this->generateTicket($reservation, $qrCodePath);

        // Notification à l'utilisateur avec l'URL du ticket
        $user->notify(new ReservationCreated($reservation, $ticketUrl));
    
        return response()->json([
            'message' => 'Réservation créée avec succès.',
            'reservation' => $reservation,
            'qr_code' => url($qrCodePath),
            'ticket_url' => $ticketUrl,
        ], 201); // 201 Created
    }
    

    // generation ticket
    private function generateTicket($reservation, $qrCodePath)
{
    // Créez une logique pour générer un ticket
    $ticketContent = [
        'reservation_id' => $reservation->id,
        'place' => $reservation->place->libelle,
        'user' => $reservation->user->prenom . ' ' . $reservation->user->nom,
        'email'=>$reservation->user->email,
        'telephone'=> $reservation->user->telephone,
        'CNI'=>$reservation->user->numero_identite,
        'trajet' => $reservation->trajet->lieu_depart . ' - ' . $reservation->trajet->lieu_arrive,
        'embarquement' =>$reservation->trajet->heure_embarquement,
        'depart'=>$reservation->trajet->heure_depart,
        'qr_code' => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(public_path($qrCodePath))), // Mettre le QR code ici
       ];

    // Vous pouvez utiliser une bibliothèque comme DomPDF ou SnappyPDF pour générer un PDF
    // Exemple fictif :
    $pdf = PDF::loadView('tickets.template', compact('ticketContent'));
    $ticketPath = 'tickets/ticket_' . $reservation->id . '.pdf';
    $pdf->save(public_path($ticketPath));

    return url($ticketPath); // Retourne l'URL du ticket généré
}




// enregistrer le ticket
public function downloadTicket($reservationId)
{
    // Trouvez la réservation par son ID
    $reservation = Reservation::findOrFail($reservationId); 

    // Chemin du QR code
    $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg'; 

    // Générez le ticket
    $ticketUrl = $this->generateTicket($reservation, $qrCodePath); 
    $user->notify(new ReservationCreated($reservation, $ticketUrl));

    return response()->json(['ticket_url' => $ticketUrl]); 
}



    // places reservees

    public function getReservedPlaces($trajetId)
{
    // Récupérer les réservations pour le trajet donné
    $reservedPlaces = Reservation::where('trajet_id', $trajetId)
        ->pluck('place_id'); // Récupérer uniquement les IDs des places réservées

    return response()->json(['reserved_places' => $reservedPlaces]);
}

// calculer le nombre de reservations

public function countReservations()
{
    $count = Reservation::count(); // Compte les trajets avec le statut 1
    return response()->json(['countreservation' => $count]);
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


    // valider qr

    public function validateQRCode(Request $request)
{
    // Assurez-vous que le contenu du QR Code est envoyé
    $request->validate(['code' => 'required|string']);

    // Décoder le contenu JSON du code QR
    $qrCodeContent = json_decode($request->input('code'), true);

    // Trouver la réservation par ID
    $reservation = Reservation::find($qrCodeContent['reservation_id']);

    // Vérifier si la réservation existe
    if (!$reservation) {
        return response()->json(['message' => 'Réservation non trouvée.'], 404); // 404 Not Found
    }

    // Vérifier si la réservation est déjà confirmée
    if ($reservation->statut === 1) {
        return response()->json(['message' => 'Cette réservation est déjà confirmée.'], 400); // 400 Bad Request
    }

    // Mettre à jour le statut de la réservation à confirmée (1)
    $reservation->update(['statut' => 1]);

    return response()->json(['message' => 'Réservation confirmée avec succès.', 'reservation' => $reservation], 200);
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

