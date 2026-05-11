<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AyantDroit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agent_id',
        'type',
        'nom_prenom',
        'date_naissance',
        'cin',
        'statut',
        'observations',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Accesseurs à inclure dans les réponses JSON/array
     */
    protected $appends = [
        'nom_complet',
        'lien_parente',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relation avec les demandes PEC où cet ayant droit est le bénéficiaire
     */
    public function demandesPEC(): HasMany
    {
        return $this->hasMany(DemandePEC::class, 'ayant_droit_id');
    }

    /**
     * Obtenir l'âge de l'ayant droit
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    /**
     * Obtenir le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->nom_prenom;
    }

    /**
     * Obtenir le lien de parenté (alias pour type)
     */
    public function getLienParenteAttribute(): string
    {
        return $this->type;
    }

    /**
     * Vérifier si l'ayant droit est validé
     */
    public function estValide(): bool
    {
        return $this->statut === 'Validé';
    }

    /**
     * Scope pour les ayants droit validés
     */
    public function scopeValides($query)
    {
        return $query->where('statut', 'Validé');
    }

    /**
     * Scope pour les ayants droit actifs (validés)
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'Validé');
    }

    /**
     * Scope pour les ayants droit en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'En attente');
    }

    /**
     * Scope pour les conjoints
     */
    public function scopeConjoints($query)
    {
        return $query->where('type', 'conjoint');
    }

    /**
     * Scope pour les enfants
     */
    public function scopeEnfants($query)
    {
        return $query->where('type', 'enfant');
    }
}
