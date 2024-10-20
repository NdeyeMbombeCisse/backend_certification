<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Tarif;
use App\Models\Trajet;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReservationEffectuee;
use App\Notifications\ReservationCreated;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Carbon\Carbon;


class ReservationController extends Controller
{
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

  


    // public function store(Request $request)
    // {
    //     // Trouver la place
    //     $place = Place::find($request->input('place_id'));
    
    //     // Vérifier si la place existe
    //     if (!$place) {
    //         return response()->json(['message' => 'Place non trouvée.'], 404); // 404 Not Found
    //     }
    
    //     // Trouver le trajet
    //     $trajet = Trajet::find($request->input('trajet_id'));
    
    //     // Vérifier si le trajet existe
    //     if (!$trajet) {
    //         return response()->json(['message' => 'Trajet non trouvé.'], 404); // 404 Not Found
    //     }
    
    //     // Vérifier si la place est déjà réservée pour ce trajet
    //     $existingReservation = Reservation::where('place_id', $place->id)
    //                                        ->where('trajet_id', $trajet->id)
    //                                        ->first();
    
    //     if ($existingReservation) {
    //         return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400); // 400 Bad Request
    //     }
    
    //     // Récupérer la nationalité de l'utilisateur
    //     $user = User::find($request->input('user_id'));
    //     if (!$user) {
    //         return response()->json(['message' => 'Utilisateur non trouvé.'], 404); // 404 Not Found
    //     }
    //     $userNationality = $user->nationnalite; 
    
    //     // Trouver la catégorie de la place
    //     $categorie = $place->categorie; 
    
        
    // $tarif = Tarif::where('categorie_id', $categorie->id)
    //               ->where('nationnalite', $userNationality)
    //               ->first();

    // // Vérifier si un tarif a été trouvé
    // if (!$tarif) {
    //     return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
    // }
    
    //     // Créer la réservation
    //     $reservation = Reservation::create([
    //         'statut' => false,
    //         'place_id' => $place->id,
    //         'trajet_id' => $trajet->id,
    //         'user_id' => $user->id,
    //     ]);
    
    //       // Mettre à jour la place pour indiquer qu'elle est réservée
  
    //     // Contenu du QR Code
    //     $qrCodeContent = [
    //         'reservation_id' => $reservation->id,
    //         'user' => [
    //             'id' => $user->id,
    //             'prenom' => $user->prenom,
    //             'nom' => $user->nom,
    //             'nationnalite' => $user->nationnalite,
    //             'CNI'=>$user->numero_identite
    //         ],
    //         'place' => [
    //             'id' => $place->id,
    //             'libelle' => $place->libelle,
    //             // 'location' => $place->location,
    //         ],
    //         'trajet' => [
    //             'id' => $trajet->id,
    //             'destination' => $trajet->lieu_arrive,
    //             'lieu_depart' => $trajet->lieu_depart,
    //             'lieu_arrive' => $trajet->lieu_arrive,
    //         ],
    //         'tarif' => [
    //             'tarif' => $tarif->tarif, // Assurez-vous que 'montant' est un champ de tarif
    //             'nationnalite' => $tarif->nationnalite,
    //         ],
    //     ];
    
    //     // Générer le QR code pour la réservation
    //     $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
    //     $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
    //     file_put_contents(public_path($qrCodePath), $qrCode);
    
    //     // Mettre à jour la réservation pour inclure le QR code
    //     $reservation->update(['qr_code' => url($qrCodePath)]);

    //     $ticketUrl = $this->generateTicket($reservation, $qrCodePath);

    //     // Notification à l'utilisateur avec l'URL du ticket
    //     $user->notify(new ReservationCreated($reservation, $ticketUrl));

    //       // Notification à l'administrateur
    //       $admins = User::role('admin')->get();

    //       foreach ($admins as $admin) {
    //           $admin->notify(new ReservationEffectuee($reservation));
    //       }
    
    //     return response()->json([
    //         'message' => 'Réservation créée avec succès.',
    //         'reservation' => $reservation,
    //         'qr_code' => url($qrCodePath),
    //         'ticket_url' => $ticketUrl,
    //     ], 201); // 201 Created
    // }
    

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

    $existingReservation = Reservation::where('place_id', $place->id)
                                           ->where('trajet_id', $trajet->id)
                                           ->first();
    
        if ($existingReservation) {
            return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400); // 400 Bad Request
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
            'CNI' => $user->numero_identite
        ],
        'place' => [
            'id' => $place->id,
            'libelle' => $place->libelle,
        ],
        'trajet' => [
            'id' => $trajet->id,
            'destination' => $trajet->lieu_arrive,
            'lieu_depart' => $trajet->lieu_depart,
            'lieu_arrive' => $trajet->lieu_arrive,
        ],
        'tarif' => [
            'tarif' => $tarif->tarif,
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

    // Notification à l'administrateur
    $admins = User::role('admin')->get();

    foreach ($admins as $admin) {
        $admin->notify(new ReservationEffectuee($reservation));
    }

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



// public function store(Request $request)
// {
//     // Valider les données de la requête
//     $validatedData = $request->validate([
//         'place_id' => 'required|exists:places,id', // Vérifie que la place existe
//         'trajet_id' => 'required|exists:trajets,id', // Vérifie que le trajet existe
//         'user_id' => 'required|exists:users,id' // Vérifie que l'utilisateur existe
//     ]);

//     // Trouver la place
//     $place = Place::find($validatedData['place_id']);

//     // Trouver le trajet
//     $trajet = Trajet::find($validatedData['trajet_id']);

//     // Vérifier si la place est déjà réservée pour ce trajet
//     $existingReservation = Reservation::where('place_id', $place->id)
//                                        ->where('trajet_id', $trajet->id)
//                                        ->first();

//     if ($existingReservation) {
//         return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400);
//     }

//     // Récupérer l'utilisateur
//     $user = User::find($validatedData['user_id']);

//     // Trouver la catégorie de la place
//     $categorie = $place->categorie;

//     // Récupérer le tarif basé sur la nationalité de l'utilisateur
//     $tarif = Tarif::where('categorie_id', $categorie->id)
//                   ->where('nationnalite', $user->nationnalite)
//                   ->first();

//     // Vérifier si un tarif a été trouvé
//     if (!$tarif) {
//         return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
//     }

//     // Créer la réservation
//     $reservation = Reservation::create([
//         'statut' => false,
//         'place_id' => $place->id,
//         'trajet_id' => $trajet->id,
//         'user_id' => $user->id,
//     ]);

//     // Mettre à jour la place pour indiquer qu'elle est réservée uniquement pour ce trajet
//     // Vous pouvez garder l'attribut is_reserved pour savoir si la place est réservée,
//     // mais vous devez gérer cela pour chaque trajet.
//     // $place->update(['is_reserved' => true]); // Indique que cette place est réservée

//     // Contenu du QR Code
//     $qrCodeContent = [
//         'reservation_id' => $reservation->id,
//         'user' => [
//             'id' => $user->id,
//             'prenom' => $user->prenom,
//             'nom' => $user->nom,
//             'nationnalite' => $user->nationnalite,
//             'CNI' => $user->numero_identite
//         ],
//         'place' => [
//             'id' => $place->id,
//             'libelle' => $place->libelle,
//         ],
//         'trajet' => [
//             'id' => $trajet->id,
//             'destination' => $trajet->lieu_arrive,
//             'lieu_depart' => $trajet->lieu_depart,
//             'lieu_arrive' => $trajet->lieu_arrive,
//         ],
//         'tarif' => [
//             'montant' => $tarif->montant,
//             'nationnalite' => $tarif->nationnalite,
//         ],
//     ];

//     // Générer le QR code pour la réservation
//     $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
//     $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
//     file_put_contents(public_path($qrCodePath), $qrCode);

//     // Mettre à jour la réservation pour inclure le QR code
//     $reservation->update(['qr_code' => url($qrCodePath)]);

//     $ticketUrl = $this->generateTicket($reservation, $qrCodePath);

//     // Notification à l'utilisateur avec l'URL du ticket
//     $user->notify(new ReservationCreated($reservation, $ticketUrl));

//     // Notification à l'administrateur
//     $admins = User::role('admin')->get();
//     foreach ($admins as $admin) {
//         $admin->notify(new ReservationEffectuee($reservation));
//     }

//     return response()->json([
//         'message' => 'Réservation créée avec succès.',
//         'reservation' => $reservation,
//         'qr_code' => url($qrCodePath),
//     ], 201); // 201 Created
// }


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
        $reservedPlaces = Reservation::where('trajet_id', $trajetId)
            ->with('place')->get();
    
        return response()->json(['data' => $reservedPlaces]); // Utilisez 'data' pour correspondre avec le frontend
    }
    
// 
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
                                ->with(['place.categorie.tarifs', 'trajet', 'user']) 
                                ->get();

    foreach ($reservations as $reservation) {
        $categorie = $reservation->place->categorie;
        $tarif = $categorie->tarifs()->where('nationnalite', $user->nationnalite)->first();
        $reservation->tarif = $tarif; 
    }

    return response()->json($reservations); 
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
    

    // nombre de reservation effectuee pour chaque semaine
    public function getReservationsByWeek()
    {
        // Récupérer toutes les réservations pour le mois en cours
        $reservations = Reservation::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();
    
        // Vérifier s'il y a des réservations
        if ($reservations->isEmpty()) {
            return response()->json(['message' => 'Aucune réservation enregistrée ce mois-ci.'], 404);
        }
    
        // Grouper les réservations par semaine
        $reservationsByWeek = $reservations->groupBy(function($reservation) {
            return Carbon::parse($reservation->created_at)->weekOfYear;
        });
    
        // Compter le nombre de réservations par semaine
        $weeklyData = $reservationsByWeek->map(function($week) {
            return $week->count(); // Compte le nombre de réservations dans chaque semaine
        });
    
        return response()->json([
            'labels' => $weeklyData->keys()->map(function($week) {
                return 'Semaine ' . $week . ' de ' . now()->year; // Optionnel : formatage des labels
            }),
            'reservations' => $weeklyData->values(), // Le nombre de réservations par semaine
        ]);
    }
    

    // voyage effectuet
    public function getVoyagesEffectuesByWeek()
{
    // Récupérer les réservations dont le statut est true pour le mois en cours
    $voyages = Reservation::where('statut', true) // Seulement les réservations effectives
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->get();

    // Vérifier s'il y a des voyages
    if ($voyages->isEmpty()) {
        return response()->json(['message' => 'Aucun voyage effectué ce mois-ci.'], 404);
    }

    // Grouper les voyages par semaine
    $voyagesByWeek = $voyages->groupBy(function($voyage) {
        return Carbon::parse($voyage->created_at)->weekOfYear;
    });

    // Compter le nombre de voyages par semaine
    $weeklyData = $voyagesByWeek->map(function($week) {
        return $week->count(); // Compte le nombre de voyages effectués dans chaque semaine
    });

    return response()->json([
        'labels' => $weeklyData->keys()->map(function($week) {
            return 'Semaine ' . $week . ' de ' . now()->year; // Optionnel : formatage des labels
        }),
        'voyages' => $weeklyData->values(), // Le nombre de voyages effectués par semaine
    ]);
}


}

