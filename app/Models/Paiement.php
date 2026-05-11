<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'demande_id',
        'montant_brut',
        'deduction',
        'montant_net',
        'date_reception_facture',
        'date_transmission_paiement',
        'date_paiement',
        'reference_paiement',
        'statut_paiement',
        'observations',
    ];

    protected $casts = [
        'montant_brut' => 'decimal:2',
        'deduction' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'date_reception_facture' => 'date',
        'date_transmission_paiement' => 'date',
        'date_paiement' => 'date',
    ];

    /**
     * Relation avec la demande PEC
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandePEC::class);
    }

    /**
     * Calculer automatiquement le montant net
     */
    public function calculerMontantNet(): void
    {
        $this->montant_net = $this->montant_brut - $this->deduction;
    }

    /**
     * Vérifier si le paiement est effectué
     */
    public function estPaye(): bool
    {
        return $this->statut_paiement === 'Payé' && $this->date_paiement !== null;
    }

    /**
     * Marquer comme payé
     */
    public function marquerPaye(string $reference = null): void
    {
        $this->statut_paiement = 'Payé';
        $this->date_paiement = now();
        if ($reference) {
            $this->reference_paiement = $reference;
        }
        $this->save();
    }

    /**
     * Scope pour les paiements en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->whereIn('statut_paiement', ['Non reçu', 'Reçu', 'En contrôle']);
    }

    /**
     * Scope pour les paiements payés
     */
    public function scopePayes($query)
    {
        return $query->where('statut_paiement', 'Payé');
    }
}
