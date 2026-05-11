<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des rôles
        $adminCgs = Role::create(['name' => 'Admin CGS']);
        $gestionnaireCgs = Role::create(['name' => 'Gestionnaire CGS']);
        $dpRh = Role::create(['name' => 'DP RH']);

        // Permissions pour les agents
        Permission::create(['name' => 'voir agents']);
        Permission::create(['name' => 'créer agents']);
        Permission::create(['name' => 'modifier agents']);
        Permission::create(['name' => 'supprimer agents']);
        Permission::create(['name' => 'importer agents']);

        // Permissions pour les ayants droit
        Permission::create(['name' => 'voir ayants droit']);
        Permission::create(['name' => 'créer ayants droit']);
        Permission::create(['name' => 'modifier ayants droit']);
        Permission::create(['name' => 'supprimer ayants droit']);
        Permission::create(['name' => 'valider ayants droit']);

        // Permissions pour les partenaires
        Permission::create(['name' => 'voir partenaires']);
        Permission::create(['name' => 'créer partenaires']);
        Permission::create(['name' => 'modifier partenaires']);
        Permission::create(['name' => 'supprimer partenaires']);

        // Permissions pour les demandes PEC
        Permission::create(['name' => 'voir demandes']);
        Permission::create(['name' => 'créer demandes']);
        Permission::create(['name' => 'modifier demandes']);
        Permission::create(['name' => 'supprimer demandes']);
        Permission::create(['name' => 'valider demandes']);
        Permission::create(['name' => 'rejeter demandes']);

        // Permissions pour les paiements
        Permission::create(['name' => 'voir paiements']);
        Permission::create(['name' => 'créer paiements']);
        Permission::create(['name' => 'modifier paiements']);
        Permission::create(['name' => 'valider paiements']);

        // Permissions pour les plafonds
        Permission::create(['name' => 'voir plafonds']);
        Permission::create(['name' => 'modifier plafonds']);

        // Permissions pour les imports
        Permission::create(['name' => 'importer sap']);
        Permission::create(['name' => 'voir écarts']);

        // Permissions pour les exports
        Permission::create(['name' => 'exporter données']);
        Permission::create(['name' => 'exporter paie']);

        // Permissions pour les paramètres
        Permission::create(['name' => 'gérer utilisateurs']);
        Permission::create(['name' => 'gérer rôles']);

        // Permissions pour les tableaux de bord
        Permission::create(['name' => 'voir tableaux de bord']);

        // Assignation des permissions au rôle Admin CGS (toutes les permissions)
        $adminCgs->givePermissionTo(Permission::all());

        // Assignation des permissions au rôle Gestionnaire CGS (lecture et suivi)
        $gestionnaireCgs->givePermissionTo([
            'voir agents', 'voir ayants droit', 'voir partenaires',
            'voir demandes', 'voir paiements', 'voir plafonds',
            'voir écarts', 'exporter données', 'voir tableaux de bord',
        ]);

        // Assignation des permissions au rôle DP RH (saisie uniquement)
        $dpRh->givePermissionTo([
            'créer demandes', 'modifier demandes', 'voir demandes',
        ]);
    }
}
