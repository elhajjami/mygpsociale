<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'beneficiaire_type' => 'required|in:agent,ayant_droit',
            'ayant_droit_id' => 'required_if:beneficiaire_type,ayant_droit|exists:ayants_droit,id',
            'partenaire_id' => 'required|exists:partenaires,id',
            'type_soin' => 'required|in:médical,chirurgical,accouchement,laboratoire,radiologie,pharmacie,dentaire,optique,autre',
            'date_soin' => 'required|date|before_or_equal:today',
            'montant_devis' => 'required|numeric|min:0',
            'diagnostic' => 'nullable|string|max:500',
            'actes_medicaux' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'urgence' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'agent_id' => 'agent',
            'beneficiaire_type' => 'type de bénéficiaire',
            'ayant_droit_id' => 'ayant droit',
            'partenaire_id' => 'partenaire',
            'type_soin' => 'type de soin',
            'date_soin' => 'date de soin',
            'montant_devis' => 'montant du devis',
            'diagnostic' => 'diagnostic',
            'actes_medicaux' => 'actes médicaux',
            'documents' => 'documents',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'urgence' => $this->has('urgence') ? true : false,
        ]);
    }
}
