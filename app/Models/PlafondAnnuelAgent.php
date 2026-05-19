<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Plafond annuel par agent et par année (un seul plafond global)
 */
class PlafondAnnuelAgent extends Model
{
    protected $table = 'plafonds_annuels_agents';

    protected $fillable = [
        'agent_id',
        'annee',
        'plafond_annuel',
        'consomme',
        'reste',
    ];

    protected $casts = [
        'plafond_annuel' => 'decimal:2',
        'consomme' => 'decimal:2',
        'reste' => 'decimal:2',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Obtenir ou créer le plafond annuel pour un agent et une année
     */
    public static function obtenirOuCreer(int $agentId, int $annee = null): self
    {
        $annee = $annee ?? now()->year;

        $plafond = self::where('agent_id', $agentId)
            ->where('annee', $annee)
            ->first();

        if (!$plafond) {
            $agent = Agent::find($agentId);
            $plafondAnnuel = $agent ? $agent->plafond_annuel : 12000;

            $plafond = self::create([
                'agent_id' => $agentId,
                'annee' => $annee,
                'plafond_annuel' => $plafondAnnuel,
                'consomme' => 0,
                'reste' => $plafondAnnuel,
            ]);
        }

        return $plafond;
    }

    /**
     * Ajouter une consommation (que ce soit médical ou clinique)
     */
    public function ajouterConsommation(float $montant): void
    {
        $this->consomme += $montant;
        $this->reste = max(0, $this->plafond_annuel - $this->consomme);
        $this->save();
    }

    /**
     * Ajouter une consommation médicale
     */
    public function ajouterConsommationMedical(float $montant): void
    {
        $this->consomme += $montant;
        $this->reste = max(0, $this->plafond_annuel - $this->consomme);
        $this->save();
    }

    /**
     * Ajouter une consommation clinique
     */
    public function ajouterConsommationClinique(float $montant): void
    {
        $this->consomme += $montant;
        $this->reste = max(0, $this->plafond_annuel - $this->consomme);
        $this->save();
    }

    /**
     * Vérifier si le plafond est dépassé
     */
    public function estDepasse(): bool
    {
        return $this->reste <= 0;
    }

    /**
     * Obtenir le pourcentage d'utilisation du plafond
     */
    public function getPourcentageUtilisationAttribute(): float
    {
        if ($this->plafond_annuel == 0) return 0;
        return round(($this->consomme / $this->plafond_annuel) * 100, 2);
    }

    /**
     * Accessors pour la compatibilité avec l'ancien code
     */
    public function getResteMedicalAttribute(): float
    {
        return $this->reste;
    }

    public function getResteCliniqueAttribute(): float
    {
        return $this->reste;
    }

    public function getConsommeMedicalAttribute(): float
    {
        return $this->consomme;
    }

    public function getConsommeCliniqueAttribute(): float
    {
        return $this->consomme;
    }

    public function getPourcentageUtilisationMedicalAttribute(): float
    {
        return $this->pourcentage_utilisation;
    }

    public function getPourcentageUtilisationCliniqueAttribute(): float
    {
        return $this->pourcentage_utilisation;
    }
}
