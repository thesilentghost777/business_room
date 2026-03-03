<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $table = 'bm_kits';

    protected $fillable = ['nom', 'description', 'prix', 'contenu', 'actif'];

    protected $casts = [
        'prix' => 'decimal:2',
        'contenu' => 'array',
        'actif' => 'boolean',
    ];

    public function achats()
    {
        return $this->hasMany(AchatKit::class, 'kit_id');
    }
}
