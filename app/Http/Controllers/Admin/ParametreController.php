<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carriere;
use App\Models\Mesure;
use App\Models\ParametrePlafond;
use App\Services\CarriereMesureService;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    protected $carriereMesureService;

    public function __construct(CarriereMesureService $carriereMesureService)
    {
        $this->carriereMesureService = $carriereMesureService;
        // Middleware géré directement dans les routes (web.php)
    }

    /**
     * Page d'index des paramètres
     */
    public function index()
    {
        $reglesCarriere = $this->carriereMesureService->getReglesCarriere();
        $codesMesure = $this->carriereMesureService->getCodesMesure();
        $parametresPlafond = ParametrePlafond::plusRecents()->take(5)->get();

        return view('admin.parametres.index', compact('reglesCarriere', 'codesMesure', 'parametresPlafond'));
    }

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PLAFONDS
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des paramètres de plafond par année
     */
    public function plafonds(Request $request)
    {
        $annee = $request->get('annee', now()->year);
        $parametres = ParametrePlafond::orderBy('annee', 'desc')->paginate(10);

        return view('admin.parametres.plafonds', compact('parametres', 'annee'));
    }

    /**
     * Formulaire de création/modification des plafonds
     */
    public function plafondEdit(?int $annee = null)
    {
        $parametre = $annee ? ParametrePlafond::pourAnnee($annee) : new ParametrePlafond(['annee' => now()->year + 1]);

        return view('admin.parametres.plafond-edit', compact('parametre'));
    }

    /**
     * Enregistrer les plafonds
     */
    public function plafondStore(Request $request)
    {
        $validated = $request->validate([
            'annee' => 'required|integer|unique:parametres_plafond,annee,' . ($request->id ?? 'NULL') . ',id',
            'plafond_execution' => 'required|numeric|min:0',
            'plafond_maitrise' => 'required|numeric|min:0',
            'plafond_cadre' => 'required|numeric|min:0',
            'plafond_hors_cadre' => 'required|numeric|min:0',
            'plafond_bo' => 'nullable|numeric|min:0',
            'actif' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        ParametrePlafond::updateOrCreate(
            ['annee' => $validated['annee']],
            $validated
        );

        return redirect()
            ->route('admin.parametres.plafonds')
            ->with('success', 'Paramètres de plafond enregistrés avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | GESTION DES CARRIÈRES
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des niveaux de carrière
     */
    public function carrieres(Request $request)
    {
        $categorie = $request->get('categorie');

        $carrieres = Carriere::query()
            ->when($categorie, function ($query, $categorie) {
                return $query->where('categorie', $categorie);
            })
            ->orderBy('ordre')
            ->paginate(50);

        return view('admin.parametres.carrieres', compact('carrieres'));
    }

    /**
     * Formulaire de création d'un niveau de carrière
     */
    public function carriereCreate()
    {
        return view('admin.parametres.carriere-create');
    }

    /**
     * Enregistrer un niveau de carrière
     */
    public function carriereStore(Request $request)
    {
        $validated = $request->validate([
            'code_niveau' => 'required|string|max:10|unique:carrieres,code_niveau',
            'libelle_niveau' => 'required|string|max:100',
            'categorie' => 'required|in:Exécution,Maîtrise,Cadre,Hors cadre',
            'prefixe_niveau' => 'required|string|max:5',
            'ordre' => 'nullable|integer|min:0',
        ]);

        Carriere::create($validated);

        return redirect()
            ->route('admin.parametres.carrieres')
            ->with('success', 'Niveau de carrière créé avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | GESTION DES MESURES
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des codes de mesure
     */
    public function mesures()
    {
        $mesures = Mesure::orderBy('type')->orderBy('code')->paginate(50);

        return view('admin.parametres.mesures', compact('mesures'));
    }

    /**
     * Formulaire de création d'un code de mesure
     */
    public function mesureCreate()
    {
        return view('admin.parametres.mesure-create');
    }

    /**
     * Enregistrer un code de mesure
     */
    public function mesureStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:mesures,code',
            'libelle' => 'required|string|max:100',
            'type' => 'required|in:sortie,suspension,retraite,actif',
            'statut_correspondant' => 'nullable|string|max:50',
            'bloque_pec' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['bloque_pec'] = $request->has('bloque_pec');

        Mesure::create($validated);

        return redirect()
            ->route('admin.parametres.mesures')
            ->with('success', 'Code de mesure créé avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION
    |--------------------------------------------------------------------------
    */

    /**
     * Synchroniser tous les agents (recalculer catégorie et statut)
     */
    public function synchroniserAgents()
    {
        $resultats = $this->carriereMesureService->synchroniserTous();

        return redirect()
            ->back()
            ->with('success', "Synchronisation terminée. {$resultats['traites']} agents traités. " .
                "{$resultats['categorie_changee']} catégorie(s) changée(s), " .
                "{$resultats['statut_change']} statut(s) changé(s), " .
                "{$resultats['ecarts_detectes']} écart(s) détecté(s).");
    }

    /**
     * Analyser un agent spécifique
     */
    public function analyserAgent(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string',
        ]);

        $agent = \App\Models\Agent::where('matricule', $validated['matricule'])->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent non trouvé',
            ], 404);
        }

        $analyse = $this->carriereMesureService->analyserAgent($agent);

        return response()->json([
            'success' => true,
            'analyse' => $analyse,
        ]);
    }

    /**
     * Appliquer les calculs à un agent
     */
    public function appliquerCalculs(Request $request)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
        ]);

        $agent = \App\Models\Agent::find($validated['agent_id']);

        $this->carriereMesureService->appliquerCalculs($agent);

        return response()->json([
            'success' => true,
            'message' => 'Calculs appliqués avec succès',
            'agent' => [
                'categorie' => $agent->categorie,
                'statut' => $agent->statut,
                'plafond' => $agent->plafond_annuel,
            ],
        ]);
    }
}
