<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class TypeCotisation extends Model
{
    protected $table = 'bm_types_cotisation';

    protected $fillable = [
        'code', 'nom', 'description', 'montant_minimum', 'montant_defaut',
        'frequence', 'obligatoire', 'donne_droit_soutien', 'actif'
    ];

    protected $casts = [
        'montant_minimum' => 'decimal:2',
        'montant_defaut' => 'decimal:2',
        'obligatoire' => 'boolean',
        'donne_droit_soutien' => 'boolean',
        'actif' => 'boolean',
    ];

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class, 'type_cotisation_id');
    }
}
