<?php

namespace App\Models\BusMetro;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'bm_configurations';

    protected $fillable = ['cle', 'valeur', 'type', 'groupe', 'description'];

    public static function get(string $cle, $default = null)
    {
        $config = static::where('cle', $cle)->first();
        if (!$config) return $default;

        return match ($config->type) {
            'integer' => (int) $config->valeur,
            'boolean' => filter_var($config->valeur, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($config->valeur, true),
            'float' => (float) $config->valeur,
            default => $config->valeur,
        };
    }

    public static function set(string $cle, $valeur, string $type = 'string', string $groupe = 'general', ?string $description = null): void
    {
        if (is_array($valeur)) {
            $valeur = json_encode($valeur);
            $type = 'json';
        }

        static::updateOrCreate(
            ['cle' => $cle],
            ['valeur' => $valeur, 'type' => $type, 'groupe' => $groupe, 'description' => $description]
        );
    }
}
