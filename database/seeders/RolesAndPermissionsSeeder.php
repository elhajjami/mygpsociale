<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les caches
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Module Agents
            'voir agents',
            'créer agents',
            'modifier agents',
            'supprimer agents',
            'importer agents',

            // Module Utilisateurs
            'voir utilisateurs',
            'créer utilisateurs',
            'modifier utilisateurs',
            'supprimer utilisateurs',
            'gérer rôles utilisateurs',

            // Module Rôles
            'voir rôles',
            'créer rôles',
            'modifier rôles',
            'supprimer rôles',

            // Module Permissions
            'voir permissions',
            'créer permissions',
            'modifier permissions',
            'supprimer permissions',

            // Module Partenaires
            'voir partenaires',
            'créer partenaires',
            'modifier partenaires',
            'supprimer partenaires',

            // Module Demandes PEC
            'voir demandes',
            'créer demandes',
            'modifier demandes',
            'supprimer demandes',
            'valider demandes',
            'rejeter demandes',

            // Module Paramètres
            'voir paramètres',
            'modifier paramètres',
            'gérer plafonds',
            'gérer carrieres',
            'gérer mesures',

            // Module Import
            'voir imports',
            'lancer imports',
            'traiter écarts',

            // Module Dashboard
            'voir dashboard',
            'voir statistiques',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Créer les rôles et assigner les permissions

        // Rôle Admin CGS - Tous les droits
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin CGS',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo(Permission::all());

        // Rôle Gestionnaire CGS
        $gestionnaireRole = Role::firstOrCreate([
            'name' => 'Gestionnaire CGS',
            'guard_name' => 'web',
        ]);
        $gestionnaireRole->givePermissionTo([
            'voir agents', 'modifier agents',
            'voir utilisateurs', 'modifier utilisateurs',
            'voir partenaires', 'créer partenaires', 'modifier partenaires',
            'voir demandes', 'valider demandes', 'rejeter demandes',
            'voir paramètres', 'gérer plafonds', 'gérer carrieres', 'gérer mesures',
            'voir imports', 'lancer imports', 'traiter écarts',
            'voir dashboard', 'voir statistiques',
        ]);

        // Rôle DP RH
        $dpRhRole = Role::firstOrCreate([
            'name' => 'DP RH',
            'guard_name' => 'web',
        ]);
        $dpRhRole->givePermissionTo([
            'voir agents',
            'voir demandes', 'créer demandes', 'modifier demandes',
            'voir dashboard',
        ]);

        // Rôle Agent (self-service)
        $agentRole = Role::firstOrCreate([
            'name' => 'Agent',
            'guard_name' => 'web',
        ]);
        $agentRole->givePermissionTo([
            'voir demandes', 'créer demandes',
        ]);

        $this->command->info('Rôles et permissions créés avec succès.');
    }
}
