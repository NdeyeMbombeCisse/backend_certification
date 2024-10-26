<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\BateauController;
use App\Http\Controllers\TrajetController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\NotificationController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// autentification et deconnexion
// Route::post("register", [AuthController::class, "register"]);
Route::post('login',[AuthController::class,'login']);
Route::get('logout',[AuthController::class,'logout']);
Route::get('refresh',[AuthController::class,'refresh']);
// crud user
Route::get('afficher_user',[AuthController::class,'afficher_user']);
Route::post('modifier_user',[AuthController::class,'modifier_user']);
Route::post('store',[AuthController::class,'store']);
Route::put('updateUser/{id}', [AuthController::class, 'update']);
Route::get('/profil', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth');
//  crud trajet
Route::apiResource('trajets', TrajetController::class);
// recuperation d'un seul trajet
Route::get('/recupererTrajet/{id}', [TrajetController::class, 'getTrajetById']);

// crud bateau
Route::apiResource('bateaux', BateauController::class);
// chnager le statut d'un bateau
Route::put('/bateaux/{id}/statut', [BateauController::class, 'updateStatut']);
// crud reservation
Route::apiResource('reservations', ReservationController::class);
//Crud information
Route::apiResource('informations', InformationController::class);
// crud place
Route::apiResource('places', PlaceController::class);
// crud des tarifs
Route::get('tarifs', [TarifController::class,'index']);
// afficher tarif parcetegorie
Route::get('categorieTarif', [TarifController::class, 'tarifparcat']);

// gestion des categorie
Route::apiResource('categories', CategorieController::class);

// reservation pour le user conectee

Route::get('/user/reservations', [ReservationController::class, 'getUserReservations']);
// nombre de reservation
Route::get('countReservation', [ReservationController::class, 'countReservations']);
// places restante
Route::get('/trajetsPlaces-restantes', [TrajetController::class, 'getPlacesRestantes']);
// afficher les trajets en cours

Route::get('/trajetsEncours', [TrajetController::class, 'getPlacesRestantes']);

// update statut
Route::patch('/trajets/{id}/statut', [TrajetController::class, 'updateStatut']);
// trajet en cours
Route::get('count', [TrajetController::class, 'countTrajets']);

// recuperer une place par son id


Route::get('/trajets/{id}/places', [PlaceController::class, 'getPlacesByTrajetId']);
// recuperer les places d'une categorie
// Route::get('/categories/{id}/places', [PlaceController::class, 'getPlacesByCategorie']);
Route::get('/trajets/{trajet}/categories/{categorie}/places', [PlaceController::class, 'getPlacesByCategorie']);

// tarif par categorie
Route::get('/api/tarifs/{categorieId}', [TarifController::class, 'getTarifsByCategorie']);

// les reservations d'un trajet
Route::get('trajets/{trajet}/reservations', [ReservationController::class, 'getReservationsByTrajet']);

// valider qr
Route::post('/validate-qr', [ReservationController::class, 'validateQRCode']);

// places reservee
Route::get('/trajets/{id}/places-reservees', [ReservationController::class, 'getReservedPlaces']);

// route pour le ticket
Route::get('/reservations/{id}/ticket', [ReservationController::class, 'downloadTicket'])
    ->name('reservations.ticket');


// place restantes

// Route pour récupérer les places disponibles en JSON
// Route::get('/trajets/{trajetId}/places', [PlaceController::class, 'getAvailablePlacesForTrajet'])
//     ->name('trajets.places.available');

    Route::get('/reservations/trajet/{trajetId}', [PlaceController::class, 'getAvailablePlacesForTrajet']);

    // notification


// Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications', [NotificationController::class, 'index']);

// chnager le statut de la place
Route::put('/placeReservee/{id}', [PlaceController::class, 'updatePlaceReservation']);

Route::get('places/trajet/{id}', [PlaceController::class, 'getPlacesByTrajet']);

// Route pour récupérer les trajets publiés par semaine
Route::get('/trajets-by-week', [TrajetController::class, 'getTrajetsByWeek']);
// resdrvation par semaine
Route::get('/reservation/weekly', [ReservationController::class, 'getReservationsByWeek']);
// voayge effectuer
Route::get('/voyages/effectues/semaine', [ReservationController::class, 'getVoyagesEffectuesByWeek']);

// paiement
Route::post('create-transaction/{reservation_id}', [PaiementController::class, 'createTransaction']);

Route::get('/reservation/success/{id}', [PaiementController::class, 'success'])->name('reservation.success');

Route::get('/reservation/error/{id}', [PaiementController::class, 'error'])->name('reservation.error');