<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactureLigne extends Model
{
    protected $fillable = [
        'facture_id',
        'type_ligne',
        'matricule',
        'nom_patient',
        'beneficiaire',
        'nature_acte',
        'cotation',
        'designation',
        'categorie',
        'quantite',
        'prix_unitaire',
        'montant',
        'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant' => 'decimal:2',
        'ordre' => 'integer',
    ];

    /**
     * Relation avec la facture
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    /**
     * Calculer le montant avant sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ligne) {
            $ligne->montant = $ligne->quantite * $ligne->prix_unitaire;
        });
    }

    /**
     * Scope pour les lignes de type acte
     */
    public function scopeActes($query)
    {
        return $query->where('type_ligne', 'acte');
    }

    /**
     * Scope pour les lignes de type prestation_clinique
     */
    public function scopePrestationsClinique($query)
    {
        return $query->where('type_ligne', 'prestation_clinique');
    }

    /**
     * Scope pour les lignes de type honoraire
     */
    public function scopeHonoraires($query)
    {
        return $query->where('type_ligne', 'honoraire');
    }

    /**
     * Scope pour les lignes de type autre
     */
    public function scopeAutres($query)
    {
        return $query->where('type_ligne', 'autre');
    }

    /**
     * Obtenir le type de ligne en français
     */
    public function getTypeLibelleAttribute(): string
    {
        return match($this->type_ligne) {
            'acte' => 'Acte médical',
            'prestation_clinique' => 'Prestation clinique',
            'honoraire' => 'Honoraire',
            'autre' => 'Autre prestation',
            default => 'Non défini',
        };
    }

    /**
     * Obtenir la description complète de la ligne
     */
    public function getDescriptionCompleteAttribute(): string
    {
        if ($this->type_ligne === 'acte') {
            return trim(($this->nature_acte ?? '') . ' ' . ($this->cotation ? "({$this->cotation})" : ''));
        }

        return $this->designation ?? '';
    }
}
