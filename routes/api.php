<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\BateauController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\CategorieController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



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
Route::get('/categories/{id}/places', [PlaceController::class, 'getPlacesByCategorie']);

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


