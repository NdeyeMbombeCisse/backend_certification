<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Tarif;
use App\Models\Place;
use App\Models\Trajet;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;


class PaiementController extends Controller
{
   

    // public function createTransaction(Request $request)
    // {
    //     // Trouver la réservation à partir de l'ID de réservation
    //     $reservation = Reservation::find($request->input('reservation_id'));
    
    //     // Vérifier si la réservation existe
    //     if (!$reservation) {
    //         return response()->json(['message' => 'Réservation non trouvée.'], 404);
    //     }
    
    //     // Récupérer les détails de la place réservée
    //     $place = Place::find($reservation->place_id);
    //     if (!$place) {
    //         return response()->json(['message' => 'Place non trouvée.'], 404);
    //     }
    
    //     // Récupérer le trajet associé à la réservation
    //     $trajet = Trajet::find($reservation->trajet_id);
    //     if (!$trajet) {
    //         return response()->json(['message' => 'Trajet non trouvé.'], 404);
    //     }
    
    //     // Récupérer la nationalité de l'utilisateur
    //     $user = User::find($reservation->user_id);
    //     if (!$user) {
    //         return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    //     }
    //     $userNationality = $user->nationnalite;
    
    //     // Trouver la catégorie de la place
    //     $categorie = $place->categorie;
    
    //     // Récupérer le tarif
    //     $tarif = Tarif::where('categorie_id', $categorie->id)
    //                   ->where('nationnalite', $userNationality)
    //                   ->first();
    
    //     // Vérifier si un tarif a été trouvé
    //     if (!$tarif) {
    //         return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
    //     }
    
    //     // Préparer les données pour l'API
    //     $data = [
    //         'full_name' => $user->prenom . ' ' . $user->nom,
    //         'amount' => $tarif->tarif, // Utiliser le tarif récupéré
    //         'phone_number' => $user->telephone // Assurez-vous que le numéro de téléphone est disponible
    //     ];
    
    //     // Effectuer la requête cURL
    //     $curl = curl_init();
    
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://api.naboopay.com/api/v1/cashout/wave',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => json_encode($data), // Convertir le tableau en JSON
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json',
    //             'Accept: application/json',
    //             'Authorization: Bearer naboo-58577a92-0cc1-4c3d-8083-60e976f8f096.b9d6bc90-9ab4-4a2f-a24d-f5a7185db821'
    //         ),
    //     ));
    
    //     $response = curl_exec($curl);
    
    //     curl_close($curl);
        
    //     // Retourner la réponse sous forme JSON
    //     return response()->json(json_decode($response));
    // }
    
    public function createTransaction(Request $request, $reservation_id)
    {
        $reservation = Reservation::find($reservation_id);
    
        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée.'], 404);
        }
    
        // Récupérer les informations liées pour le paiement
        $user = $reservation->user;
        $trajet = $reservation->trajet;
        $place = $reservation->place;
        $tarif = Tarif::where('categorie_id', $place->categorie->id)
                      ->where('nationnalite', $user->nationnalite)
                      ->first();
    
        if (!$tarif) {
            return response()->json(['message' => 'Tarif non trouvé pour cette catégorie et nationalité.'], 404);
        }
    
        // Formatage du tarif
        $amount = (int)str_replace([' ', 'FCFA'], '', $tarif->tarif); 
    
        // Préparation des données de paiement pour l'API NabooPay
        $paymentData = [
            "method_of_payment" => ["WAVE"],
           
            "products" => [
                [
                    "name" => "Réservation Trajet: " . $trajet->lieu_depart . " - " . $trajet->lieu_arrive,
                    "category" => "Transport",
                    "amount" => $amount, 
                    "quantity" => 1,
                    "description" => "Paiement pour la réservation de la place pour le trajet de " . $trajet->lieu_depart . " à " . $trajet->lieu_arrive
                    
                ]
            ],
            // "success_url" => route('reservation.success', ['id' => $reservation->id]),
            // "error_url" => route('reservation.error', ['id' => $reservation->id]),
            "is_escrow" => true,
            "is_merchant" => true
        ];
    
        // Initialiser cURL
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.naboopay.com/api/v1/transaction/create-transaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($paymentData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer naboo-58577a92-0cc1-4c3d-8083-60e976f8f096.b9d6bc90-9ab4-4a2f-a24d-f5a7185db821'
            ],
        ]);
    
        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['message' => 'Erreur lors de la requête: ' . $error], 500);
        }
        curl_close($curl);

       
    
        return response()->json([
            'message' => 'Paiement initié avec succès.',
            'payment_response' => json_decode($response, true)
        ]);
    }
    
}



