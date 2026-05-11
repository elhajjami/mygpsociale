<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AgentsSapImport;
use App\Imports\AyantsDroitImport;
use App\Models\EcartSapCgs;
use App\Services\EcartSapService;
use App\Services\PlafondService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    /**
     * Page d'import des agents SAP
     */
    public function agents()
    {
        return view('admin.import.agents');
    }

    /**
     * Traitement de l'import des agents SAP
     */
    public function importAgents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new AgentsSapImport();
            Excel::import($import, $request->file('file'));

            $message = 'Import terminé avec succès ! ';
            $message .= $import->getImportedCount() . ' nouvel(s) agent(s) importé(s). ';

            if ($import->getUpdatedCount() > 0) {
                $message .= $import->getUpdatedCount() . ' agent(s) mis à jour. ';
            }

            return redirect()
                ->route('admin.import.agents')
                ->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Ligne {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()
                ->back()
                ->with('error', 'Erreurs de validation: ' . implode(' | ', $errorMessages))
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erreur import agents: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Page d'import des ayants droit
     */
    public function ayantsDroit()
    {
        return view('admin.import.ayants-droit');
    }

    /**
     * Traitement de l'import des ayants droit
     */
    public function importAyantsDroit(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new AyantsDroitImport();
            Excel::import($import, $request->file('file'));

            $message = 'Import terminé avec succès ! ';
            $message .= $import->getImportedCount() . ' nouvel(s) ayant(s) droit importé(s). ';

            if ($import->getUpdatedCount() > 0) {
                $message .= $import->getUpdatedCount() . ' ayant(s) droit mis à jour. ';
            }

            return redirect()
                ->route('admin.import.ayants-droit')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur import ayants droit: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Page de gestion des écarts SAP/CGS
     */
    public function ecarts(Request $request)
    {
        $typeEcart = $request->get('type_ecart');
        $traite = $request->get('traite');

        $query = EcartSapCgs::query();

        if ($typeEcart) {
            $query->where('type_ecart', $typeEcart);
        }

        if ($traite !== null) {
            $query->where('traite', $traite === '1');
        }

        $ecarts = $query->with('traiteur')
            ->orderBy('date_detection', 'desc')
            ->paginate(50)
            ->appends($request->all());

        // Obtenir les statistiques
        $ecartSapService = new EcartSapService();
        $statistiques = $ecartSapService->getStatistiquesEcarts();

        return view('admin.import.ecarts', compact('ecarts', 'statistiques'));
    }

    /**
     * Marquer un écart comme traité
     */
    public function marquerEcartTraite(Request $request, int $id)
    {
        $ecartSapService = new EcartSapService();

        if ($ecartSapService->marquerTraite($id, auth()->id())) {
            return redirect()
                ->back()
                ->with('success', 'Écart marqué comme traité.');
        }

        return redirect()
            ->back()
            ->with('error', 'Écart non trouvé.');
    }

    /**
     * Marquer plusieurs écarts comme traités
     */
    public function marquerEcartsTraites(Request $request)
    {
        $request->validate([
            'ecarts' => 'required|array',
            'ecarts.*' => 'integer',
        ]);

        $ecartSapService = new EcartSapService();
        $count = $ecartSapService->marquerTraitesMultiple($request->ecarts, auth()->id());

        return redirect()
            ->back()
            ->with('success', "{$count} écart(s) marqué(s) comme traité(s).");
    }

    /**
     * Lancer la détection automatique des écarts
     */
    public function detecterEcarts()
    {
        $plafondService = new PlafondService();

        // Initialiser les plafonds pour l'année courante
        $nouveauxPlafonds = $plafondService->initialiserPlafondsAnnee();

        return redirect()
            ->route('admin.import.ecarts')
            ->with('success', "Détection des écarts terminée. {$nouveauxPlafonds} plafond(s) initialisé(s).");
    }

    /**
     * Télécharger le modèle de fichier pour import des agents
     */
    public function telechargerModeleAgents()
    {
        $chemin = public_path('templates/modele_import_agents.xlsx');

        if (!file_exists($chemin)) {
            return redirect()
                ->back()
                ->with('error', 'Le modèle de fichier n\'est pas disponible.');
        }

        return response()->download($chemin, 'modele_import_agents.xlsx');
    }

    /**
     * Télécharger le modèle de fichier pour import des ayants droit
     */
    public function telechargerModeleAyantsDroit()
    {
        $chemin = public_path('templates/modele_import_ayants_droit.xlsx');

        if (!file_exists($chemin)) {
            return redirect()
                ->back()
                ->with('error', 'Le modèle de fichier n\'est pas disponible.');
        }

        return response()->download($chemin, 'modele_import_ayants_droit.xlsx');
    }
}
