<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class AchatKit extends Model
{
    protected $table = 'bm_achats_kits';

    protected $fillable = [
        'adherent_id', 'kit_id', 'montant', 'reference_paiement',
        'token_paiement', 'statut', 'moyen_paiement', 'agent_id'
    ];

    protected $casts = ['montant' => 'decimal:2'];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function kit()
    {
        return $this->belongsTo(Kit::class, 'kit_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
