<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametrePlafond extends Model
{
    protected $table = 'parametres_plafond';

    protected $fillable = [
        'annee',
        'plafond_execution',
        'plafond_maitrise',
        'plafond_cadre',
        'plafond_hors_cadre',
        'plafond_bo',
        'actif',
        'notes',
    ];

    protected $casts = [
        'annee' => 'integer',
        'plafond_execution' => 'decimal:2',
        'plafond_maitrise' => 'decimal:2',
        'plafond_cadre' => 'decimal:2',
        'plafond_hors_cadre' => 'decimal:2',
        'plafond_bo' => 'decimal:2',
        'actif' => 'boolean',
    ];

    /**
     * Obtenir les paramètres pour une année
     */
    public static function pourAnnee(int $annee): ?self
    {
        return self::where('annee', $annee)->first();
    }

    /**
     * Obtenir les paramètres de l'année courante
     */
    public static function anneeCourante(): ?self
    {
        return self::pourAnnee(now()->year);
    }

    /**
     * Obtenir le plafond pour une catégorie (méthode d'instance)
     */
    public function plafondPourCategorie(string $categorie): float
    {
        return match($categorie) {
            'Exécution' => $this->plafond_execution,
            'Maîtrise' => $this->plafond_maitrise,
            'Cadre' => $this->plafond_cadre,
            'Hors cadre' => $this->plafond_hors_cadre,
            default => $this->plafond_execution,
        };
    }

    /**
     * Obtenir le plafond pour une catégorie et une année (méthode statique)
     */
    public static function getPlafond(string $categorie, ?int $annee = null): float
    {
        $annee = $annee ?? now()->year;
        $parametre = self::pourAnnee($annee);

        if (!$parametre) {
            // Valeurs par défaut
            return match($categorie) {
                'Exécution' => 12000,
                'Maîtrise' => 15000,
                'Cadre' => 18000,
                'Hors cadre' => 18000,
                default => 12000,
            };
        }

        return $parametre->plafondPourCategorie($categorie);
    }

    /**
     * Scope pour les paramètres actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour ordonner par année décroissante
     */
    public function scopePlusRecents($query)
    {
        return $query->orderBy('annee', 'desc');
    }

    /**
     * Obtenir un tableau des plafonds par catégorie
     */
    public function toArray(): array
    {
        return [
            'annee' => $this->annee,
            'plafonds' => [
                'Exécution' => (float) $this->plafond_execution,
                'Maîtrise' => (float) $this->plafond_maitrise,
                'Cadre' => (float) $this->plafond_cadre,
                'Hors cadre' => (float) $this->plafond_hors_cadre,
            ],
            'actif' => $this->actif,
        ];
    }
}
