<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class CarnetRecette extends Model
{
    protected $table = 'bm_carnets_recettes';

    protected $fillable = [
        'adherent_id', 'date_recette', 'montant_recette', 'montant_depense',
        'description', 'categorie', 'photo_justificatif_url', 'valide_par', 'valide'
    ];

    protected $casts = [
        'date_recette' => 'date',
        'montant_recette' => 'decimal:2',
        'montant_depense' => 'decimal:2',
        'valide' => 'boolean',
    ];

    public function adherent()
    {
        return $this->belongsTo(Adherent::class, 'adherent_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getBeneficeAttribute(): float
    {
        return (float) ($this->montant_recette - $this->montant_depense);
    }
}
