<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation; // Assurez-vous d'importer le modèle correspondant

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = [
            [
              
                'statut' => 0, // Utilisez un entier pour le statut si c'est un boolean ou tinyint
                'trajet_id' => 2,
                'user_id' => 1,
                'place_id' => 1
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::create($reservation); // Insère chaque réservation dans la base de données
        }
    }
}
