<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bateau;


class BateauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bateaus = [
            [
                'libelle' => "Alioune Sitoe Diatta",
                'description' => "Aline Sitoé Diatta est un ferry, construit en 2006-2007, à Berne en Allemagne, effectuant depuis mars 2008 la traversée entre Ziguinchor et Dakar à travers l’Océan Atlantique. Il passe devant l’embouchure de la Gambie. Son nom rend hommage à Aline Sitoé Diatta, héroïne de la résistance casamançaise.",
                'statut' => "0",
            ],

            [
                'libelle' => "Diambone",
                'description' => "Le Diambogne a une capacité de 200 passagers et 13 camions chargés à 35 tonnes chacun1. Il sert au transport de personnes, de véhicules et de marchandises. Il ne possède pas de cabines ni de restaurant contrairement à l'Aline Sitoé Diatta. Les passagers voyagent en fauteuil et chacun a droit à 20 kg de bagages. La durée du trajet est d’environ 15 h.",
                'statut' => "1",
            ],

            [
                'libelle' => "Aguene",
                'description' => "Le Aguene a une capacité de 200 passagers et 13 camions chargés à 35 tonnes chacun1. Il sert au transport de personnes, de véhicules et de marchandises. Il ne possède pas de cabines ni de restaurant contrairement à l'Aline Sitoé Diatta. Les passagers voyagent en fauteuil et chacun a droit à 20 kg de bagages. La durée du trajet est d’environ 15 h.",
                'statut' => "0",
            ],
        ];

        foreach ($bateaus as $bateau) {
            Bateau::create($bateau);
        }
    }
}
