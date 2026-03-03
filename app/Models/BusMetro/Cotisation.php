<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    protected $table = 'bm_cotisations';

    protected $fillable = [
        'adherent_id', 'type_cotisation_id', 'montant', 'date_cotisation',
        'mode_paiement', 'reference_paiement', 'token_paiement',
        'statut', 'agent_id', 'commentaire'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_cotisation' => 'date',
    ];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function typeCotisation()
    {
        return $this->belongsTo(TypeCotisation::class, 'type_cotisation_id');
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
