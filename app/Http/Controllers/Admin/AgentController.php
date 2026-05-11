<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Models\Agent;
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
     * Afficher la liste des agents
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $categorie = $request->get('categorie');

        $agents = Agent::query()
            ->when($search, function ($query, $search) {
                return $query->rechercher($search);
            })
            ->when($statut, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->when($categorie, function ($query, $categorie) {
                return $query->where('categorie', $categorie);
            })
            ->withCount('ayantsDroit')
            ->orderBy('nom')
            ->paginate(25)
            ->appends($request->all());

        $statistiques = [
            'total' => Agent::count(),
            'actifs' => Agent::where('statut', 'Actif')->count(),
            'retraites' => Agent::where('statut', 'Retraité')->count(),
        ];

        return view('admin.agents.index', compact('agents', 'statistiques'));
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
     * Afficher les détails d'un agent
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

        return view('admin.agents.show', compact('agent', 'plafondAnneeCourante'));
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
                return [
                    'id' => $agent->id,
                    'matricule' => $agent->matricule,
                    'nom_complet' => $agent->nom_complet,
                    'categorie' => $agent->categorie,
                    'dp_affectation' => $agent->dp_affectation,
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
                'categorie' => $agent->categorie,
                'dp_affectation' => $agent->dp_affectation,
                'statut' => $agent->statut,
                'peut_beneficier_pec' => $agent->peutBeneficierPEC(),
            ],
        ]);
    }
}
