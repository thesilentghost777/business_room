<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Remboursement extends Model
{
    protected $table = 'bm_remboursements';

    protected $fillable = [
        'financement_id', 'echeancier_id', 'adherent_id', 'montant',
        'mode_paiement', 'reference_paiement', 'token_paiement',
        'statut', 'agent_id', 'commentaire'
    ];

    protected $casts = ['montant' => 'decimal:2'];

    public function financement()
    {
        return $this->belongsTo(Financement::class, 'financement_id');
    }

    public function echeancier()
    {
        return $this->belongsTo(Echeancier::class, 'echeancier_id');
    }

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function transaction()
    {
        return $this->morphOne(TransactionPaiement::class, 'payable');
    }
}
