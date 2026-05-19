<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero',
        'type_facture',
        'demande_pec_id',
        'partenaire_id',
        'date_facture',
        'date_echeance',
        'nom_patient',
        'hospitalisation_du',
        'hospitalisation_au',
        'montant_ht',
        'montant_ttc',
        'montant_clinique',
        'montant_honoraires',
        'montant_autres',
        'part_adherent',
        'part_cnops',
        'part_assurance',
        'statut',
        'observations',
        'conditions_reglement',
        'created_by',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'hospitalisation_du' => 'date',
        'hospitalisation_au' => 'date',
        'montant_ht' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'montant_clinique' => 'decimal:2',
        'montant_honoraires' => 'decimal:2',
        'montant_autres' => 'decimal:2',
        'part_adherent' => 'decimal:2',
        'part_cnops' => 'decimal:2',
        'part_assurance' => 'decimal:2',
    ];

    /**
     * Relation avec la demande PEC
     */
    public function demandePec(): BelongsTo
    {
        return $this->belongsTo(DemandePEC::class, 'demande_pec_id');
    }

    /**
     * Relation avec le partenaire
     */
    public function partenaire(): BelongsTo
    {
        return $this->belongsTo(Partenaire::class);
    }

    /**
     * Relation avec les lignes de facture
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(FactureLigne::class)->orderBy('ordre');
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les factures par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type_facture', $type);
    }

    /**
     * Scope pour les factures par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les factures brouillon
     */
    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    /**
     * Scope pour les factures générées
     */
    public function scopeGeneree($query)
    {
        return $query->where('statut', 'generee');
    }

    /**
     * Scope pour les factures payees
     */
    public function scopePayee($query)
    {
        return $query->where('statut', 'payee');
    }

    /**
     * Générer un numéro de facture unique
     */
    public static function genererNumero(): string
    {
        $prefixe = config('facturation.prefixe', 'FAC-');
        $debutSerie = config('facturation.debut_serie', 1000);

        $dernierNumero = self::withTrashed()
            ->where('numero', 'like', $prefixe . '%')
            ->orderBy('id', 'desc')
            ->value('numero');

        if ($dernierNumero) {
            $suffix = (int) str_replace($prefixe, '', $dernierNumero);
            $suffix++;
        } else {
            $suffix = $debutSerie;
        }

        return $prefixe . str_pad($suffix, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculer et mettre à jour le total de la facture
     */
    public function calculerTotal(): void
    {
        $totalTtc = $this->lignes->sum('montant');

        if ($this->type_facture === 'clinique') {
            $this->montant_clinique = $this->lignes
                ->where('type_ligne', 'prestation_clinique')
                ->sum('montant');
            $this->montant_honoraires = $this->lignes
                ->where('type_ligne', 'honoraire')
                ->sum('montant');
            $this->montant_autres = $this->lignes
                ->where('type_ligne', 'autre')
                ->sum('montant');
        }

        $this->montant_ht = $totalTtc;
        $this->montant_ttc = $totalTtc;
        $this->saveQuietly();
    }

    /**
     * Obtenir le montant total en lettres (français)
     */
    public function montantEnLettres(): string
    {
        return $this->convertirMontantEnLettres($this->montant_ttc);
    }

    /**
     * Convertir un montant en lettres (français)
     */
    private function convertirMontantEnLettres($montant): string
    {
        // Pour l'instant, une version simple
        // TODO: Implémenter une conversion complète en lettres
        $montant = number_format($montant, 2, ',', ' ');
        return $montant . ' Dirhams';
    }

    /**
     * Marquer la facture comme générée
     */
    public function marquerGeneree(): void
    {
        $this->update(['statut' => 'generee']);
    }

    /**
     * Marquer la facture comme envoyée
     */
    public function marquerEnvoyee(): void
    {
        $this->update(['statut' => 'envoyee']);
    }

    /**
     * Marquer la facture comme payée
     */
    public function marquerPayee(): void
    {
        $this->update(['statut' => 'payee']);
    }

    /**
     * Marquer la facture comme annulée
     */
    public function marquerAnnulee(): void
    {
        $this->update(['statut' => 'annulee']);
    }

    /**
     * Vérifier si la facture peut être modifiée
     */
    public function peutEtreModifiee(): bool
    {
        return in_array($this->statut, ['brouillon', 'generee']);
    }

    /**
     * Obtenir le type de facture en français
     */
    public function getTypeAttribut(): string
    {
        return match($this->type_facture) {
            'medical' => 'Formation Médicale',
            'clinique' => 'Clinique',
            default => 'Non défini',
        };
    }

    /**
     * Obtenir le statut en français avec badge color
     */
    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'brouillon' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Brouillon</span>',
            'generee' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Générée</span>',
            'envoyee' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Envoyée</span>',
            'payee' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Payée</span>',
            'annulee' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Annulée</span>',
            default => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">' . $this->statut . '</span>',
        };
    }
}
