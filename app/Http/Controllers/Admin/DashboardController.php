<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandePEC;
use App\Models\EcartSapCgs;
use App\Models\Agent;
use App\Models\PlafondAnnuel;
use App\Services\PlafondService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    public function index(Request $request)
    {
        $periode = $request->get('periode', 'mois'); // mois, trimestre, annee
        $annee = $request->get('annee', now()->year);
        $mois = $request->get('mois', now()->month);

        // Statistiques générales
        $statistiques = [
            'agents' => [
                'total' => Agent::count(),
                'actifs' => Agent::actifs()->count(),
                'retraites' => Agent::where('statut', 'Retraité')->count(),
            ],
            'demandes' => [
                'en_attente' => DemandePEC::enAttente()->count(),
                'validees' => DemandePEC::validees()->count(),
                'rejetees' => DemandePEC::where('statut', 'Rejetée')->count(),
                'expirees' => DemandePEC::expirees()->count(),
            ],
            'montants' => [
                'engage' => PlafondAnnuel::where('annee', $annee)->sum('montant_engage'),
                'consome' => PlafondAnnuel::where('annee', $annee)->sum('montant_consome'),
            ],
            'ecarts' => [
                'non_trites' => EcartSapCgs::nonTraites()->count(),
            ],
        ];

        // Alertes de plafond
        $plafondService = new PlafondService();
        $alertesPlafond = $plafondService->getAlertesPlafond($annee);

        // Demandes récentes
        $demandesRecentes = DemandePEC::with('agent', 'partenaire')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Écarts récents non traités
        $ecartsRecents = EcartSapCgs::nonTraites()
            ->orderBy('date_detection', 'desc')
            ->limit(5)
            ->get();

        // Consommation par catégorie
        $consommationParCategorie = $this->getConsommationParCategorie($annee);

        // Top 5 des partenaires
        $topPartenaires = $this->getTopPartenaires($annee);

        // Demandes par statut (pour graphique)
        $demandesParStatut = DemandePEC::select('statut', DB::raw('count(*) as total'))
            ->whereYear('created_at', $annee)
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return view('admin.dashboard.index', compact(
            'statistiques',
            'alertesPlafond',
            'demandesRecentes',
            'ecartsRecents',
            'consommationParCategorie',
            'topPartenaires',
            'demandesParStatut',
            'annee',
            'mois'
        ));
    }

    /**
     * Obtenir la consommation par catégorie
     */
    protected function getConsommationParCategorie(int $annee): array
    {
        $categories = ['Exécution', 'Maîtrise', 'Cadre', 'Hors cadre'];
        $resultats = [];

        foreach ($categories as $categorie) {
            $agents = Agent::where('categorie', $categorie)->pluck('id');
            $plafonds = PlafondAnnuel::whereIn('agent_id', $agents)
                ->where('annee', $annee)
                ->get();

            $totalPlafond = $plafonds->sum('plafond_annuel');
            $totalConsome = $plafonds->sum('montant_consome');
            $totalEngage = $plafonds->sum('montant_engage');

            $resultats[$categorie] = [
                'plafond_total' => $totalPlafond,
                'consome' => $totalConsome,
                'engage' => $totalEngage,
                'reste' => $totalPlafond - $totalConsome - $totalEngage,
                'pourcentage' => $totalPlafond > 0 ? (($totalConsome + $totalEngage) / $totalPlafond) * 100 : 0,
            ];
        }

        return $resultats;
    }

    /**
     * Obtenir le top 5 des partenaires
     */
    protected function getTopPartenaires(int $annee): array
    {
        return DemandePEC::select('partenaires.nom', DB::raw('COUNT(*) as nb_demandes'), DB::raw('SUM(montant_devis) as montant_total'))
            ->join('partenaires', 'demandes_pec.partenaire_id', '=', 'partenaires.id')
            ->whereYear('demandes_pec.created_at', $annee)
            ->whereIn('demandes_pec.statut', ['Validée', 'Payée', 'Clôturée'])
            ->groupBy('partenaires.id', 'partenaires.nom')
            ->orderBy('montant_total', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * API pour les données du graphique des demandes par mois
     */
    public function apiDemandesParMois(Request $request)
    {
        $annee = $request->get('annee', now()->year);

        $demandes = DemandePEC::select(DB::raw('MONTH(created_at) as mois'), DB::raw('count(*) as total'))
            ->whereYear('created_at', $annee)
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        return response()->json($demandes);
    }

    /**
     * API pour les alertes en temps réel
     */
    public function apiAlertes()
    {
        $plafondService = new PlafondService();
        $alertes = $plafondService->getAlertesPlafond();

        $ecartsCritiques = EcartSapCgs::nonTraites()
            ->whereIn('type_ecart', ['Statut incohérent', 'Âge ≥ 63 ans'])
            ->count();

        return response()->json([
            'plafond' => count($alertes),
            'ecarts' => $ecartsCritiques,
            'total' => count($alertes) + $ecartsCritiques,
        ]);
    }
}
