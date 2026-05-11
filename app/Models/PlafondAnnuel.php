<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlafondAnnuel extends Model
{
    protected $table = 'plafonds_annuels';

    protected $fillable = [
        'agent_id',
        'annee',
        'plafond_annuel',
        'montant_consome',
        'montant_engage',
        'reste_disponible',
    ];

    protected $casts = [
        'plafond_annuel' => 'decimal:2',
        'montant_consome' => 'decimal:2',
        'montant_engage' => 'decimal:2',
        'reste_disponible' => 'decimal:2',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Obtenir ou créer le plafond annuel pour un agent
     */
    public static function pourAgent(int $agentId, int $annee = null): self
    {
        $annee = $annee ?? now()->year;

        $plafond = self::where('agent_id', $agentId)
            ->where('annee', $annee)
            ->first();

        if (!$plafond) {
            $agent = Agent::find($agentId);
            $plafond = self::create([
                'agent_id' => $agentId,
                'annee' => $annee,
                'plafond_annuel' => $agent ? $agent->plafond_annuel : 0,
                'montant_consome' => 0,
                'montant_engage' => 0,
                'reste_disponible' => $agent ? $agent->plafond_annuel : 0,
            ]);
        }

        return $plafond;
    }

    /**
     * Ajouter un montant engagé
     */
    public function ajouterEngage(float $montant): void
    {
        $this->montant_engage += $montant;
        $this->reste_disponible = $this->plafond_annuel - $this->montant_consome - $this->montant_engage;
        $this->save();
    }

    /**
     * Confirmer un montant engagé (le transformer en consommé)
     */
    public function confirmerEngage(float $montant): void
    {
        $this->montant_engage -= $montant;
        $this->montant_consome += $montant;
        $this->reste_disponible = $this->plafond_annuel - $this->montant_consome - $this->montant_engage;
        $this->save();
    }

    /**
     * Annuler un montant engagé
     */
    public function annulerEngage(float $montant): void
    {
        $this->montant_engage -= $montant;
        $this->reste_disponible = $this->plafond_annuel - $this->montant_consome - $this->montant_engage;
        $this->save();
    }

    /**
     * Vérifier si un montant est disponible
     */
    public function montantDisponible(float $montant): bool
    {
        return $this->reste_disponible >= $montant;
    }

    /**
     * Obtenir le pourcentage de consommation
     */
    public function getPourcentageConsommeAttribute(): float
    {
        if ($this->plafond_annuel == 0) {
            return 0;
        }

        return ($this->montant_consome / $this->plafond_annuel) * 100;
    }

    /**
     * Obtenir le pourcentage d'engagement
     */
    public function getPourcentageEngageAttribute(): float
    {
        if ($this->plafond_annuel == 0) {
            return 0;
        }

        return (($this->montant_consome + $this->montant_engage) / $this->plafond_annuel) * 100;
    }

    /**
     * Scope pour l'année courante
     */
    public function scopeAnneeCourante($query)
    {
        return $query->where('annee', now()->year);
    }
}
