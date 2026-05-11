<?php

namespace App\Http\Controllers\DpRh;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandePECRequest;
use App\Models\Agent;
use App\Models\DemandePEC;
use App\Models\AyantDroit;
use App\Models\Partenaire;
use App\Services\PlafondService;
use App\Services\BonPecPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DemandeController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    /**
     * Liste des demandes PEC
     */
    public function index(Request $request)
    {
        $statut = $request->get('statut');
        $agent = $request->get('agent_id');
        $partenaire = $request->get('partenaire_id');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $demandes = DemandePEC::query()
            ->with(['agent', 'partenaire', 'beneficiaire'])
            ->when($statut, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->when($agent, function ($query, $agent) {
                return $query->where('agent_id', $agent);
            })
            ->when($partenaire, function ($query, $partenaire) {
                return $query->where('partenaire_id', $partenaire);
            })
            ->when($dateDebut, function ($query, $dateDebut) {
                return $query->whereDate('date_soin', '>=', $dateDebut);
            })
            ->when($dateFin, function ($query, $dateFin) {
                return $query->whereDate('date_soin', '<=', $dateFin);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->appends($request->all());

        // Statistiques pour le résumé
        $statistiques = [
            'en_attente' => DemandePEC::enAttente()->count(),
            'validees' => DemandePEC::validees()->count(),
            'rejetees' => DemandePEC::where('statut', 'Rejetée')->count(),
            'payees' => DemandePEC::where('statut', 'Payée')->count(),
        ];

        return view('dprh.demandes.index', compact('demandes', 'statistiques'));
    }

    /**
     * Formulaire de création d'une demande
     */
    public function create()
    {
        // Villes disponibles
        $villes = ['Fès', 'Meknès'];

        return view('dprh.demandes.create', compact('villes'));
    }

    /**
     * Enregistrer une nouvelle demande PEC
     */
    public function store(DemandePECRequest $request)
    {
        $plafondService = new PlafondService();

        // Vérifier le plafond disponible
        $disponible = $plafondService->verifierPlafondDisponible(
            $request->agent_id,
            $request->montant_devis
        );

        if (!$disponible) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Le plafond annuel de l\'agent est insuffisant pour cette demande.');
        }

        DB::beginTransaction();
        try {
            $demande = DemandePEC::create($request->validated() + [
                'numero_demande' => $this->genererNumeroDemande(),
                'statut' => 'En attente',
                'cree_par' => Auth::id(),
            ]);

            // Mettre à jour le plafond (montant engagé)
            $plafondService->mettreAJourPlafond(
                $request->agent_id,
                $request->montant_devis,
                'engagement'
            );

            DB::commit();

            return redirect()
                ->route('dprh.demandes.show', $demande)
                ->with('success', 'Demande PEC créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une demande
     */
    public function show(string $id)
    {
        $demande = DemandePEC::with([
            'agent',
            'agent.ayantsDroit',
            'partenaire',
            'beneficiaire',
            'createur',
            'validateur',
            'documents',
        ])->findOrFail($id);

        // Vérifier le plafond disponible
        $plafondService = new PlafondService();
        $plafond = $plafondService->getPlafondAgent($demande->agent_id);

        return view('dprh.demandes.show', compact('demande', 'plafond'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(string $id)
    {
        $demande = DemandePEC::findOrFail($id);

        if (!$demande->peutEtreModifiee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        // Villes disponibles
        $villes = ['Fès', 'Meknès'];

        return view('dprh.demandes.edit', compact('demande', 'villes'));
    }

    /**
     * Mettre à jour une demande
     */
    public function update(DemandePECRequest $request, string $id)
    {
        $demande = DemandePEC::findOrFail($id);

        if (!$demande->peutEtreModifiee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $plafondService = new PlafondService();
        $ancienMontant = $demande->montant_devis;
        $nouveauMontant = $request->montant_devis;

        DB::beginTransaction();
        try {
            // Si le montant change, ajuster le plafond
            if ($ancienMontant != $nouveauMontant) {
                // D'abord annuler l'ancien engagement
                $plafondService->annulerEngagement($demande->agent_id, $ancienMontant);

                // Vérifier le nouveau plafond
                if (!$plafondService->verifierPlafondDisponible($demande->agent_id, $nouveauMontant)) {
                    throw new \Exception('Le plafond annuel est insuffisant pour le nouveau montant.');
                }
            }

            $demande->update($request->validated());

            // Créer le nouvel engagement
            if ($ancienMontant != $nouveauMontant) {
                $plafondService->mettreAJourPlafond($demande->agent_id, $nouveauMontant, 'engagement');
            }

            DB::commit();

            return redirect()
                ->route('dprh.demandes.show', $demande)
                ->with('success', 'Demande mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une demande
     */
    public function destroy(string $id)
    {
        $demande = DemandePEC::findOrFail($id);

        if (!$demande->peutEtreModifiee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette demande ne peut plus être supprimée.');
        }

        DB::beginTransaction();
        try {
            $montant = $demande->montant_devis;
            $agentId = $demande->agent_id;

            $demande->delete();

            // Annuler l'engagement sur le plafond
            $plafondService = new PlafondService();
            $plafondService->annulerEngagement($agentId, $montant);

            DB::commit();

            return redirect()
                ->route('dprh.demandes.index')
                ->with('success', 'Demande supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Valider une demande PEC
     */
    public function valider(Request $request, string $id)
    {
        $request->validate([
            'commentaire_validation' => 'nullable|string',
        ]);

        $demande = DemandePEC::findOrFail($id);

        if (!$demande->peutEtreValidee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette demande ne peut pas être validée dans son état actuel.');
        }

        $plafondService = new PlafondService();

        if (!$plafondService->verifierPlafondDisponible($demande->agent_id, $demande->montant_devis)) {
            return redirect()
                ->back()
                ->with('error', 'Le plafond annuel de l\'agent est insuffisant.');
        }

        DB::beginTransaction();
        try {
            $demande->update([
                'statut' => 'Validée',
                'validee_par' => Auth::id(),
                'date_validation' => now(),
                'commentaire_validation' => $request->commentaire_validation,
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Demande validée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Rejeter une demande PEC
     */
    public function rejeter(Request $request, string $id)
    {
        $request->validate([
            'motif_rejet' => 'required|string',
        ]);

        $demande = DemandePEC::findOrFail($id);

        if (!$demande->peutEtreRejetee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette demande ne peut pas être rejetée dans son état actuel.');
        }

        DB::beginTransaction();
        try {
            $demande->update([
                'statut' => 'Rejetée',
                'validee_par' => Auth::id(),
                'date_validation' => now(),
                'motif_rejet' => $request->motif_rejet,
            ]);

            // Annuler l'engagement sur le plafond
            $plafondService = new PlafondService();
            $plafondService->annulerEngagement($demande->agent_id, $demande->montant_devis);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Demande rejetée.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Marquer comme payée
     */
    public function marquerPayee(Request $request, string $id)
    {
        $request->validate([
            'montant_regle' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'reference_paiement' => 'nullable|string',
        ]);

        $demande = DemandePEC::findOrFail($id);

        if ($demande->statut !== 'Validée') {
            return redirect()
                ->back()
                ->with('error', 'Seules les demandes validées peuvent être marquées comme payées.');
        }

        DB::beginTransaction();
        try {
            $demande->update([
                'statut' => 'Payée',
                'montant_regle' => $request->montant_regle,
                'date_paiement' => $request->date_paiement,
                'reference_paiement' => $request->reference_paiement,
            ]);

            // Mettre à jour le plafond (montant consommé)
            $plafondService = new PlafondService();
            $plafondService->convertirEngagementEnConsommation($demande->agent_id, $demande->montant_devis);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Paiement enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Imprimer le bon PEC
     */
    public function imprimerBon(string $id)
    {
        $demande = DemandePEC::with([
            'agent',
            'beneficiaire',
            'partenaire',
        ])->findOrFail($id);

        if ($demande->statut !== 'Validée' && $demande->statut !== 'Payée') {
            return redirect()
                ->back()
                ->with('error', 'Le bon PEC ne peut être imprimé que pour les demandes validées.');
        }

        $pdfService = new BonPecPdfService();
        $pdf = $pdfService->generer($demande);

        return $pdf->download('bon_pec_' . $demande->numero_demande . '.pdf');
    }

    /**
     * Voir le bon PEC en HTML (pour test/aperçu)
     */
    public function voirBon(string $id)
    {
        $demande = DemandePEC::with([
            'agent',
            'beneficiaire',
            'partenaire',
        ])->findOrFail($id);

        $beneficiaire = $demande->beneficiaire ?? $demande->agent;

        $data = [
            'numero_demande' => $demande->numero_demande,
            'date_generation' => now()->format('d/m/Y'),
            'date_soin' => $demande->date_soin->format('d/m/Y'),
            'date_validation' => $demande->date_validation?->format('d/m/Y'),
            'type_soin' => $demande->type_prestation ?? $demande->type_soin ?? 'Soins médicaux',
            'description' => $demande->description ?? $demande->diagnostic ?? null,
            'montant_devis' => number_format($demande->montant_devis, 2, ',', ' '),
            'montant_regle' => $demande->montant_regle ? number_format($demande->montant_regle, 2, ',', ' ') : null,
            'agent' => $demande->agent,
            'beneficiaire_type' => $demande->beneficiaire_type === 'ayant_droit' ? 'Ayant droit' : 'Agent',
            'beneficiaire_nom' => $beneficiaire->nom . ' ' . $beneficiaire->prenom,
            'beneficiaire_date_naissance' => $beneficiaire->date_naissance?->format('d/m/Y') ?? '-',
            'lien_parente' => $demande->beneficiaire_type === 'ayant_droit'
                ? ($beneficiaire->type ?? 'Ayant droit')
                : 'Titulaire',
            'partenaire' => $demande->partenaire,
        ];

        return view('pdf.bon-pec', $data);
    }

    /**
     * Obtenir les ayants droit d'un agent (API)
     */
    public function apiAyantsDroit(Request $request)
    {
        try {
            $request->validate([
                'agent_id' => 'required|exists:agents,id',
            ]);

            $ayantsDroit = AyantDroit::where('agent_id', $request->agent_id)
                ->actifs()
                ->get()
                ->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'nom_complet' => $ad->nom_complet,
                        'lien_parente' => $ad->lien_parente,
                        'date_naissance' => $ad->date_naissance,
                    ];
                });

            return response()->json($ayantsDroit);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Obtenir les partenaires par ville et type (API)
     */
    public function apiPartenaires(Request $request)
    {
        $request->validate([
            'ville' => 'required|string',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
        ]);

        $partenaires = Partenaire::actifs()
            ->parVille($request->ville)
            ->parType($request->type_structure)
            ->when($request->specialite && $request->type_structure === 'médecin', function ($q) use ($request) {
                return $q->where('specialite', $request->specialite);
            })
            ->orderBy('nom')
            ->get(['id', 'nom', 'numero_convention', 'specialite']);

        return response()->json($partenaires);
    }

    /**
     * Vérifier le plafond disponible (API)
     */
    public function apiVerifierPlafond(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'montant' => 'required|numeric|min:0',
        ]);

        $plafondService = new PlafondService();
        $disponible = $plafondService->verifierPlafondDisponible(
            $request->agent_id,
            $request->montant
        );

        $plafond = $plafondService->getPlafondAgent($request->agent_id);

        return response()->json([
            'disponible' => $disponible,
            'plafond_annuel' => $plafond ? $plafond->plafond_annuel : 0,
            'reste_disponible' => $plafond ? $plafond->reste_disponible : 0,
            'montant_engage' => $plafond ? $plafond->montant_engage : 0,
            'montant_consome' => $plafond ? $plafond->montant_consome : 0,
        ]);
    }

    /**
     * Générer un numéro de demande unique
     */
    protected function genererNumeroDemande(): string
    {
        $prefix = 'PEC-' . now()->format('Ymd');
        $dernierNumero = DemandePEC::where('numero_demande', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('numero_demande');

        if ($dernierNumero) {
            $suffix = (int) substr($dernierNumero, -4);
            $suffix++;
        } else {
            $suffix = 1;
        }

        return $prefix . '-' . str_pad($suffix, 4, '0', STR_PAD_LEFT);
    }
}
