<?php

namespace Database\Seeders;

use App\Models\Mesure;
use Illuminate\Database\Seeder;

class MesureSeeder extends Seeder
{
    public function run(): void
    {
        $mesures = [
            // Codes de sortie
            [
                'code' => 'DE',
                'libelle' => 'Départ définitif',
                'type' => 'sortie',
                'statut_correspondant' => 'Sorti',
                'bloque_pec' => true,
                'description' => 'Agent ayant quitté définitivement l\'entreprise',
            ],
            [
                'code' => 'DC',
                'libelle' => 'Décès',
                'type' => 'sortie',
                'statut_correspondant' => 'Décédé',
                'bloque_pec' => true,
                'description' => 'Agent décédé',
            ],
            [
                'code' => 'RD',
                'libelle' => 'Radiation',
                'type' => 'sortie',
                'statut_correspondant' => 'Sorti',
                'bloque_pec' => true,
                'description' => 'Agent radié des effectifs',
            ],
            [
                'code' => 'RE',
                'libelle' => 'Retraite',
                'type' => 'retraite',
                'statut_correspondant' => 'Retraité',
                'bloque_pec' => false,
                'description' => 'Agent à la retraite (peut encore bénéficier de la PEC selon les règles)',
            ],

            // Codes de suspension
            [
                'code' => 'SU',
                'libelle' => 'Suspension',
                'type' => 'suspension',
                'statut_correspondant' => 'Suspendu',
                'bloque_pec' => true,
                'description' => 'Agent suspendu temporairement',
            ],
            [
                'code' => 'SC',
                'libelle' => 'Sans solde',
                'type' => 'suspension',
                'statut_correspondant' => 'Suspendu',
                'bloque_pec' => true,
                'description' => 'Agent en congé sans solde',
            ],
            [
                'code' => 'MA',
                'libelle' => 'Maladie',
                'type' => 'suspension',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent en maladie (reste actif pour la PEC)',
            ],

            // Codes spéciaux
            [
                'code' => 'AT',
                'libelle' => 'Accident de travail',
                'type' => 'actif',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent en accident de travail',
            ],
            [
                'code' => 'MT',
                'libelle' => 'Mise à disposition',
                'type' => 'actif',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent mis à disposition d\'une autre entité',
            ],

            // Codes carrière (précision de position)
            [
                'code' => 'TI',
                'libelle' => 'Titulaire',
                'type' => 'actif',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent titulaire de son poste',
            ],
            [
                'code' => 'FU',
                'libelle' => 'Fonctionnaire',
                'type' => 'actif',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent fonctionnaire',
            ],
            [
                'code' => 'CO',
                'libelle' => 'Contractuel',
                'type' => 'actif',
                'statut_correspondant' => 'Actif',
                'bloque_pec' => false,
                'description' => 'Agent contractuel',
            ],
        ];

        foreach ($mesures as $mesure) {
            Mesure::firstOrCreate(
                ['code' => $mesure['code']],
                $mesure
            );
        }
    }
}
