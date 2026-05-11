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
            'matricule' => 'required|string|max:20|unique:agents,matricule,' . $this->route('agent'),
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'cin' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'categorie' => 'required|in:Exécution,Maîtrise,Cadre,Hors cadre',
            'niveau' => 'nullable|string|max:10',
            'degre' => 'nullable|string|max:10',
            'dp_affectation' => 'nullable|string|max:100',
            'population' => 'nullable|in:BO,autre',
            'statut' => 'required|in:Actif,Retraité,Sorti,Décédé,Suspendu,Supprimé',
            'date_entree' => 'nullable|date',
            'date_sortie' => 'nullable|date|after:date_entree',
            'date_retraite' => 'nullable|date',
            'numero_immatriculation' => 'nullable|string|max:50',
            'numero_affiliation' => 'nullable|string|max:50',
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
            'categorie' => 'catégorie',
            'niveau' => 'niveau',
            'degre' => 'degré',
            'dp_affectation' => 'DP / affectation',
            'population' => 'population',
            'statut' => 'statut',
            'date_entree' => "date d'entrée",
            'date_sortie' => 'date de sortie',
            'date_retraite' => 'date de retraite',
            'numero_immatriculation' => 'numéro d\'immatriculation',
            'numero_affiliation' => 'numéro d\'affiliation',
        ];
    }
}
