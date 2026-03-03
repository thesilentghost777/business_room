<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'bm_audit_logs';

    protected $fillable = [
        'action', 'model_type', 'model_id',
        'user_type', 'user_id',
        'ancien_valeurs', 'nouvelles_valeurs', 'ip_address'
    ];

    protected $casts = [
        'ancien_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
    ];

    public static function log(string $action, $model = null, ?array $anciennes = null, ?array $nouvelles = null): self
    {
        $userType = 'user';
        $userId = null;

        if (auth('busmetro')->check()) {
            $userId = auth('busmetro')->id();
        } elseif (auth('adherent')->check()) {
            $userType = 'adherent';
            $userId = auth('adherent')->id();
        }

        return static::create([
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'user_type' => $userType,
            'user_id' => $userId ?? 0,
            'ancien_valeurs' => $anciennes,
            'nouvelles_valeurs' => $nouvelles,
            'ip_address' => request()->ip(),
        ]);
    }
}
