<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Financement extends Model
{
    protected $table = 'bm_financements';

    protected $fillable = [
        'reference', 'demande_id', 'adherent_id', 'session_id',
        'montant_accorde', 'taux_interet', 'duree_mois', 'montant_mensuel',
        'date_debut', 'date_fin', 'montant_total_du', 'montant_rembourse',
        'penalites_totales', 'statut', 'approuve_par'
    ];

    protected $casts = [
        'montant_accorde' => 'decimal:2',
        'taux_interet' => 'decimal:2',
        'montant_mensuel' => 'decimal:2',
        'montant_total_du' => 'decimal:2',
        'montant_rembourse' => 'decimal:2',
        'penalites_totales' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($f) {
            if (empty($f->reference)) {
                $f->reference = 'FIN' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
            }
        });
    }

    public function demande()
    {
        return $this->belongsTo(DemandeFinancement::class, 'demande_id');
    }

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionFinancement::class, 'session_id');
    }

    public function echeanciers()
    {
        return $this->hasMany(Echeancier::class, 'financement_id');
    }

    public function remboursements()
    {
        return $this->hasMany(Remboursement::class, 'financement_id');
    }

    public function approbateur()
    {
        return $this->belongsTo(User::class, 'approuve_par');
    }

    public function getResteAPayerAttribute(): float
    {
        return (float) ($this->montant_total_du - $this->montant_rembourse);
    }

    public function getTauxRemboursementAttribute(): float
    {
        if ($this->montant_total_du <= 0) return 0;
        return round(($this->montant_rembourse / $this->montant_total_du) * 100, 2);
    }

    public function getProchainEcheancierAttribute()
    {
        return $this->echeanciers()
            ->whereIn('statut', ['a_venir', 'en_attente', 'retard'])
            ->orderBy('date_echeance')
            ->first();
    }
}
