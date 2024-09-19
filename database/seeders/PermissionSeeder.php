<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Permissions= [
            'reserver',
            's\'inscrire',
            's\'autehntifier',
            'ajouter bateau',
            'ajouter informaion',
            'modifier information',
            'supprimer information',
            'voir liste des reservation',
            'voir  les reservations d\'un trajet',
            'voir tous les reservation',
            'Ajouter un trajet',
            'modifier un trajet',
            'supprimer un trajet',
            'bloquer un trajet',
            ''



        ];
    }
}
// naboo-5b3c904b-0dcc-4095-bee2-f1f76ee76696.d5d2ab5a-0adb-4859-8dd7-e7c83b163c9b
curl --location --request PUT 'https://api.naboopay.com/api/v1/transaction/create-transaction' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer naboo-5b3c904b-0dcc-4095-bee2-f1f76ee76696.d5d2ab5a-0adb-4859-8dd7-e7c83b163c9b' \
--data-raw '{
  "method_of_payment": ["WAVE"],
  "products": [{
    "name": "T-shirt",
    "category": "Clothing",
    "amount": 100,
    "quantity": 2,
    "description": "A cool t-shirt."
  }],
  "is_escrow": false
}'
