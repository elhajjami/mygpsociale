<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandePEC extends Model
{
    use SoftDeletes;

    protected $table = 'demandes_pec';

    protected $fillable = [
        'numero_demande',
        'agent_id',
        'beneficiaire_type',
        'ayant_droit_id',
        'type_demande',
        'nature_examens',
        'ville',
        'partenaire_id',
        'specialite',
        'montant_devis',
        'date_devis',
        'fichier_devis',
        'urgence',
        'observations',
        'statut',
        'motif_rejet',
        'date_validation',
        'date_expiration',
        'created_by',
        'validated_by',
        'date_creation',
    ];

    protected $casts = [
        'date_devis' => 'date',
        'date_validation' => 'date',
        'date_expiration' => 'date',
        'date_creation' => 'datetime',
        'urgence' => 'boolean',
        'montant_devis' => 'decimal:2',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relation avec l'ayant droit
     */
    public function ayantDroit(): BelongsTo
    {
        return $this->belongsTo(AyantDroit::class);
    }

    /**
     * Relation avec le partenaire
     */
    public function partenaire(): BelongsTo
    {
        return $this->belongsTo(Partenaire::class);
    }

    /**
     * Relation avec le créateur
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec le validateur
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Relation avec le paiement
     */
    public function paiement(): HasOne
    {
        return $this->hasOne(Paiement::class, 'demande_id');
    }

    /**
     * Obtenir le nom du bénéficiaire
     */
    public function getBeneficiaireNomAttribute(): string
    {
        return match($this->beneficiaire_type) {
            'agent' => $this->agent->nom_complet ?? 'N/A',
            'conjoint', 'enfant' => $this->ayantDroit->nom_prenom ?? 'N/A',
            default => 'N/A',
        };
    }

    /**
     * Vérifier si la demande est en attente
     */
    public function estEnAttente(): bool
    {
        return $this->statut === 'En attente de validation';
    }

    /**
     * Vérifier si la demande est validée
     */
    public function estValidee(): bool
    {
        return $this->statut === 'Validée';
    }

    /**
     * Vérifier si la demande est rejetée
     */
    public function estRejetee(): bool
    {
        return $this->statut === 'Rejetée';
    }

    /**
     * Vérifier si la demande est expirée
     */
    public function estExpiree(): bool
    {
        return $this->statut === 'Expirée' ||
               ($this->date_expiration && now()->gt($this->date_expiration));
    }

    /**
     * Vérifier si la demande est de type hospitalisation
     */
    public function estHospitalisation(): bool
    {
        return $this->type_demande === 'hospitalisation';
    }

    /**
     * Générer le numéro de demande
     */
    public static function genererNumero(string $type, int $annee): string
    {
        $prefix = $type === 'hospitalisation' ? 'LPPS' : 'PEC';
        $derniere = self::whereYear('created_at', $annee)
            ->where('numero_demande', 'like', "SRM-FM/{$prefix}/{$annee}%")
            ->orderBy('numero_demande', 'desc')
            ->first();

        $numero = $derniere ? ((int) substr($derniere->numero_demande, -4) + 1) : 1;

        return sprintf('SRM-FM/%s/%04d/%04d', $prefix, $annee, $numero);
    }

    /**
     * Calculer la date d'expiration (3 mois après validation)
     */
    public function calculerDateExpiration(): void
    {
        if ($this->date_validation) {
            $this->date_expiration = $this->date_validation->addMonths(3);
        }
    }

    /**
     * Scope pour les demandes en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'En attente de validation');
    }

    /**
     * Scope pour les demandes validées
     */
    public function scopeValidees($query)
    {
        return $query->where('statut', 'Validée');
    }

    /**
     * Scope pour les demandes par agent
     */
    public function scopeParAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope pour les demandes par statut
     */
    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les demandes expirées
     */
    public function scopeExpirees($query)
    {
        return $query->where('statut', 'Expirée')
            ->orWhere('date_expiration', '<', now());
    }
}
