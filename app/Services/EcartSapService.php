<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\EcartSapCgs;
use Illuminate\Support\Collection;

class EcartSapService
{
    /**
     * Détecter les écarts après un import SAP
     */
    public function detecterEcartsApresImport(): array
    {
        $resultats = [
            'agents_sap_absents_cgs' => 0,
            'agents_cgs_absents_sap' => 0,
            'statuts_incoherents' => 0,
            'ages_superieurs_63' => 0,
            'donnees_divergentes' => 0,
        ];

        // Récupérer tous les agents CGS
        $agentsCgs = Agent::all();
        $matriculesSap = Agent::pluck('matricule')->toArray();

        foreach ($agentsCgs as $agentCgs) {
            // Vérifier si l'agent est toujours dans SAP (on suppose que l'import vient de se faire)
            // Cette vérification se fera lors du prochain import SAP

            // Vérifier les agents actifs dans CGS mais qui devraient être bloqués
            if ($agentCgs->statut === 'Actif') {
                // Vérifier l'âge
                if ($agentCgs->age >= 63) {
                    EcartSapCgs::firstOrCreate(
                        [
                            'type_ecart' => 'Âge ≥ 63 ans',
                            'matricule' => $agentCgs->matricule,
                            'traite' => false,
                        ],
                        [
                            'donnee_sap' => $agentCgs->age . ' ans',
                            'donnee_cgs' => null,
                            'details' => "Agent âgé de {$agentCgs->age} ans - limite dépassée",
                            'date_detection' => now(),
                        ]
                    );
                    $resultats['ages_superieurs_63']++;
                }

                // Vérifier le statut (sera fait lors de l'import SAP)
            }
        }

        return $resultats;
    }

    /**
     * Comparer les agents SAP avec CGS (à appeler après import SAP)
     */
    public function comparerSapCgs(Collection $agentsSap): array
    {
        $resultats = [
            'nouveau_sap' => 0,
            'manquant_sap' => 0,
            'statut_change' => 0,
            'ecarts_total' => 0,
        ];

        $matriculesSap = $agentsSap->pluck('matricule')->toArray();
        $agentsCgs = Agent::all();

        // Agents dans SAP mais pas dans CGS
        foreach ($agentsSap as $agentSap) {
            $agentCgs = $agentsCgs->firstWhere('matricule', $agentSap['matricule']);

            if (!$agentCgs) {
                EcartSapCgs::creer(
                    'Agent SAP absent CGS',
                    $agentSap['matricule'],
                    json_encode($agentSap),
                    null,
                    'Nouvel agent détecté dans SAP - Création recommandée'
                );
                $resultats['nouveau_sap']++;
                $resultats['ecarts_total']++;
            }
        }

        // Agents dans CGS mais pas dans SAP
        foreach ($agentsCgs as $agentCgs) {
            if (!in_array($agentCgs->matricule, $matriculesSap)) {
                EcartSapCgs::creer(
                    'Agent CGS absent SAP',
                    $agentCgs->matricule,
                    null,
                    json_encode($agentCgs->toArray()),
                    'Agent absent de l\'extraction SAP - Vérification nécessaire'
                );
                $resultats['manquant_sap']++;
                $resultats['ecarts_total']++;
            }
        }

        return $resultats;
    }

    /**
     * Obtenir les écarts non traités
     */
    public function getEcartsNonTraites(): Collection
    {
        return EcartSapCgs::nonTraites()
            ->orderBy('date_detection', 'desc')
            ->get();
    }

    /**
     * Obtenir les statistiques des écarts
     */
    public function getStatistiquesEcarts(): array
    {
        return [
            'total' => EcartSapCgs::count(),
            'non_trites' => EcartSapCgs::nonTraites()->count(),
            'traites' => EcartSapCgs::traites()->count(),
            'par_type' => EcartSapCgs::select('type_ecart')
                ->selectRaw('type_ecart, COUNT(*) as total')
                ->groupBy('type_ecart')
                ->pluck('total', 'type_ecart')
                ->toArray(),
        ];
    }

    /**
     * Marquer un écart comme traité
     */
    public function marquerTraite(int $ecartId, int $userId): bool
    {
        $ecart = EcartSapCgs::find($ecartId);

        if (!$ecart) {
            return false;
        }

        $ecart->marquerTraite($userId);
        return true;
    }

    /**
     * Marquer plusieurs écarts comme traités
     */
    public function marquerTraitesMultiple(array $ecartIds, int $userId): int
    {
        $count = 0;
        foreach ($ecartIds as $id) {
            if ($this->marquerTraite($id, $userId)) {
                $count++;
            }
        }
        return $count;
    }
}
