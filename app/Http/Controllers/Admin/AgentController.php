<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\AgentSap;
use App\Models\AyantDroit;
use App\Models\PlafondAnnuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    /**
     * Comparer le nom d'un agent CGS avec un agent SAP
     * Gère les deux formats: nom_prenom combiné ou nom+prenom séparés
     */
    protected function comparerNoms(Agent $agentCgs, AgentSap $agentSap): bool
    {
        $nomCgs = trim(($agentCgs->nom ?? '') . ' ' . ($agentCgs->prenom ?? ''));
        $nomSap = $agentSap->nom_prenom ?? trim(($agentSap->nom ?? '') . ' ' . ($agentSap->prenom ?? ''));

        return $nomCgs === $nomSap;
    }

    /**
     * Obtenir le nom complet depuis un agent SAP
     */
    protected function getNomCompletSap(AgentSap $agentSap): string
    {
        return $agentSap->nom_prenom ?? trim(($agentSap->nom ?? '') . ' ' . ($agentSap->prenom ?? ''));
    }

    /**
     * Afficher la liste des agents avec comparaison SAP/CGS
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $comparaison = $request->get('comparaison'); // filtre: tous, correspond, different, absent_sap

        // Charger les données SAP pour comparaison
        $agentsSap = AgentSap::all()->keyBy('matricule');

        // Calculer les agents SAP absents dans CGS (pour statistiques)
        $matriculesCgs = Agent::pluck('matricule')->toArray();
        $absentCgsCount = $agentsSap->reject(function ($agentSap) use ($matriculesCgs) {
            return in_array($agentSap->matricule, $matriculesCgs);
        })->count();

        $query = Agent::query()
            ->when($search, function ($query, $search) {
                return $query->rechercher($search);
            })
            ->when($statut, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->withCount('ayantsDroit')
            ->orderBy('nom');

        // Pagination
        $perPage = 25;
        $page = $request->get('page', 1);

        // Filtrer par comparaison SAP si demandé
        if ($comparaison && $comparaison !== 'tous') {
            if ($comparaison === 'absent_sap') {
                // Agents qui n'existent pas dans SAP
                $matriculesSap = $agentsSap->keys()->toArray();
                if (!empty($matriculesSap)) {
                    $query->whereNotIn('matricule', $matriculesSap);
                }
                $agents = $query->paginate($perPage, ['*'], 'page', $page);
            } elseif ($comparaison === 'absent_cgs') {
                // Agents SAP qui n'existent pas dans CGS
                $matriculesCgs = Agent::pluck('matricule')->toArray();
                $agentsSapAbsents = $agentsSap->reject(function ($agentSap) use ($matriculesCgs) {
                    return in_array($agentSap->matricule, $matriculesCgs);
                })->values();

                // Paginer
                $currentPageItems = $agentsSapAbsents->slice(($page - 1) * $perPage, $perPage)->values();
                $agents = new \Illuminate\Pagination\LengthAwarePaginator(
                    $currentPageItems,
                    $agentsSapAbsents->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                // Marquer comme venant de SAP
                $agents->setCollection($agents->getCollection()->map(function ($agentSap) {
                    $agentSap->is_sap_only = true;
                    $agentSap->statut_comparaison = 'absent_cgs';
                    return $agentSap;
                }));

                // Statistiques pour ce cas
                $statistiques = [
                    'total' => Agent::count(),
                    'actifs' => Agent::where('statut', 'Actif')->count(),
                    'retraites' => Agent::where('statut', 'Retraité')->count(),
                    'total_sap' => $agentsSap->count(),
                    'correspondent' => 0,
                    'different' => 0,
                    'absent_sap' => 0,
                    'absent_cgs' => $absentCgsCount,
                ];

                return view('admin.agents.index', compact('agents', 'agentsSap', 'statistiques'));
            } elseif (in_array($comparaison, ['correspond', 'different'])) {
                // Pour 'correspond' et 'different', on doit charger tous les agents avec équivalent SAP
                // puis filtrer en mémoire et paginer le résultat
                $matriculesSap = $agentsSap->keys()->toArray();

                // Construire une nouvelle requête pour tous les agents (sans pagination)
                $allAgentsQuery = Agent::query()
                    ->whereIn('matricule', $matriculesSap)
                    ->when($search, function ($q) use ($search) {
                        return $q->rechercher($search);
                    })
                    ->when($statut, function ($q) use ($statut) {
                        return $q->where('statut', $statut);
                    })
                    ->withCount('ayantsDroit')
                    ->orderBy('nom')
                    ->get();

                // Filtrer en mémoire
                $filtered = $allAgentsQuery->filter(function ($agent) use ($agentsSap, $comparaison) {
                    $agentSap = $agentsSap->get($agent->matricule);

                    if (!$agentSap) {
                        return false;
                    }

                    $correspond = (
                        $this->comparerNoms($agent, $agentSap) &&
                        $agent->statut === $agentSap->statut &&
                        $agent->date_naissance == $agentSap->date_naissance
                    );

                    if ($comparaison === 'correspond') {
                        return $correspond;
                    } else { // different
                        return !$correspond;
                    }
                })->values();

                // Paginer manuellement
                $currentPageItems = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
                $agents = new \Illuminate\Pagination\LengthAwarePaginator(
                    $currentPageItems,
                    $filtered->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                // Annoter les agents avec leurs infos SAP
                $agents->getCollection()->transform(function ($agent) use ($agentsSap) {
                    $agentSap = $agentsSap->get($agent->matricule);

                    if ($agentSap) {
                        $agent->donnees_sap = $agentSap;
                        $agent->correspond_sap = (
                            $this->comparerNoms($agent, $agentSap) &&
                            $agent->statut === $agentSap->statut &&
                            $agent->date_naissance == $agentSap->date_naissance
                        );
                        $agent->statut_comparaison = $agent->correspond_sap ? 'correspond' : 'different';
                    } else {
                        $agent->donnees_sap = null;
                        $agent->correspond_sap = false;
                        $agent->statut_comparaison = 'absent_sap';
                    }

                    return $agent;
                });

                // Statistiques
                $statistiques = [
                    'total' => Agent::count(),
                    'actifs' => Agent::where('statut', 'Actif')->count(),
                    'retraites' => Agent::where('statut', 'Retraité')->count(),
                    'total_sap' => $agentsSap->count(),
                ];

                // Calculer les stats pour le filtre actuel
                $statistiques['correspondent'] = 0;
                $statistiques['different'] = 0;
                $statistiques['absent_sap'] = 0;
                $statistiques['absent_cgs'] = $absentCgsCount;

                foreach (Agent::all() as $agent) {
                    $agentSap = $agentsSap->get($agent->matricule);

                    if (!$agentSap) {
                        $statistiques['absent_sap']++;
                    } elseif (
                        $this->comparerNoms($agent, $agentSap) &&
                        $agent->statut === $agentSap->statut &&
                        $agent->date_naissance == $agentSap->date_naissance
                    ) {
                        $statistiques['correspondent']++;
                    } else {
                        $statistiques['different']++;
                    }
                }

                return view('admin.agents.index', compact('agents', 'statistiques'));
            }
        } else {
            $agents = $query->paginate($perPage, ['*'], 'page', $page);
        }

        // Annoter tous les agents avec leurs infos SAP
        $agents->getCollection()->transform(function ($agent) use ($agentsSap) {
            $agentSap = $agentsSap->get($agent->matricule);

            if ($agentSap) {
                $agent->donnees_sap = $agentSap;
                $agent->correspond_sap = (
                    $this->comparerNoms($agent, $agentSap) &&
                    $agent->statut === $agentSap->statut &&
                    $agent->date_naissance == $agentSap->date_naissance
                );
                $agent->statut_comparaison = $agent->correspond_sap ? 'correspond' : 'different';
            } else {
                $agent->donnees_sap = null;
                $agent->correspond_sap = false;
                $agent->statut_comparaison = 'absent_sap';
            }

            return $agent;
        });

        // Statistiques globales
        $statistiques = [
            'total' => Agent::count(),
            'actifs' => Agent::where('statut', 'Actif')->count(),
            'retraites' => Agent::where('statut', 'Retraité')->count(),
            'total_sap' => $agentsSap->count(),
        ];

        // Calculer les stats de comparaison pour tous les agents
        $tousAgents = Agent::all();
        $statistiques['correspondent'] = 0;
        $statistiques['different'] = 0;
        $statistiques['absent_sap'] = 0;
        $statistiques['absent_cgs'] = $absentCgsCount;

        foreach ($tousAgents as $agent) {
            $agentSap = $agentsSap->get($agent->matricule);

            if (!$agentSap) {
                $statistiques['absent_sap']++;
            } elseif (
                $this->comparerNoms($agent, $agentSap) &&
                $agent->statut === $agentSap->statut &&
                $agent->date_naissance == $agentSap->date_naissance
            ) {
                $statistiques['correspondent']++;
            } else {
                $statistiques['different']++;
            }
        }

        return view('admin.agents.index', compact('agents', 'statistiques'));
    }

    /**
     * Obtenir les données de comparaison SAP/CGS pour un agent (API)
     */
    public function getComparaisonSap($id)
    {
        $agent = Agent::findOrFail($id);
        $agentSap = AgentSap::where('matricule', $agent->matricule)->first();

        $data = [
            'agent' => [
                'id' => $agent->id,
                'matricule' => $agent->matricule,
                'nom' => $agent->nom,
                'prenom' => $agent->prenom,
                'statut' => $agent->statut,
                'date_naissance' => $agent->date_naissance ? $agent->date_naissance->format('d/m/Y') : null,
            ],
        ];

        if ($agentSap) {
            $nomCompletSap = $this->getNomCompletSap($agentSap);
            $nomCompletCgs = $agent->nom_complet;

            $data['sap'] = [
                'nom' => $nomCompletSap,
                'statut' => $agentSap->statut,
                'date_naissance' => $agentSap->date_naissance ? $agentSap->date_naissance->format('d/m/Y') : null,
                'date_import_sap' => $agentSap->date_import_sap ? $agentSap->date_import_sap->format('d/m/Y H:i') : null,
                'fichier_import' => $agentSap->fichier_import,
            ];

            // Calculer les différences
            $data['differences'] = [];
            if ($nomCompletCgs !== $nomCompletSap) {
                $data['differences']['nom'] = ['cgs' => $nomCompletCgs, 'sap' => $nomCompletSap];
            }
            if ($agent->statut !== $agentSap->statut) {
                $data['differences']['statut'] = ['cgs' => $agent->statut, 'sap' => $agentSap->statut];
            }
            if ($agent->date_naissance != $agentSap->date_naissance) {
                $data['differences']['date_naissance'] = [
                    'cgs' => $agent->date_naissance ? $agent->date_naissance->format('d/m/Y') : 'N/A',
                    'sap' => $agentSap->date_naissance ? $agentSap->date_naissance->format('d/m/Y') : 'N/A'
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.agents.create');
    }

    /**
     * Enregistrer un nouvel agent
     */
    public function store(AgentRequest $request)
    {
        $agent = Agent::create($request->validated());

        // Créer le plafond annuel pour l'année courante
        PlafondAnnuel::create([
            'agent_id' => $agent->id,
            'annee' => now()->year,
            'plafond_annuel' => $agent->plafond_annuel,
            'reste_disponible' => $agent->plafond_annuel,
        ]);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Agent créé avec succès.');
    }

    /**
     * Afficher les détails d'un agent avec comparaison SAP
     */
    public function show(string $id)
    {
        $agent = Agent::with([
            'ayantsDroit',
            'demandesPEC' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(10);
            },
            'plafondsAnnuels' => function ($q) {
                $q->orderBy('annee', 'desc');
            },
            'carrieres' => function ($q) {
                $q->orderBy('date_debut', 'desc');
            },
            'mesures' => function ($q) {
                $q->orderBy('date_debut', 'desc');
            },
        ])->findOrFail($id);

        // Obtenir le plafond de l'année courante
        $plafondAnneeCourante = PlafondAnnuel::pourAgent($agent->id);

        // Obtenir les données SAP correspondantes
        $agentSap = AgentSap::where('matricule', $agent->matricule)->first();

        // Comparer les données
        $differences = null;
        $correspond = true;

        if ($agentSap) {
            $differences = [];
            $nomCompletCgs = $agent->nom_complet;
            $nomCompletSap = $this->getNomCompletSap($agentSap);

            if ($nomCompletCgs !== $nomCompletSap) {
                $differences['nom'] = ['cgs' => $nomCompletCgs, 'sap' => $nomCompletSap];
                $correspond = false;
            }
            if ($agent->statut !== $agentSap->statut) {
                $differences['statut'] = ['cgs' => $agent->statut, 'sap' => $agentSap->statut];
                $correspond = false;
            }
            if ($agent->date_naissance != $agentSap->date_naissance) {
                $differences['date_naissance'] = ['cgs' => $agent->date_naissance, 'sap' => $agentSap->date_naissance];
                $correspond = false;
            }

            if (empty($differences)) {
                $differences = null;
            }
        }

        return view('admin.agents.show', compact('agent', 'plafondAnneeCourante', 'agentSap', 'differences', 'correspond'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(string $id)
    {
        $agent = Agent::findOrFail($id);
        return view('admin.agents.edit', compact('agent'));
    }

    /**
     * Mettre à jour un agent
     */
    public function update(AgentRequest $request, string $id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update($request->validated());

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Agent mis à jour avec succès.');
    }

    /**
     * Supprimer un agent
     */
    public function destroy(string $id)
    {
        $agent = Agent::findOrFail($id);
        $matricule = $agent->matricule;
        $nom = $agent->nom_complet;

        $agent->delete();

        return redirect()
            ->route('admin.agents.index')
            ->with('success', "L'agent {$matricule} ({$nom}) a été supprimé.");
    }

    /**
     * API autocomplète pour la recherche d'agents
     */
    public function autocomplete(Request $request)
    {
        $search = $request->get('search', $request->get('q'));

        if (empty($search)) {
            return response()->json([]);
        }

        $agents = Agent::rechercher($search)
            ->actifs()
            ->limit(20)
            ->get()
            ->map(function ($agent) {
                $plafond = PlafondAnnuel::pourAgent($agent->id);

                return [
                    'id' => $agent->id,
                    'matricule' => $agent->matricule,
                    'nom_complet' => $agent->nom_complet,
                    'dp_affectation' => $agent->dp_affectation,
                    'categorie' => $agent->getCategorieFromCarriere(),
                    'plafond_restant' => $plafond ? number_format($plafond->reste_disponible, 2, ',', ' ') . ' DH' : '-',
                ];
            });

        return response()->json($agents);
    }

    /**
     * Obtenir les informations d'un agent par matricule (API)
     */
    public function parMatricule(Request $request)
    {
        $request->validate(['matricule' => 'required']);

        $agent = Agent::where('matricule', $request->matricule)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'agent' => [
                'id' => $agent->id,
                'matricule' => $agent->matricule,
                'nom' => $agent->nom,
                'prenom' => $agent->prenom,
                'nom_complet' => $agent->nom_complet,
                'cin' => $agent->cin,
                'dp_affectation' => $agent->dp_affectation,
                'statut' => $agent->statut,
                'peut_beneficier_pec' => $agent->peutBeneficierPEC(),
            ],
        ]);
    }
}
