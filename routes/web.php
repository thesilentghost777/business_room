<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusMetro\AuthController;
use App\Http\Controllers\BusMetro\WebhookController;
use App\Http\Controllers\BusMetro\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\BusMetro\Admin\AdherentController as AdminAdherent;
use App\Http\Controllers\BusMetro\Admin\UserController as AdminUser;
use App\Http\Controllers\BusMetro\Admin\ConfigurationController as AdminConfig;
use App\Http\Controllers\BusMetro\Admin\SessionFinancementController as AdminSession;
use App\Http\Controllers\BusMetro\Admin\FinancementController as AdminFinancement;
use App\Http\Controllers\BusMetro\Admin\CotisationController as AdminCotisation;
use App\Http\Controllers\BusMetro\Admin\SoutienController as AdminSoutien;
use App\Http\Controllers\BusMetro\Admin\TransactionController as AdminTransaction;
use App\Http\Controllers\BusMetro\Agent\DashboardController as AgentDashboard;
use App\Http\Controllers\BusMetro\Agent\EnrolementController as AgentEnrolement;
use App\Http\Controllers\BusMetro\Agent\CollecteController as AgentCollecte;
use App\Http\Controllers\BusMetro\Agent\CarnetRecetteController as AgentCarnet;
use App\Http\Controllers\BusMetro\Direction\DashboardController as DirectionDashboard;
use App\Http\Controllers\BusMetro\Direction\AnalyseController as DirectionAnalyse;
use App\Http\Controllers\BusMetro\Adherent\DashboardController as AdherentDashboard;
use App\Http\Controllers\BusMetro\Adherent\PaiementController as AdherentPaiement;
use App\Http\Controllers\BusMetro\Adherent\FinancementController as AdherentFinancement;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('busmetro')->name('busmetro.')->group(function () {

    // ================================================================
    // WEBHOOK (pas d'auth)
    // ================================================================
    Route::post('/webhook/moneyfusion', [WebhookController::class, 'moneyfusion'])->name('webhook.moneyfusion');

    // ================================================================
    // AUTH STAFF
    // ================================================================
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ================================================================
    // AUTH ADHERENTS
    // ================================================================
    Route::prefix('espace-membre')->name('adherent.')->group(function () {
        Route::get('/connexion', [AuthController::class, 'showAdherentLoginForm'])->name('login');
        Route::post('/connexion', [AuthController::class, 'adherentLogin'])->name('login.submit');
        Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');
        Route::post('/deconnexion', [AuthController::class, 'adherentLogout'])->name('logout');
    });

    // ================================================================
    // ADMIN
    // ================================================================
    Route::prefix('admin')->name('admin.')->middleware(['auth:busmetro', 'bm.role:admin'])->group(function () {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // Adhérents
        Route::resource('adherents', AdminAdherent::class)->except('create', 'store');
        Route::post('adherents/{adherent}/statut', [AdminAdherent::class, 'changerStatut'])->name('adherents.statut');
        Route::post('adherents/{adherent}/reset-password', [AdminAdherent::class, 'resetPassword'])->name('adherents.reset-password');

        // Utilisateurs internes
        Route::resource('users', AdminUser::class);
        Route::post('users/{user}/toggle', [AdminUser::class, 'toggleActive'])->name('users.toggle');

        // Configuration
        Route::get('configuration', [AdminConfig::class, 'index'])->name('configuration.index');
        Route::post('configuration', [AdminConfig::class, 'updateConfigs'])->name('configuration.update');

        Route::get('configuration/kits', [AdminConfig::class, 'kits'])->name('configuration.kits');
        Route::post('configuration/kits', [AdminConfig::class, 'storeKit'])->name('configuration.kits.store');
        Route::put('configuration/kits/{kit}', [AdminConfig::class, 'updateKit'])->name('configuration.kits.update');

        Route::get('configuration/types-cotisation', [AdminConfig::class, 'typesCotisation'])->name('configuration.types-cotisation');
        Route::post('configuration/types-cotisation', [AdminConfig::class, 'storeTypeCotisation'])->name('configuration.types-cotisation.store');
        Route::put('configuration/types-cotisation/{type}', [AdminConfig::class, 'updateTypeCotisation'])->name('configuration.types-cotisation.update');

        Route::get('configuration/criteres-scoring', [AdminConfig::class, 'criteresScoring'])->name('configuration.criteres-scoring');
        Route::post('configuration/criteres-scoring', [AdminConfig::class, 'storeCritereScoring'])->name('configuration.criteres-scoring.store');
        Route::put('configuration/criteres-scoring/{critere}', [AdminConfig::class, 'updateCritereScoring'])->name('configuration.criteres-scoring.update');

        Route::get('configuration/profils', [AdminConfig::class, 'profils'])->name('configuration.profils');
        Route::post('configuration/profils', [AdminConfig::class, 'storeProfil'])->name('configuration.profils.store');
        Route::put('configuration/profils/{profil}', [AdminConfig::class, 'updateProfil'])->name('configuration.profils.update');

        // Sessions de financement
        Route::resource('sessions', AdminSession::class);
        Route::post('sessions/{session}/statut', [AdminSession::class, 'changerStatut'])->name('sessions.statut');
        Route::post('sessions/{session}/scoring', [AdminSession::class, 'lancerScoring'])->name('sessions.scoring');
        Route::post('sessions/{session}/selection', [AdminSession::class, 'selectionnerBeneficiaires'])->name('sessions.selection');
        Route::post('demandes/{demande}/valider', [AdminSession::class, 'validerDemande'])->name('demandes.valider');
        Route::post('demandes/{demande}/financer', [AdminSession::class, 'accorderFinancement'])->name('demandes.financer');

        // Financements
        Route::get('financements', [AdminFinancement::class, 'index'])->name('financements.index');
        Route::get('financements/statistiques', [AdminFinancement::class, 'statistiques'])->name('financements.statistiques');
        Route::get('financements/{financement}', [AdminFinancement::class, 'show'])->name('financements.show');
        Route::post('financements/penalites', [AdminFinancement::class, 'appliquerPenalites'])->name('financements.penalites');

        // Cotisations
        Route::get('cotisations', [AdminCotisation::class, 'index'])->name('cotisations.index');
        Route::get('cotisations/rapport', [AdminCotisation::class, 'rapport'])->name('cotisations.rapport');

        // Soutiens NKH
        Route::get('soutiens', [AdminSoutien::class, 'index'])->name('soutiens.index');
        Route::post('soutiens/{soutien}/traiter', [AdminSoutien::class, 'traiter'])->name('soutiens.traiter');
        Route::post('soutiens/{soutien}/verser', [AdminSoutien::class, 'verser'])->name('soutiens.verser');

        // Transactions
        Route::get('transactions', [AdminTransaction::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}', [AdminTransaction::class, 'show'])->name('transactions.show');
    });

    // ================================================================
    // AGENT TERRAIN
    // ================================================================
    Route::prefix('agent')->name('agent.')->middleware(['auth:busmetro', 'bm.role:agent,admin'])->group(function () {
        Route::get('/', [AgentDashboard::class, 'index'])->name('dashboard');

        // Enrôlement
        Route::get('enrolement', [AgentEnrolement::class, 'index'])->name('enrolement.index');
        Route::get('enrolement/nouveau', [AgentEnrolement::class, 'create'])->name('enrolement.create');
        Route::post('enrolement', [AgentEnrolement::class, 'store'])->name('enrolement.store');
        Route::get('enrolement/{adherent}', [AgentEnrolement::class, 'show'])->name('enrolement.show');
        Route::post('enrolement/{adherent}/kit', [AgentEnrolement::class, 'acheterKit'])->name('enrolement.kit');

        // Collecte cotisations
        Route::get('collecte/cotisation', [AgentCollecte::class, 'cotisationForm'])->name('collecte.cotisation');
        Route::post('collecte/cotisation/rechercher', [AgentCollecte::class, 'rechercherAdherent'])->name('collecte.rechercher');
        Route::post('collecte/cotisation', [AgentCollecte::class, 'enregistrerCotisation'])->name('collecte.cotisation.store');

        // Collecte remboursements
        Route::get('collecte/remboursement', [AgentCollecte::class, 'remboursementForm'])->name('collecte.remboursement');
        Route::post('collecte/remboursement/rechercher', [AgentCollecte::class, 'rechercherFinancement'])->name('collecte.remboursement.rechercher');
        Route::post('collecte/remboursement', [AgentCollecte::class, 'enregistrerRemboursement'])->name('collecte.remboursement.store');

        // Carnets de recettes
        Route::get('carnets', [AgentCarnet::class, 'index'])->name('carnets.index');
        Route::post('carnets/{recette}/valider', [AgentCarnet::class, 'valider'])->name('carnets.valider');
    });

    // ================================================================
    // DIRECTION FINANCEMENT
    // ================================================================
    Route::prefix('direction')->name('direction.')->middleware(['auth:busmetro', 'bm.role:direction,admin'])->group(function () {
        Route::get('/', [DirectionDashboard::class, 'index'])->name('dashboard');
        Route::get('sessions', [DirectionAnalyse::class, 'sessions'])->name('sessions');
        Route::get('sessions/{session}', [DirectionAnalyse::class, 'analyserSession'])->name('sessions.analyse');
        Route::post('demandes/{demande}/valider', [DirectionAnalyse::class, 'validerDemande'])->name('demandes.valider');
        Route::post('demandes/{demande}/financer', [DirectionAnalyse::class, 'accorderFinancement'])->name('demandes.financer');
    });

    // ================================================================
    // ESPACE ADHERENT (membre)
    // ================================================================
    Route::prefix('espace-membre')->name('adherent.')->middleware(['auth:adherent'])->group(function () {
        Route::get('/', [AdherentDashboard::class, 'index'])->name('dashboard');
        Route::get('profil', [AdherentDashboard::class, 'profil'])->name('profil');
        Route::put('profil', [AdherentDashboard::class, 'updateProfil'])->name('profil.update');
        Route::get('notifications', [AdherentDashboard::class, 'notifications'])->name('notifications');
        Route::post('notifications/{notification}/lire', [AdherentDashboard::class, 'lireNotification'])->name('notifications.lire');

        // Cotisations
        Route::get('cotisations', [AdherentPaiement::class, 'cotisations'])->name('cotisations');
        Route::post('cotisations/payer', [AdherentPaiement::class, 'payerCotisation'])->name('cotisations.payer');
        Route::get('cotisations/callback', [AdherentPaiement::class, 'callbackCotisation'])->name('cotisations.callback');

        // Kit
        Route::get('/kits', [AdherentPaiement::class, 'kits'])->name('kits');
        Route::post('kit/acheter', [AdherentPaiement::class, 'acheterKit'])->name('kit.acheter');

        // Financement
        Route::get('financement', [AdherentFinancement::class, 'index'])->name('financement');
        Route::post('financement/postuler', [AdherentFinancement::class, 'postuler'])->name('financement.postuler');
        Route::get('financement/remboursement', [AdherentFinancement::class, 'remboursement'])->name('financement.remboursement');
        Route::post('financement/rembourser', [AdherentFinancement::class, 'payerRemboursement'])->name('financement.rembourser');

        // Carnet de recettes
        Route::get('carnet', [AdherentFinancement::class, 'carnet'])->name('carnet');
        Route::post('carnet', [AdherentFinancement::class, 'ajouterRecette'])->name('carnet.ajouter');
    });

});
