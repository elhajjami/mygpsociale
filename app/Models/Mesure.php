<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mesure disciplinaire d'un agent (historique)
 * Table synchronisée depuis db_radeef.mesures
 */
class Mesure extends Model
{
    protected $fillable = [
        'matricule',
        'date_modification',
        'date_debut',
        'motif',
        'categorie',
    ];

    protected $casts = [
        'date_modification' => 'date',
        'date_debut' => 'date',
    ];

    /**
     * Relation avec l'agent
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'matricule', 'matricule');
    }

    /**
     * Scope pour les mesures en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('date_debut', '<=', now());
    }

    /**
     * Scope pour obtenir les mesures d'un agent
     */
    public function scopeParAgent($query, string $matricule)
    {
        return $query->where('matricule', $matricule)
            ->orderBy('date_debut', 'desc');
    }

    /**
     * Obtenir la dernière mesure d'un agent
     */
    public static function getDernierePourAgent(string $matricule): ?self
    {
        return self::where('matricule', $matricule)
            ->orderBy('date_debut', 'desc')
            ->first();
    }
}
