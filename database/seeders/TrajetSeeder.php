<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trajet; // Importation du modèle Trajet

class TrajetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trajets = [
            [
                'date_depart' => "2002-11-17",  // Format de date correct
                'date_arrivee'  => "2002-12-17", // Format de date correct
                'lieu_depart' => "Dakar",
                'lieu_arrive' => "Ziguinchor",
                'image' => "image",  // Peut être un chemin d'image valide
                'statut' => false,  // Statut en tant que booléen
                'heure_embarquement' => "12:00",  // Format d'heure correct
                'heure_depart' => "20:00",  // Format d'heure correct
                'bateau_id' => 1  // Clé étrangère, assure-toi qu'un bateau existe avec cet ID
            ],
        ];

        // Insertion des données dans la table trajets
        foreach ($trajets as $trajet) {
            Trajet::create($trajet);
        }
    }
}
