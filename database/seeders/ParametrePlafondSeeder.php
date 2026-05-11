<?php

namespace Database\Seeders;

use App\Models\ParametrePlafond;
use Illuminate\Database\Seeder;

class ParametrePlafondSeeder extends Seeder
{
    public function run(): void
    {
        $anneeCourante = now()->year;

        // Créer pour l'année courante et les 2 années précédentes
        for ($i = 0; $i < 3; $i++) {
            $annee = $anneeCourante - $i;

            ParametrePlafond::firstOrCreate(
                ['annee' => $annee],
                [
                    'plafond_execution' => 12000,
                    'plafond_maitrise' => 15000,
                    'plafond_cadre' => 18000,
                    'plafond_hors_cadre' => 18000,
                    'plafond_bo' => 18000,
                    'actif' => ($i === 0), // Seulement l'année courante est active
                    'notes' => $i === 0 ? 'Paramètres en vigueur' : 'Archives',
                ]
            );
        }
    }
}
