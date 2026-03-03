<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Parrainage extends Model
{
    protected $table = 'bm_parrainages';

    protected $fillable = [
        'parrain_id', 'filleul_id', 'date_parrainage', 'statut', 'bonus_points'
    ];

    protected $casts = [
        'date_parrainage' => 'date',
        'bonus_points' => 'decimal:2',
    ];

    public function parrain()
    {
        return $this->belongsTo(Adherent::class, 'parrain_id');
    }

    public function filleul()
    {
        return $this->belongsTo(Adherent::class, 'filleul_id');
    }
}
