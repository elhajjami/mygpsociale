<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Plafond annuel par catégorie
 */
class PlafondCategorie extends Model
{
    protected $table = 'plafonds_categories';

    protected $fillable = [
        'categorie',
        'plafond',
    ];

    protected $casts = [
        'plafond' => 'decimal:2',
    ];

    /**
     * Obtenir le plafond pour une catégorie donnée
     */
    public static function getPlafond(string $categorie): float
    {
        $plafond = self::where('categorie', $categorie)->first();

        return $plafond ? $plafond->plafond : 12000; // Valeur par défaut
    }

    /**
     * Obtenir tous les plafonds
     */
    public static function tousLesPlafonds(): array
    {
        return self::all()->pluck('plafond', 'categorie')->toArray();
    }

    /**
     * Scope pour rechercher par catégorie
     */
    public function scopeParCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }
}
