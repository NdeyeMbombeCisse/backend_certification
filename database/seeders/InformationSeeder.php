<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Information;


class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $informations = [
            [
                'titre' => "first information",
                'description' => "ma premiere information",
                
            ],
        ];

        foreach ($informations as $information) {
            Information::create($information);
        }
    }
}
