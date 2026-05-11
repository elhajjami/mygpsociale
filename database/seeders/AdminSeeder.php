<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de l'administrateur par défaut
        $admin = User::firstOrCreate(
            ['email' => 'admin@srm-fm.ma'],
            [
                'name' => 'Administrateur CGS',
                'password' => Hash::make('admin123'),
                'matricule' => 'ADMIN001',
                'telephone' => '0600000000',
                'dp_affectation' => 'Siège',
            ]
        );

        // Assigner le rôle Admin CGS
        $admin->assignRole('Admin CGS');

        $this->command->info('Administrateur créé avec succès :');
        $this->command->info('Email: admin@srm-fm.ma');
        $this->command->info('Mot de passe: admin123');
        $this->command->warn('Veuillez changer le mot de passe après la première connexion !');

        // Créer un utilisateur de test pour DP RH
        $dpRh = User::firstOrCreate(
            ['email' => 'dprh@srm-fm.ma'],
            [
                'name' => 'DP RH Fès',
                'password' => Hash::make('dprh123'),
                'matricule' => 'DPRH001',
                'telephone' => '0611111111',
                'dp_affectation' => 'Fès',
            ]
        );

        $dpRh->assignRole('DP RH');

        $this->command->info('DP RH de test créé :');
        $this->command->info('Email: dprh@srm-fm.ma');
        $this->command->info('Mot de passe: dprh123');
    }
}
