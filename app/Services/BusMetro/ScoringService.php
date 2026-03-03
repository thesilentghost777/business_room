<?php

namespace App\Services\BusMetro;

use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\CritereScoring;
use App\Models\BusMetro\Score;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\Configuration;
use Carbon\Carbon;

class ScoringService
{
    /**
     * Calculer le score global d'un adhérent
     */
    public function calculerScore(Adherent $adherent, ?SessionFinancement $session = null): float
    {
        $criteres = CritereScoring::where('actif', true)->orderBy('ordre')->get();
        $scoreTotal = 0;
        $poidsTotal = 0;

        foreach ($criteres as $critere) {
            $points = $this->calculerCritere($adherent, $critere);
            $poidsTotal += $critere->poids;

            // Sauvegarder le score par critère
            Score::updateOrCreate(
                [
                    'adherent_id' => $adherent->id,
                    'critere_id' => $critere->id,
                    'session_id' => $session?->id,
                ],
                [
                    'points' => $points,
                    'details' => ['calcul' => "Score pour {$critere->nom}"],
                ]
            );

            $scoreTotal += $points * $critere->poids;
        }

        // Score normalisé sur 100
        $scoreNormalise = $poidsTotal > 0 ? ($scoreTotal / ($poidsTotal * 20)) * 100 : 0;
        $scoreNormalise = min(100, round($scoreNormalise, 2));

        // Mettre à jour le score de l'adhérent
        $adherent->update(['score_actuel' => $scoreNormalise]);

        return $scoreNormalise;
    }

    /**
     * Calculer un critère spécifique
     */
    protected function calculerCritere(Adherent $adherent, CritereScoring $critere): float
    {
        return match ($critere->code) {
            'regularite_cotisations' => $this->scoreRegulariteCotisations($adherent, $critere->max_points),
            'parrainage' => $this->scoreParrainage($adherent, $critere->max_points),
            'anciennete' => $this->scoreAnciennete($adherent, $critere->max_points),
            'activite_economique' => $this->scoreActiviteEconomique($adherent, $critere->max_points),
            'discipline_financiere' => $this->scoreDisciplineFinanciere($adherent, $critere->max_points),
            'carnet_recettes' => $this->scoreCarnetRecettes($adherent, $critere->max_points),
            default => 0,
        };
    }

    /**
     * Score basé sur la régularité des cotisations NKD
     */
    protected function scoreRegulariteCotisations(Adherent $adherent, int $maxPoints): float
    {
        $joursDepuisAdhesion = $adherent->date_adhesion
            ? Carbon::parse($adherent->date_adhesion)->diffInDays(now())
            : 0;

        if ($joursDepuisAdhesion <= 0) return 0;

        // Nombre de cotisations NKD validées
        $cotisationsNKD = $adherent->cotisationsValides()
            ->whereHas('typeCotisation', fn($q) => $q->where('code', 'NKD'))
            ->count();

        // Ratio de régularité (1 cotisation par jour attendue)
        $ratio = min(1, $cotisationsNKD / max(1, $joursDepuisAdhesion));

        return round($ratio * $maxPoints, 2);
    }

    /**
     * Score basé sur le nombre de filleuls actifs
     */
    protected function scoreParrainage(Adherent $adherent, int $maxPoints): float
    {
        $objectifFilleuls = (int) Configuration::get('objectif_filleuls_scoring', 5);
        $filleulsActifs = $adherent->filleuls()->where('statut', 'actif')->where('kit_achete', true)->count();

        $ratio = min(1, $filleulsActifs / max(1, $objectifFilleuls));

        return round($ratio * $maxPoints, 2);
    }

    /**
     * Score basé sur l'ancienneté dans le programme
     */
    protected function scoreAnciennete(Adherent $adherent, int $maxPoints): float
    {
        if (!$adherent->date_adhesion) return 0;

        $moisAnciennete = Carbon::parse($adherent->date_adhesion)->diffInMonths(now());
        $moisObjectif = (int) Configuration::get('mois_anciennete_max_score', 12);

        $ratio = min(1, $moisAnciennete / max(1, $moisObjectif));

        return round($ratio * $maxPoints, 2);
    }

    /**
     * Score basé sur l'activité économique déclarée
     */
    protected function scoreActiviteEconomique(Adherent $adherent, int $maxPoints): float
    {
        $score = 0;

        // A une activité déclarée
        if ($adherent->activite_economique) $score += $maxPoints * 0.3;

        // A des documents d'activité
        if ($adherent->document_activite_url) $score += $maxPoints * 0.2;

        // Revenu mensuel déclaré
        if ($adherent->revenu_mensuel > 0) {
            $score += $maxPoints * 0.3;
        }

        // Profil complet
        if ($adherent->profil_id) $score += $maxPoints * 0.2;

        return round(min($maxPoints, $score), 2);
    }

    /**
     * Score basé sur la discipline financière (pas de retard, etc.)
     */
    protected function scoreDisciplineFinanciere(Adherent $adherent, int $maxPoints): float
    {
        // Vérifier les remboursements passés
        $financementsTermines = $adherent->financements()->where('statut', 'solde')->count();
        $financementsDefaut = $adherent->financements()->where('statut', 'defaut')->count();

        if ($financementsTermines == 0 && $financementsDefaut == 0) {
            // Premier financement, score neutre
            return round($maxPoints * 0.5, 2);
        }

        $total = $financementsTermines + $financementsDefaut;
        $ratio = $financementsTermines / max(1, $total);

        return round($ratio * $maxPoints, 2);
    }

    /**
     * Score basé sur la tenue du carnet de recettes
     */
    protected function scoreCarnetRecettes(Adherent $adherent, int $maxPoints): float
    {
        $derniers30Jours = $adherent->carnetsRecettes()
            ->where('date_recette', '>=', now()->subDays(30))
            ->count();

        // Objectif: au moins 20 entrées sur 30 jours
        $ratio = min(1, $derniers30Jours / 20);

        return round($ratio * $maxPoints, 2);
    }

    /**
     * Calculer les scores de tous les adhérents pour une session
     */
    public function calculerScoresSession(SessionFinancement $session): void
    {
        $adherents = Adherent::where('statut', 'actif')
            ->where('kit_achete', true)
            ->get();

        foreach ($adherents as $adherent) {
            $this->calculerScore($adherent, $session);
        }
    }

    /**
     * Sélectionner les meilleurs profils pour une session
     */
    public function selectionnerBeneficiaires(SessionFinancement $session): array
    {
        // Calculer tous les scores d'abord
        $this->calculerScoresSession($session);

        // Récupérer les adhérents éligibles avec leur score, triés par score
        $adherents = Adherent::where('statut', 'actif')
            ->where('kit_achete', true)
            ->where('score_actuel', '>=', $session->score_minimum)
            ->doesntHave('financementEnCours')
            ->orderByDesc('score_actuel')
            ->limit($session->nombre_beneficiaires_max)
            ->get();

        return $adherents->toArray();
    }
}
