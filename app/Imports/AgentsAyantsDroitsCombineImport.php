<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\AyantDroit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentsAyantsDroitsCombineImport implements ToCollection, WithHeadingRow
{
    protected $agentsImported = 0;
    protected $agentsUpdated = 0;
    protected $ayantsImported = 0;
    protected $ayantsUpdated = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 car ligne 1 = en-têtes

            $matricule = $this->cleanMatricule($row['matricule'] ?? '');

            if (empty($matricule)) {
                $this->errors[] = "Ligne $rowNumber: Matricule manquant";
                continue;
            }

            // Extraire nom et prénom depuis nom_prenom (champ combiné pour l'agent)
            $nomPrenom = $row['nom_prenom'] ?? $row['nom_prenom_agent'] ?? '';
            $nom = $this->extractNom($nomPrenom);
            $prenom = $this->extractPrenom($nomPrenom);

            // Préparer les données de l'agent
            $agentData = [
                'matricule' => $matricule,
                'nom' => $nom,
                'prenom' => $prenom,
                'statut' => $this->normalizeStatut($row['statut'] ?? 'Actif'),
                'date_naissance' => $this->parseDate($row['date_naissance'] ?? null),
            ];

            // Trouver ou créer l'agent
            $agent = Agent::where('matricule', $matricule)->first();

            if ($agent) {
                $agent->update($agentData);
                $this->agentsUpdated++;
            } else {
                $agent = Agent::create($agentData);
                $this->agentsImported++;
            }

            // Traiter l'ayant droit si les champs sont remplis
            $nomAyant = $row['nom_ayant_droit'] ?? $row['nom'] ?? '';
            $prenomAyant = $row['prenom_ayant_droit'] ?? $row['prenom'] ?? '';
            $typeAyant = $row['type'] ?? $row['type_ayant_droit'] ?? '';

            // Si on a un nom ou prénom d'ayant droit, on l'ajoute
            if (!empty($nomAyant) || !empty($prenomAyant)) {
                $this->importerAyantDroit($agent, $row, $rowNumber);
            } elseif (!empty($typeAyant)) {
                // Cas où le type est spécifié mais pas le nom (ex: "non marié")
                // On peut logger ou ignorer
            }
        }
    }

    /**
     * Importer un ayant droit pour un agent
     */
    protected function importerAyantDroit(Agent $agent, $row, $rowNumber)
    {
        $nomAyant = $row['nom_ayant_droit'] ?? $row['nom'] ?? '';
        $prenomAyant = $row['prenom_ayant_droit'] ?? $row['prenom'] ?? '';
        $nomPrenomAyant = trim($nomAyant . ' ' . $prenomAyant);

        if (empty($nomPrenomAyant)) {
            return;
        }

        $typeAyant = $this->normalizeTypeAyant($row['type'] ?? $row['type_ayant_droit'] ?? '');

        // Vérifier si l'ayant droit existe déjà
        $existant = AyantDroit::where('agent_id', $agent->id)
            ->where('type', $typeAyant)
            ->where('nom_prenom', 'LIKE', '%' . $nomPrenomAyant . '%')
            ->first();

        $data = [
            'agent_id' => $agent->id,
            'type' => $typeAyant,
            'nom_prenom' => $nomPrenomAyant,
            'date_naissance' => $this->parseDate($row['date_naissance_ayant_droit'] ?? $row['date_naissance_ayont_droit'] ?? null),
            'statut' => 'Validé',
        ];

        if ($existant) {
            $existant->update($data);
            $this->ayantsUpdated++;
        } else {
            AyantDroit::create($data);
            $this->ayantsImported++;
        }
    }

    /**
     * Extraire le nom depuis nom_prenom
     */
    protected function extractNom($nomPrenom)
    {
        if (empty($nomPrenom)) {
            return '';
        }

        $parts = explode(' ', trim($nomPrenom), 2);
        return $parts[0] ?? '';
    }

    /**
     * Extraire le prénom depuis nom_prenom
     */
    protected function extractPrenom($nomPrenom)
    {
        if (empty($nomPrenom)) {
            return '';
        }

        $parts = explode(' ', trim($nomPrenom), 2);
        return $parts[1] ?? '';
    }

    /**
     * Normaliser le statut
     */
    protected function normalizeStatut($statut)
    {
        $statut = strtolower(trim($statut));

        if (in_array($statut, ['actif', 'active', 'actifve'])) {
            return 'Actif';
        } elseif (in_array($statut, ['retraite', 'retraité', 'retraitée'])) {
            return 'Retraité';
        }

        return $statut ?: 'Actif';
    }

    /**
     * Normaliser le type d'ayant droit
     */
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

    /**
     * Nettoyer le matricule
     */
    protected function cleanMatricule($matricule)
    {
        return ltrim(preg_replace('/[^0-9]/', '', $matricule), '0') ?: '0';
    }

    /**
     * Parser une date depuis différents formats
     */
    protected function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // Format numérique Excel
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

        // Formats supportés
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

        // Parser automatique
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

    public function getAyantsUpdatedCount(): int
    {
        return $this->ayantsUpdated;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
