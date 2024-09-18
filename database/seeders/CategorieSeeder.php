<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'libelle' => "feuteuil pullman",
                'description' => "Feuteuil pullman d'une seule personne",
                
            ],
           
            [
                'libelle' => "cabine 2",
                'description' => "Cabine pour 2 personnes",
            ],
           
            [
                'libelle' => "cabine 4",
                'description' => "Cabine pour 4 personnes",
               
            ],
           
            [
                'libelle' => "cabine 8",
                'description' => "Cabine pour 8 personnes",
                
            ],
           
           
        ];

        foreach ($categories as $categorie) {
            Categorie::create($categorie);
        }
    }
}
