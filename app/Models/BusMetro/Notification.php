<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'bm_notifications';

    protected $fillable = [
        'destinataire_type', 'destinataire_id', 'titre', 'message',
        'type', 'lien', 'lu', 'lu_le'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'lu_le' => 'datetime',
    ];

    public function marquerCommeLu(): void
    {
        $this->update(['lu' => true, 'lu_le' => now()]);
    }

    public static function envoyer(string $destType, int $destId, string $titre, string $message, string $type = 'info', ?string $lien = null): self
    {
        return static::create([
            'destinataire_type' => $destType,
            'destinataire_id' => $destId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'lien' => $lien,
        ]);
    }
}
