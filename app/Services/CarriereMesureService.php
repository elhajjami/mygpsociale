<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Carriere;
use App\Models\Mesure;
use App\Models\EcartSapCgs;
use Illuminate\Support\Facades\DB;

class CarriereMesureService
{
    /**
     * Mettre à jour la catégorie et le statut calculés pour tous les agents
     */
    public function synchroniserTous(): array
    {
        $resultats = [
            'traites' => 0,
            'categorie_changee' => 0,
            'statut_change' => 0,
            'ecarts_detectes' => 0,
        ];

        Agent::chunk(100, function ($agents) use (&$resultats) {
            foreach ($agents as $agent) {
                $resultat = $this->synchroniserAgent($agent);

                $resultats['traites']++;
                if ($resultat['categorie_changee']) {
                    $resultats['categorie_changee']++;
                }
                if ($resultat['statut_change']) {
                    $resultats['statut_change']++;
                }
                if ($resultat['ecart_cree']) {
                    $resultats['ecarts_detectes']++;
                }
            }
        });

        return $resultats;
    }

    /**
     * Synchroniser un agent : calculer catégorie et statut
     */
    public function synchroniserAgent(Agent $agent): array
    {
        $resultat = [
            'categorie_changee' => false,
            'statut_change' => false,
            'ecart_cree' => false,
        ];

        // Calculer la catégorie depuis le niveau (Carrière)
        $nouvelleCategorie = Carriere::getCategorieFromNiveau($agent->niveau);

        if ($agent->categorie !== $nouvelleCategorie) {
            // Créer un écart si les catégories diffèrent
            if (!empty($agent->categorie) && $agent->categorie !== $nouvelleCategorie) {
                EcartSapCgs::firstOrCreate(
                    [
                        'type_ecart' => 'Catégorie incohérente',
                        'matricule' => $agent->matricule,
                        'traite' => false,
                    ],
                    [
                        'donnee_sap' => "Niveau: {$agent->niveau} → Catégorie: {$nouvelleCategorie}",
                        'donnee_cgs' => "Catégorie enregistrée: {$agent->categorie}",
                        'details' => "La catégorie ne correspond pas au niveau. Recalcul recommandé.",
                        'date_detection' => now(),
                    ]
                );
                $resultat['ecart_cree'] = true;
            }

            $agent->categorie_calculee = $nouvelleCategorie;
            $resultat['categorie_changee'] = true;
        }

        // Calculer le statut depuis la situation administrative (Mesure)
        $ancienStatut = $agent->statut_calcule ?? $agent->statut;

        // Vérifier la situation administrative (DE = Départ, etc.)
        $nouveauStatut = $this->calculerStatutDepuisSituation($agent);

        if ($nouveauStatut !== $ancienStatut) {
            // Créer un écart si le statut change vers une sortie
            if (in_array($nouveauStatut, ['Sorti', 'Décédé', 'Suspendu'])) {
                EcartSapCgs::firstOrCreate(
                    [
                        'type_ecart' => 'Statut incohérent',
                        'matricule' => $agent->matricule,
                        'traite' => false,
                    ],
                    [
                        'donnee_sap' => "Situation: {$agent->situation_administrative} → Statut: {$nouveauStatut}",
                        'donnee_cgs' => "Statut enregistré: {$ancienStatut}",
                        'details' => "Le statut devrait être '{$nouveauStatut}' selon la situation administrative.",
                        'date_detection' => now(),
                    ]
                );
                $resultat['ecart_cree'] = true;
            }

            $agent->statut_calcule = $nouveauStatut;
            $resultat['statut_change'] = true;
        }

        $agent->saveQuietly();

        return $resultat;
    }

    /**
     * Calculer le statut depuis la situation administrative
     */
    protected function calculerStatutDepuisSituation(Agent $agent): string
    {
        // 1. Vérifier si population = "DE" (Départ)
        if ($agent->population === 'DE') {
            return 'Sorti';
        }

        // 2. Vérifier la situation administrative (codes de mesure)
        if (!empty($agent->situation_administrative)) {
            return Mesure::getStatutCGS($agent->situation_administrative);
        }

        // 3. Vérifier les dates
        if ($agent->date_sortie && $agent->date_sortie->isPast()) {
            return 'Sorti';
        }

        if ($agent->date_retraite && $agent->date_retraite->isPast()) {
            return 'Retraité';
        }

        // 4. Statut enregistré
        return $agent->statut ?? 'Actif';
    }

    /**
     * Appliquer les valeurs calculées aux valeurs réelles
     */
    public function appliquerCalculs(Agent $agent): void
    {
        if ($agent->categorie_calculee) {
            $agent->categorie = $agent->categorie_calculee;
        }

        if ($agent->statut_calcule) {
            $agent->statut = $agent->statut_calcule;
        }

        $agent->save();
    }

    /**
     * Obtenir les règles de carrière sous forme de tableau
     */
    public function getReglesCarriere(): array
    {
        return [
            'Exécution' => [
                'prefixe' => 'E',
                'plafond' => ParametrePlafond::getPlafond('Exécution'),
                'niveaux' => Carriere::getCodesForCategorie('Exécution'),
            ],
            'Maîtrise' => [
                'prefixe' => 'M',
                'plafond' => ParametrePlafond::getPlafond('Maîtrise'),
                'niveaux' => Carriere::getCodesForCategorie('Maîtrise'),
            ],
            'Cadre' => [
                'prefixe' => 'C',
                'plafond' => ParametrePlafond::getPlafond('Cadre'),
                'niveaux' => Carriere::getCodesForCategorie('Cadre'),
            ],
            'Hors cadre' => [
                'prefixe' => 'H',
                'plafond' => ParametrePlafond::getPlafond('Hors cadre'),
                'niveaux' => Carriere::getCodesForCategorie('Hors cadre'),
            ],
        ];
    }

    /**
     * Obtenir les codes de mesure sous forme de tableau
     */
    public function getCodesMesure(): array
    {
        return Mesure::actifs()
            ->get()
            ->map(function ($mesure) {
                return [
                    'code' => $mesure->code,
                    'libelle' => $mesure->libelle,
                    'type' => $mesure->type,
                    'statut' => $mesure->statut_correspondant,
                    'bloque_pec' => $mesure->bloque_pec,
                ];
            })
            ->keyBy('code')
            ->toArray();
    }

    /**
     * Analyser un agent et retourner les informations de carrière/mesure
     */
    public function analyserAgent(Agent $agent): array
    {
        $categorieCalculee = Carriere::getCategorieFromNiveau($agent->niveau);
        $statutCalcule = $this->calculerStatutDepuisSituation($agent);

        return [
            'agent' => [
                'matricule' => $agent->matricule,
                'nom_complet' => $agent->nom_complet,
                'niveau' => $agent->niveau,
                'population' => $agent->population,
                'situation_administrative' => $agent->situation_administrative,
            ],
            'carriere' => [
                'niveau' => $agent->niveau,
                'categorie_enregistree' => $agent->categorie,
                'categorie_calculee' => $categorieCalculee,
                'cohérente' => $agent->categorie === $categorieCalculee,
                'plafond' => ParametrePlafond::getPlafond($categorieCalculee),
            ],
            'mesure' => [
                'situation' => $agent->situation_administrative,
                'statut_enregistre' => $agent->statut,
                'statut_calcule' => $statutCalcule,
                'cohérent' => $agent->statut === $statutCalcule,
                'bloque_pec' => Mesure::bloquesPEC($agent->situation_administrative ?? ''),
            ],
        ];
    }
}
