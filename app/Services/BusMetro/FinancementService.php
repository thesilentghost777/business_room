<?php

namespace App\Services\BusMetro;

use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Financement;
use App\Models\BusMetro\Echeancier;
use App\Models\BusMetro\Remboursement;
use App\Models\BusMetro\DemandeFinancement;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\TransactionPaiement;
use App\Models\BusMetro\Configuration;
use App\Models\BusMetro\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancementService
{
    protected MoneyFusionService $moneyFusion;

    public function __construct(MoneyFusionService $moneyFusion)
    {
        $this->moneyFusion = $moneyFusion;
    }

    /**
     * Soumettre une demande de financement
     */
    public function soumettreDemandeFinancement(
        Adherent $adherent,
        SessionFinancement $session,
        float $montant,
        string $motif,
        ?string $descriptionProjet = null
    ): DemandeFinancement {
        // Vérifications
        if (!$adherent->peutDemanderFinancement()) {
            throw new \Exception('Vous ne remplissez pas les conditions pour demander un financement.');
        }

        if (!$session->estOuverteAuxCandidatures()) {
            throw new \Exception('Les candidatures pour cette session sont fermées.');
        }

        $plafond = $adherent->profil?->plafond_financement ?? 0;
        if ($montant > $plafond) {
            throw new \Exception("Le montant maximum pour votre profil est de {$plafond} FCFA.");
        }

        // Vérifier qu'il n'a pas déjà postulé
        $dejaPostule = DemandeFinancement::where('adherent_id', $adherent->id)
            ->where('session_id', $session->id)
            ->exists();

        if ($dejaPostule) {
            throw new \Exception('Vous avez déjà postulé pour cette session.');
        }

        return DemandeFinancement::create([
            'adherent_id' => $adherent->id,
            'session_id' => $session->id,
            'montant_demande' => $montant,
            'motif' => $motif,
            'description_projet' => $descriptionProjet,
            'score_total' => $adherent->score_actuel,
            'statut' => 'en_attente',
        ]);
    }

    /**
     * Valider et créer un financement
     */
    public function accorderFinancement(
        DemandeFinancement $demande,
        float $montantAccorde,
        int $dureeMois,
        float $tauxInteret,
        int $approuvePar
    ): Financement {
        return DB::transaction(function () use ($demande, $montantAccorde, $dureeMois, $tauxInteret, $approuvePar) {
            // Calculer les montants
            $interets = $montantAccorde * ($tauxInteret / 100) * ($dureeMois / 12);
            $montantTotalDu = $montantAccorde + $interets;
            $montantMensuel = ceil($montantTotalDu / $dureeMois);

            $dateDebut = now();
            $dateFin = now()->addMonths($dureeMois);

            // Créer le financement
            $financement = Financement::create([
                'demande_id' => $demande->id,
                'adherent_id' => $demande->adherent_id,
                'session_id' => $demande->session_id,
                'montant_accorde' => $montantAccorde,
                'taux_interet' => $tauxInteret,
                'duree_mois' => $dureeMois,
                'montant_mensuel' => $montantMensuel,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'montant_total_du' => $montantTotalDu,
                'statut' => 'en_cours',
                'approuve_par' => $approuvePar,
            ]);

            // Générer l'échéancier
            $this->genererEcheancier($financement);

            // Mettre à jour la demande
            $demande->update([
                'statut' => 'financee',
                'validee_par' => $approuvePar,
                'date_validation' => now(),
            ]);

            // Notification
            Notification::envoyer(
                'adherent',
                $demande->adherent_id,
                'Financement accordé !',
                "Votre financement de {$montantAccorde} FCFA a été approuvé. Durée: {$dureeMois} mois.",
                'success'
            );

            return $financement;
        });
    }

    /**
     * Générer l'échéancier de remboursement
     */
    public function genererEcheancier(Financement $financement): void
    {
        $montantParEcheance = $financement->montant_mensuel;
        $restant = $financement->montant_total_du;

        for ($i = 1; $i <= $financement->duree_mois; $i++) {
            $montant = ($i === $financement->duree_mois) ? $restant : $montantParEcheance;
            $montant = min($montant, $restant);

            Echeancier::create([
                'financement_id' => $financement->id,
                'numero_echeance' => $i,
                'montant_du' => $montant,
                'date_echeance' => Carbon::parse($financement->date_debut)->addMonths($i),
                'statut' => $i === 1 ? 'en_attente' : 'a_venir',
            ]);

            $restant -= $montant;
        }
    }

    /**
     * Enregistrer un remboursement en espèces
     */
    public function enregistrerRemboursementEspeces(
        Financement $financement,
        float $montant,
        int $agentId,
        ?int $echeancierIdCible = null,
        ?string $commentaire = null
    ): Remboursement {
        $remboursement = Remboursement::create([
            'financement_id' => $financement->id,
            'echeancier_id' => $echeancierIdCible,
            'adherent_id' => $financement->adherent_id,
            'montant' => $montant,
            'mode_paiement' => 'especes',
            'statut' => 'valide',
            'agent_id' => $agentId,
            'commentaire' => $commentaire,
        ]);

        $this->traiterRemboursement($remboursement);

        return $remboursement;
    }

    /**
     * Initier un remboursement via MoneyFusion
     */
    public function initierRemboursementMobile(
        Financement $financement,
        float $montant,
        ?int $echeancierIdCible = null
    ): array {
        $adherent = $financement->adherent;

        $remboursement = Remboursement::create([
            'financement_id' => $financement->id,
            'echeancier_id' => $echeancierIdCible,
            'adherent_id' => $adherent->id,
            'montant' => $montant,
            'mode_paiement' => 'moneyfusion',
            'statut' => 'en_attente',
        ]);

        $transaction = TransactionPaiement::create([
            'type' => 'remboursement',
            'adherent_id' => $adherent->id,
            'payable_type' => Remboursement::class,
            'payable_id' => $remboursement->id,
            'montant' => $montant,
            'numero_telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
            'personal_info' => [
                'type' => 'remboursement',
                'financement_id' => $financement->id,
                'remboursement_id' => $remboursement->id,
            ],
        ]);

        $result = $this->moneyFusion->initierPaiement([
            'montant' => $montant,
            'telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
            'type' => 'remboursement',
            'reference' => $transaction->reference_interne,
            'adherent_id' => $adherent->id,
            'description' => "Remboursement financement {$financement->reference}",
        ]);

        if ($result['success']) {
            $transaction->update([
                'token_paiement' => $result['token'],
                'url_paiement' => $result['url'],
            ]);
            $remboursement->update(['token_paiement' => $result['token']]);

            return ['success' => true, 'url' => $result['url'], 'token' => $result['token']];
        }

        $remboursement->update(['statut' => 'echoue']);
        throw new \Exception($result['message']);
    }

    /**
     * Traiter un remboursement validé (mettre à jour échéancier + financement)
     */
    public function traiterRemboursement(Remboursement $remboursement): void
    {
        $financement = $remboursement->financement;
        $montantRestant = $remboursement->montant;

        // Si un échéancier spécifique est ciblé
        if ($remboursement->echeancier_id) {
            $echeancier = Echeancier::find($remboursement->echeancier_id);
            if ($echeancier) {
                $aAppliquer = min($montantRestant, $echeancier->reste_a_payer);
                $echeancier->increment('montant_paye', $aAppliquer);
                $montantRestant -= $aAppliquer;

                if ($echeancier->montant_paye >= ($echeancier->montant_du + $echeancier->penalite)) {
                    $echeancier->update(['statut' => 'paye', 'date_paiement' => now()]);
                } else {
                    $echeancier->update(['statut' => 'partiel']);
                }
            }
        }

        // Appliquer le reste aux échéances en attente/retard
        if ($montantRestant > 0) {
            $echeances = $financement->echeanciers()
                ->whereIn('statut', ['en_attente', 'retard', 'partiel'])
                ->orderBy('numero_echeance')
                ->get();

            foreach ($echeances as $echeance) {
                if ($montantRestant <= 0) break;

                $aAppliquer = min($montantRestant, $echeance->reste_a_payer);
                $echeance->increment('montant_paye', $aAppliquer);
                $montantRestant -= $aAppliquer;

                if ($echeance->montant_paye >= ($echeance->montant_du + $echeance->penalite)) {
                    $echeance->update(['statut' => 'paye', 'date_paiement' => now()]);
                } else {
                    $echeance->update(['statut' => 'partiel']);
                }
            }
        }

        // Mettre à jour le financement
        $totalRembourse = $financement->remboursements()->where('statut', 'valide')->sum('montant');
        $financement->update(['montant_rembourse' => $totalRembourse]);

        // Vérifier si soldé
        if ($totalRembourse >= $financement->montant_total_du) {
            $financement->update(['statut' => 'solde']);
            Notification::envoyer(
                'adherent', $financement->adherent_id,
                'Financement soldé !',
                'Félicitations ! Votre financement a été intégralement remboursé.',
                'success'
            );
        }

        // Activer la prochaine échéance
        $prochaineEcheance = $financement->echeanciers()
            ->where('statut', 'a_venir')
            ->orderBy('numero_echeance')
            ->first();

        if ($prochaineEcheance) {
            $prochaineEcheance->update(['statut' => 'en_attente']);
        }
    }

    /**
     * Appliquer les pénalités de retard
     */
    public function appliquerPenalites(): void
    {
        $tauxPenalite = (float) Configuration::get('taux_penalite_retard', 2); // % par jour de retard

        $echeancesEnRetard = Echeancier::whereIn('statut', ['en_attente', 'partiel'])
            ->where('date_echeance', '<', now())
            ->with('financement')
            ->get();

        foreach ($echeancesEnRetard as $echeance) {
            $joursRetard = Carbon::parse($echeance->date_echeance)->diffInDays(now());
            $penalite = round(($echeance->montant_du * $tauxPenalite / 100) * $joursRetard, 2);

            $echeance->update([
                'penalite' => $penalite,
                'statut' => 'retard',
            ]);

            // Mettre à jour les pénalités totales du financement
            $totalPenalites = $echeance->financement->echeanciers()->sum('penalite');
            $echeance->financement->update(['penalites_totales' => $totalPenalites]);
        }
    }

    /**
     * Statistiques de financement
     */
    public function getStatistiquesGlobales(): array
    {
        return [
            'total_finance' => (float) Financement::sum('montant_accorde'),
            'total_rembourse' => (float) Financement::sum('montant_rembourse'),
            'financements_en_cours' => Financement::where('statut', 'en_cours')->count(),
            'financements_soldes' => Financement::where('statut', 'solde')->count(),
            'financements_defaut' => Financement::where('statut', 'defaut')->count(),
            'taux_remboursement_global' => $this->calculerTauxRemboursementGlobal(),
            'penalites_totales' => (float) Financement::sum('penalites_totales'),
        ];
    }

    protected function calculerTauxRemboursementGlobal(): float
    {
        $totalDu = (float) Financement::sum('montant_total_du');
        $totalRembourse = (float) Financement::sum('montant_rembourse');

        return $totalDu > 0 ? round(($totalRembourse / $totalDu) * 100, 2) : 0;
    }
}
