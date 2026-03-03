<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class CritereScoring extends Model
{
    protected $table = 'bm_criteres_scoring';

    protected $fillable = [
        'code', 'nom', 'description', 'poids', 'max_points', 'regles', 'actif', 'ordre'
    ];

    protected $casts = [
        'regles' => 'array',
        'actif' => 'boolean',
    ];

    public function scores()
    {
        return $this->hasMany(Score::class, 'critere_id');
    }
}
