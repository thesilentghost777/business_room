<?php

namespace App\Services\BusMetro;

use App\Models\BusMetro\TransactionPaiement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoneyFusionService
{
    protected string $apiUrl;

    public function __construct()
    {
        // L'URL contient déjà la clé : https://www.pay.moneyfusion.net/nkap_dey/663d0674d4b3a39b/pay/
        $this->apiUrl = config('services.moneyfusion.api_url', env('MONEYFUSION_API_URL'));
    }

    /**
     * Initier un paiement via MoneyFusion
     */
    public function initierPaiement(array $data): array
    {
        $paymentData = [
            'totalPrice' => (int) $data['montant'],
            'article' => [
                [$data['description'] ?? 'Paiement' => (int) $data['montant']]
            ],
            'personal_Info' => [
                [
                    'type' => $data['type'], // cotisation, kit, remboursement
                    'reference' => $data['reference'],
                    'adherent_id' => $data['adherent_id'],
                ]
            ],
            'numeroSend' => $data['telephone'],
            'nomclient' => $data['nom_client'],
            'return_url' => $data['return_url'] ?? config('app.url') . '/busmetro/paiement/callback',
            'webhook_url' => config('app.url') . '/busmetro/webhook/moneyfusion',
        ];

        try {
            $response = Http::post($this->apiUrl, $paymentData);
            $result = $response->json();

            if ($result && isset($result['statut']) && $result['statut'] === true) {
                return [
                    'success' => true,
                    'token' => $result['token'],
                    'url' => $result['url'],
                    'message' => $result['message'] ?? 'Paiement initié',
                ];
            }

            Log::error('MoneyFusion: Erreur initiation', ['response' => $result]);
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Erreur lors de l\'initiation du paiement',
            ];
        } catch (\Exception $e) {
            Log::error('MoneyFusion: Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Impossible de contacter le service de paiement',
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function verifierStatut(string $token): array
    {
        try {
            $response = Http::get("https://www.pay.moneyfusion.net/paiementNotif/{$token}");
            $result = $response->json();

            if ($result && isset($result['statut']) && $result['statut'] === true) {
                return [
                    'success' => true,
                    'data' => $result['data'],
                    'statut_paiement' => $result['data']['statut'] ?? 'pending',
                ];
            }

            return [
                'success' => false,
                'message' => 'Impossible de récupérer le statut',
            ];
        } catch (\Exception $e) {
            Log::error('MoneyFusion: Erreur vérification', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur de vérification',
            ];
        }
    }

    /**
     * Traiter un webhook MoneyFusion
     */
    public function traiterWebhook(array $data): bool
    {
        $token = $data['tokenPay'] ?? null;
        $event = $data['event'] ?? null;

        if (!$token) {
            Log::warning('MoneyFusion Webhook: token manquant', $data);
            return false;
        }

        $transaction = TransactionPaiement::where('token_paiement', $token)->first();

        if (!$transaction) {
            Log::warning('MoneyFusion Webhook: transaction introuvable', ['token' => $token]);
            return false;
        }

        // Déterminer le nouveau statut
        $nouveauStatut = match ($event) {
            'payin.session.completed' => 'paid',
            'payin.session.cancelled' => 'failure',
            'payin.session.pending' => 'pending',
            default => null,
        };

        // Ignorer si pas de changement ou statut déjà final
        if (!$nouveauStatut || $transaction->statut === $nouveauStatut || $transaction->statut === 'paid') {
            return true;
        }

        $transaction->update([
            'statut' => $nouveauStatut,
            'moyen_paiement' => $data['moyen'] ?? $transaction->moyen_paiement,
            'numero_transaction_externe' => $data['numeroTransaction'] ?? null,
            'frais' => $data['frais'] ?? 0,
            'webhook_data' => $data,
        ]);

        // Si paiement réussi, finaliser selon le type
        if ($nouveauStatut === 'paid') {
            $this->finaliserPaiement($transaction);
        }

        return true;
    }

    /**
     * Finaliser un paiement réussi
     */
    protected function finaliserPaiement(TransactionPaiement $transaction): void
    {
        $payable = $transaction->payable;

        if (!$payable) return;

        switch ($transaction->type) {
            case 'cotisation':
                $payable->update(['statut' => 'valide']);
                break;

            case 'kit':
                $payable->update(['statut' => 'paye']);
                $adherent = $transaction->adherent;
                if ($adherent) {
                    $adherent->update([
                        'kit_achete' => true,
                        'date_adhesion' => now(),
                        'statut' => 'actif',
                    ]);
                }
                break;

            case 'remboursement':
                $payable->update(['statut' => 'valide']);
                // Mettre à jour l'échéancier et le financement
                if ($payable instanceof \App\Models\BusMetro\Remboursement) {
                    app(FinancementService::class)->traiterRemboursement($payable);
                }
                break;
        }

        // Notification
        \App\Models\BusMetro\Notification::envoyer(
            'adherent',
            $transaction->adherent_id,
            'Paiement confirmé',
            "Votre paiement de {$transaction->montant} FCFA a été confirmé.",
            'success'
        );
    }
}
