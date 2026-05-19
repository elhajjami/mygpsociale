<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\DemandePEC;
use App\Models\PlafondAnnuelAgent;

class PlafondService
{
    /**
     * Vérifier si un agent a un plafond suffisant pour une demande
     */
    public function verifierPlafond(int $agentId, float $montantDemande, string $typePrestation = 'medical'): array
    {
        $agent = Agent::find($agentId);

        if (!$agent) {
            return [
                'succes' => false,
                'message' => 'Agent non trouvé',
                'plafond_annuel' => 0,
                'montant_consome' => 0,
                'reste_disponible' => 0,
            ];
        }

        $plafond = PlafondAnnuelAgent::obtenirOuCreer($agentId);

        // Déterminer le type de facture selon la prestation
        $typeFacture = in_array($typePrestation, ['chirurgie', 'hospitalisation']) ? 'clinique' : 'medical';

        // Pour medical : on considère le montant total
        // Pour clinique : on considère que la part adhérent sera d'environ 20-30% (estimation)
        $montantAConsommer = $typeFacture === 'medical' ? $montantDemande : ($montantDemande * 0.3);

        if ($typeFacture === 'medical') {
            $resteDisponible = $plafond->reste_medical;
            $montantConsome = $plafond->consomme_medical;
        } else {
            $resteDisponible = $plafond->reste_clinique;
            $montantConsome = $plafond->consomme_clinique;
        }

        $resteApresDemande = $resteDisponible - $montantAConsommer;

        $resultat = [
            'succes' => $resteApresDemande >= 0,
            'message' => $resteApresDemande >= 0
                ? 'Plafond suffisant'
                : 'Plafond insuffisant',
            'plafond_annuel' => $plafond->plafond_annuel,
            'montant_consome' => $montantConsome,
            'reste_disponible' => $resteDisponible,
            'reste_apres_demande' => max(0, $resteApresDemande),
            'montant_demande' => $montantDemande,
            'montant_a_consommer' => $montantAConsommer,
            'depassement' => $resteApresDemande < 0 ? abs($resteApresDemande) : 0,
            'type_facture' => $typeFacture,
            'agent' => $agent,
            'pourcentage_utilise' => $typeFacture === 'medical'
                ? $plafond->pourcentage_utilisation_medical
                : $plafond->pourcentage_utilisation_clinique,
        ];

        return $resultat;
    }

    /**
     * Engager un montant sur le plafond d'un agent (lors de la validation d'une demande)
     * Note: cette méthode n'est plus utilisée avec le nouveau système
     */
    public function engagerMontant(int $agentId, float $montant): PlafondAnnuelAgent
    {
        // Avec le nouveau système, on ne crée plus d'engagement
        // Le plafond est mis à jour uniquement lors de la facturation
        return PlafondAnnuelAgent::obtenirOuCreer($agentId);
    }

    /**
     * Annuler un montant engagé (rejet de demande ou annulation)
     * Note: cette méthode n'est plus utilisée avec le nouveau système
     */
    public function annulerEngagement(int $agentId, float $montant): PlafondAnnuelAgent
    {
        // Avec le nouveau système, on n'annule plus d'engagement
        return PlafondAnnuelAgent::obtenirOuCreer($agentId);
    }

    /**
     * Obtenir les statistiques de consommation d'un agent
     */
    public function getStatistiquesAgent(int $agentId, int $annee = null): array
    {
        $annee = $annee ?? now()->year;
        $agent = Agent::find($agentId);
        $plafond = PlafondAnnuelAgent::obtenirOuCreer($agentId, $annee);

        return [
            'agent' => $agent,
            'annee' => $annee,
            'plafond_annuel' => $plafond->plafond_annuel,
            'consomme_medical' => $plafond->consomme_medical,
            'consomme_clinique' => $plafond->consomme_clinique,
            'reste_medical' => $plafond->reste_medical,
            'reste_clinique' => $plafond->reste_clinique,
            'pourcentage_utilisation_medical' => $plafond->pourcentage_utilisation_medical,
            'pourcentage_utilisation_clinique' => $plafond->pourcentage_utilisation_clinique,
        ];
    }

    /**
     * Obtenir le plafond annuel d'un agent pour l'année courante
     */
    public function getPlafondAgent(int $agentId): ?PlafondAnnuelAgent
    {
        return PlafondAnnuelAgent::obtenirOuCreer($agentId);
    }

    /**
     * Vérifier si un agent a suffisamment de plafond disponible
     */
    public function verifierPlafondDisponible(int $agentId, float $montant, string $typePrestation = 'medical'): bool
    {
        $resultat = $this->verifierPlafond($agentId, $montant, $typePrestation);
        return $resultat['succes'];
    }

    /**
     * Mettre à jour le plafond après facturation
     */
    public function mettreAJourPlafond(int $agentId, float $montant, string $typeFacture): void
    {
        $plafond = PlafondAnnuelAgent::obtenirOuCreer($agentId);

        if ($typeFacture === 'medical') {
            $plafond->ajouterConsommationMedical($montant);
        } elseif ($typeFacture === 'clinique') {
            // Pour clinique, le montant passé doit être la part adhérent
            $plafond->ajouterConsommationClinique($montant);
        }
    }

    /**
     * Convertir un engagement en consommation (non utilisé avec nouveau système)
     */
    public function convertirEngagementEnConsommation(int $agentId, float $montant): void
    {
        // Plus nécessaire avec le nouveau système
    }

    /**
     * Obtenir les alertes de plafond pour les agents ayant dépassé 80% de leur plafond
     */
    public function getAlertesPlafond(int $annee = null): array
    {
        $annee = $annee ?? now()->year;
        $alertes = [];

        // Récupérer tous les plafonds annuels pour l'année spécifiée
        $plafonds = PlafondAnnuelAgent::where('annee', $annee)
            ->with('agent')
            ->get();

        foreach ($plafonds as $plafond) {
            $pourcentageMedical = $plafond->pourcentage_utilisation_medical;
            $pourcentageClinique = $plafond->pourcentage_utilisation_clinique;

            // Alerte si plus de 80% utilisé
            if ($pourcentageMedical > 80) {
                $alertes[] = [
                    'type' => 'medical',
                    'agent_id' => $plafond->agent_id,
                    'agent' => [
                        'nom' => $plafond->agent?->nom ?? 'N/A',
                        'prenom' => $plafond->agent?->prenom ?? '',
                        'categorie' => $plafond->agent?->categorie ?? 'N/A',
                    ],
                    'matricule' => $plafond->agent?->matricule ?? 'N/A',
                    'pourcentage' => $pourcentageMedical,
                    'reste' => $plafond->reste_medical,
                    'plafond_annuel' => $plafond->plafond_annuel,
                    'niveau' => $pourcentageMedical > 95 ? 'critique' : 'avertissement',
                    'severite' => $pourcentageMedical > 95 ? 'critique' : 'avertissement',
                ];
            }

            if ($pourcentageClinique > 80) {
                $alertes[] = [
                    'type' => 'clinique',
                    'agent_id' => $plafond->agent_id,
                    'agent' => [
                        'nom' => $plafond->agent?->nom ?? 'N/A',
                        'prenom' => $plafond->agent?->prenom ?? '',
                        'categorie' => $plafond->agent?->categorie ?? 'N/A',
                    ],
                    'matricule' => $plafond->agent?->matricule ?? 'N/A',
                    'pourcentage' => $pourcentageClinique,
                    'reste' => $plafond->reste_clinique,
                    'plafond_annuel' => $plafond->plafond_annuel,
                    'niveau' => $pourcentageClinique > 95 ? 'critique' : 'avertissement',
                    'severite' => $pourcentageClinique > 95 ? 'critique' : 'avertissement',
                ];
            }
        }

        // Trier par sévérité puis par pourcentage décroissant
        usort($alertes, function($a, $b) {
            if ($a['severite'] !== $b['severite']) {
                return $a['severite'] === 'critique' ? -1 : 1;
            }
            return $b['pourcentage'] <=> $a['pourcentage'];
        });

        return $alertes;
    }

    /**
     * Initialiser les plafonds annuels pour tous les agents de l'année courante
     */
    public function initialiserPlafondsAnnee(int $annee = null): int
    {
        $annee = $annee ?? now()->year;
        $compteur = 0;

        $agents = Agent::all();

        foreach ($agents as $agent) {
            // Vérifier si le plafond existe déjà
            $existant = PlafondAnnuelAgent::where('agent_id', $agent->id)
                ->where('annee', $annee)
                ->first();

            if (!$existant) {
                $plafondAnnuel = $agent->plafond_annuel ?? 12000;

                PlafondAnnuelAgent::create([
                    'agent_id' => $agent->id,
                    'annee' => $annee,
                    'plafond_annuel' => $plafondAnnuel,
                    'consomme' => 0,
                    'reste' => $plafondAnnuel,
                ]);

                $compteur++;
            }
        }

        return $compteur;
    }
}
