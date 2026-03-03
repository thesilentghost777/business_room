<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'bm_scores';

    protected $fillable = [
        'adherent_id', 'session_id', 'critere_id', 'points', 'details'
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'details' => 'array',
    ];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionFinancement::class, 'session_id');
    }

    public function critere()
    {
        return $this->belongsTo(CritereScoring::class, 'critere_id');
    }
}
