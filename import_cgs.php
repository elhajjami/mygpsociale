<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Agent;
use App\Models\AyantDroit;

// Charger l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Import CGS.xlsx ===\n\n";

// Charger le fichier Excel
$filePath = 'C:/wamp64/www/CGS.xlsx';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();

echo "Total lignes: $highestRow\n\n";

// Compteurs
$agentsImportes = 0;
$agentsMisAJour = 0;
$ayantsDroitsImportes = 0;
$erreurs = 0;

// Importer à partir de la ligne 7
for ($row = 7; $row <= $highestRow; $row++) {
    try {
        $rowData = $sheet->rangeToArray("A{$row}:S{$row}", null, true, false)[0];

        $matricule = trim($rowData[0] ?? ''); // A
        $nom = trim($rowData[1] ?? ''); // B
        $prenom = trim($rowData[2] ?? ''); // C
        $cin = trim($rowData[3] ?? ''); // D
        $dateNaissance = $rowData[4] ?? null; // E
        $numeroAffiliation = trim($rowData[5] ?? ''); // F
        $dpAffectation = trim($rowData[11] ?? ''); // L
        $dateEmbauche = $rowData[10] ?? null; // K
        $statut = trim($rowData[18] ?? 'Actif'); // S

        // Ignorer les lignes vides
        if (empty($matricule) || empty($nom)) {
            continue;
        }

        // Formater la date de naissance
        if ($dateNaissance) {
            if (is_numeric($dateNaissance)) {
                $dateNaissanceObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateNaissance);
                $dateNaissance = $dateNaissanceObj->format('Y-m-d');
            } else {
                // Format JJ/MM/AAAA
                $parts = explode('/', $dateNaissance);
                if (count($parts) === 3) {
                    $dateNaissance = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
            }
        }

        // Formater la date d'embauche
        if ($dateEmbauche) {
            if (is_numeric($dateEmbauche)) {
                $dateEmbaucheObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateEmbauche);
                $dateEmbauche = $dateEmbaucheObj->format('Y-m-d');
            } else {
                $parts = explode('/', $dateEmbauche);
                if (count($parts) === 3) {
                    $dateEmbauche = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
            }
        }

        // Déterminer la catégorie
        $categorie = 'Exécution';
        if (in_array(substr($matricule, 0, 1), ['3', '4', '5'])) {
            $categorie = 'Maîtrise';
        } elseif (in_array(substr($matricule, 0, 1), ['6', '7', '8', '9'])) {
            $categorie = 'Cadre';
        }

        // Créer ou mettre à jour l'agent
        $agent = Agent::updateOrCreate(
            ['matricule' => $matricule],
            [
                'nom' => strtoupper($nom),
                'prenom' => ucfirst($prenom),
                'cin' => $cin ?: null,
                'date_naissance' => $dateNaissance,
                'numero_affiliation' => $numeroAffiliation ?: null,
                'dp_affectation' => $dpAffectation ?: null,
                'date_embauche' => $dateEmbauche,
                'statut' => $statut === 'Actif' ? 'Actif' : 'Retraité',
                'categorie' => $categorie,
                'sexe' => 'M', // Par défaut
            ]
        );

        if ($agent->wasRecentlyCreated) {
            $agentsImportes++;
        } else {
            $agentsMisAJour++;
        }

        // Importer l'ayant droit si présent
        $ayantDroitNom = trim($rowData[6] ?? ''); // G
        $ayantDroitLien = trim($rowData[7] ?? ''); // H
        $ayantDroitDateNaiss = $rowData[8] ?? null; // I

        if (!empty($ayantDroitNom) && !empty($ayantDroitLien) && $ayantDroitLien !== 'Agent') {
            // Formater la date de naissance de l'ayant droit
            if ($ayantDroitDateNaiss) {
                if (is_numeric($ayantDroitDateNaiss)) {
                    $dateNaissanceObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($ayantDroitDateNaiss);
                    $ayantDroitDateNaiss = $dateNaissanceObj->format('Y-m-d');
                } else {
                    $parts = explode('/', $ayantDroitDateNaiss);
                    if (count($parts) === 3) {
                        $ayantDroitDateNaiss = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                    }
                }
            }

            AyantDroit::updateOrCreate(
                [
                    'agent_id' => $agent->id,
                    'type' => ucfirst($ayantDroitLien),
                ],
                [
                    'nom_prenom' => strtoupper($ayantDroitNom),
                    'date_naissance' => $ayantDroitDateNaiss,
                    'statut' => 'Validé',
                ]
            );

            $ayantsDroitsImportes++;
        }

        echo "Ligne $row: $matricule - $nom $prenom\n";

    } catch (\Exception $e) {
        $erreurs++;
        echo "Erreur ligne $row: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Résumé ===\n";
echo "Agents importés: $agentsImportes\n";
echo "Agents mis à jour: $agentsMisAJour\n";
echo "Ayants droit importés: $ayantsDroitsImportes\n";
echo "Erreurs: $erreurs\n";

echo "\nImport terminé !\n";
