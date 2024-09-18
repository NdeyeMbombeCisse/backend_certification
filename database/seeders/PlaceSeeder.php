<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;


class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = [
            // feuteuil
            [
                'libelle' => "f1",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "f2",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "f3",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "f4",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "f5",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "f6",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "f7",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "f8",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "f9",
                'categorie_id'=> "1",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "f10",
                'categorie_id'=> "1",
                'id_bateau' => "1"
            ],


            [
                'libelle' => "c2A",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2B",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2C",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2D",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c2E",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2F",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2G",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2H",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2I",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c2J",
                'categorie_id'=> "2",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c4A",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4B",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c4C",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4D",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c4E",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4F",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4G",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4H",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4I",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c4J",
                'categorie_id'=> "3",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8A",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8B",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8C",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8D",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8E",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8F",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8G",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8H",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],

            [
                'libelle' => "c8I",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],
            [
                'libelle' => "c8J",
                'categorie_id'=> "4",
                'id_bateau' => "1"

            ],


        
        ]; 

        foreach ($places as $place) {
            // Assurez-vous que les données de catégorie et de bateau existent
            // Vous pouvez également vérifier si la place existe déjà
            Place::updateOrCreate(
                ['libelle' => $place['libelle'], 'categorie_id' => $place['categorie_id'], 'id_bateau' => $place['id_bateau']],
                $place
            );
        }
    }
}
