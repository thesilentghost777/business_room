<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class SoutienNkh extends Model
{
    protected $table = 'bm_soutiens_nkh';

    protected $fillable = [
        'adherent_id', 'montant', 'motif', 'statut', 'approuve_par', 'date_versement'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_versement' => 'date',
    ];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function approbateur()
    {
        return $this->belongsTo(User::class, 'approuve_par');
    }
}
