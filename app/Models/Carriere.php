<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Carrière d'un agent (historique)
 * Table synchronisée depuis db_radeef.carriere_cmr
 */
class Carriere extends Model
{
    protected $table = 'carrieres';

    protected $fillable = [
        'matricule',
        'motif_modification',
        'date_debut',
        'cat',
        'niv',
        'deg',
        'date_fin',
        'date_modification',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_modification' => 'date',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'matricule', 'matricule');
    }

    /**
     * Scope pour obtenir les carrières d'un agent
     */
    public function scopeParAgent($query, string $matricule)
    {
        return $query->where('matricule', $matricule)
            ->orderBy('date_debut', 'desc');
    }

    /**
     * Scope pour la carrière actuelle
     */
    public function scopeActuelle($query)
    {
        return $query->whereNull('date_fin')
            ->orWhere('date_fin', '>=', now());
    }

    /**
     * Obtenir la carrière actuelle d'un agent
     */
    public static function getActuellePourAgent(string $matricule): ?self
    {
        return self::where('matricule', $matricule)
            ->whereNull('date_fin')
            ->orderBy('date_debut', 'desc')
            ->first();
    }

    /**
     * Obtenir l'historique complet de carrière d'un agent
     */
    public static function getHistoriquePourAgent(string $matricule)
    {
        return self::where('matricule', $matricule)
            ->orderBy('date_debut', 'desc')
            ->get();
    }

    /**
     * Obtenir la catégorie complète (Exécution, Maîtrise, Cadre, Hors cadre)
     * À partir du premier caractère du niveau (niv)
     */
    public function getCategorieCompleteAttribute(): string
    {
        // Utiliser le premier caractère de niv (ex: M12 -> M = Maîtrise)
        $prefixe = $this->niv ? strtoupper(substr($this->niv, 0, 1)) : null;

        return match($prefixe) {
            'E' => 'Exécution',
            'M' => 'Maîtrise',
            'C' => 'Cadre',
            'H' => 'Hors cadre',
            default => $this->cat ?? 'Non défini',
        };
    }
}
