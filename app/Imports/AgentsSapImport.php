<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\EcartSapCgs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AgentsSapImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $imported = 0;
    protected $updated = 0;
    protected $ecarts = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $matricule = $this->cleanMatricule($row['matricule'] ?? '');

            if (empty($matricule)) {
                continue;
            }

            $data = [
                'matricule' => $matricule,
                'nom' => $row['nom'] ?? null,
                'prenom' => $row['prenom'] ?? null,
                'cin' => $this->cleanCIN($row['cin'] ?? null),
                'date_naissance' => $this->parseDate($row['date_naissance'] ?? null),
                'categorie' => $row['categorie'] ?? null,
                'niveau' => $row['niveau'] ?? null,
                'degre' => $row['degre'] ?? null,
                'dp_affectation' => $row['dp_affectation'] ?? $row['affectation'] ?? null,
                'population' => $row['population'] ?? 'autre',
                'statut' => $row['statut'] ?? 'Actif',
                'date_entree' => $this->parseDate($row['date_entree'] ?? null),
                'date_sortie' => $this->parseDate($row['date_sortie'] ?? null),
                'date_retraite' => $this->parseDate($row['date_retraite'] ?? null),
                'numero_immatriculation' => $row['numero_immatriculation'] ?? $row['immatriculation'] ?? null,
                'numero_affiliation' => $row['numero_affiliation'] ?? $row['affiliation'] ?? null,
            ];

            $existingAgent = Agent::where('matricule', $matricule)->first();

            if ($existingAgent) {
                // Vérifier les écarts
                $this->detecterEcarts($existingAgent, $data, $row);

                // Mise à jour de l'agent existant
                $existingAgent->update($data);
                $this->updated++;
            } else {
                // Création d'un nouvel agent
                Agent::create($data);
                $this->imported++;

                // Créer un écart : agent présent dans SAP mais pas dans CGS
                EcartSapCgs::creer(
                    'Agent SAP absent CGS',
                    $matricule,
                    json_encode($data),
                    null,
                    'Nouvel agent importé depuis SAP'
                );
            }
        }
    }

    /**
     * Détecter les écarts entre SAP et CGS
     */
    protected function detecterEcarts(Agent $agent, array $dataSap, $rowOriginal)
    {
        $ecartsDetectes = false;

        // Vérifier le statut
        if ($dataSap['statut'] !== $agent->statut) {
            if (in_array($dataSap['statut'], ['Retraité', 'Sorti', 'Décédé', 'Supprimé']) &&
                $agent->statut === 'Actif') {
                EcartSapCgs::creer(
                    'Statut incohérent',
                    $agent->matricule,
                    $dataSap['statut'],
                    $agent->statut,
                    "Agent {$dataSap['statut']} dans SAP mais Actif dans CGS - Blocage PEC recommandé"
                );
                $ecartsDetectes = true;
            }
        }

        // Vérifier l'âge (≥ 63 ans)
        if ($dataSap['date_naissance']) {
            $age = \Carbon\Carbon::parse($dataSap['date_naissance'])->age;
            if ($age >= 63) {
                EcartSapCgs::creer(
                    'Âge ≥ 63 ans',
                    $agent->matricule,
                    $age . ' ans',
                    null,
                    "Agent atteint ou dépasse 63 ans - Alertes à prévoir"
                );
                $ecartsDetectes = true;
            }
        }

        // Vérifier les données divergentes
        $champsCritiques = ['nom', 'prenom', 'cin', 'dp_affectation', 'categorie'];
        $divergences = [];

        foreach ($champsCritiques as $champ) {
            if ($dataSap[$champ] && $dataSap[$champ] != $agent->$champ) {
                $divergences[] = "$champ: CGS={$agent->$champ}, SAP={$dataSap[$champ]}";
            }
        }

        if (!empty($divergences)) {
            EcartSapCgs::creer(
                'Données divergentes',
                $agent->matricule,
                json_encode($dataSap),
                json_encode($agent->only($champsCritiques)),
                "Divergences détectées: " . implode('; ', $divergences)
            );
            $ecartsDetectes = true;
        }
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

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'matricule' => 'required',
            'nom' => 'nullable|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'cin' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'categorie' => 'nullable|in:Exécution,Maîtrise,Cadre,Hors cadre',
            'statut' => 'nullable|in:Actif,Retraité,Sorti,Décédé,Suspendu,Supprimé',
        ];
    }

    /**
     * Obtenir le nombre d'agents importés
     */
    public function getImportedCount(): int
    {
        return $this->imported;
    }

    /**
     * Obtenir le nombre d'agents mis à jour
     */
    public function getUpdatedCount(): int
    {
        return $this->updated;
    }

    /**
     * Obtenir le nombre d'écarts détectés
     */
    public function getEcartsCount(): int
    {
        return count($this->ecarts);
    }
}
