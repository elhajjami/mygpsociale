<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AyantDroit;
use Illuminate\Http\Request;

class AyantDroitController extends Controller
{
    /**
     * Afficher le formulaire de création d'un ayant droit pour un agent
     */
    public function create($agentId)
    {
        $agent = Agent::findOrFail($agentId);
        return view('admin.ayants-droit.create', compact('agent'));
    }

    /**
     * Enregistrer un nouvel ayant droit
     */
    public function store(Request $request, $agentId)
    {
        $agent = Agent::findOrFail($agentId);

        $validated = $request->validate([
            'type' => 'required|in:conjoint,enfant',
            'nom_prenom' => 'required|string|max:200',
            'date_naissance' => 'required|date|before:today',
            'cin' => 'nullable|string|max:20',
            'statut' => 'required|in:Validé,En attente,Rejeté',
            'observations' => 'nullable|string',
        ]);

        $validated['agent_id'] = $agent->id;

        AyantDroit::create($validated);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'Ayant droit ajouté avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'un ayant droit
     */
    public function edit($id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);
        return view('admin.ayants-droit.edit', compact('ayantDroit'));
    }

    /**
     * Mettre à jour un ayant droit
     */
    public function update(Request $request, $id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:conjoint,enfant',
            'nom_prenom' => 'required|string|max:200',
            'date_naissance' => 'required|date|before:today',
            'cin' => 'nullable|string|max:20',
            'statut' => 'required|in:Validé,En attente,Rejeté',
            'observations' => 'nullable|string',
        ]);

        $ayantDroit->update($validated);

        return redirect()
            ->route('admin.agents.show', $ayantDroit->agent_id)
            ->with('success', 'Ayant droit mis à jour avec succès.');
    }

    /**
     * Supprimer un ayant droit
     */
    public function destroy($id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);
        $agentId = $ayantDroit->agent_id;
        $nom = $ayantDroit->nom_prenom;

        $ayantDroit->delete();

        return redirect()
            ->route('admin.agents.show', $agentId)
            ->with('success', "L'ayant droit {$nom} a été supprimé.");
    }

    /**
     * Valider un ayant droit
     */
    public function valider($id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);
        $ayantDroit->update(['statut' => 'Validé']);

        return redirect()
            ->route('admin.agents.show', $ayantDroit->agent_id)
            ->with('success', 'Ayant droit validé avec succès.');
    }

    /**
     * Mettre en attente un ayant droit
     */
    public function mettreEnAttente($id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);
        $ayantDroit->update(['statut' => 'En attente']);

        return redirect()
            ->route('admin.agents.show', $ayantDroit->agent_id)
            ->with('success', 'Ayant droit mis en attente.');
    }

    /**
     * Rejeter un ayant droit
     */
    public function rejeter($id)
    {
        $ayantDroit = AyantDroit::findOrFail($id);
        $ayantDroit->update(['statut' => 'Rejeté']);

        return redirect()
            ->route('admin.agents.show', $ayantDroit->agent_id)
            ->with('success', 'Ayant droit rejeté.');
    }
}
