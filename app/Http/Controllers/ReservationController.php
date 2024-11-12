<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Place;
use App\Models\Tarif;
use App\Models\Trajet;
use App\Models\Noconnect;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReservationCreated;
use App\Notifications\ReservationEffectuee;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Support\Facades\Log;



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
   
          // Mettre à jour la place pour indiquer qu'elle est réservée
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
 
 

 
 

private function generateTicket($reservation, $qrCodePath)
{
    // Récupérer la place et sa catégorie
    $place = $reservation->place;
    $categorie = $place->categorie;

    // Récupérer le tarif en fonction de la catégorie et de la nationalité de l'utilisateur
    $userNationality = $reservation->user->nationnalite;
    $tarif = Tarif::where('categorie_id', $categorie->id)
                  ->where('nationnalite', $userNationality)
                  ->first();

    // Créez une logique pour générer un ticket
    $ticketContent = [
        'reservation_id' => $reservation->id,
        'place' => $place->libelle,
        'statut' => $reservation ->statut,
        'categorie' => $categorie->libelle, // Assurez-vous d'avoir un champ 'libelle' dans votre table 'categories'
        'tarif' => $tarif ? $tarif->tarif : 'N/A', // Assurez-vous que 'montant' est le nom du champ contenant le tarif
        'user' => $reservation->user->prenom . ' ' . $reservation->user->nom,
        'nationalite' => $reservation->user->nationnalite,
        'date_reservation' => $reservation->created_at,
        'date_depart' => $reservation->trajet->date_depart,
        'email' => $reservation->user->email,
        'telephone' => $reservation->user->telephone,
        'CNI' => $reservation->user->numero_identite,
        'trajet' => $reservation->trajet->lieu_depart . ' - ' . $reservation->trajet->lieu_arrive,
        'embarquement' => $reservation->trajet->heure_embarquement,
        'depart' => $reservation->trajet->heure_depart,
        'qr_code' => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(public_path($qrCodePath))),
    ];

    // Vous pouvez utiliser une bibliothèque comme DomPDF ou SnappyPDF pour générer un PDF
    $pdf = PDF::loadView('tickets.template', compact('ticketContent'));
    $ticketPath = 'tickets/ticket_' . $reservation->id . '.pdf';
    $pdf->save(public_path($ticketPath));

    return url($ticketPath); // Retourne l'URL du ticket généré
}

  
  


// public function storeNoConnect(Request $request)
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

//     // Récupérer l'âge de la personne pour qui la réservation est effectuée
//     $personAge = $request->input('age');
//     $isDiscounted = false;

//     // Si l'âge est entre 4 et 12 ans, ajuster la logique
//     if ($personAge >= 4 && $personAge <= 12) {
//         // Créer la nouvelle personne dans la table no_connects avec les champs minimaux pour un enfant
//         $noConnect = Noconnect::create([
//             'prenom' => $request->input('prenom'),
//             'nom' => $request->input('nom'),
//             'numero_registre' => $request->input('numero_registre'),
//             'age' => $request->input('age'),
//             'nationnalite' => $request->input('nationnalite'),
//             'numero_identite' => null,  // Pas nécessaire pour un enfant
//         ]);
//     } else {
//         // Si la personne n'est pas un enfant, on crée avec toutes les informations, y compris le numéro d'identité
//         $noConnect = Noconnect::create([
//             'prenom' => $request->input('prenom'),
//             'nom' => $request->input('nom'),
//             'age' => $request->input('age'),
//             'nationnalite' => $request->input('nationnalite'),
//             'numero_identite' => $request->input('numero_identite'),
//         ]);
//     }
    
//     // Logique pour appliquer la réduction ou non
//     $tarif = Tarif::where('categorie_id', $place->categorie->id)
//                   ->where('nationnalite', $noConnect->nationnalite)
//                   ->first();

//     if (!$tarif) {
//         return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
//     }

//     // Appliquer un tarif réduit si l'âge est entre 4 et 12 ans
//     $discountedTarif = ($personAge >= 4 && $personAge <= 12) 
//                         ? floatval($tarif->tarif) / 2
//                         : floatval($tarif->tarif);

//     // Créer la réservation
//     $reservation = Reservation::create([
//         'statut' => false,
//         'place_id' => $place->id,
//         'trajet_id' => $trajet->id,
//         'user_id' => auth()->user()->id, 
//         'no_connect_id' => $noConnect->id,
//     ]);

//     // Générer le QR code
//     $qrCodeContent = [
//         'reservation_id' => $reservation->id,
//         'no_connect' => [
//             'id' => $noConnect->id,
//             'prenom' => $noConnect->prenom,
//             'nom' => $noConnect->nom,
//             'nationnalite' => $noConnect->nationnalite,
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
//             'tarif' => [
//                 'tarif' => $discountedTarif, // Utiliser le tarif réduit
//                 'nationnalite' => $tarif->nationnalite,
//             ],
//         ],
//     ];

//     $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
//     $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
//     file_put_contents(public_path($qrCodePath), $qrCode);

//     // Mettre à jour la réservation pour inclure le QR code
//     $reservation->update(['qr_code' => url($qrCodePath)]);

//     $ticketUrl = $this->generateTicket2($reservation, $qrCodePath);

//     // Notification à l'utilisateur connecté
//     $user = auth()->user();
//     if ($user) {
//         $user->notify(new ReservationCreated($reservation, $ticketUrl));
//     }

//     // Notification à l'administrateur
//     $admins = User::role('admin')->get();
//     foreach ($admins as $admin) {
//         $admin->notify(new ReservationEffectuee($reservation));
//     }

//     return response()->json([
//         'message' => 'Réservation créée avec succès.',
//         'reservation' => $reservation,
//         'qr_code' => url($qrCodePath),
//         'ticket_url' => $ticketUrl,
//     ], 201); // 201 Created
// }


public function storeNoConnect(Request $request)
{
    // Trouver la place
    $place = Place::find($request->input('place_id'));

    if (!$place) {
        return response()->json(['message' => 'Place non trouvée.'], 404);
    }

    // Trouver le trajet
    $trajet = Trajet::find($request->input('trajet_id'));

    if (!$trajet) {
        return response()->json(['message' => 'Trajet non trouvé.'], 404);
    }

    // Vérifier si la place est déjà réservée pour ce trajet
    $existingReservation = Reservation::where('place_id', $place->id)
                                       ->where('trajet_id', $trajet->id)
                                       ->first();

    if ($existingReservation) {
        return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400);
    }

    // Récupérer l'âge de la personne
    $personAge = $request->input('age');

    // Créer la personne dans la table no_connects
    $noConnect = Noconnect::create([
        'prenom' => $request->input('prenom'),
        'nom' => $request->input('nom'),
        'age' => $request->input('age'),
        'nationnalite' => $request->input('nationnalite'),
        'numero_identite' => $personAge >= 4 && $personAge <= 12 ? null : $request->input('numero_identite'),
    ]);

    // Récupérer le tarif
    $tarif = Tarif::where('categorie_id', $place->categorie->id)
                  ->where('nationnalite', $noConnect->nationnalite)
                  ->first();

    if (!$tarif) {
        return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
    }

    // Appliquer un tarif réduit si l'âge est entre 4 et 12 ans
    $discountedTarif = ($personAge >= 4 && $personAge <= 12) ? floatval($tarif->tarif) / 2 : floatval($tarif->tarif);

    // Créer la réservation
    $reservation = Reservation::create([
        'statut' => false,
        'place_id' => $place->id,
        'trajet_id' => $trajet->id,
        'user_id' => auth()->user()->id,
        'no_connect_id' => $noConnect->id,
    ]);

    // Vérification du tarif récupéré et du tarif appliqué
    Log::info('Tarif récupéré: ', ['tarif' => $tarif->tarif]);
    Log::info('Tarif réduit appliqué: ', ['discountedTarif' => $discountedTarif]);

    // Générer le QR code
    $qrCodeContent = [
        'reservation_id' => $reservation->id,
        'no_connect' => [
            'id' => $noConnect->id,
            'prenom' => $noConnect->prenom,
            'nom' => $noConnect->nom,
            'nationnalite' => $noConnect->nationnalite,
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
            'tarif' => [
                'tarif' => $discountedTarif,
                'nationnalite' => $tarif->nationnalite,
            ],
        ],
    ];

    // Générer le QR code
    $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
    $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
    file_put_contents(public_path($qrCodePath), $qrCode);

    // Mettre à jour la réservation pour inclure le QR code
    $reservation->update(['qr_code' => url($qrCodePath)]);

    $ticketUrl = $this->generateTicket2($reservation, $qrCodePath);

    // Notification à l'utilisateur connecté
    $user = auth()->user();
    if ($user) {
        $user->notify(new ReservationCreated($reservation, $ticketUrl));
    }

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
    ], 201);
}


public function generateTicket2(Reservation $reservation)
{
    // Charger les relations nécessaires pour éviter les erreurs
    $reservation->load(['no_connect', 'place', 'trajet', 'user']);

    // Vérifier si les relations sont chargées correctement
    if (!$reservation->no_connect || !$reservation->place || !$reservation->trajet || !$reservation->user) {
        throw new \Exception("Certaines informations nécessaires pour générer le ticket sont manquantes.");
    }

    // Générer le contenu du QR Code basé sur les informations de réservation
    $qrCodeContent = json_encode([
        'reservation_id' => $reservation->id,
        'no_connect' => [
            'id' => $reservation->no_connect->id,
            'prenom' => $reservation->no_connect->prenom,
            'nom' => $reservation->no_connect->nom,
            'nationnalite' => $reservation->no_connect->nationnalite,
            'age' => $reservation ->no_connect->age,
'CNI' => $reservation->no_connect ? $reservation->no_connect->numero_identite : 'Non renseigné',
'numero_registre' => $reservation->no_connect ? $reservation->no_connect->numero_registre : 'Non renseigné',
        ],
        'place' => [
            'libelle' => $reservation->place->libelle,
        ],
        'trajet' => [
            'id' => $reservation->trajet->id,
            'lieu_depart' => $reservation->trajet->lieu_depart,
            'lieu_arrive' => $reservation->trajet->lieu_arrive,
            'embarquement' => $reservation->trajet->heure_embarquement,
            'heuredepart' => $reservation->trajet->heure_depart,
            'date_depart' => $reservation->trajet->date_depart,


        ],
        'tarif' => $reservation->tarif,

    ]);

    // Créer le QR code et le sauvegarder
    $qrCode = QrCode::size(300)->generate($qrCodeContent);
    $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
    file_put_contents(public_path($qrCodePath), $qrCode);

    // Chemin du fichier PDF du ticket
    $pdfPath = 'tickets/ticket_' . $reservation->id . '.pdf';

    // Générer le ticket PDF avec la vue associée
    $pdf = Pdf::loadView('tickets.autre', [
        'reservation' => $reservation,
        'qrCodePath' => $qrCodePath,
    ]);

    // Sauvegarder le fichier PDF
    $pdf->save(public_path($pdfPath));

    // Retourner l'URL complète du ticket
    return url($pdfPath);
}

// nouveau







// public function store(Request $request)
// {
//     // Récupérer la place et le trajet
//     $place = Place::find($request->input('place_id'));
//     $trajet = Trajet::find($request->input('trajet_id'));

//     // Vérification si la place et le trajet existent
//     if (!$place || !$trajet) {
//         return response()->json(['message' => 'Place ou trajet non trouvé.'], 404);
//     }

//     // Vérifier si la place est déjà réservée pour ce trajet
//     $existingReservation = Reservation::where('place_id', $place->id)
//                                        ->where('trajet_id', $trajet->id)
//                                        ->first();

//     if ($existingReservation) {
//         return response()->json(['message' => 'Cette place est déjà réservée pour ce trajet.'], 400);
//     }

//     // Déterminer si l'utilisateur réserve pour lui-même ou pour une autre personne
//     $isOtherPerson = $request->input('is_other_person', false);

//     // Variables par défaut
//     $prenom = $nom = $numeroRegstre = $telephone = $nationality = $email = $dateNaissance = null;
//     $age = 0;

//     if ($isOtherPerson) {
//         // Informations pour une autre personne
//         $prenom = $request->input('prenom');
//         $nom = $request->input('nom');
//         $numeroRegstre = $request->input('numero_registre');
//         $telephone = $request->input('telephone');
//         $nationality = $request->input('nationnalite');
//         $email = $request->input('email'); // Nouveau champ pour l'email
//         $dateNaissance = $request->input('date_naissance');

//         // Calculer l'âge à partir de la date de naissance
//         if ($dateNaissance) {
//             $age = Carbon::parse($dateNaissance)->age;
//         }

//         // Si l'âge est supérieur à 12, ne pas demander l'âge
//         if ($age > 12) {
//             $dateNaissance = null;
//         }
//     } else {
//         // Si c'est pour l'utilisateur authentifié, on utilise ses informations
//         $user = auth()->user();
//         $prenom = $user->prenom;
//         $nom = $user->nom;
//         $numeroRegstre = $user->numero_identite;
//         $telephone = $user->telephone;
//         $nationality = $user->nationnalite;
//         $email = $user->email; // Utiliser l'email de l'utilisateur
//         $dateNaissance = $user->date_naissance;
//         $age = Carbon::parse($dateNaissance)->age; // Calculer l'âge de l'utilisateur connecté
//     }

//     // Récupérer le tarif pour la catégorie et la nationalité
//     $categorie = $place->categorie;
//     $tarif = Tarif::where('categorie_id', $categorie->id)
//                   ->where('nationnalite', $nationality)
//                   ->first();

//     if (!$tarif) {
//         return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
//     }

//     // Assurez-vous que le tarif est un nombre
//     $tarifMontant = floatval($tarif->tarif); // Convertir le tarif de string à float

//     if (!is_numeric($tarifMontant)) {
//         return response()->json(['message' => 'Tarif invalide'], 400);
//     }

//     // Appliquer un tarif réduit de 50 % si l'âge est entre 4 et 12 ans
//     if ($age >= 4 && $age <= 12) {
//         $tarifMontant *= 0.5;
//     }

//     // Créer la réservation
//     $reservation = Reservation::create([
//         'statut' => false,
//         'place_id' => $place->id,
//         'trajet_id' => $trajet->id,
//         'user_id' => auth()->id(),
//         'prenom' => $prenom,
//         'nom' => $nom,
//         'numero_identite' => $numeroRegstre,
//         'telephone' => $telephone,
//         'nationnalite' => $nationality,
//         'email' => $email,
//     ]);

//     // Contenu du QR code
//     $qrCodeContent = [
//         'reservation_id' => $reservation->id,
//         'personne' => [
//             'prenom' => $prenom,
//             'nom' => $nom,
//             'nationality' => $nationality,
//             'CNI' => $numeroRegstre,
//             'date_naissance' => $dateNaissance,
//             'age' => $age,
//         ],
//         'place' => [
//             'id' => $place->id,
//             'libelle' => $place->libelle,
//         ],
//         'trajet' => [
//             'id' => $trajet->id,
//             'destination' => $trajet->lieu_arrive,
//             'lieu_depart' => $trajet->lieu_depart,
//         ],
//         'tarif' => [
//             'montant' => $tarifMontant,
//             'nationality' => $tarif->nationnalite,
//         ],
//     ];

//     // Générer le QR code
//     $qrCode = QrCode::size(300)->generate(json_encode($qrCodeContent));
//     $qrCodePath = 'qr_codes/reservation_' . $reservation->id . '.svg';
//     file_put_contents(public_path($qrCodePath), $qrCode);

//     // Mettre à jour la réservation pour inclure le QR code
//     $reservation->update(['qr_code' => url($qrCodePath)]);

//     // Générer le ticket pour la réservation
//     $ticketUrl = $this->generateTicket($reservation, $qrCodePath);

//     // Notification pour l'utilisateur connecté
//     auth()->user()->notify(new ReservationCreated($reservation, $ticketUrl));

//     // Notification pour l'administrateur
//     $admins = User::role('admin')->get();
//     foreach ($admins as $admin) {
//         $admin->notify(new ReservationEffectuee($reservation));
//     }

//     return response()->json([
//         'message' => 'Réservation créée avec succès.',
//         'reservation' => $reservation,
//         'qr_code' => url($qrCodePath),
//         'ticket_url' => $ticketUrl,
//     ], 201);
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

