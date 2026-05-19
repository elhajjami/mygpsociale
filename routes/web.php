<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AyantDroitController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\PartenaireController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\DpRh\DemandeController;
use App\Http\Controllers\DpRh\FacturationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil - redirection vers login ou dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Routes d'authentification
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Routes protégées (authentification requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profil utilisateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Tableau de bord
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');
    Route::get('/api/demandes-par-mois', [DashboardController::class, 'apiDemandesParMois'])->name('api.demandes-par-mois');
    Route::get('/api/alertes', [DashboardController::class, 'apiAlertes'])->name('api.alertes');

    // API pour vérifier l'authentification (évite les redirects)
    Route::get('/api/auth/check', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? auth()->user()->id : null
        ]);
    })->name('api.auth.check');

    /*
    |--------------------------------------------------------------------------
    | Routes Administration
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Utilisateurs
        |--------------------------------------------------------------------------
        */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        /*
        |--------------------------------------------------------------------------
        | Agents
        |--------------------------------------------------------------------------
        */
        Route::prefix('agents')->name('agents.')->group(function () {
            Route::get('/', [AgentController::class, 'index'])->name('index');
            Route::get('/create', [AgentController::class, 'create'])->name('create');
            Route::post('/', [AgentController::class, 'store'])->name('store');

            // Routes spécifiques AVANT la route générique {id}
            Route::get('/autocomplete', [AgentController::class, 'autocomplete'])->name('autocomplete');
            Route::get('/par-matricule', [AgentController::class, 'parMatricule'])->name('par-matricule');

            // Route comparaison - utiliser un paramètre nommé différemment pour éviter les conflits
            Route::get('/comparaison/{id}', [AgentController::class, 'getComparaisonSap'])
                ->name('comparaison-sap');

            // Routes avec {id}
            Route::get('/{id}/edit', [AgentController::class, 'edit'])->name('edit');
            Route::get('/{id}', [AgentController::class, 'show'])->name('show');
            Route::put('/{id}', [AgentController::class, 'update'])->name('update');
            Route::delete('/{id}', [AgentController::class, 'destroy'])->name('destroy');

            // Ayants droit
            Route::get('/{agentId}/ayants-droit/create', [AyantDroitController::class, 'create'])->name('ayants-droit.create');
            Route::post('/{agentId}/ayants-droit', [AyantDroitController::class, 'store'])->name('ayants-droit.store');
        });

        /*
        |--------------------------------------------------------------------------
        | Ayants droit (routes directes)
        |--------------------------------------------------------------------------
        */
        Route::prefix('ayants-droit')->name('ayants-droit.')->group(function () {
            Route::get('/{id}/edit', [AyantDroitController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AyantDroitController::class, 'update'])->name('update');
            Route::delete('/{id}', [AyantDroitController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/valider', [AyantDroitController::class, 'valider'])->name('valider');
            Route::post('/{id}/mettre-en-attente', [AyantDroitController::class, 'mettreEnAttente'])->name('mettre-en-attente');
            Route::post('/{id}/rejeter', [AyantDroitController::class, 'rejeter'])->name('rejeter');
        });

        /*
        |--------------------------------------------------------------------------
        | Partenaires
        |--------------------------------------------------------------------------
        */
        Route::prefix('partenaires')->name('partenaires.')->group(function () {
            Route::get('/', [PartenaireController::class, 'index'])->name('index');
            Route::get('/create', [PartenaireController::class, 'create'])->name('create');
            Route::post('/', [PartenaireController::class, 'store'])->name('store');
            Route::get('/{id}', [PartenaireController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PartenaireController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PartenaireController::class, 'update'])->name('update');
            Route::delete('/{id}', [PartenaireController::class, 'destroy'])->name('destroy');

            // API
            Route::get('/api/par-ville-type', [PartenaireController::class, 'apiParVilleEtType'])->name('api.par-ville-type');
            Route::get('/api/villes', [PartenaireController::class, 'villes'])->name('api.villes');
            Route::get('/api/specialites', [PartenaireController::class, 'specialites'])->name('api.specialites');
        });

        /*
        |--------------------------------------------------------------------------
        | Import SAP
        |--------------------------------------------------------------------------
        */
        Route::prefix('import')->name('import.')->group(function () {
            // Agents
            Route::get('/agents', [ImportController::class, 'agents'])->name('agents');
            Route::post('/agents', [ImportController::class, 'importAgents'])->name('agents.import');
            Route::post('/agents-ayants-droits', [ImportController::class, 'importAgentsAyantsDroits'])->name('agents-ayants-droits.import');
            Route::post('/cgs', [ImportController::class, 'importCGS'])->name('cgs.import');
            Route::get('/telecharger/modele-agents', [ImportController::class, 'telechargerModeleAgents'])->name('modele-agents');
            Route::get('/telecharger/modele-agents-combine', [ImportController::class, 'telechargerModeleAgentsCombine'])->name('modele-agents-combine');

            // Ayants droit
            Route::get('/ayants-droit', [ImportController::class, 'ayantsDroit'])->name('ayants-droit');
            Route::post('/ayants-droit', [ImportController::class, 'importAyantsDroit'])->name('ayants-droit.import');
            Route::get('/telecharger/modele-ayants-droit', [ImportController::class, 'telechargerModeleAyantsDroit'])->name('modele-ayants-droit');

            // Écarts SAP/CGS
            Route::get('/ecarts', [ImportController::class, 'ecarts'])->name('ecarts');
            Route::post('/ecarts/{id}/traiter', [ImportController::class, 'marquerEcartTraite'])->name('ecarts.traiter');
            Route::post('/ecarts/traiter-multiple', [ImportController::class, 'marquerEcartsTraites'])->name('ecarts.traiter-multiple');
            Route::post('/ecarts/detecter', [ImportController::class, 'detecterEcarts'])->name('ecarts.detecter');
        });

        /*
        |--------------------------------------------------------------------------
        | Paramètres (Carrière, Mesure, Plafonds)
        |--------------------------------------------------------------------------
        */
        Route::prefix('parametres')->name('parametres.')->group(function () {
            Route::get('/', [ParametreController::class, 'index'])->name('index');

            // Plafonds
            Route::get('/plafonds', [ParametreController::class, 'plafonds'])->name('plafonds');
            Route::get('/plafonds/edit', [ParametreController::class, 'plafondEdit'])->name('plafonds.edit');
            Route::post('/plafonds', [ParametreController::class, 'plafondStore'])->name('plafonds.store');

            // Carrières
            Route::get('/carrieres', [ParametreController::class, 'carrieres'])->name('carrieres');
            Route::get('/carrieres/create', [ParametreController::class, 'carriereCreate'])->name('carrieres.create');
            Route::post('/carrieres', [ParametreController::class, 'carriereStore'])->name('carrieres.store');

            // Mesures
            Route::get('/mesures', [ParametreController::class, 'mesures'])->name('mesures');
            Route::get('/mesures/create', [ParametreController::class, 'mesureCreate'])->name('mesures.create');
            Route::post('/mesures', [ParametreController::class, 'mesureStore'])->name('mesures.store');

            // Synchronisation
            Route::post('/synchroniser', [ParametreController::class, 'synchroniserAgents'])->name('synchroniser');
            Route::post('/analyser-agent', [ParametreController::class, 'analyserAgent'])->name('analyser-agent');
            Route::post('/appliquer-calculs', [ParametreController::class, 'appliquerCalculs'])->name('appliquer-calculs');
        });

        /*
        |--------------------------------------------------------------------------
        | Gestion des Rôles
        |--------------------------------------------------------------------------
        */
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::post('/{role}/permissions', [RoleController::class, 'managePermissions'])->name('permissions');
        });

        /*
        |--------------------------------------------------------------------------
        | Gestion des Permissions
        |--------------------------------------------------------------------------
        */
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::get('/create', [PermissionController::class, 'create'])->name('create');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Gestion des Utilisateurs
        |--------------------------------------------------------------------------
        */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/roles', [UserManagementController::class, 'manageRoles'])->name('roles');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes DP / RH
    |--------------------------------------------------------------------------
    */
    Route::prefix('dprh')->name('dprh.')->middleware(['role:Admin CGS|Gestionnaire CGS|DP RH'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Demandes PEC
        |--------------------------------------------------------------------------
        */
        Route::prefix('demandes')->name('demandes.')->group(function () {
            Route::get('/', [DemandeController::class, 'index'])->name('index');
            Route::get('/create', [DemandeController::class, 'create'])->name('create');
            Route::post('/', [DemandeController::class, 'store'])->name('store');
            Route::get('/{id}', [DemandeController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [DemandeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DemandeController::class, 'update'])->name('update');
            Route::delete('/{id}', [DemandeController::class, 'destroy'])->name('destroy');

            // Validation
            Route::post('/{id}/valider', [DemandeController::class, 'valider'])->name('valider');
            Route::post('/{id}/rejeter', [DemandeController::class, 'rejeter'])->name('rejeter');

            // Paiement
            Route::post('/{id}/marquer-payee', [DemandeController::class, 'marquerPayee'])->name('marquer-payee');

            // Impression
            Route::get('/{id}/imprimer-bon', [DemandeController::class, 'imprimerBon'])->name('imprimer-bon');
            Route::get('/{id}/voir-bon', [DemandeController::class, 'voirBon'])->name('voir-bon');

            // API
            Route::get('/api/ayants-droit', [DemandeController::class, 'apiAyantsDroit'])->name('api.ayants-droit');
            Route::get('/api/partenaires', [DemandeController::class, 'apiPartenaires'])->name('api.partenaires');
            Route::get('/api/verifier-plafond', [DemandeController::class, 'apiVerifierPlafond'])->name('api.verifier-plafond');
            Route::get('/api/plafond-agent', [DemandeController::class, 'apiPlafondAgent'])->name('api.plafond-agent');

            // DEBUG - À supprimer après résolution
            Route::get('/debug/ayants-droit', [DemandeController::class, 'debugAyantsDroit'])->name('debug.ayants-droit');
        });

        /*
        |--------------------------------------------------------------------------
        | Facturation
        |--------------------------------------------------------------------------
        */
        Route::prefix('facturation')->name('facturation.')->group(function () {
            Route::get('/', [FacturationController::class, 'index'])->name('index');
            Route::get('/create', [FacturationController::class, 'create'])->name('create');
            Route::get('/create/{demande_pec}', [FacturationController::class, 'create'])->name('create-from-pec');
            Route::post('/', [FacturationController::class, 'store'])->name('store');
            Route::get('/{id}', [FacturationController::class, 'show'])->name('show');
            Route::get('/{id}/telecharger', [FacturationController::class, 'telechargerPdf'])->name('telecharger');
            Route::delete('/{id}', [FacturationController::class, 'destroy'])->name('destroy');

            // API AJAX
            Route::get('/api/pec-search', [FacturationController::class, 'apiPecSearch'])->name('api.pec-search');
            Route::get('/api/pec-details/{id}', [FacturationController::class, 'apiPecDetails'])->name('api.pec-details');
            Route::get('/api/verifier-plafond', [FacturationController::class, 'apiVerifierPlafond'])->name('api.verifier-plafond');

            // Vérification session pour diagnostic
            Route::get('/api/check-session', function () {
                return response()->json([
                    'authenticated' => auth()->check(),
                    'user_id' => auth()->id(),
                    'session_id' => session()->getId(),
                    'session_has_token' => session()->has('_token'),
                    'csrf_token' => csrf_token(),
                    'session_lifetime' => config('session.lifetime'),
                    'time_remaining' => session()->get('last_activity')
                        ? (config('session.lifetime') * 60) - (now()->timestamp - session()->get('last_activity'))
                        : null,
                ]);
            })->name('api.check-session');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes Agent (portail self-service)
    |--------------------------------------------------------------------------
    */
    Route::prefix('agent')->name('agent.')->middleware(['role:agent'])->group(function () {
        // À implémenter plus tard pour le self-service des agents
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
