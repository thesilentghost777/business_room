<?php

namespace App\Models\BusMetro;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $table = 'bm_users';

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'password',
        'role', 'photo_url', 'zone_affectation', 'is_active', 'derniere_connexion'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'derniere_connexion' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function adherentsEnroles()
    {
        return $this->hasMany(Adherent::class, 'agent_id');
    }

    public function cotisationsCollectees()
    {
        return $this->hasMany(Cotisation::class, 'agent_id');
    }

    public function remboursementsCollectes()
    {
        return $this->hasMany(Remboursement::class, 'agent_id');
    }

    // Helpers
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isDirection(): bool
    {
        return $this->role === 'direction';
    }
}
