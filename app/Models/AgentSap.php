<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentSap extends Model
{
    protected $table = 'agents_sap';

    protected $fillable = [
        'matricule',
        'nom_prenom',  // Nom complet depuis SAP
        'nom',
        'prenom',
        'cin',
        'date_recrutement',
        'date_naissance',
        'dp_affectation',
        'statut',
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
        'date_import_sap',
        'fichier_import',
        'import_par',
    ];

    protected $casts = [
        'date_recrutement' => 'date',
        'date_naissance' => 'date',
        'date_entree' => 'date',
        'date_sortie' => 'date',
        'date_retraite' => 'date',
        'date_affiliation' => 'date',
        'date_import_sap' => 'datetime',
    ];

    /**
     * Relation avec l'agent CGS correspondant
     */
    public function agentCgs()
    {
        return $this->belongsTo(Agent::class, 'matricule', 'matricule');
    }

    /**
     * Relation avec les ayants droit SAP
     */
    public function ayantsDroitSap()
    {
        return $this->hasMany(AyantDroitSap::class, 'matricule_agent', 'matricule');
    }

    /**
     * Vérifier si les données correspondent à celles de CGS
     */
    public function correspondACgs(): bool
    {
        $agentCgs = $this->agentCgs;

        if (!$agentCgs) {
            return false;
        }

        // Comparer avec nom_prenom SAP ou nom+prenom séparés
        $nomSap = $this->nom_prenom ?? trim(($this->nom ?? '') . ' ' . ($this->prenom ?? ''));
        $nomCgs = trim(($agentCgs->nom ?? '') . ' ' . ($agentCgs->prenom ?? ''));

        return $nomSap === $nomCgs
            && $this->statut === $agentCgs->statut
            && $this->date_naissance == $agentCgs->date_naissance;
    }

    /**
     * Obtenir les différences avec CGS
     */
    public function differencesAvecCgs(): array
    {
        $agentCgs = $this->agentCgs;

        if (!$agentCgs) {
            return [
                'statut' => 'Absent dans CGS',
                'details' => [],
            ];
        }

        $differences = [];

        // Comparer avec nom_prenom SAP ou nom+prenom séparés
        $nomSap = $this->nom_prenom ?? trim(($this->nom ?? '') . ' ' . ($this->prenom ?? ''));
        $nomCgs = trim(($agentCgs->nom ?? '') . ' ' . ($agentCgs->prenom ?? ''));

        if ($nomSap !== $nomCgs) {
            $differences['nom'] = [
                'sap' => $nomSap,
                'cgs' => $nomCgs,
            ];
        }

        if ($this->statut !== $agentCgs->statut) {
            $differences['statut'] = [
                'sap' => $this->statut,
                'cgs' => $agentCgs->statut,
            ];
        }

        if ($this->date_naissance != $agentCgs->date_naissance) {
            $differences['date_naissance'] = [
                'sap' => $this->date_naissance,
                'cgs' => $agentCgs->date_naissance,
            ];
        }

        return empty($differences) ? [] : [
            'statut' => 'Différences détectées',
            'details' => $differences,
        ];
    }
}
