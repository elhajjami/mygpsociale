<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\PlafondAnnuel;
use App\Models\PlafondAnnuelAgent;

class Agent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'cin',
        'date_recrutement',
        'date_naissance',
        'dp_affectation',
        'statut',
        'situation_administrative', // Pour stocker les codes de mesure (DE, SU, etc.)
        'date_entree',
        'date_sortie',
        'date_retraite',
        'numero_immatriculation',
        'date_affiliation',
        'compte_bancaire',
        'cle_bancaire',
        'banque',
        'info_banque',
        'observations',
        'user_id',
        'statut_calcule', // Statut calculé depuis la situation
    ];

    protected $casts = [
        'date_recrutement' => 'date',
        'date_naissance' => 'date',
        'date_entree' => 'date',
        'date_sortie' => 'date',
        'date_retraite' => 'date',
        'date_affiliation' => 'date',
    ];

    /**
     * Relation avec les ayants droit
     */
    public function ayantsDroit(): HasMany
    {
        return $this->hasMany(AyantDroit::class);
    }

    /**
     * Relation avec les demandes PEC
     */
    public function demandesPEC(): HasMany
    {
        return $this->hasMany(DemandePEC::class);
    }

    /**
     * Relation avec les plafonds annuels par catégorie
     */
    public function plafondsAnnuels(): HasMany
    {
        return $this->hasMany(PlafondAnnuel::class);
    }

    /**
     * Relation avec les plafonds annuels par année de consommation
     */
    public function plafondsAnnuelsAgents(): HasMany
    {
        return $this->hasMany(PlafondAnnuelAgent::class);
    }

    /**
     * Obtenir le plafond annuel de l'année courante
     */
    public function getPlafondAnneeCouranteAttribute(): ?PlafondAnnuelAgent
    {
        return $this->plafondsAnnuelsAgents()
            ->where('annee', now()->year)
            ->first();
    }

    /**
     * Relation avec les retenues paie
     */
    public function retenuesPaie(): HasMany
    {
        return $this->hasMany(RetenuePaie::class);
    }

    /**
     * Relation avec les mesures disciplinaires
     */
    public function mesures(): HasMany
    {
        return $this->hasMany(Mesure::class, 'matricule', 'matricule');
    }

    /**
     * Relation avec l'historique des carrières
     */
    public function carrieres(): HasMany
    {
        return $this->hasMany(Carriere::class, 'matricule', 'matricule');
    }

    /**
     * Relation avec l'utilisateur (DP RH)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir l'âge de l'agent
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
        return trim($this->nom . ' ' . $this->prenom);
    }

    /**
     * Obtenir le plafond annuel selon la catégorie (depuis la carrière)
     */
    public function getPlafondAnnuelAttribute(): float
    {
        $categorie = $this->getCategorieFromCarriere();
        return PlafondCategorie::getPlafond($categorie);
    }

    /**
     * Obtenir la carrière actuelle depuis la table carrieres
     */
    public function getCarriereActuelleAttribute(): ?Carriere
    {
        return $this->carrieres()
            ->actuelle()
            ->first();
    }

    /**
     * Obtenir la dernière mesure depuis la table mesures
     */
    public function getDerniereMesureAttribute(): ?Mesure
    {
        return $this->mesures()
            ->enCours()
            ->latest('date_debut')
            ->first();
    }

    /**
     * Obtenir la catégorie depuis la carrière actuelle
     */
    public function getCategorieFromCarriere(): string
    {
        $carriere = $this->carriere_actuelle;

        if ($carriere && $carriere->cat) {
            return $carriere->categorie_complete;
        }

        return $this->categorie ?? 'Exécution';
    }

    /**
     * Calculer automatiquement la catégorie depuis le niveau (Carrière)
     */
    public function calculerCategorie(): string
    {
        return $this->getCategorieFromCarriere();
    }

    /**
     * Calculer automatiquement le statut depuis la situation administrative (Mesure)
     */
    public function calculerStatut(): string
    {
        // Si un statut est déjà défini et n'est pas "Actif", le conserver
        if (in_array($this->statut, ['Retraité', 'Sorti', 'Décédé', 'Suspendu'])) {
            return $this->statut;
        }

        // Vérifier la situation administrative
        if (!empty($this->situation_administrative)) {
            $analyse = Mesure::analyserSituation($this->situation_administrative);

            if ($analyse['est_sortie'] || $analyse['statut'] !== 'Actif') {
                return $analyse['statut'];
            }
        }

        // Vérifier la date de retraite
        if ($this->date_retraite && $this->date_retraite->isPast()) {
            return 'Retraité';
        }

        // Vérifier la date de sortie
        if ($this->date_sortie && $this->date_sortie->isPast()) {
            return 'Sorti';
        }

        return 'Actif';
    }

    /**
     * Mettre à jour le statut calculé
     */
    public function mettreAJourCalculs(): void
    {
        $this->statut_calcule = $this->calculerStatut();
        $this->saveQuietly();
    }

    /**
     * Vérifier si l'agent peut bénéficier d'une PEC
     */
    public function peutBeneficierPEC(): bool
    {
        $statut = $this->statut_calcule ?? $this->calculerStatut();

        if ($statut !== 'Actif') {
            return false;
        }

        // Vérifier si bloqué par une mesure
        if (!empty($this->situation_administrative)) {
            if (Mesure::bloquesPEC($this->situation_administrative)) {
                return false;
            }
        }

        // Vérifier l'âge (limite à 63 ans pour les ayants droit, selon les règles)
        return $this->age < 63;
    }

    /**
     * Scope pour les agents actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'Actif');
    }

    /**
     * Scope pour rechercher par matricule ou nom
     */
    public function scopeRechercher($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('matricule', 'like', "%{$terme}%")
              ->orWhere('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('cin', 'like', "%{$terme}%");
        });
    }
}
