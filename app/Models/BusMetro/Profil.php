<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'bm_profils';

    protected $fillable = [
        'code', 'nom', 'description', 'documents_requis',
        'plafond_financement', 'conditions_scoring', 'actif'
    ];

    protected $casts = [
        'documents_requis' => 'array',
        'conditions_scoring' => 'array',
        'plafond_financement' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function adherents()
    {
        return $this->hasMany(Adherent::class, 'profil_id');
    }
}
