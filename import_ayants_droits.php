<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Agent;
use App\Models\AyantDroit;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Import Ayants Droit depuis CGS.xlsx ===\n\n";

$filePath = 'C:/wamp64/www/CGS.xlsx';

if (!file_exists($filePath)) {
    echo "Erreur: Fichier non trouvé\n";
    exit(1);
}

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

$rowIterator = $worksheet->getRowIterator();
$rowIterator->resetStart(6); // Commencer à la ligne 6 (en-têtes)

$totalLignes = 0;
$importes = 0;
$erreurs = 0;

// Vider la table ayant_droits
echo "Vidage de la table ayant_droits...\n";
AyantDroit::query()->delete();

foreach ($rowIterator as $row) {
    $rowData = [];
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);

    foreach ($cellIterator as $cell) {
        $rowData[] = $cell->getValue();
    }

    // Vérifier si on a des données
    if (empty($rowData[0])) {
        continue;
    }

    $totalLignes++;

    $matricule = $rowData[0] ?? null;
    $nomBeneficiaire = trim($rowData[6] ?? '');
    $qualite = trim($rowData[7] ?? '');
    $dateNaissance = $rowData[8] ?? null;

    // Ignorer les lignes où la qualité est "Agent" (ce sont les agents eux-mêmes)
    if (strtoupper($qualite) === 'AGENT') {
        continue;
    }

    // Ignorer si pas de matricule ou pas de nom de bénéficiaire
    if (!$matricule || !$nomBeneficiaire || !$qualite) {
        continue;
    }

    try {
        // Rechercher l'agent par matricule
        $agent = Agent::where('matricule', $matricule)->first();

        if (!$agent) {
            echo "Ligne {$row->getRowIndex()}: Agent non trouvé (Matricule: {$matricule})\n";
            $erreurs++;
            continue;
        }

        // Formater la date de naissance si elle est au format Excel
        if (is_numeric($dateNaissance)) {
            $dateNaissance = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateNaissance)->format('Y-m-d');
        } elseif ($dateNaissance) {
            // Formater la date si elle est au format JJ/MM/AAAA
            $dateNaissance = preg_replace('/\//', '-', $dateNaissance);
            try {
                $date = new \DateTime($dateNaissance);
                $dateNaissance = $date->format('Y-m-d');
            } catch (\Exception $e) {
                $dateNaissance = null;
            }
        }

        // Normaliser la qualité
        $qualite = ucfirst(strtolower($qualite));
        $qualiteMap = [
            'Conjoint' => 'Conjoint',
            'Enfant' => 'Enfant',
            'conjoint' => 'Conjoint',
            'enfant' => 'Enfant',
        ];
        $type = $qualiteMap[$qualite] ?? $qualite;

        // Créer l'ayant droit
        AyantDroit::create([
            'agent_id' => $agent->id,
            'type' => $type,
            'nom_prenom' => $nomBeneficiaire,
            'date_naissance' => $dateNaissance,
            'statut' => 'Validé',
        ]);

        echo "Ligne {$row->getRowIndex()}: {$agent->matricule} - {$nomBeneficiaire} ({$type})\n";
        $importes++;

    } catch (\Exception $e) {
        echo "Erreur ligne {$row->getRowIndex()}: " . $e->getMessage() . "\n";
        $erreurs++;
    }
}

echo "\n=== Résumé ===\n";
echo "Lignes traitées: {$totalLignes}\n";
echo "Ayants droit importés: {$importes}\n";
echo "Erreurs: {$erreurs}\n";
echo "\nImport terminé !\n";
