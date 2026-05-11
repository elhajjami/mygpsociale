<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcartSapCgs extends Model
{
    protected $table = 'ecarts_sap_cgs';

    protected $fillable = [
        'type_ecart',
        'matricule',
        'donnee_sap',
        'donnee_cgs',
        'details',
        'date_detection',
        'traite',
        'date_traitement',
        'traite_by',
    ];

    protected $casts = [
        'date_detection' => 'date',
        'date_traitement' => 'datetime',
        'traite' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a traité l'écart
     */
    public function traiteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_by');
    }

    /**
     * Marquer l'écart comme traité
     */
    public function marquerTraite(int $userId): void
    {
        $this->traite = true;
        $this->date_traitement = now();
        $this->traite_by = $userId;
        $this->save();
    }

    /**
     * Scope pour les écarts non traités
     */
    public function scopeNonTraites($query)
    {
        return $query->where('traite', false);
    }

    /**
     * Scope pour les écarts traités
     */
    public function scopeTraites($query)
    {
        return $query->where('traite', true);
    }

    /**
     * Scope pour les écarts par type
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type_ecart', $type);
    }

    /**
     * Scope pour les écarts par matricule
     */
    public function scopeParMatricule($query, string $matricule)
    {
        return $query->where('matricule', $matricule);
    }

    /**
     * Scope pour les écarts récents
     */
    public function scopeRecent($query, int $jours = 7)
    {
        return $query->where('date_detection', '>=', now()->subDays($jours));
    }

    /**
     * Créer un écart
     */
    public static function creer(
        string $type,
        string $matricule,
        ?string $donneeSap = null,
        ?string $donneeCgs = null,
        ?string $details = null
    ): self {
        return self::create([
            'type_ecart' => $type,
            'matricule' => $matricule,
            'donnee_sap' => $donneeSap,
            'donnee_cgs' => $donneeCgs,
            'details' => $details,
            'date_detection' => now(),
            'traite' => false,
        ]);
    }
}
