<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class DemandePECRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_id' => 'required|exists:agents,id',
            'type_beneficiaire' => 'required|in:agent,ayant_droit',
            'ayant_droit_id' => 'required_if:type_beneficiaire,ayant_droit|nullable|exists:ayant_droits,id',
            'partenaire_id' => 'required|exists:partenaires,id',
            'type_prestation' => 'required|in:consultation,analyse,radiologie,medicament,chirurgie,autre',
            'nature_examens' => 'required|string|max:255',
            'date_soin' => 'required|date',
            'montant_devis' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'ville' => 'required|string|in:Fès,Meknès',
            'type_structure' => 'required|string|in:clinique,laboratoire,médecin,radiologie',
            'fichier_devis' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'agent_id' => 'agent',
            'type_beneficiaire' => 'type de bénéficiaire',
            'ayant_droit_id' => 'ayant droit',
            'partenaire_id' => 'partenaire',
            'type_prestation' => 'type de prestation',
            'nature_examens' => 'nature des examens',
            'date_soin' => 'date de soin',
            'montant_devis' => 'montant du devis',
            'description' => 'description',
            'ville' => 'ville',
            'type_structure' => 'type de structure',
            'fichier_devis' => 'fichier du devis',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Log pour débogage
        \Log::info('DemandePECRequest prepareForValidation', [
            'type_beneficiaire' => $this->input('type_beneficiaire'),
            'ayant_droit_id' => $this->input('ayant_droit_id'),
            'ayant_droit_id_type' => gettype($this->input('ayant_droit_id')),
            'agent_id' => $this->input('agent_id'),
            'all_data' => $this->all(),
        ]);

        // S'assurer que ayant_droit_id est null si type_beneficiaire est 'agent'
        if ($this->input('type_beneficiaire') === 'agent') {
            $this->merge(['ayant_droit_id' => null]);
        }

        // S'assurer que description est null si vide
        if (empty($this->input('description'))) {
            $this->merge(['description' => null]);
        }
    }

    /**
     * Passer les données après validation
     */
    public function passedValidation(): void
    {
        \Log::info('DemandePECRequest passedValidation', [
            'validated' => $this->validated(),
        ]);
    }
}
