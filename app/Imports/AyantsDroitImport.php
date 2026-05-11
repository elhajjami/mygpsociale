<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\AyantDroit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AyantsDroitImport implements ToCollection, WithHeadingRow
{
    protected $imported = 0;
    protected $updated = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $matricule = $this->cleanMatricule($row['matricule'] ?? '');

            if (empty($matricule)) {
                continue;
            }

            $agent = Agent::where('matricule', $matricule)->first();

            if (!$agent) {
                // Créer un écart si l'agent n'existe pas
                \App\Models\EcartSapCgs::creer(
                    'Ayants droit divergents',
                    $matricule,
                    null,
                    null,
                    "Agent non trouvé pour l'ayant droit: " . ($row['nom_prenom'] ?? '')
                );
                continue;
            }

            // Déterminer le type (conjoint ou enfant)
            $type = $this->determineType($row);

            // Vérifier si l'ayant droit existe déjà
            $existant = AyantDroit::where('agent_id', $agent->id)
                ->where('type', $type)
                ->where('nom_prenom', $row['nom_prenom'] ?? '')
                ->first();

            $data = [
                'agent_id' => $agent->id,
                'type' => $type,
                'nom_prenom' => $row['nom_prenom'] ?? null,
                'date_naissance' => $this->parseDate($row['date_naissance'] ?? null),
                'cin' => $this->cleanCIN($row['cin'] ?? null),
                'statut' => 'Validé',
            ];

            if ($existant) {
                $existant->update($data);
                $this->updated++;
            } else {
                AyantDroit::create($data);
                $this->imported++;
            }
        }
    }

    /**
     * Déterminer le type d'ayant droit
     */
    protected function determineType($row)
    {
        // Vérifier explicitement le type
        if (isset($row['type'])) {
            $type = strtolower($row['type']);
            if (in_array($type, ['conjoint', 'époux', 'épouse'])) {
                return 'conjoint';
            } elseif (in_array($type, ['enfant', 'fils', 'fille'])) {
                return 'enfant';
            }
        }

        // Sinon, essayer de déterminer à partir d'autres champs
        if (isset($row['lien_de_parente'])) {
            $lien = strtolower($row['lien_de_parente']);
            if (in_array($lien, ['conjoint', 'époux', 'épouse', 'mari'])) {
                return 'conjoint';
            } elseif (in_array($lien, ['enfant', 'fils', 'fille'])) {
                return 'enfant';
            }
        }

        // Par défaut, on suppose que c'est un enfant si non spécifié
        return 'enfant';
    }

    /**
     * Nettoyer le matricule
     */
    protected function cleanMatricule($matricule)
    {
        return ltrim(preg_replace('/[^0-9]/', '', $matricule), '0') ?: '0';
    }

    /**
     * Nettoyer le CIN
     */
    protected function cleanCIN($cin)
    {
        if (empty($cin)) {
            return null;
        }

        return strtoupper(preg_replace('/\s+/', '', trim($cin)));
    }

    /**
     * Parser une date
     */
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

        try {
            return \Carbon\Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getUpdatedCount(): int
    {
        return $this->updated;
    }
}
