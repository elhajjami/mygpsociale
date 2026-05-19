<?php

namespace App\Services;

use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturePdfService
{
    /**
     * Générer une facture en PDF
     */
    public function generer(Facture $facture): \Barryvdh\DomPDF\PDF
    {
        $data = $this->preparerDonnees($facture);

        $vue = $facture->type_facture === 'clinique'
            ? 'pdf.facture-clinique'
            : 'pdf.facture-medical';

        $pdf = PDF::loadView($vue, $data);
        $pdf->setPaper('a4');
        $pdf->setOption([
            'dpi' => config('facturation.pdf.dpi', 150),
            'defaultFont' => config('facturation.pdf.font', 'Times New Roman'),
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf;
    }

    /**
     * Générer et sauvegarder une facture
     */
    public function genererEtSauvegarder(Facture $facture): string
    {
        $pdf = $this->generer($facture);
        $filename = "factures/facture_{$facture->numero}.pdf";

        \Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Préparer les données pour la vue PDF
     */
    protected function preparerDonnees(Facture $facture): array
    {
        $partenaire = $facture->partenaire;
        $demandePec = $facture->demandePec;

        $data = [
            'facture' => $facture,
            'numero' => $facture->numero,
            'date_facture' => $facture->date_facture->format('d/m/Y'),
            'date_echeance' => $facture->date_echeance?->format('d/m/Y'),
            'type_facture' => $facture->type_facture,
            'type_libelle' => $facture->type_facture === 'clinique' ? 'Facture Clinique' : 'Facture Formation Médicale',

            // Partenaire - Structure de référence
            'partenaire' => $partenaire,
            'partenaire_nom' => $partenaire->nom ?? 'N/A',
            'partenaire_adresse' => $partenaire->adresse ?? '',
            'partenaire_ville' => $partenaire->ville ?? '',
            'partenaire_tel' => $partenaire->telephone ?? '',
            'partenaire_fax' => $partenaire->fax ?? '',
            'partenaire_rib' => $partenaire->rib ?? '',
            'partenaire_agence' => $partenaire->agence ?? '',
            'partenaire_ice' => $partenaire->ice ?? '',
            'partenaire_patente' => $partenaire->patente ?? '',
            'partenaire_if' => $partenaire->if ?? '',
            'partenaire_cnss' => $partenaire->cnss ?? '',

            // Montants
            'montant_ht' => number_format($facture->montant_ht, 2, ',', ' '),
            'montant_ttc' => number_format($facture->montant_ttc, 2, ',', ' '),
            'montant_lettres' => $facture->montantEnLettres(),

            // Lignes
            'lignes' => $facture->lignes,
            'lignes_actes' => $facture->lignes->where('type_ligne', 'acte'),
            'lignes_prestations' => $facture->lignes->where('type_ligne', 'prestation_clinique'),
            'lignes_honoraires' => $facture->lignes->where('type_ligne', 'honoraire'),
            'lignes_autres' => $facture->lignes->where('type_ligne', 'autre'),

            // Conditions
            'conditions' => $facture->conditions_reglement ?? config('facturation.conditions_paiement'),
            'observations' => $facture->observations,

            // Entreprise émettrice
            'entreprise' => config('facturation.entreprise'),
        ];

        // Champs spécifiques pour type clinique
        if ($facture->type_facture === 'clinique') {
            $data['nom_patient'] = $facture->nom_patient;
            $data['hospitalisation_du'] = $facture->hospitalisation_du?->format('d/m/Y');
            $data['hospitalisation_au'] = $facture->hospitalisation_au?->format('d/m/Y');
            $data['montant_clinique'] = number_format($facture->montant_clinique ?? 0, 2, ',', ' ');
            $data['montant_honoraires'] = number_format($facture->montant_honoraires ?? 0, 2, ',', ' ');
            $data['montant_autres'] = number_format($facture->montant_autres ?? 0, 2, ',', ' ');
            // Parts de prise en charge
            $data['part_adherent'] = $facture->part_adherent ? number_format($facture->part_adherent, 2, ',', ' ') . ' DH' : '..............................';
            $data['part_cnops'] = $facture->part_cnops ? number_format($facture->part_cnops, 2, ',', ' ') . ' DH' : '..............................';
            $data['part_assurance'] = $facture->part_assurance ? number_format($facture->part_assurance, 2, ',', ' ') . ' DH' : '..............................';
        }

        // Informations liées à la demande PEC
        if ($demandePec) {
            $data['numero_demande'] = $demandePec->numero_demande;
            $data['agent'] = $demandePec->agent;
        }

        return $data;
    }

    /**
     * Obtenir une vue HTML pour aperçu
     */
    public function apercu(Facture $facture): string
    {
        $data = $this->preparerDonnees($facture);

        $vue = $facture->type_facture === 'clinique'
            ? 'pdf.facture-clinique'
            : 'pdf.facture-medical';

        return view($vue, $data)->render();
    }
}
