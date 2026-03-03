<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class SessionFinancement extends Model
{
    protected $table = 'bm_sessions_financement';

    protected $fillable = [
        'nom', 'trimestre', 'annee', 'date_debut_candidature', 'date_fin_candidature',
        'date_selection', 'date_debut_financement', 'budget_total',
        'nombre_beneficiaires_max', 'score_minimum', 'statut', 'creee_par'
    ];

    protected $casts = [
        'date_debut_candidature' => 'date',
        'date_fin_candidature' => 'date',
        'date_selection' => 'date',
        'date_debut_financement' => 'date',
        'budget_total' => 'decimal:2',
        'score_minimum' => 'decimal:2',
    ];

    public function createur()
    {
        return $this->belongsTo(User::class, 'creee_par');
    }

    public function demandes()
    {
        return $this->hasMany(DemandeFinancement::class, 'session_id');
    }

    public function financements()
    {
        return $this->hasMany(Financement::class, 'session_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'session_id');
    }

    public function estOuverteAuxCandidatures(): bool
    {
        return $this->statut === 'candidature'
            && now()->between($this->date_debut_candidature, $this->date_fin_candidature);
    }

    public function getNombreDemandesAttribute(): int
    {
        return $this->demandes()->count();
    }

    public function getMontantFinanceAttribute(): float
    {
        return (float) $this->financements()->sum('montant_accorde');
    }
}
