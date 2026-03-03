<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class TransactionPaiement extends Model
{
    protected $table = 'bm_transactions_paiement';

    protected $fillable = [
        'reference_interne', 'type', 'adherent_id',
        'payable_type', 'payable_id',
        'montant', 'frais', 'token_paiement',
        'numero_telephone', 'nom_client', 'url_paiement',
        'statut', 'moyen_paiement', 'numero_transaction_externe',
        'webhook_data', 'personal_info'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'frais' => 'decimal:2',
        'webhook_data' => 'array',
        'personal_info' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tx) {
            if (empty($tx->reference_interne)) {
                $tx->reference_interne = 'BM' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 6));
            }
        });
    }

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function estPayee(): bool
    {
        return $this->statut === 'paid';
    }

    public function estEnAttente(): bool
    {
        return $this->statut === 'pending';
    }
}
