<?php

namespace App\Models\BusMetro;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Adherent extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $table = 'bm_adherents';
    protected $guard = 'adherent';

    protected $fillable = [
        'matricule', 'nom', 'prenom', 'telephone', 'email', 'password',
        'date_naissance', 'sexe', 'photo_url',
        'profil_id', 'activite_economique', 'description_activite', 'revenu_mensuel',
        'ville', 'quartier', 'adresse',
        'piece_identite_type', 'piece_identite_numero', 'piece_identite_url',
        'document_activite_url', 'photo_identite_url',
        'code_parrainage', 'parrain_id',
        'kit_achete', 'date_adhesion',
        'score_actuel', 'agent_id', 'statut'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_naissance' => 'date',
        'date_adhesion' => 'datetime',
        'kit_achete' => 'boolean',
        'revenu_mensuel' => 'decimal:2',
        'score_actuel' => 'decimal:2',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($adherent) {
            if (empty($adherent->matricule)) {
                $adherent->matricule = static::generateMatricule();
            }
            if (empty($adherent->code_parrainage)) {
                $adherent->code_parrainage = static::generateCodeParrainage();
            }
        });
    }

    public static function generateMatricule(): string
    {
        $prefix = 'BM';
        $year = date('y');
        $count = static::withTrashed()->whereYear('created_at', date('Y'))->count() + 1;
        return $prefix . $year . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public static function generateCodeParrainage(): string
    {
        do {
            $code = 'BR' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (static::where('code_parrainage', $code)->exists());
        return $code;
    }

    // Relations
    public function profil()
    {
        return $this->belongsTo(Profil::class, 'profil_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function parrain()
    {
        return $this->belongsTo(Adherent::class, 'parrain_id');
    }

    public function filleuls()
    {
        return $this->hasMany(Adherent::class, 'parrain_id');
    }

    public function parrainages()
    {
        return $this->hasMany(Parrainage::class, 'parrain_id');
    }

    public function achatKit()
    {
        return $this->hasOne(AchatKit::class, 'adherent_id')->where('statut', 'paye');
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class, 'adherent_id');
    }

    public function cotisationsValides()
    {
        return $this->hasMany(Cotisation::class, 'adherent_id')->where('statut', 'valide');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'adherent_id');
    }

    public function demandes()
    {
        return $this->hasMany(DemandeFinancement::class, 'adherent_id');
    }

    public function financements()
    {
        return $this->hasMany(Financement::class, 'adherent_id');
    }

    public function financementEnCours()
    {
        return $this->hasOne(Financement::class, 'adherent_id')->where('statut', 'en_cours');
    }

    public function carnetsRecettes()
    {
        return $this->hasMany(CarnetRecette::class, 'adherent_id');
    }

    public function soutiens()
    {
        return $this->hasMany(SoutienNkh::class, 'adherent_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'destinataire_id')
            ->where('destinataire_type', 'adherent');
    }

    // Helpers
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif' && $this->kit_achete;
    }

    public function peutDemanderFinancement(): bool
    {
        return $this->estActif()
            && !$this->financementEnCours
            && $this->score_actuel >= (float) Configuration::get('score_minimum_financement', 60);
    }

    public function getNombreCotisationsAttribute(): int
    {
        return $this->cotisationsValides()->count();
    }

    public function getTotalCotisationsAttribute(): float
    {
        return (float) $this->cotisationsValides()->sum('montant');
    }
}
