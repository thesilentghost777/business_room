<?php

namespace App\Services\BusMetro;

use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\TransactionPaiement;
use App\Models\BusMetro\Notification;

class CotisationService
{
    protected MoneyFusionService $moneyFusion;

    public function __construct(MoneyFusionService $moneyFusion)
    {
        $this->moneyFusion = $moneyFusion;
    }

    /**
     * Enregistrer une cotisation en espèces (par un agent)
     */
    public function enregistrerEspeces(
        Adherent $adherent,
        TypeCotisation $type,
        float $montant,
        int $agentId,
        ?string $commentaire = null
    ): Cotisation {
        if ($montant < $type->montant_minimum) {
            throw new \Exception("Le montant minimum est de {$type->montant_minimum} FCFA");
        }

        $cotisation = Cotisation::create([
            'adherent_id' => $adherent->id,
            'type_cotisation_id' => $type->id,
            'montant' => $montant,
            'date_cotisation' => now()->toDateString(),
            'mode_paiement' => 'especes',
            'statut' => 'valide',
            'agent_id' => $agentId,
            'commentaire' => $commentaire,
        ]);

        Notification::envoyer(
            'adherent',
            $adherent->id,
            'Cotisation enregistrée',
            "Votre cotisation {$type->code} de {$montant} FCFA a été enregistrée.",
            'success'
        );

        return $cotisation;
    }

    /**
     * Initier une cotisation via MoneyFusion
     */
    public function initierPaiementMobile(
        Adherent $adherent,
        TypeCotisation $type,
        float $montant
    ): array {
        if ($montant < $type->montant_minimum) {
            throw new \Exception("Le montant minimum est de {$type->montant_minimum} FCFA");
        }

        // Créer la cotisation en attente
        $cotisation = Cotisation::create([
            'adherent_id' => $adherent->id,
            'type_cotisation_id' => $type->id,
            'montant' => $montant,
            'date_cotisation' => now()->toDateString(),
            'mode_paiement' => 'moneyfusion',
            'statut' => 'en_attente',
        ]);

        // Créer la transaction
        $transaction = TransactionPaiement::create([
            'type' => 'cotisation',
            'adherent_id' => $adherent->id,
            'payable_type' => Cotisation::class,
            'payable_id' => $cotisation->id,
            'montant' => $montant,
            'numero_telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
            'personal_info' => [
                'type' => 'cotisation',
                'cotisation_id' => $cotisation->id,
                'adherent_id' => $adherent->id,
                'type_cotisation' => $type->code,
            ],
        ]);

        // Appeler MoneyFusion
        $result = $this->moneyFusion->initierPaiement([
            'montant' => $montant,
            'telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
            'type' => 'cotisation',
            'reference' => $transaction->reference_interne,
            'adherent_id' => $adherent->id,
            'description' => "Cotisation {$type->code}",
            'return_url' => route('busmetro.adherent.cotisations.callback'),
        ]);

        if ($result['success']) {
            $transaction->update([
                'token_paiement' => $result['token'],
                'url_paiement' => $result['url'],
            ]);
            $cotisation->update(['token_paiement' => $result['token']]);

            return [
                'success' => true,
                'url' => $result['url'],
                'token' => $result['token'],
                'cotisation' => $cotisation,
            ];
        }

        $cotisation->update(['statut' => 'echoue']);
        $transaction->update(['statut' => 'failure']);

        throw new \Exception($result['message']);
    }

    /**
     * Récupérer les statistiques de cotisations d'un adhérent
     */
    public function getStatistiques(Adherent $adherent): array
    {
        $cotisations = $adherent->cotisationsValides();

        return [
            'total_cotisations' => $cotisations->count(),
            'total_montant' => (float) $cotisations->sum('montant'),
            'total_nkd' => (float) $cotisations->whereHas('typeCotisation', fn($q) => $q->where('code', 'NKD'))->sum('montant'),
            'total_nkh' => (float) $cotisations->whereHas('typeCotisation', fn($q) => $q->where('code', 'NKH'))->sum('montant'),
            'derniere_cotisation' => $cotisations->latest()->first(),
            'cotisations_ce_mois' => $cotisations->whereMonth('date_cotisation', now()->month)->count(),
        ];
    }
}
