<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Bateau;
use App\Models\Trajet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([

            BateauSeeder::class,
            CategorieSeeder::class,
            TarifSeeder::class,
            PlaceSeeder::class,
            UserSeeder::class,
            TrajetSeeder::class,
            TrajetSeeder::class,
            ReservationSeeder::class,
            InformationSeeder::class,
            RoleSeeder::class,

              
        ]);
    }
}
