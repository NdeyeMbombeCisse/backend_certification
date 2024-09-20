<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Liste des permissions
        $permissions = [
            'reserver',
            's\'inscrire',
            's\'authentifier',
            'ajouter bateau',
            'ajouter information',
            'modifier information',
            'supprimer information',
            'voir la liste des paiements',
            'voir liste des réservations',
            'voir les réservations d\'un trajet',
            'voir toutes les réservations',
            'ajouter un trajet',
            'modifier un trajet',
            'supprimer un trajet',
            'bloquer un trajet',
            'attribuer des rôles',
            'voir profil',
            'modifier profil',
            'voir historique des réservations',
            'se déconnecter',
            'voir liste des trajets',
            'voir information',
            'recevoir notification',
        ];

        // Création des permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Rôles et leurs permissions
        $roles = [
            'super_admin' => [
                'ajouter bateau',
                'ajouter information',
                'modifier information',
                'supprimer information',
                'voir la liste des paiements',
                'attribuer des rôles',
                'voir liste des trajets',
                'se déconnecter',
                'voir information',
            ],
            'admin' => [
                's\'authentifier',
                'voir la liste des paiements',
                'voir liste des réservations',
                'voir les réservations d\'un trajet',
                'voir toutes les réservations',
                'ajouter un trajet',
                'modifier un trajet',
                'supprimer un trajet',
                'bloquer un trajet',
                'voir liste des trajets',
                'se déconnecter',
                'voir information',
                'recevoir notification',
            ],
            'userSimple' => [
                's\'inscrire',
                's\'authentifier',
                'voir profil',
                'modifier profil',
                'voir historique des réservations',
                'se déconnecter',
                'voir liste des trajets',
                'voir information',
                'recevoir notification',
            ],
        ];

        // Attribution des permissions aux rôles
        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
            foreach ($permissions as $permission) {
                $permissionInstance = Permission::where('name', $permission)->where('guard_name', 'api')->first();
                if ($permissionInstance) {
                    $role->givePermissionTo($permissionInstance);
                } else {
                    echo "La permission $permission n'existe pas pour le guard api.\n";
                }
            }
        }
    }
}

// naboo-5b3c904b-0dcc-4095-bee2-f1f76ee76696.d5d2ab5a-0adb-4859-8dd7-e7c83b163c9b
