<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'matricule', 'telephone', 'dp_affectation', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec les demandes créées
     */
    public function demandesCreees(): HasMany
    {
        return $this->hasMany(DemandePEC::class, 'created_by');
    }

    /**
     * Relation avec les demandes validées
     */
    public function demandesValidees(): HasMany
    {
        return $this->hasMany(DemandePEC::class, 'validated_by');
    }

    /**
     * Vérifier si l'utilisateur est un admin CGS
     */
    public function isAdminCgs(): bool
    {
        return $this->hasRole('Admin CGS');
    }

    /**
     * Vérifier si l'utilisateur est un gestionnaire CGS
     */
    public function isGestionnaireCgs(): bool
    {
        return $this->hasRole('Gestionnaire CGS');
    }

    /**
     * Vérifier si l'utilisateur est une DP RH
     */
    public function isDpRh(): bool
    {
        return $this->hasRole('DP RH');
    }
}
