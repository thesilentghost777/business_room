<?php

namespace App\Http\Controllers\BusMetro\Adherent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\Kit;
use App\Models\BusMetro\AchatKit;
use App\Models\BusMetro\TransactionPaiement;
use App\Services\BusMetro\CotisationService;
use App\Services\BusMetro\MoneyFusionService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{

    // ===== KITS =====
public function kits()
{
    $adherent = auth('adherent')->user();

    $kits = Kit::where('actif', true)->get();

    $achatEnCours = null;
    if ($adherent->kit_achete) {
        $achatEnCours = AchatKit::where('adherent_id', $adherent->id)
            ->where('statut', 'paye')
            ->with('kit')
            ->latest()
            ->first();
    }

    return view('busmetro.adherent.kits', compact('kits', 'achatEnCours'));
}

// ===== COTISATIONS MOBILE =====
public function cotisations()
{
    $adherent = auth('adherent')->user();

    $typesCotisation = TypeCotisation::where('actif', true)->get();

    $cotisations = $adherent->cotisations()
        ->with('typeCotisation')
        ->orderByDesc('date_cotisation')
        ->paginate(20);

    return view('busmetro.adherent.cotisations', compact('typesCotisation', 'cotisations'));
}

    public function payerCotisation(Request $request, CotisationService $cotisationService)
    {
        $request->validate([
            'type_cotisation_id' => 'required|exists:bm_types_cotisation,id',
            'montant' => 'required|numeric|min:1',
        ]);

        $adherent = auth('adherent')->user();
        $type = TypeCotisation::findOrFail($request->type_cotisation_id);

        try {
            $result = $cotisationService->initierPaiementMobile($adherent, $type, $request->montant);
            return redirect($result['url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function callbackCotisation(Request $request)
    {
        return redirect()->route('busmetro.adherent.cotisations')
            ->with('info', 'Votre paiement est en cours de traitement.');
    }

    // ===== ACHAT KIT =====
    public function acheterKit(Request $request, MoneyFusionService $moneyFusion)
    {
        $adherent = auth('adherent')->user();

        if ($adherent->kit_achete) {
            return back()->with('error', 'Vous avez déjà acheté un kit');
        }

        $request->validate(['kit_id' => 'required|exists:bm_kits,id']);
        $kit = Kit::findOrFail($request->kit_id);

        $achat = AchatKit::create([
            'adherent_id' => $adherent->id,
            'kit_id' => $kit->id,
            'montant' => $kit->prix,
            'statut' => 'en_attente',
            'moyen_paiement' => 'moneyfusion',
        ]);

        $transaction = TransactionPaiement::create([
            'type' => 'kit',
            'adherent_id' => $adherent->id,
            'payable_type' => AchatKit::class,
            'payable_id' => $achat->id,
            'montant' => $kit->prix,
            'numero_telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
        ]);

        $result = $moneyFusion->initierPaiement([
            'montant' => $kit->prix,
            'telephone' => $adherent->telephone,
            'nom_client' => $adherent->nom_complet,
            'type' => 'kit',
            'reference' => $transaction->reference_interne,
            'adherent_id' => $adherent->id,
            'description' => "Kit: {$kit->nom}",
            'return_url' => route('busmetro.adherent.dashboard'),
        ]);

        if ($result['success']) {
            $transaction->update(['token_paiement' => $result['token'], 'url_paiement' => $result['url']]);
            $achat->update(['token_paiement' => $result['token']]);
            return redirect($result['url']);
        }

        return back()->with('error', 'Erreur: ' . $result['message']);
    }
}
