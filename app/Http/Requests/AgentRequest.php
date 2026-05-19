<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matricule' => 'required|string|max:20|unique:agents,matricule,' . $this->route('id'),
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'cin' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'date_recrutement' => 'nullable|date',
            'dp_affectation' => 'nullable|string|max:100',
            'statut' => 'required|in:Actif,Retraité,Sorti,Décédé,Suspendu,Supprimé',
            'date_entree' => 'nullable|date',
            'date_sortie' => 'nullable|date|after:date_entree',
            'date_retraite' => 'nullable|date',
            'numero_immatriculation' => 'nullable|string|max:50',
            'date_affiliation' => 'nullable|date',
            'compte_bancaire' => 'nullable|string|max:50',
            'cle_bancaire' => 'nullable|string|max:20',
            'banque' => 'nullable|string|max:100',
            'info_banque' => 'nullable|string',
            'observations' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'matricule' => 'matricule',
            'nom' => 'nom',
            'prenom' => 'prénom',
            'cin' => 'CIN',
            'date_naissance' => 'date de naissance',
            'date_recrutement' => 'date de recrutement',
            'dp_affectation' => 'DP / affectation',
            'statut' => 'statut',
            'date_entree' => "date d'entrée",
            'date_sortie' => 'date de sortie',
            'date_retraite' => 'date de retraite',
            'numero_immatriculation' => 'numéro d\'immatriculation',
            'date_affiliation' => 'date d\'affiliation',
            'compte_bancaire' => 'compte bancaire',
            'cle_bancaire' => 'clé bancaire',
            'banque' => 'banque',
            'info_banque' => 'informations bancaires',
        ];
    }
}
