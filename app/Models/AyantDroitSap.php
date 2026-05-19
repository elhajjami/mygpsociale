<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AyantDroitSap extends Model
{
    protected $table = 'ayants_droit_sap';

    protected $fillable = [
        'matricule_agent',
        'type',
        'nom_prenom',
        'date_naissance',
        'cin',
        'statut',
        'observations',
        'date_import_sap',
        'fichier_import',
        'import_par',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_import_sap' => 'datetime',
    ];

    /**
     * Relation avec l'agent SAP
     */
    public function agentSap()
    {
        return $this->belongsTo(AgentSap::class, 'matricule_agent', 'matricule');
    }

    /**
     * Trouver l'ayant droit CGS correspondant
     */
    public function ayantDroitCgs()
    {
        $agentCgs = Agent::where('matricule', $this->matricule_agent)->first();

        if (!$agentCgs) {
            return null;
        }

        return AyantDroit::where('agent_id', $agentCgs->id)
            ->where('type', $this->type)
            ->first();
    }

    /**
     * Vérifier si les données correspondent à celles de CGS
     */
    public function correspondACgs(): bool
    {
        $ayantCgs = $this->ayantDroitCgs();

        if (!$ayantCgs) {
            return false;
        }

        return $this->nom_prenom === $ayantCgs->nom_prenom
            && $this->date_naissance == $ayantCgs->date_naissance;
    }
}
