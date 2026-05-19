<?php

namespace App\Imports;

use App\Models\AgentSap;
use App\Models\AyantDroitSap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentsSapDataImport implements ToCollection, WithHeadingRow
{
    protected $agentsImported = 0;
    protected $agentsUpdated = 0;
    protected $ayantsImported = 0;
    protected $errors = [];
    protected $nomFichier;

    public function __construct($nomFichier = null)
    {
        $this->nomFichier = $nomFichier;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $matricule = $this->cleanMatricule($row['matricule'] ?? '');

            if (empty($matricule)) {
                $this->errors[] = "Ligne $rowNumber: Matricule manquant";
                continue;
            }

            // Récupérer nom_prenom directement (champ combiné)
            $nomPrenom = $row['nom_prenom'] ?? $row['nom_prenom_agent'] ?? '';

            // Préparer les données de l'agent SAP
            $agentData = [
                'matricule' => $matricule,
                'nom' => null,  // Ne plus séparer
                'prenom' => null,  // Ne plus séparer
                'nom_prenom' => $this->cleanString($nomPrenom),  // Stocker le nom complet
                'statut' => $this->normalizeStatut($row['statut'] ?? 'Actif'),
                'date_naissance' => $this->parseDate($row['date_naissance'] ?? null),
                'date_import_sap' => now(),
                'fichier_import' => $this->nomFichier,
                'import_par' => auth()->id(),
            ];

            // Trouver ou créer l'agent SAP
            $agentSap = AgentSap::where('matricule', $matricule)->first();

            if ($agentSap) {
                $agentSap->update($agentData);
                $this->agentsUpdated++;
            } else {
                $agentSap = AgentSap::create($agentData);
                $this->agentsImported++;
            }

            // Traiter l'ayant droit si présent
            $nomAyant = $row['nom_ayant_droit'] ?? $row['nom'] ?? '';
            $prenomAyant = $row['prenom_ayant_droit'] ?? $row['prenom'] ?? '';

            if (!empty($nomAyant) || !empty($prenomAyant)) {
                $this->importerAyantDroitSap($agentSap, $row, $rowNumber);
            }
        }
    }

    /**
     * Importer un ayant droit SAP
     */
    protected function importerAyantDroitSap(AgentSap $agentSap, $row, $rowNumber)
    {
        $nomAyant = $row['nom_ayant_droit'] ?? $row['nom'] ?? '';
        $prenomAyant = $row['prenom_ayant_droit'] ?? $row['prenom'] ?? '';
        $nomPrenomAyant = trim($nomAyant . ' ' . $prenomAyant);

        if (empty($nomPrenomAyant)) {
            return;
        }

        $typeAyant = $this->normalizeTypeAyant($row['type'] ?? $row['type_ayant_droit'] ?? '');

        // Vérifier si l'ayant droit existe déjà
        $existant = AyantDroitSap::where('matricule_agent', $agentSap->matricule)
            ->where('type', $typeAyant)
            ->where('nom_prenom', 'LIKE', '%' . $nomPrenomAyant . '%')
            ->first();

        $data = [
            'matricule_agent' => $agentSap->matricule,
            'type' => $typeAyant,
            'nom_prenom' => $nomPrenomAyant,
            'date_naissance' => $this->parseDate($row['date_naissance_ayant_droit'] ?? $row['date_naissance_ayont_droit'] ?? null),
            'statut' => 'Validé',
            'date_import_sap' => now(),
            'fichier_import' => $this->nomFichier,
            'import_par' => auth()->id(),
        ];

        if ($existant) {
            $existant->update($data);
        } else {
            AyantDroitSap::create($data);
            $this->ayantsImported++;
        }
    }

    protected function extractNom($nomPrenom)
    {
        if (empty($nomPrenom)) {
            return '';
        }

        $parts = explode(' ', trim($nomPrenom), 2);
        return $parts[0] ?? '';
    }

    protected function extractPrenom($nomPrenom)
    {
        if (empty($nomPrenom)) {
            return '';
        }

        $parts = explode(' ', trim($nomPrenom), 2);
        return $parts[1] ?? '';
    }

    protected function cleanString($value)
    {
        if (empty($value)) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    protected function normalizeStatut($statut)
    {
        $statut = strtolower(trim($statut));

        // Statuts actifs
        if (in_array($statut, ['actif', 'active', 'actifve', 'statuaire titulaire', 'statuaire', 'titulaire', 'actif titulaire', 'titulaire actif'])) {
            return 'Actif';
        }

        // Retraités
        if (in_array($statut, ['retraite', 'retraité', 'retraitée', 'retraite', 'retire'])) {
            return 'Retraité';
        }

        // Sorti
        if (in_array($statut, ['sorti', 'sortie', 'départ', 'depart'])) {
            return 'Sorti';
        }

        // Décédé
        if (in_array($statut, ['décédé', 'décédé', 'decede', 'decedé', 'décés', 'deces'])) {
            return 'Décédé';
        }

        // Valeur par défaut si non reconnu
        return 'Actif';
    }

    protected function normalizeTypeAyant($type)
    {
        $type = strtolower(trim($type));

        if (in_array($type, ['conjoint', 'époux', 'épouse', 'epoux', 'epouse', 'mari', 'femme'])) {
            return 'conjoint';
        } elseif (in_array($type, ['enfant', 'fils', 'fille'])) {
            return 'enfant';
        }

        return $type ?: 'enfant';
    }

    protected function cleanMatricule($matricule)
    {
        return ltrim(preg_replace('/[^0-9]/', '', $matricule), '0') ?: '0';
    }

    protected function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        if (is_numeric($dateValue)) {
            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', '1899-12-30')
                    ->addDays(intval($dateValue))
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $dateString = (string) $dateValue;
        $formats = ['d.m.Y', 'd/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d'];

        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAgentsImportedCount(): int
    {
        return $this->agentsImported;
    }

    public function getAgentsUpdatedCount(): int
    {
        return $this->agentsUpdated;
    }

    public function getAyantsImportedCount(): int
    {
        return $this->ayantsImported;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
