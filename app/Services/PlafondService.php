<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\DemandePEC;
use App\Models\PlafondAnnuel;

class PlafondService
{
    /**
     * Vérifier si un agent a un plafond suffisant pour une demande
     */
    public function verifierPlafond(int $agentId, float $montantDemande): array
    {
        $agent = Agent::find($agentId);

        if (!$agent) {
            return [
                'succes' => false,
                'message' => 'Agent non trouvé',
                'plafond_annuel' => 0,
                'montant_consome' => 0,
                'montant_engage' => 0,
                'reste_disponible' => 0,
            ];
        }

        $plafond = PlafondAnnuel::pourAgent($agentId);

        $resteDisponible = $plafond->plafond_annuel - $plafond->montant_consome - $plafond->montant_engage;

        $resultat = [
            'succes' => $resteDisponible >= $montantDemande,
            'message' => $resteDisponible >= $montantDemande
                ? 'Plafond suffisant'
                : 'Plafond insuffisant',
            'plafond_annuel' => $plafond->plafond_annuel,
            'montant_consome' => $plafond->montant_consome,
            'montant_engage' => $plafond->montant_engage,
            'reste_disponible' => $resteDisponible,
            'montant_demande' => $montantDemande,
            'depassement' => $montantDemande > $resteDisponible ? $montantDemande - $resteDisponible : 0,
            'agent' => $agent,
        ];

        return $resultat;
    }

    /**
     * Engager un montant sur le plafond d'un agent (lors de la validation d'une demande)
     */
    public function engagerMontant(int $agentId, float $montant): PlafondAnnuel
    {
        $plafond = PlafondAnnuel::pourAgent($agentId);
        $plafond->ajouterEngage($montant);

        return $plafond->refresh();
    }

    /**
     * Confirmer un montant engagé (lors du paiement effectif)
     */
    public function confirmerEngagement(int $agentId, float $montant): PlafondAnnuel
    {
        $plafond = PlafondAnnuel::pourAgent($agentId);
        $plafond->confirmerEngage($montant);

        return $plafond->refresh();
    }

    /**
     * Annuler un montant engagé (rejet de demande ou annulation)
     */
    public function annulerEngagement(int $agentId, float $montant): PlafondAnnuel
    {
        $plafond = PlafondAnnuel::pourAgent($agentId);
        $plafond->annulerEngage($montant);

        return $plafond->refresh();
    }

    /**
     * Obtenir les statistiques de consommation d'un agent
     */
    public function getStatistiquesAgent(int $agentId, int $annee = null): array
    {
        $annee = $annee ?? now()->year;
        $agent = Agent::find($agentId);
        $plafond = PlafondAnnuel::pourAgent($agentId, $annee);

        return [
            'agent' => $agent,
            'annee' => $annee,
            'plafond_annuel' => $plafond->plafond_annuel,
            'montant_consome' => $plafond->montant_consome,
            'montant_engage' => $plafond->montant_engage,
            'reste_disponible' => $plafond->reste_disponible,
            'pourcentage_consomme' => $plafond->pourcentage_consomme,
            'pourcentage_engage' => $plafond->pourcentage_engage,
        ];
    }

    /**
     * Obtenir les alertes de plafond (agents ayant dépassé 80% de leur plafond)
     */
    public function getAlertesPlafond(int $annee = null): array
    {
        $annee = $annee ?? now()->year;
        $alertes = [];

        $plafonds = PlafondAnnuel::where('annee', $annee)->get();

        foreach ($plafonds as $plafond) {
            $pourcentage = $plafond->pourcentage_engage;

            if ($pourcentage >= 100) {
                $alertes[] = [
                    'type' => 'critique',
                    'agent' => $plafond->agent,
                    'plafond' => $plafond,
                    'message' => 'Plafond dépassé',
                ];
            } elseif ($pourcentage >= 90) {
                $alertes[] = [
                    'type' => 'urgent',
                    'agent' => $plafond->agent,
                    'plafond' => $plafond,
                    'message' => 'Plafond bientôt épuisé',
                ];
            } elseif ($pourcentage >= 80) {
                $alertes[] = [
                    'type' => 'avertissement',
                    'agent' => $plafond->agent,
                    'plafond' => $plafond,
                    'message' => 'Plafond à plus de 80%',
                ];
            }
        }

        return $alertes;
    }

    /**
     * Initialiser les plafonds annuels pour tous les agents
     */
    public function initialiserPlafondsAnnee(int $annee = null): int
    {
        $annee = $annee ?? now()->year;
        $compteur = 0;

        Agent::actifs()->chunk(100, function ($agents) use ($annee, &$compteur) {
            foreach ($agents as $agent) {
                $plafond = PlafondAnnuel::firstOrCreate(
                    [
                        'agent_id' => $agent->id,
                        'annee' => $annee,
                    ],
                    [
                        'plafond_annuel' => $agent->plafond_annuel,
                        'montant_consome' => 0,
                        'montant_engage' => 0,
                        'reste_disponible' => $agent->plafond_annuel,
                    ]
                );

                if ($plafond->wasRecentlyCreated) {
                    $compteur++;
                }
            }
        });

        return $compteur;
    }

    /**
     * Obtenir le plafond annuel d'un agent pour l'année courante
     */
    public function getPlafondAgent(int $agentId): ?PlafondAnnuel
    {
        return PlafondAnnuel::pourAgent($agentId);
    }

    /**
     * Vérifier si un agent a suffisamment de plafond disponible
     */
    public function verifierPlafondDisponible(int $agentId, float $montant): bool
    {
        $resultat = $this->verifierPlafond($agentId, $montant);
        return $resultat['succes'];
    }

    /**
     * Mettre à jour le plafond (engagement ou consommation)
     */
    public function mettreAJourPlafond(int $agentId, float $montant, string $type): void
    {
        if ($type === 'engagement') {
            $this->engagerMontant($agentId, $montant);
        } elseif ($type === 'consommation') {
            $this->confirmerEngagement($agentId, $montant);
        }
    }

    /**
     * Convertir un engagement en consommation
     */
    public function convertirEngagementEnConsommation(int $agentId, float $montant): void
    {
        $plafond = PlafondAnnuel::pourAgent($agentId);

        if (!$plafond) {
            throw new \Exception("Aucun plafond trouvé pour l'agent {$agentId}");
        }

        $plafond->decrement('montant_engage', $montant);
        $plafond->increment('montant_consome', $montant);
    }
}
