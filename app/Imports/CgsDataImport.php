<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\AyantDroit;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Collection;

class CgsDataImport implements ToCollection
{

    protected $agentsImported = 0;
    protected $agentsUpdated = 0;
    protected $ayantsImported = 0;
    protected $ayantsUpdated = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        // Ignorer la ligne d'en-tête (ligne 1)
        $rows = $rows->slice(1);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 car on commence à la ligne 2

            // $row est un tableau indexé (0-based)
            // Convertir en tableau simple
            $rowData = $row->toArray();

            // Extraire le matricule (colonne A = index 0)
            $matricule = $this->cleanMatricule($this->getCellValue($rowData, 0));

            if (empty($matricule) || $matricule === '0') {
                continue;
            }

            // Extraire les données
            $nom = $this->cleanString($this->getCellValue($rowData, 1)); // Nom de l'Agent
            $prenom = $this->cleanString($this->getCellValue($rowData, 2)); // Prénom de l'Agent
            $etat = $this->cleanString($this->getCellValue($rowData, 18)); // Etat

            // Si pas de nom, essayer depuis la colonne NOM (index 12)
            if (empty($nom)) {
                $nomComplet = $this->cleanString($this->getCellValue($rowData, 12));
                if (!empty($nomComplet)) {
                    $parts = explode(' ', $nomComplet, 2);
                    $nom = $parts[0] ?? '';
                    $prenom = $parts[1] ?? $prenom;
                }
            }

            // Vérifier que le nom n'est pas vide
            if (empty($nom)) {
                $this->errors[] = "Ligne $rowNumber: Nom manquant pour matricule $matricule";
                continue;
            }

            $agentData = [
                'matricule' => $matricule,
                'nom' => $nom,
                'prenom' => $prenom,
                'date_naissance' => $this->parseExcelDate($this->getCellValue($rowData, 3)), // Date naissance
                'date_recrutement' => $this->parseExcelDate($this->getCellValue($rowData, 4)), // Date recrutement
                'date_affiliation' => $this->parseExcelDate($this->getCellValue($rowData, 5)), // Date Affiliation Agent
                'cin' => $this->cleanString($this->getCellValue($rowData, 13)), // CIN
                // Champs bancaires
                'compte_bancaire' => $this->cleanString($this->getCellValue($rowData, 15)), // Compte Bancaire
                'cle_bancaire' => $this->cleanString($this->getCellValue($rowData, 16)), // Clé bancaire
                'banque' => $this->cleanString($this->getCellValue($rowData, 17)), // BANQUE
                'info_banque' => $this->cleanString($this->getCellValue($rowData, 14)), // Info banque
                'statut' => $this->parseStatutFromEtat($etat),
            ];

            // Trouver ou créer l'agent
            $agent = Agent::where('matricule', $matricule)->first();

            if ($agent) {
                // Filtrer les valeurs null pour ne pas écraser avec null
                $agentData = array_filter($agentData, function ($value) {
                    return $value !== null;
                });
                $agent->update($agentData);
                $this->agentsUpdated++;
            } else {
                $agent = Agent::create($agentData);
                $this->agentsImported++;
            }

            // Traiter l'ayant droit (bénéficiaire) si présent
            $nomBeneficiaire = $this->cleanString($this->getCellValue($rowData, 6)); // Nom bénéficiaire
            $qualite = $this->cleanString($this->getCellValue($rowData, 7)); // Qualité

            if (!empty($nomBeneficiaire) && !empty($qualite) && strtolower($qualite) !== 'agent') {
                $this->importerAyantDroit($agent, $rowData, $rowNumber);
            }
        }
    }

    /**
     * Importer un ayant droit (bénéficiaire)
     */
    protected function importerAyantDroit(Agent $agent, $rowData, $rowNumber)
    {
        $nomBeneficiaire = $this->cleanString($this->getCellValue($rowData, 6));
        $qualite = $this->normalizeQualite($this->getCellValue($rowData, 7));
        $dateNaissance = $this->parseExcelDate($this->getCellValue($rowData, 8)); // Date naissance Bénéficiaire

        if (empty($nomBeneficiaire) || empty($qualite)) {
            return;
        }

        // Vérifier si l'ayant droit existe déjà
        $existant = AyantDroit::where('agent_id', $agent->id)
            ->where('type', $qualite)
            ->where('nom_prenom', 'LIKE', '%' . $nomBeneficiaire . '%')
            ->first();

        $data = [
            'agent_id' => $agent->id,
            'type' => $qualite,
            'nom_prenom' => $nomBeneficiaire,
            'date_naissance' => $dateNaissance,
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
     * Obtenir une valeur depuis un tableau indexé
     */
    protected function getCellValue($array, $index)
    {
        return $array[$index] ?? null;
    }

    /**
     * Parser une date Excel numérique ou texte
     */
    protected function parseExcelDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // Si c'est déjà un objet Carbon ou DateTime
        if ($dateValue instanceof \Carbon\Carbon) {
            return $dateValue->format('Y-m-d');
        }

        if ($dateValue instanceof \DateTime) {
            return \Carbon\Carbon::instance($dateValue)->format('Y-m-d');
        }

        // Format numérique Excel
        if (is_numeric($dateValue)) {
            try {
                return ExcelDate::excelToDateTimeObject($dateValue)->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback: conversion manuelle
                try {
                    $days = floatval($dateValue);
                    $baseDate = new \DateTime('1899-12-30');
                    $baseDate->modify("+$days days");
                    return $baseDate->format('Y-m-d');
                } catch (\Exception $e2) {
                    return null;
                }
            }
        }

        // Format texte
        $dateString = trim((string) $dateValue);
        if (empty($dateString)) {
            return null;
        }

        $formats = ['d.m.Y', 'd/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'];

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

    /**
     * Nettoyer une chaîne de caractères
     */
    protected function cleanString($value)
    {
        if (empty($value)) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    /**
     * Normaliser la qualité (type d'ayant droit)
     */
    protected function normalizeQualite($qualite)
    {
        $qualite = strtolower(trim((string) $qualite));

        if (in_array($qualite, ['conjoint', 'époux', 'épouse', 'epoux', 'epouse', 'mari', 'femme'])) {
            return 'conjoint';
        }

        if (in_array($qualite, ['enfant', 'fils', 'fille'])) {
            return 'enfant';
        }

        return 'enfant';
    }

    /**
     * Nettoyer le matricule
     */
    protected function cleanMatricule($matricule)
    {
        if (empty($matricule)) {
            return '0';
        }

        $matricule = preg_replace('/[^0-9]/', '', (string) $matricule);
        $matricule = ltrim($matricule, '0');

        return $matricule ?: '0';
    }

    /**
     * Parser le statut depuis le champ Etat
     */
    protected function parseStatutFromEtat($etat)
    {
        if (empty($etat)) {
            return 'Actif';
        }

        $etat = strtolower($etat);

        if (strpos($etat, 'retraité') !== false || strpos($etat, 'retraite') !== false) {
            return 'Retraité';
        }

        if (strpos($etat, 'sorti') !== false || strpos($etat, 'sortie') !== false) {
            return 'Sorti';
        }

        if (strpos($etat, 'décédé') !== false || strpos($etat, 'decede') !== false || strpos($etat, 'décès') !== false) {
            return 'Décédé';
        }

        if (strpos($etat, 'suspendu') !== false) {
            return 'Suspendu';
        }

        return 'Actif';
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
