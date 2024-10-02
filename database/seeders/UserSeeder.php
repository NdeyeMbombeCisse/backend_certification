<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérification et création des rôles s'ils n'existent pas déjà
        $roles = ['superAdmin', 'admin'];

        foreach ($roles as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }

        $users = [
            [
                'prenom' => "Celine",
                'nom' => "Mendy",
                'email' => "babs@gmail.com",
                'telephone' => "778955327",
                'numero_identite' => "0999809800876",
                'nationnalite' => "senegalais",
                'password' => bcrypt('password'),  // N'oubliez pas de hasher le mot de passe
                'image' => "image",
                'role' => 'superAdmin', // Rôle de l'utilisateur
            ],

            [
                'prenom' => "Ndeye Coumba",
                'nom' => "Cisse",
                'email' => "gueye@gmail.com",
                'telephone' => "785433212",
                'numero_identite' => "8730128730984",
                'nationnalite' => "senegalais",
                'password' => bcrypt('password'),  // N'oubliez pas de hasher le mot de passe
                'image' => "image",
                'role' => 'admin', // Rôle de l'utilisateur
            ],
        ];

        foreach ($users as $userData) {
            // Créer l'utilisateur sans la clé 'role'
            $user = User::create([
                'prenom' => $userData['prenom'],
                'nom' => $userData['nom'],
                'email' => $userData['email'],
                'telephone' => $userData['telephone'],
                'numero_identite' => $userData['numero_identite'],
                'nationnalite' => $userData['nationnalite'],
                'password' => $userData['password'],
                'image' => $userData['image'],
            ]);

            // Assigner le rôle correspondant à l'utilisateur
            $user->assignRole($userData['role']);
        }
    }
}
