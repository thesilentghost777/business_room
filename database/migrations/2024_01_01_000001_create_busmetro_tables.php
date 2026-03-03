<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ================================================================
        // CONFIGURATIONS
        // ================================================================
        Schema::create('bm_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->text('valeur');
            $table->string('type')->default('string'); // string, integer, float, boolean, json
            $table->string('groupe')->default('general');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ================================================================
        // UTILISATEURS INTERNES (Admin, Agent, Direction)
        // ================================================================
        Schema::create('bm_users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'agent', 'direction'])->default('agent');
            $table->string('photo_url')->nullable();
            $table->string('zone_affectation')->nullable(); // pour agents
            $table->boolean('is_active')->default(true);
            $table->timestamp('derniere_connexion')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // ================================================================
        // PROFILS ADHERENTS (4 catégories)
        // ================================================================
        Schema::create('bm_profils', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // jeune_sans_emploi, petit_metier, salarie, entrepreneur
            $table->string('nom');
            $table->text('description')->nullable();
            $table->json('documents_requis'); // liste des documents nécessaires
            $table->decimal('plafond_financement', 12, 2)->default(0);
            $table->json('conditions_scoring')->nullable(); // règles spécifiques
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // ================================================================
        // ADHERENTS
        // ================================================================
        Schema::create('bm_adherents', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->string('photo_url')->nullable();

            // Profil
            $table->foreignId('profil_id')->nullable()->constrained('bm_profils');
            $table->string('activite_economique')->nullable();
            $table->text('description_activite')->nullable();
            $table->decimal('revenu_mensuel', 12, 2)->default(0);

            // Localisation
            $table->string('ville')->nullable();
            $table->string('quartier')->nullable();
            $table->string('adresse')->nullable();

            // Documents
            $table->string('piece_identite_type')->nullable(); // CNI, passeport, etc.
            $table->string('piece_identite_numero')->nullable();
            $table->string('piece_identite_url')->nullable();
            $table->string('document_activite_url')->nullable();
            $table->string('photo_identite_url')->nullable();

            // Parrainage
            $table->string('code_parrainage')->unique();
            $table->foreignId('parrain_id')->nullable()->constrained('bm_adherents');

            // Kit
            $table->boolean('kit_achete')->default(false);
            $table->timestamp('date_adhesion')->nullable();

            // Scoring
            $table->decimal('score_actuel', 5, 2)->default(0);

            // Agent qui l'a enrôlé
            $table->foreignId('agent_id')->nullable()->constrained('bm_users');

            // Status
            $table->enum('statut', ['en_attente', 'actif', 'suspendu', 'radie'])->default('en_attente');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // ================================================================
        // KITS D'ADHESION
        // ================================================================
        Schema::create('bm_kits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 10, 2);
            $table->json('contenu'); // ["Carte CASS", "Cahier de recettes", "Supports pédagogiques"]
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // ================================================================
        // ACHATS DE KITS
        // ================================================================
        Schema::create('bm_achats_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents')->onDelete('cascade');
            $table->foreignId('kit_id')->constrained('bm_kits');
            $table->decimal('montant', 10, 2);
            $table->string('reference_paiement')->nullable();
            $table->string('token_paiement')->nullable();
            $table->enum('statut', ['en_attente', 'paye', 'echoue'])->default('en_attente');
            $table->string('moyen_paiement')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('bm_users');
            $table->timestamps();
        });

        // ================================================================
        // TYPES DE COTISATION (NKD, NKH)
        // ================================================================
        Schema::create('bm_types_cotisation', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // NKD, NKH
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('montant_minimum', 10, 2)->default(0);
            $table->decimal('montant_defaut', 10, 2)->default(0);
            $table->enum('frequence', ['journalier', 'hebdomadaire', 'mensuel'])->default('journalier');
            $table->boolean('obligatoire')->default(false);
            $table->boolean('donne_droit_soutien')->default(false); // NKH donne droit soutien non remboursable
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // ================================================================
        // COTISATIONS
        // ================================================================
        Schema::create('bm_cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents')->onDelete('cascade');
            $table->foreignId('type_cotisation_id')->constrained('bm_types_cotisation');
            $table->decimal('montant', 10, 2);
            $table->date('date_cotisation');
            $table->string('mode_paiement')->nullable(); // especes, mobile_money, moneyfusion
            $table->string('reference_paiement')->nullable();
            $table->string('token_paiement')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'echoue', 'annule'])->default('en_attente');
            $table->foreignId('agent_id')->nullable()->constrained('bm_users');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });

        // ================================================================
        // CRITERES DE SCORING
        // ================================================================
        Schema::create('bm_criteres_scoring', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->integer('poids')->default(1); // coefficient
            $table->integer('max_points')->default(20);
            $table->json('regles')->nullable(); // règles de calcul
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

          // ================================================================
        // SESSIONS DE FINANCEMENT (trimestrielles)
        // ================================================================
        Schema::create('bm_sessions_financement', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->integer('trimestre'); // 1,2,3,4
            $table->integer('annee');
            $table->date('date_debut_candidature');
            $table->date('date_fin_candidature');
            $table->date('date_selection')->nullable();
            $table->date('date_debut_financement')->nullable();
            $table->decimal('budget_total', 15, 2)->default(0);
            $table->integer('nombre_beneficiaires_max')->default(0);
            $table->decimal('score_minimum', 5, 2)->default(60);
            $table->enum('statut', [
                'preparation', 'candidature', 'selection',
                'validation', 'financement', 'cloturee'
            ])->default('preparation');
            $table->foreignId('creee_par')->nullable()->constrained('bm_users');
            $table->timestamps();
        });

        // ================================================================
        // SCORES DES ADHERENTS
        // ================================================================
        Schema::create('bm_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('bm_sessions_financement');
            $table->foreignId('critere_id')->constrained('bm_criteres_scoring');
            $table->decimal('points', 5, 2)->default(0);
            $table->json('details')->nullable(); // détails du calcul
            $table->timestamps();
        });



        // ================================================================
        // DEMANDES DE FINANCEMENT
        // ================================================================
        Schema::create('bm_demandes_financement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('bm_sessions_financement')->onDelete('cascade');
            $table->decimal('montant_demande', 12, 2);
            $table->text('motif');
            $table->text('description_projet')->nullable();
            $table->decimal('score_total', 5, 2)->default(0);
            $table->integer('rang')->nullable();
            $table->enum('statut', [
                'en_attente', 'pre_selectionnee', 'selectionnee',
                'validee', 'rejetee', 'financee'
            ])->default('en_attente');
            $table->text('commentaire_direction')->nullable();
            $table->foreignId('validee_par')->nullable()->constrained('bm_users');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();

            $table->unique(['adherent_id', 'session_id']);
        });

        // ================================================================
        // FINANCEMENTS ACCORDES
        // ================================================================
        Schema::create('bm_financements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('demande_id')->constrained('bm_demandes_financement');
            $table->foreignId('adherent_id')->constrained('bm_adherents');
            $table->foreignId('session_id')->constrained('bm_sessions_financement');
            $table->decimal('montant_accorde', 12, 2);
            $table->decimal('taux_interet', 5, 2)->default(0); // en %
            $table->integer('duree_mois');
            $table->decimal('montant_mensuel', 12, 2);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('montant_total_du', 12, 2);
            $table->decimal('montant_rembourse', 12, 2)->default(0);
            $table->decimal('penalites_totales', 12, 2)->default(0);
            $table->enum('statut', ['en_cours', 'solde', 'defaut', 'restructure'])->default('en_cours');
            $table->foreignId('approuve_par')->nullable()->constrained('bm_users');
            $table->timestamps();
        });

        // ================================================================
        // ECHEANCIERS DE REMBOURSEMENT
        // ================================================================
        Schema::create('bm_echeanciers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('bm_financements')->onDelete('cascade');
            $table->integer('numero_echeance');
            $table->decimal('montant_du', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->date('date_echeance');
            $table->date('date_paiement')->nullable();
            $table->decimal('penalite', 10, 2)->default(0);
            $table->enum('statut', ['a_venir', 'en_attente', 'paye', 'partiel', 'retard', 'impaye'])->default('a_venir');
            $table->timestamps();
        });

        // ================================================================
        // REMBOURSEMENTS
        // ================================================================
        Schema::create('bm_remboursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('bm_financements');
            $table->foreignId('echeancier_id')->nullable()->constrained('bm_echeanciers');
            $table->foreignId('adherent_id')->constrained('bm_adherents');
            $table->decimal('montant', 10, 2);
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->string('token_paiement')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'echoue'])->default('en_attente');
            $table->foreignId('agent_id')->nullable()->constrained('bm_users');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });

        // ================================================================
        // SOUTIENS NKH (non remboursables)
        // ================================================================
        Schema::create('bm_soutiens_nkh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents');
            $table->decimal('montant', 10, 2);
            $table->text('motif');
            $table->enum('statut', ['demande', 'approuve', 'rejete', 'verse'])->default('demande');
            $table->foreignId('approuve_par')->nullable()->constrained('bm_users');
            $table->date('date_versement')->nullable();
            $table->timestamps();
        });

        // ================================================================
        // PARRAINAGES
        // ================================================================
        Schema::create('bm_parrainages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parrain_id')->constrained('bm_adherents');
            $table->foreignId('filleul_id')->constrained('bm_adherents');
            $table->date('date_parrainage');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->decimal('bonus_points', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['parrain_id', 'filleul_id']);
        });

        // ================================================================
        // CARNET DE RECETTES DIGITAL
        // ================================================================
        Schema::create('bm_carnets_recettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained('bm_adherents')->onDelete('cascade');
            $table->date('date_recette');
            $table->decimal('montant_recette', 12, 2);
            $table->decimal('montant_depense', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('categorie')->nullable(); // vente, service, autre
            $table->string('photo_justificatif_url')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('bm_users');
            $table->boolean('valide')->default(false);
            $table->timestamps();
        });

        // ================================================================
        // TRANSACTIONS PAIEMENT (MoneyFusion)
        // ================================================================
        Schema::create('bm_transactions_paiement', function (Blueprint $table) {
            $table->id();
            $table->string('reference_interne')->unique();
            $table->enum('type', ['cotisation', 'kit', 'remboursement']);
            $table->foreignId('adherent_id')->constrained('bm_adherents');
            $table->morphs('payable'); // polymorphic: cotisation, achat_kit, remboursement
            $table->decimal('montant', 10, 2);
            $table->decimal('frais', 10, 2)->default(0);
            $table->string('token_paiement')->nullable()->unique();
            $table->string('numero_telephone');
            $table->string('nom_client');
            $table->string('url_paiement')->nullable();
            $table->enum('statut', ['pending', 'paid', 'failure', 'no_paid'])->default('pending');
            $table->string('moyen_paiement')->nullable();
            $table->string('numero_transaction_externe')->nullable();
            $table->json('webhook_data')->nullable();
            $table->json('personal_info')->nullable();
            $table->timestamps();
        });

        // ================================================================
        // NOTIFICATIONS
        // ================================================================
        Schema::create('bm_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('destinataire_type', ['user', 'adherent']);
            $table->unsignedBigInteger('destinataire_id');
            $table->string('titre');
            $table->text('message');
            $table->string('type')->default('info'); // info, success, warning, danger
            $table->string('lien')->nullable();
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_le')->nullable();
            $table->timestamps();

            $table->index(['destinataire_type', 'destinataire_id']);
        });

        // ================================================================
        // HISTORIQUE DES ACTIONS (Audit Log)
        // ================================================================
        Schema::create('bm_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // create, update, delete, login, etc.
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->enum('user_type', ['user', 'adherent']);
            $table->unsignedBigInteger('user_id');
            $table->json('ancien_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bm_audit_logs');
        Schema::dropIfExists('bm_notifications');
        Schema::dropIfExists('bm_transactions_paiement');
        Schema::dropIfExists('bm_carnets_recettes');
        Schema::dropIfExists('bm_parrainages');
        Schema::dropIfExists('bm_soutiens_nkh');
        Schema::dropIfExists('bm_remboursements');
        Schema::dropIfExists('bm_echeanciers');
        Schema::dropIfExists('bm_financements');
        Schema::dropIfExists('bm_demandes_financement');
        Schema::dropIfExists('bm_sessions_financement');
        Schema::dropIfExists('bm_scores');
        Schema::dropIfExists('bm_criteres_scoring');
        Schema::dropIfExists('bm_cotisations');
        Schema::dropIfExists('bm_types_cotisation');
        Schema::dropIfExists('bm_achats_kits');
        Schema::dropIfExists('bm_kits');
        Schema::dropIfExists('bm_adherents');
        Schema::dropIfExists('bm_profils');
        Schema::dropIfExists('bm_users');
        Schema::dropIfExists('bm_configurations');
    }
};
