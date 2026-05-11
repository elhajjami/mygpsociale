<?php

namespace App\Services;

use App\Models\DemandePEC;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BonPecPdfService
{
    /**
     * Générer un bon PEC en PDF
     */
    public function generer(DemandePEC $demande): \Barryvdh\DomPDF\PDF
    {
        $data = $this->preparerDonnees($demande);

        $pdf = PDF::loadView('pdf.bon-pec', $data);
        $pdf->setPaper('a4');
        $pdf->setOption([
            'dpi' => 150,
            'defaultFont' => 'Times New Roman',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf;
    }

    /**
     * Générer et sauvegarder un bon PEC
     */
    public function genererEtSauvegarder(DemandePEC $demande): string
    {
        $pdf = $this->generer($demande);
        $filename = "bons_pec/bon_pec_{$demande->numero_demande}.pdf";

        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Préparer les données pour la vue PDF
     */
    protected function preparerDonnees(DemandePEC $demande): array
    {
        $beneficiaire = $demande->beneficiaire ?? $demande->agent;

        return [
            'numero_demande' => $demande->numero_demande,
            'date_generation' => now()->format('d/m/Y'),
            'date_soin' => $demande->date_soin->format('d/m/Y'),
            'date_validation' => $demande->date_validation?->format('d/m/Y'),
            'type_soin' => $demande->type_prestation ?? $demande->type_soin ?? 'Soins médicaux',
            'description' => $demande->description ?? $demande->diagnostic ?? null,
            'montant_devis' => number_format($demande->montant_devis, 2, ',', ' '),
            'montant_regle' => $demande->montant_regle ? number_format($demande->montant_regle, 2, ',', ' ') : null,

            // Agent
            'agent' => $demande->agent,

            // Bénéficiaire
            'beneficiaire_type' => $demande->beneficiaire_type === 'ayant_droit' ? 'Ayant droit' : 'Agent',
            'beneficiaire_nom' => $beneficiaire->nom . ' ' . $beneficiaire->prenom,
            'beneficiaire_date_naissance' => $beneficiaire->date_naissance?->format('d/m/Y') ?? '-',
            'lien_parente' => $demande->beneficiaire_type === 'ayant_droit'
                ? ($beneficiaire->type ?? 'Ayant droit')
                : 'Titulaire',

            // Partenaire
            'partenaire' => $demande->partenaire,
        ];
    }

    /**
     * Générer un bon PEC provisoire (avant validation)
     */
    public function genererProvisoire(DemandePEC $demande): \Barryvdh\DomPDF\PDF
    {
        $data = $this->preparerDonnees($demande);
        $data['provisoire'] = true;

        $pdf = PDF::loadView('pdf.bon-pec', $data);
        $pdf->setPaper('a4');

        return $pdf;
    }
}
