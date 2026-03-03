<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Echeancier extends Model
{
    protected $table = 'bm_echeanciers';

    protected $fillable = [
        'financement_id', 'numero_echeance', 'montant_du', 'montant_paye',
        'date_echeance', 'date_paiement', 'penalite', 'statut'
    ];

    protected $casts = [
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'penalite' => 'decimal:2',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    public function financement()
    {
        return $this->belongsTo(Financement::class, 'financement_id');
    }

    public function remboursements()
    {
        return $this->hasMany(Remboursement::class, 'echeancier_id');
    }

    public function estEnRetard(): bool
    {
        return $this->statut !== 'paye' && $this->date_echeance->isPast();
    }

    public function getResteAPayerAttribute(): float
    {
        return max(0, (float) ($this->montant_du + $this->penalite - $this->montant_paye));
    }
}
