<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetenuePaie extends Model
{
    protected $table = 'retenues_paie';

    protected $fillable = [
        'agent_id',
        'mois',
        'annee',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
        'observation',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Obtenir la retenue mensuelle selon la catégorie de l'agent
     */
    public static function getRetenueMensuelle(string $categorie): float
    {
        return match($categorie) {
            'Exécution' => 500,
            'Maîtrise' => 1000,
            'Cadre', 'Hors cadre' => 2000,
            default => 0,
        };
    }

    /**
     * Vérifier si la retenue est active
     */
    public function estActive(): bool
    {
        return $this->statut === 'active';
    }

    /**
     * Suspendre la retenue
     */
    public function suspendre(): void
    {
        $this->statut = 'suspendue';
        $this->save();
    }

    /**
     * Réactiver la retenue
     */
    public function reactiver(): void
    {
        $this->statut = 'active';
        $this->save();
    }

    /**
     * Clôturer la retenue
     */
    public function clôturer(): void
    {
        $this->statut = 'clôturée';
        $this->date_fin = now();
        $this->save();
    }

    /**
     * Scope pour les retenues actives
     */
    public function scopeActives($query)
    {
        return $query->where('statut', 'active');
    }

    /**
     * Scope pour les retenues d'une période donnée
     */
    public function scopePeriode($query, int $annee, int $mois)
    {
        return $query->where('annee', $annee)->where('mois', $mois);
    }

    /**
     * Scope pour les retenues de l'année courante
     */
    public function scopeAnneeCourante($query)
    {
        return $query->where('annee', now()->year);
    }
}
