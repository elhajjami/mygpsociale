<?php

namespace Database\Seeders;

use App\Models\Carriere;
use Illuminate\Database\Seeder;

class CarriereSeeder extends Seeder
{
    public function run(): void
    {
        $carrieres = [
            // Exécution (E1 à E12)
            ['code_niveau' => 'E1', 'libelle_niveau' => 'Exécution 1ère échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 1],
            ['code_niveau' => 'E2', 'libelle_niveau' => 'Exécution 2ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 2],
            ['code_niveau' => 'E3', 'libelle_niveau' => 'Exécution 3ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 3],
            ['code_niveau' => 'E4', 'libelle_niveau' => 'Exécution 4ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 4],
            ['code_niveau' => 'E5', 'libelle_niveau' => 'Exécution 5ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 5],
            ['code_niveau' => 'E6', 'libelle_niveau' => 'Exécution 6ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 6],
            ['code_niveau' => 'E7', 'libelle_niveau' => 'Exécution 7ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 7],
            ['code_niveau' => 'E8', 'libelle_niveau' => 'Exécution 8ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 8],
            ['code_niveau' => 'E9', 'libelle_niveau' => 'Exécution 9ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 9],
            ['code_niveau' => 'E10', 'libelle_niveau' => 'Exécution 10ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 10],
            ['code_niveau' => 'E11', 'libelle_niveau' => 'Exécution 11ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 11],
            ['code_niveau' => 'E12', 'libelle_niveau' => 'Exécution 12ème échelle', 'categorie' => 'Exécution', 'prefixe_niveau' => 'E', 'ordre' => 12],

            // Maîtrise (M1 à M10)
            ['code_niveau' => 'M1', 'libelle_niveau' => 'Maîtrise 1ère échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 21],
            ['code_niveau' => 'M2', 'libelle_niveau' => 'Maîtrise 2ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 22],
            ['code_niveau' => 'M3', 'libelle_niveau' => 'Maîtrise 3ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 23],
            ['code_niveau' => 'M4', 'libelle_niveau' => 'Maîtrise 4ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 24],
            ['code_niveau' => 'M5', 'libelle_niveau' => 'Maîtrise 5ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 25],
            ['code_niveau' => 'M6', 'libelle_niveau' => 'Maîtrise 6ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 26],
            ['code_niveau' => 'M7', 'libelle_niveau' => 'Maîtrise 7ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 27],
            ['code_niveau' => 'M8', 'libelle_niveau' => 'Maîtrise 8ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 28],
            ['code_niveau' => 'M9', 'libelle_niveau' => 'Maîtrise 9ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 29],
            ['code_niveau' => 'M10', 'libelle_niveau' => 'Maîtrise 10ème échelle', 'categorie' => 'Maîtrise', 'prefixe_niveau' => 'M', 'ordre' => 30],

            // Cadre (C1 à C10)
            ['code_niveau' => 'C1', 'libelle_niveau' => 'Cadre 1ère échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 41],
            ['code_niveau' => 'C2', 'libelle_niveau' => 'Cadre 2ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 42],
            ['code_niveau' => 'C3', 'libelle_niveau' => 'Cadre 3ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 43],
            ['code_niveau' => 'C4', 'libelle_niveau' => 'Cadre 4ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 44],
            ['code_niveau' => 'C5', 'libelle_niveau' => 'Cadre 5ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 45],
            ['code_niveau' => 'C6', 'libelle_niveau' => 'Cadre 6ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 46],
            ['code_niveau' => 'C7', 'libelle_niveau' => 'Cadre 7ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 47],
            ['code_niveau' => 'C8', 'libelle_niveau' => 'Cadre 8ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 48],
            ['code_niveau' => 'C9', 'libelle_niveau' => 'Cadre 9ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 49],
            ['code_niveau' => 'C10', 'libelle_niveau' => 'Cadre 10ème échelle', 'categorie' => 'Cadre', 'prefixe_niveau' => 'C', 'ordre' => 50],

            // Hors cadre (H1 à H5)
            ['code_niveau' => 'H1', 'libelle_niveau' => 'Hors cadre 1ère échelle', 'categorie' => 'Hors cadre', 'prefixe_niveau' => 'H', 'ordre' => 61],
            ['code_niveau' => 'H2', 'libelle_niveau' => 'Hors cadre 2ème échelle', 'categorie' => 'Hors cadre', 'prefixe_niveau' => 'H', 'ordre' => 62],
            ['code_niveau' => 'H3', 'libelle_niveau' => 'Hors cadre 3ème échelle', 'categorie' => 'Hors cadre', 'prefixe_niveau' => 'H', 'ordre' => 63],
            ['code_niveau' => 'H4', 'libelle_niveau' => 'Hors cadre 4ème échelle', 'categorie' => 'Hors cadre', 'prefixe_niveau' => 'H', 'ordre' => 64],
            ['code_niveau' => 'H5', 'libelle_niveau' => 'Hors cadre 5ème échelle', 'categorie' => 'Hors cadre', 'prefixe_niveau' => 'H', 'ordre' => 65],
        ];

        foreach ($carrieres as $carriere) {
            Carriere::firstOrCreate(
                ['code_niveau' => $carriere['code_niveau']],
                $carriere
            );
        }
    }
}
