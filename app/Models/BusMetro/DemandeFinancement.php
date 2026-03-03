<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class DemandeFinancement extends Model
{
    protected $table = 'bm_demandes_financement';

    protected $fillable = [
        'adherent_id', 'session_id', 'montant_demande', 'motif', 'description_projet',
        'score_total', 'rang', 'statut', 'commentaire_direction', 'validee_par', 'date_validation'
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2',
        'score_total' => 'decimal:2',
        'date_validation' => 'datetime',
    ];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionFinancement::class, 'session_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validee_par');
    }

    public function financement()
    {
        return $this->hasOne(Financement::class, 'demande_id');
    }
}
