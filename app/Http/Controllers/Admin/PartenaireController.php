<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $ville = $request->get('ville');
        $type = $request->get('type_structure');
        $statut = $request->get('statut');

        $partenaires = Partenaire::query()
            ->when($search, function ($query, $search) {
                return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('numero_convention', 'like', "%{$search}%");
            })
            ->when($ville, function ($query, $ville) {
                return $query->where('ville', $ville);
            })
            ->when($type, function ($query, $type) {
                return $query->where('type_structure', $type);
            })
            ->when($statut, function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->orderBy('nom')
            ->paginate(25)
            ->appends($request->all());

        return view('admin.partenaires.index', compact('partenaires'));
    }

    public function create()
    {
        return view('admin.partenaires.create');
    }

    public function store(Request $request)
    {
        $specialites = [
            'Multidisciplinaire', 'Biologie médicale',
            'Cardiologie', 'Dermatologie', 'Gastro-entérologie', 'Gynécologie',
            'Médecine générale', 'Médecine interne', 'Néphrologie', 'Neurologie',
            'Ophtalmologie', 'ORL', 'Pédiatrie', 'Pneumologie', 'Psychiatrie',
            'Radiologie', 'Rhumatologie', 'Stomatologie', 'Chirurgie générale', 'Chirurgie orthopédique'
        ];

        $validated = $request->validate([
            'numero_convention' => 'required|string|max:50|unique:partenaires,numero_convention',
            'nom' => 'required|string|max:200',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'ville' => 'required|in:Fès,Meknès',
            'specialite' => 'nullable|in:' . implode(',', $specialites) . '|required_if:type_structure,clinique,laboratoire,médecin',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'date_effet' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_effet',
            'statut' => 'required|in:active,expirée,suspendue,résiliée',
            'coordonnees' => 'nullable|string',
            'observations' => 'nullable|string',
            // Champs facturation
            'adresse' => 'nullable|string|max:500',
            'fax' => 'nullable|string|max:20',
            'rib' => 'nullable|string|max:24',
            'banque' => 'nullable|string|max:100',
            'agence' => 'nullable|string|max:100',
            'ice' => 'nullable|string|max:20',
            'patente' => 'nullable|string|max:50',
            'if' => 'nullable|string|max:20',
            'cnss' => 'nullable|string|max:30',
        ]);

        Partenaire::create($validated);

        return redirect()
            ->route('admin.partenaires.index')
            ->with('success', 'Partenaire créé avec succès.');
    }

    public function show(string $id)
    {
        $partenaire = Partenaire::with(['demandesPEC' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(20);
        }])->findOrFail($id);

        return view('admin.partenaires.show', compact('partenaire'));
    }

    public function edit(string $id)
    {
        $partenaire = Partenaire::findOrFail($id);
        return view('admin.partenaires.edit', compact('partenaire'));
    }

    public function update(Request $request, string $id)
    {
        $partenaire = Partenaire::findOrFail($id);

        $specialites = [
            'Multidisciplinaire', 'Biologie médicale',
            'Cardiologie', 'Dermatologie', 'Gastro-entérologie', 'Gynécologie',
            'Médecine générale', 'Médecine interne', 'Néphrologie', 'Neurologie',
            'Ophtalmologie', 'ORL', 'Pédiatrie', 'Pneumologie', 'Psychiatrie',
            'Radiologie', 'Rhumatologie', 'Stomatologie', 'Chirurgie générale', 'Chirurgie orthopédique'
        ];

        $validated = $request->validate([
            'numero_convention' => 'required|string|max:50|unique:partenaires,numero_convention,' . $id,
            'nom' => 'required|string|max:200',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'ville' => 'required|in:Fès,Meknès',
            'specialite' => 'nullable|in:' . implode(',', $specialites),
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'date_effet' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_effet',
            'statut' => 'required|in:active,expirée,suspendue,résiliée',
            'coordonnees' => 'nullable|string',
            'observations' => 'nullable|string',
            // Champs facturation
            'adresse' => 'nullable|string|max:500',
            'fax' => 'nullable|string|max:20',
            'rib' => 'nullable|string|max:24',
            'banque' => 'nullable|string|max:100',
            'agence' => 'nullable|string|max:100',
            'ice' => 'nullable|string|max:20',
            'patente' => 'nullable|string|max:50',
            'if' => 'nullable|string|max:20',
            'cnss' => 'nullable|string|max:30',
        ]);

        $partenaire->update($validated);

        return redirect()
            ->route('admin.partenaires.show', $partenaire)
            ->with('success', 'Partenaire mis à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $partenaire = Partenaire::findOrFail($id);
        $partenaire->delete();

        return redirect()
            ->route('admin.partenaires.index')
            ->with('success', 'Partenaire supprimé avec succès.');
    }

    /**
     * API pour récupérer les partenaires par ville et type
     */
    public function apiParVilleEtType(Request $request)
    {
        $specialites = [
            'Multidisciplinaire', 'Biologie médicale',
            'Cardiologie', 'Dermatologie', 'Gastro-entérologie', 'Gynécologie',
            'Médecine générale', 'Médecine interne', 'Néphrologie', 'Neurologie',
            'Ophtalmologie', 'ORL', 'Pédiatrie', 'Pneumologie', 'Psychiatrie',
            'Radiologie', 'Rhumatologie', 'Stomatologie', 'Chirurgie générale', 'Chirurgie orthopédique'
        ];

        $request->validate([
            'ville' => 'required|in:Fès,Meknès',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'specialite' => 'nullable|in:' . implode(',', $specialites),
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
     * Obtenir les villes disponibles
     */
    public function villes()
    {
        $villes = ['Fès', 'Meknès'];

        return response()->json($villes);
    }

    /**
     * Obtenir les spécialités pour les médecins, cliniques et laboratoires
     */
    public function specialites()
    {
        $specialites = [
            'Multidisciplinaire', 'Biologie médicale',
            'Cardiologie', 'Dermatologie', 'Gastro-entérologie', 'Gynécologie',
            'Médecine générale', 'Médecine interne', 'Néphrologie', 'Neurologie',
            'Ophtalmologie', 'ORL', 'Pédiatrie', 'Pneumologie', 'Psychiatrie',
            'Radiologie', 'Rhumatologie', 'Stomatologie', 'Chirurgie générale', 'Chirurgie orthopédique'
        ];

        return response()->json($specialites);
    }
}
