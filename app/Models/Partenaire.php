<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partenaire extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_convention',
        'nom',
        'type_structure',
        'ville',
        'specialite',
        'date_effet',
        'date_fin',
        'statut',
        'coordonnees',
        'observations',
    ];

    protected $casts = [
        'date_effet' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relation avec les demandes PEC
     */
    public function demandesPEC(): HasMany
    {
        return $this->hasMany(DemandePEC::class);
    }

    /**
     * Vérifier si la convention est active
     */
    public function estActive(): bool
    {
        if ($this->statut !== 'active') {
            return false;
        }

        $now = now();
        if ($this->date_effet && $now->lt($this->date_effet)) {
            return false;
        }

        if ($this->date_fin && $now->gt($this->date_fin)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si la convention est expirée
     */
    public function estExpiree(): bool
    {
        return $this->date_fin && now()->gt($this->date_fin);
    }

    /**
     * Scope pour les partenaires actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'active')
            ->where(function($q) {
                $q->whereNull('date_effet')
                  ->orWhere('date_effet', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('date_fin')
                  ->orWhere('date_fin', '>=', now());
            });
    }

    /**
     * Scope pour les partenaires par ville
     */
    public function scopeParVille($query, $ville)
    {
        return $query->where('ville', $ville);
    }

    /**
     * Scope pour les partenaires par type de structure
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type_structure', $type);
    }

    /**
     * Scope pour les médecins par spécialité
     */
    public function scopeParSpecialite($query, $specialite)
    {
        return $query->where('type_structure', 'médecin')
            ->where('specialite', $specialite);
    }
}
