<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'prenom' => "Ndeye Mbombe",
                'nom' => "Cisse",
                'email' => "ndeyecisse188@gmail.com",
                'telephone'=>"784055367",
                'numero_identite'=>"1234567890876",
                'nationnalite'=>"senegalais",
                'password'=>"password",
                'image'=>"image",
            ],

            [
                'prenom' => "Ndeye Coumba",
                'nom' => "Cisse",
                'email' => "coumbacisse188@gmail.com",
                'telephone'=>"774055367",
                'numero_identite'=>"1234500890876",
                'nationnalite'=>"senegalais",
                'password'=>"password",
                'image' =>"image"

            ],
        

        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
