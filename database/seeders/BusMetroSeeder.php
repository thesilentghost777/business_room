<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusMetro\Configuration;
use App\Models\BusMetro\Profil;
use App\Models\BusMetro\Kit;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\CritereScoring;
use App\Models\BusMetro\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class BusMetroSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ADMIN PAR DÉFAUT =====

        $users = [
            [
                'nom'               => 'Dupont',
                'prenom'            => 'Admin',
                'email'             => 'admin@example.com',
                'telephone'         => '611223344',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'photo_url'         => null,
                'zone_affectation'  => null,
                'is_active'         => true,
                'derniere_connexion' => Carbon::now(),
                'remember_token'    => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nom'               => 'Martin',
                'prenom'            => 'Agent',
                'email'             => 'agent@example.com',
                'telephone'         => '622334455',
                'password'          => Hash::make('password'),
                'role'              => 'agent',
                'photo_url'         => null,
                'zone_affectation'  => 'Zone Nord',
                'is_active'         => true,
                'derniere_connexion' => null,
                'remember_token'    => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nom'               => 'Nguema',
                'prenom'            => 'Direction',
                'email'             => 'direction@example.com',
                'telephone'         => '633445566',
                'password'          => Hash::make('password'),
                'role'              => 'direction',
                'photo_url'         => null,
                'zone_affectation'  => null,
                'is_active'         => true,
                'derniere_connexion' => null,
                'remember_token'    => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
        ];

        DB::table('bm_users')->insert($users);

        // ===== CONFIGURATIONS =====
        $configs = [
            ['cle' => 'bonus_parrainage', 'valeur' => '2', 'type' => 'float', 'groupe' => 'parrainage', 'description' => 'Points bonus par filleul'],
            ['cle' => 'objectif_filleuls_scoring', 'valeur' => '5', 'type' => 'integer', 'groupe' => 'scoring', 'description' => 'Nombre de filleuls pour score max'],
            ['cle' => 'mois_anciennete_max_score', 'valeur' => '12', 'type' => 'integer', 'groupe' => 'scoring', 'description' => 'Mois pour score ancienneté max'],
            ['cle' => 'score_minimum_financement', 'valeur' => '60', 'type' => 'float', 'groupe' => 'financement', 'description' => 'Score minimum pour demander financement'],
            ['cle' => 'taux_penalite_retard', 'valeur' => '2', 'type' => 'float', 'groupe' => 'financement', 'description' => 'Taux de pénalité par jour de retard (%)'],
            ['cle' => 'taux_interet_defaut', 'valeur' => '5', 'type' => 'float', 'groupe' => 'financement', 'description' => 'Taux d\'intérêt par défaut (%)'],
            ['cle' => 'duree_financement_defaut', 'valeur' => '6', 'type' => 'integer', 'groupe' => 'financement', 'description' => 'Durée par défaut en mois'],
            ['cle' => 'nom_application', 'valeur' => 'Business Room', 'type' => 'string', 'groupe' => 'general', 'description' => 'Nom de l\'application'],
        ];

        foreach ($configs as $c) {
            Configuration::create($c);
        }

        // ===== PROFILS =====
        $profils = [
            [
                'code' => 'jeune_sans_emploi',
                'nom' => 'Jeune sans emploi',
                'description' => 'Jeunes à la recherche d\'un premier emploi ou d\'une activité génératrice de revenus',
                'documents_requis' => ['CNI ou Acte de naissance', 'Photo 4x4', 'CV'],
                'plafond_financement' => 500000,
            ],
            [
                'code' => 'petit_metier',
                'nom' => 'Petit métier',
                'description' => 'Artisans, commerçants informels et petits prestataires de services',
                'documents_requis' => ['CNI', 'Photo 4x4', 'Justificatif d\'activité', 'Photo du lieu d\'activité'],
                'plafond_financement' => 1000000,
            ],
            [
                'code' => 'salarie',
                'nom' => 'Salarié',
                'description' => 'Employés souhaitant développer une activité parallèle ou investir',
                'documents_requis' => ['CNI', 'Photo 4x4', 'Bulletin de salaire', 'Attestation de travail'],
                'plafond_financement' => 2000000,
            ],
            [
                'code' => 'entrepreneur',
                'nom' => 'Entrepreneur',
                'description' => 'Entrepreneurs avec une activité établie cherchant à se développer',
                'documents_requis' => ['CNI', 'Photo 4x4', 'Registre de commerce', 'Plan d\'affaires', 'États financiers'],
                'plafond_financement' => 5000000,
            ],
        ];

        foreach ($profils as $p) {
            Profil::create($p);
        }

        // ===== KITS =====
        Kit::create([
            'nom' => 'Kit d\'adhésion Standard',
            'description' => 'Kit complet pour rejoindre le programme Business Room',
            'prix' => 15000,
            'contenu' => [
                'Carte CASS',
                'Cahier de recettes',
                'Guide du membre',
                'Supports pédagogiques',
                'Badge membre'
            ],
        ]);

        // ===== TYPES DE COTISATION =====
        TypeCotisation::create([
            'code' => 'NKD',
            'nom' => 'Cotisation Journalière NKD',
            'description' => 'Cotisation journalière obligatoire pour maintenir votre statut actif et améliorer votre score',
            'montant_minimum' => 500,
            'montant_defaut' => 1000,
            'frequence' => 'journalier',
            'obligatoire' => true,
            'donne_droit_soutien' => false,
        ]);

        TypeCotisation::create([
            'code' => 'NKH',
            'nom' => 'Cotisation Stratégique NKH',
            'description' => 'Cotisation stratégique donnant droit à un soutien non remboursable',
            'montant_minimum' => 2000,
            'montant_defaut' => 5000,
            'frequence' => 'hebdomadaire',
            'obligatoire' => false,
            'donne_droit_soutien' => true,
        ]);

        // ===== CRITERES DE SCORING =====
        $criteres = [
            [
                'code' => 'regularite_cotisations',
                'nom' => 'Régularité des cotisations',
                'description' => 'Évalue la régularité des cotisations NKD par rapport au nombre de jours depuis l\'adhésion',
                'poids' => 5,
                'max_points' => 20,
                'ordre' => 1,
            ],
            [
                'code' => 'parrainage',
                'nom' => 'Parrainage',
                'description' => 'Nombre de filleuls actifs parrainés',
                'poids' => 2,
                'max_points' => 20,
                'ordre' => 2,
            ],
            [
                'code' => 'anciennete',
                'nom' => 'Ancienneté',
                'description' => 'Durée de participation au programme',
                'poids' => 2,
                'max_points' => 20,
                'ordre' => 3,
            ],
            [
                'code' => 'activite_economique',
                'nom' => 'Activité économique',
                'description' => 'Évaluation de l\'activité économique déclarée et documentée',
                'poids' => 3,
                'max_points' => 20,
                'ordre' => 4,
            ],
            [
                'code' => 'discipline_financiere',
                'nom' => 'Discipline financière',
                'description' => 'Historique de remboursement et comportement financier',
                'poids' => 4,
                'max_points' => 20,
                'ordre' => 5,
            ],
            [
                'code' => 'carnet_recettes',
                'nom' => 'Tenue du carnet de recettes',
                'description' => 'Régularité de la tenue du carnet de recettes digital',
                'poids' => 2,
                'max_points' => 20,
                'ordre' => 6,
            ],
        ];


        foreach ($criteres as $c) {
            CritereScoring::create($c);
        }
    }
}
