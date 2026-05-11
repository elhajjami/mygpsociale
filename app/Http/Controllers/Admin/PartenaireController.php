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

        // Obtenir les villes uniques pour le filtre
        $villes = Partenaire::select('ville')->distinct()->orderBy('ville')->pluck('ville')->filter();

        return view('admin.partenaires.index', compact('partenaires', 'villes'));
    }

    public function create()
    {
        return view('admin.partenaires.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_convention' => 'required|string|max:50|unique:partenaires,numero_convention',
            'nom' => 'required|string|max:200',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'ville' => 'required|string|max:100',
            'specialite' => 'nullable|string|max:100|required_if:type_structure,médecin',
            'date_effet' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_effet',
            'statut' => 'required|in:active,expirée,suspendue,résiliée',
            'coordonnees' => 'nullable|string',
            'observations' => 'nullable|string',
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

        $validated = $request->validate([
            'numero_convention' => 'required|string|max:50|unique:partenaires,numero_convention,' . $id,
            'nom' => 'required|string|max:200',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'ville' => 'required|string|max:100',
            'specialite' => 'nullable|string|max:100',
            'date_effet' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_effet',
            'statut' => 'required|in:active,expirée,suspendue,résiliée',
            'coordonnees' => 'nullable|string',
            'observations' => 'nullable|string',
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
        $request->validate([
            'ville' => 'required|string',
            'type_structure' => 'required|in:clinique,laboratoire,médecin,radiologie',
            'specialite' => 'nullable|string',
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
        $villes = Partenaire::select('ville')
            ->distinct()
            ->orderBy('ville')
            ->pluck('ville')
            ->filter();

        return response()->json($villes);
    }

    /**
     * Obtenir les spécialités pour les médecins
     */
    public function specialites()
    {
        $specialites = Partenaire::where('type_structure', 'médecin')
            ->select('specialite')
            ->distinct()
            ->orderBy('specialite')
            ->pluck('specialite')
            ->filter();

        return response()->json($specialites);
    }
}
