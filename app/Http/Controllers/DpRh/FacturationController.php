<?php

namespace App\Http\Controllers\DpRh;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\FactureLigne;
use App\Models\DemandePEC;
use App\Models\Partenaire;
use App\Models\PlafondAnnuelAgent;
use App\Services\FacturePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturationController extends Controller
{
    /**
     * Liste des factures
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        $statut = $request->get('statut');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $factures = Facture::query()
            ->with(['demandePec', 'partenaire', 'createur'])
            ->when($type, function ($query, $type) {
                return $query->where('type_facture', $type);
            })
            ->when($statut, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->when($dateDebut, function ($query, $dateDebut) {
                return $query->whereDate('date_facture', '>=', $dateDebut);
            })
            ->when($dateFin, function ($query, $dateFin) {
                return $query->whereDate('date_facture', '<=', $dateFin);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->appends($request->all());

        // Statistiques
        $statistiques = [
            'brouillon' => Facture::brouillon()->count(),
            'generee' => Facture::generee()->count(),
            'envoyee' => Facture::where('statut', 'envoyee')->count(),
            'payee' => Facture::payee()->count(),
            'total' => Facture::count(),
        ];

        return view('dprh.facturation.index', compact('factures', 'statistiques'));
    }

    /**
     * Formulaire de création de facture
     */
    public function create($demandePecId = null)
    {
        $partenaires = Partenaire::orderBy('nom')->get(['id', 'nom', 'type_structure', 'ville']);
        $demandePec = null;

        if ($demandePecId) {
            $demandePec = DemandePEC::with(['agent', 'ayantDroit', 'partenaire'])
                ->findOrFail($demandePecId);
        }

        // Préparer les partenaires pour JavaScript (filtrage par type)
        $partenairesJs = $partenaires->map(function($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'ville' => $p->ville,
                'type_structure' => $p->type_structure
            ];
        })->values();

        return view('dprh.facturation.create', compact('partenaires', 'partenairesJs', 'demandePec'));
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        // === LOG 1: Début du traitement ===
        Log::info('FacturationController@store - Début création facture', [
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'ip' => $request->ip(),
            'expects_json' => $request->expectsJson(),
            'has_csrf_token' => $request->hasSession() && session()->has('_token'),
            'request_data_summary' => [
                'type_facture' => $request->input('type_facture'),
                'partenaire_id' => $request->input('partenaire_id'),
                'demande_pec_id' => $request->input('demande_pec_id'),
                'lignes_count' => count($request->input('lignes', [])),
            ],
        ]);

        // Filtrer les lignes vides (sans prix_unitaire ou avec prix = 0)
        $lignes = collect($request->input('lignes', []))
            ->filter(function($ligne) {
                // Ignorer les lignes sans prix
                if (empty($ligne['prix_unitaire']) || floatval($ligne['prix_unitaire']) <= 0) {
                    return false;
                }

                // Pour les types autre que 'acte', vérifier aussi que designation est rempli
                if (isset($ligne['type_ligne']) && $ligne['type_ligne'] !== 'acte') {
                    // Pour prestation_clinique, honoraire, autre : designation est requis
                    return !empty($ligne['designation']);
                }

                // Pour 'acte', le prix suffit
                return true;
            })
            ->values()
            ->all();

        // Préparer les données pour validation
        $dataToValidate = $request->except('lignes');
        $dataToValidate['lignes'] = $lignes;

        // Validation
        $validator = \Validator::make($dataToValidate, [
            'type_facture' => 'required|in:medical,clinique',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'demande_pec_id' => 'nullable|exists:demandes_pec,id',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date|after:date_facture',
            'nom_patient' => 'required_if:type_facture,clinique',
            'hospitalisation_du' => 'nullable|date',
            'hospitalisation_au' => 'nullable|date|after_or_equal:hospitalisation_du',
            'part_adherent' => 'nullable|numeric|min:0',
            'part_cnops' => 'nullable|numeric|min:0',
            'part_assurance' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string',
            'conditions_reglement' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.type_ligne' => 'required|in:acte,prestation_clinique,honoraire,autre',
            'lignes.*.designation' => 'required_unless:lignes.*.type_ligne,acte',
            'lignes.*.nature_acte' => 'required_if:lignes.*.type_ligne,acte',
            'lignes.*.quantite' => 'required|numeric|min:0',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            // === LOG ERREUR VALIDATION ===
            Log::warning('FacturationController@store - Erreur de validation', [
                'user_id' => Auth::id(),
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->except(['_token', 'lignes']),
                'lignes_count' => count($lignes),
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // === LOG 2: Validation réussie ===
        Log::info('FacturationController@store - Validation réussie', [
            'user_id' => Auth::id(),
            'type_facture' => $request->type_facture,
            'lignes_count' => count($lignes),
            'lignes_summary' => collect($lignes)->map(fn($l) => [
                'type' => $l['type_ligne'] ?? 'unknown',
                'prix' => $l['prix_unitaire'] ?? 0,
            ])->groupBy('type')->map->count(),
        ]);

        // Utiliser les lignes filtrées
        $request->merge(['lignes' => $lignes]);

        DB::beginTransaction();
        try {
            // Récupérer l'agent depuis la demande PEC si disponible
            $agentId = null;
            $anneeFacture = null;

            if ($request->demande_pec_id) {
                $demandePec = DemandePEC::find($request->demande_pec_id);
                if ($demandePec) {
                    $agentId = $demandePec->agent_id;
                    Log::debug('FacturationController@store - PEC trouvée', [
                        'demande_pec_id' => $request->demande_pec_id,
                        'agent_id' => $agentId,
                    ]);
                } else {
                    Log::warning('FacturationController@store - PEC non trouvée', [
                        'demande_pec_id' => $request->demande_pec_id,
                    ]);
                }
            }

            $facture = Facture::create([
                'numero' => Facture::genererNumero(),
                'type_facture' => $request->type_facture,
                'partenaire_id' => $request->partenaire_id,
                'demande_pec_id' => $request->demande_pec_id,
                'date_facture' => $request->date_facture,
                'date_echeance' => $request->date_echeance,
                'nom_patient' => $request->nom_patient,
                'hospitalisation_du' => $request->hospitalisation_du,
                'hospitalisation_au' => $request->hospitalisation_au,
                'part_adherent' => $request->part_adherent,
                'part_cnops' => $request->part_cnops,
                'part_assurance' => $request->part_assurance,
                'observations' => $request->observations,
                'conditions_reglement' => $request->conditions_reglement,
                'statut' => 'generee',
                'created_by' => Auth::id(),
            ]);

            // Créer les lignes de facture
            foreach ($request->lignes as $index => $ligneData) {
                FactureLigne::create([
                    'facture_id' => $facture->id,
                    'type_ligne' => $ligneData['type_ligne'],
                    'matricule' => $ligneData['matricule'] ?? null,
                    'nom_patient' => $ligneData['nom_patient'] ?? null,
                    'beneficiaire' => $ligneData['beneficiaire'] ?? null,
                    'nature_acte' => $ligneData['nature_acte'] ?? null,
                    'cotation' => $ligneData['cotation'] ?? null,
                    'designation' => $ligneData['designation'] ?? null,
                    'categorie' => $ligneData['categorie'] ?? null,
                    'quantite' => $ligneData['quantite'],
                    'prix_unitaire' => $ligneData['prix_unitaire'],
                    'ordre' => $index,
                ]);
            }

            // Calculer les totaux
            $facture->calculerTotal();

            // === LOG 3: Facture créée avec totaux ===
            Log::info('FacturationController@store - Facture créée, totaux calculés', [
                'facture_id' => $facture->id,
                'facture_numero' => $facture->numero,
                'montant_ht' => $facture->montant_ht,
                'montant_ttc' => $facture->montant_ttc,
                'lignes_created' => $facture->lignes()->count(),
            ]);

            // TOUJOURS recalculer la part adhérent: Total - CNOPS - Assurance
            $partAdherent = $facture->montant_ttc - ($request->part_cnops ?? 0) - ($request->part_assurance ?? 0);
            $facture->part_adherent = max(0, $partAdherent);
            $facture->saveQuietly();

            // Mettre à jour le plafond annuel de l'agent (un seul plafond global)
            if ($agentId) {
                $anneeFacture = $facture->date_facture->year;
                $plafondAnnuel = PlafondAnnuelAgent::obtenirOuCreer($agentId, $anneeFacture);

                // Pour formation médicale : cumuler le montant total
                // Pour hospitalisation : cumuler uniquement la part adhérent
                $montantAConsommer = $facture->type_facture === 'medical'
                    ? $facture->montant_ttc
                    : ($facture->part_adherent ?? 0);

                $plafondAnnuel->ajouterConsommation($montantAConsommer);

                // === LOG 4: Plafond mis à jour ===
                Log::info('FacturationController@store - Plafond mis à jour', [
                    'agent_id' => $agentId,
                    'annee_facture' => $anneeFacture,
                    'montant_consome' => $montantAConsommer,
                ]);
            }

            DB::commit();

            // === LOG 5: Transaction commitée ===
            Log::info('FacturationController@store - Transaction commitée avec succès', [
                'facture_id' => $facture->id,
                'facture_numero' => $facture->numero,
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
            ]);

            // Réponse AJAX ou redirect normal
            if ($request->expectsJson()) {
                // === LOG 6: Réponse AJAX ===
                Log::info('FacturationController@store - Réponse AJAX succès', [
                    'facture_id' => $facture->id,
                    'redirect_url' => route('dprh.facturation.show', $facture),
                ]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('dprh.facturation.show', $facture)
                ]);
            }

            // === LOG 6: Redirect normal ===
            Log::info('FacturationController@store - Redirect vers show', [
                'facture_id' => $facture->id,
                'redirect_route' => 'dprh.facturation.show',
            ]);

            return redirect()
                ->route('dprh.facturation.show', $facture)
                ->with('success', 'Facture créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            // === LOG ERREUR: Exception capturée ===
            Log::error('FacturationController@store - Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'class' => get_class($e),
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'request_data_summary' => [
                    'type_facture' => $request->input('type_facture'),
                    'partenaire_id' => $request->input('partenaire_id'),
                ],
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                // === LOG: Réponse AJAX erreur ===
                Log::warning('FacturationController@store - Réponse AJAX erreur', [
                    'error_message' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['general' => $e->getMessage()],
                    'message' => $e->getMessage()
                ], 422);
            }

            // === LOG: Redirect avec erreur ===
            Log::warning('FacturationController@store - Redirect back avec erreur', [
                'error_message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()])
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une facture
     */
    public function show($id)
    {
        $facture = Facture::with([
            'demandePec.agent',
            'partenaire',
            'lignes',
            'createur'
        ])->findOrFail($id);

        return view('dprh.facturation.show', compact('facture'));
    }

    /**
     * Télécharger le PDF de la facture
     */
    public function telechargerPdf($id)
    {
        $facture = Facture::with(['demandePec', 'partenaire', 'lignes'])->findOrFail($id);

        $pdfService = new FacturePdfService();
        $pdf = $pdfService->generer($facture);

        return $pdf->download('facture_' . $facture->numero . '.pdf');
    }

    /**
     * Supprimer une facture
     */
    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);

        if (!$facture->peutEtreModifiee()) {
            return redirect()
                ->back()
                ->with('error', 'Cette facture ne peut plus être supprimée.');
        }

        DB::beginTransaction();
        try {
            $facture->lignes()->delete();
            $facture->delete();

            DB::commit();

            return redirect()
                ->route('dprh.facturation.index')
                ->with('success', 'Facture supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * API: Rechercher des demandes PEC pour facturation
     */
    public function apiPecSearch(Request $request)
    {
        $search = $request->get('q');
        $typeStructure = $request->get('type_structure');
        $exclureType = $request->get('exclure_type'); // 'clinique' pour medical

        $demandes = DemandePEC::query()
            ->with(['agent', 'ayantDroit', 'partenaire'])
            ->whereIn('statut', ['Validée', 'Payée'])
            ->whereDoesntHave('facture') // Pas déjà facturées
            ->when($typeStructure, function ($query, $typeStructure) {
                // Filtrer par type_structure exact
                $query->where('type_structure', $typeStructure);
            })
            ->when($exclureType, function ($query, $exclureType) {
                // Exclure un type (pour medical: exclure clinique)
                $query->where('type_structure', '!=', $exclureType);
            })
            ->when($search, function ($query, $search) {
                // Grouper les conditions de recherche avec des parenthèses
                $query->where(function ($q) use ($search) {
                    $q->where('numero_demande', 'like', "%{$search}%")
                      ->orWhereHas('agent', function ($subQ) use ($search) {
                          $subQ->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%")
                                ->orWhere('matricule', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('date_validation', 'desc')
            ->limit(20)
            ->get();

        return response()->json($demandes->map(function ($demande) {
            // Log pour débogage
            Log::info('API PEC Search - Demande:', [
                'id' => $demande->id,
                'numero' => $demande->numero_demande,
                'beneficiaire_type' => $demande->beneficiaire_type,
                'ayant_droit_id' => $demande->ayant_droit_id,
                'ayantDroit' => $demande->ayantDroit ? $demande->ayantDroit->toArray() : null,
            ]);

            // Déterminer le type de bénéficiaire pour l'affichage
            $beneficiaireType = 'Agent';
            $beneficiaireNom = $demande->agent->nom . ' ' . $demande->agent->prenom;
            $matricule = $demande->agent->matricule;

            // Vérifier si c'est un ayant droit
            if ($demande->beneficiaire_type === 'ayant_droit') {
                Log::info('C est un ayant droit', [
                    'ayantDroit' => $demande->ayantDroit,
                    'ayant_droit_id' => $demande->ayant_droit_id,
                ]);
                if ($demande->ayantDroit) {
                    $beneficiaireType = ucfirst($demande->ayantDroit->type); // 'Conjoint' ou 'Enfant'
                    $beneficiaireNom = $demande->ayantDroit->nom_prenom;
                    Log::info('Bénéficiaire ayant droit:', [
                        'type' => $beneficiaireType,
                        'nom' => $beneficiaireNom,
                    ]);
                }
            }

            return [
                'id' => $demande->id,
                'numero' => $demande->numero_demande,
                'agent' => $demande->agent->nom . ' ' . $demande->agent->prenom,
                'agent_nom' => $demande->agent->nom,
                'agent_prenom' => $demande->agent->prenom,
                'matricule' => $matricule,
                'partenaire' => $demande->partenaire->nom ?? null,
                'partenaire_id' => $demande->partenaire_id ?? null,
                'montant' => number_format($demande->montant_devis, 2, '.', ''),
                'nature_examens' => $demande->nature_examens,
                'date_soin' => $demande->date_soin?->format('d/m/Y'),
                'type' => $demande->type_prestation,
                // Nouveau: type de bénéficiaire et nom du bénéficiaire
                'beneficiaire_type' => $demande->beneficiaire_type,
                'beneficiaire' => $beneficiaireType, // 'Agent', 'Conjoint', ou 'Enfant'
                'beneficiaire_nom' => $beneficiaireNom,
                'ayant_droit_type' => $demande->ayantDroit->type ?? null,
            ];
        }));
    }

    /**
     * API: Obtenir les détails d'une demande PEC pour pré-remplir
     */
    public function apiPecDetails($id)
    {
        $demande = DemandePEC::with(['agent', 'ayantDroit', 'partenaire'])->findOrFail($id);

        return response()->json([
            'demande_pec_id' => $demande->id,
            'partenaire_id' => $demande->partenaire_id,
            'partenaire_nom' => $demande->partenaire->nom ?? null,
            'partenaire_type' => $demande->partenaire->type_structure ?? null,
            'numero_demande' => $demande->numero_demande,
            'type_facture' => $demande->type_prestation === 'chirurgie' ? 'clinique' : 'medical',
            'date_soin' => $demande->date_soin?->format('Y-m-d'),
            'montant_devis' => $demande->montant_devis,
            'nature_examens' => $demande->nature_examens,
            'agent_nom' => $demande->agent->nom,
            'agent_prenom' => $demande->agent->prenom,
            'agent_matricile' => $demande->agent->matricule,
        ]);
    }

    /**
     * API: Vérifier le plafond annuel restant d'un agent
     */
    public function apiVerifierPlafond(Request $request)
    {
        $agentId = $request->input('agent_id');
        $typeFacture = $request->input('type_facture'); // 'medical' ou 'clinique'
        $montantEstime = (float) $request->input('montant_estime', 0);
        $partAdherentEstimee = (float) $request->input('part_adherent_estimee', 0);

        if (!$agentId) {
            return response()->json([
                'success' => false,
                'message' => 'Agent ID requis'
            ], 422);
        }

        $annee = now()->year;
        $plafondAnnuel = PlafondAnnuelAgent::obtenirOuCreer($agentId, $annee);

        // Calculer le montant à consommer pour cette facture
        $montantAConsommer = $typeFacture === 'medical'
            ? $montantEstime
            : $partAdherentEstimee;

        // Calculer ce qui restera après cette facture
        $resteApresFacture = $plafondAnnuel->reste - $montantAConsommer;
        $plafondDepasse = $resteApresFacture < 0;

        return response()->json([
            'success' => true,
            'plafond' => [
                'annee' => $annee,
                'plafond_annuel' => $plafondAnnuel->plafond_annuel,
                'consomme' => $plafondAnnuel->consomme,
                'reste' => $plafondAnnuel->reste,
                'reste_apres_facture' => $resteApresFacture,
                'plafond_depasse' => $plafondDepasse,
                'pourcentage_utilisation' => $plafondAnnuel->pourcentage_utilisation,
                'pourcentage_utilisation_clinique' => $plafondAnnuel->pourcentage_utilisation_clinique,
            ]
        ]);
    }
}
