<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categorie;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void



    {
        $tarifs = [
            [
                'categorie_id' => 1,
                'nationnalite' => "senegalais",
                'tarif' => "5000 FCFA",
                'libelle' =>"libelle"
            ],
            [
                'categorie_id' => 1,
                'nationnalite' => "etranger resident",
                'tarif' => "10 900 FCFA",
                'libelle' =>"libelle"


            ],
            [
                'categorie_id' => 1,
                'nationnalite' => "etranger non resident",
                'tarif' => "15 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 2, 
                'nationnalite' => "senegalais",
                'tarif' => "26 500 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 2,
                'nationnalite' => "etranger resident",
                'tarif' => "26 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 2,
                'nationnalite' => "etranger non resident",
                'tarif' => "30 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 3,
                'nationnalite' => "senegalais",
                'tarif' => "24 500 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 3,
                'nationnalite' => "etranger resident",
                'tarif' => "24 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 3,
                'nationnalite' => "etranger non resident",
                'tarif' => "28 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 4,
                'nationnalite' => "senegalais",
                'tarif' => "12 500 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 4,
                'nationnalite' => "etranger resident",
                'tarif' => "12 900 FCFA",
                'libelle' =>"libelle"

            ],
            [
                'categorie_id' => 4,
                'nationnalite' => "etranger non resident",
                'tarif' => "18 900 FCFA",
                'libelle' =>"libelle"

            ]
        ];

       
        foreach ($tarifs as $data) {
            // Récupérez la catégorie en utilisant le modèle Categorie
            $categorie = Categorie::find($data['categorie_id']);
            
            if ($categorie) {
                // Vous pouvez également utiliser $categorie->name pour obtenir le libellé de la catégorie
                DB::table('tarifs')->insert([
                    'categorie_id' => $data['categorie_id'],
                    'libelle' => $categorie->libelle, // Ajoutez le libellé ici
                    'nationnalite' => $data['nationnalite'],
                    'tarif' => $data['tarif']
                ]);
            } else {
                // Gérer le cas où la catégorie n'existe pas
                echo "Catégorie ID {$data['categorie_id']} non trouvée.\n";
            }
        }
    }
}
