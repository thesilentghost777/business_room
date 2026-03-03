<?php

namespace App\Http\Controllers\BusMetro\Agent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Profil;
use App\Models\BusMetro\Kit;
use App\Models\BusMetro\AchatKit;
use App\Models\BusMetro\TransactionPaiement;
use App\Models\BusMetro\Parrainage;
use App\Models\BusMetro\Configuration;
use App\Services\BusMetro\MoneyFusionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EnrolementController extends Controller
{
    public function index(Request $request)
    {
        $agent = auth('busmetro')->user();
        $query = Adherent::where('agent_id', $agent->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                ->orWhere('prenom', 'like', "%$s%")
                ->orWhere('telephone', 'like', "%$s%")
                ->orWhere('matricule', 'like', "%$s%"));
        }

        $adherents = $query->with('profil')->orderByDesc('created_at')->paginate(20);
        return view('busmetro.agent.enrolement.index', compact('adherents'));
    }

    public function create()
    {
        $profils = Profil::where('actif', true)->get();
        $kits = Kit::where('actif', true)->get();
        return view('busmetro.agent.enrolement.create', compact('profils', 'kits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:bm_adherents,telephone',
            'email' => 'nullable|email|unique:bm_adherents,email',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:M,F',
            'profil_id' => 'nullable|exists:bm_profils,id',
            'activite_economique' => 'nullable|string',
            'revenu_mensuel' => 'nullable|numeric|min:0',
            'ville' => 'nullable|string',
            'quartier' => 'nullable|string',
            'adresse' => 'nullable|string',
            'piece_identite_type' => 'nullable|string',
            'piece_identite_numero' => 'nullable|string',
            'code_parrainage' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        $agent = auth('busmetro')->user();
        $parrainId = null;

        if (!empty($validated['code_parrainage'])) {
            $parrain = Adherent::where('code_parrainage', $validated['code_parrainage'])->where('statut', 'actif')->first();
            $parrainId = $parrain?->id;
        }

        $adherent = Adherent::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'agent_id' => $agent->id,
            'parrain_id' => $parrainId,
            'statut' => 'en_attente',
        ]);

        if ($parrainId) {
            Parrainage::create([
                'parrain_id' => $parrainId,
                'filleul_id' => $adherent->id,
                'date_parrainage' => now(),
                'bonus_points' => (float) Configuration::get('bonus_parrainage', 2),
            ]);
        }

        return redirect()->route('busmetro.agent.enrolement.show', $adherent)
            ->with('success', "Adhérent enrôlé. Matricule: {$adherent->matricule}");
    }

    public function show(Adherent $adherent)
    {
        $this->authorizeAgent($adherent);
        $adherent->load(['profil', 'parrain', 'achatKit.kit', 'cotisationsValides']);
        $kits = Kit::where('actif', true)->get();

        return view('busmetro.agent.enrolement.show', compact('adherent', 'kits'));
    }

    public function acheterKit(Request $request, Adherent $adherent, MoneyFusionService $moneyFusion)
    {
        $request->validate([
            'kit_id' => 'required|exists:bm_kits,id',
            'mode_paiement' => 'required|in:especes,moneyfusion',
        ]);

        $kit = Kit::findOrFail($request->kit_id);

        if ($request->mode_paiement === 'especes') {
            $achat = AchatKit::create([
                'adherent_id' => $adherent->id,
                'kit_id' => $kit->id,
                'montant' => $kit->prix,
                'statut' => 'paye',
                'moyen_paiement' => 'especes',
                'agent_id' => auth('busmetro')->id(),
            ]);

            $adherent->update(['kit_achete' => true, 'date_adhesion' => now(), 'statut' => 'actif']);

            return back()->with('success', 'Kit acheté en espèces');
        }

        // MoneyFusion
        $achat = AchatKit::create([
            'adherent_id' => $adherent->id,
            'kit_id' => $kit->id,
            'montant' => $kit->prix,
            'statut' => 'en_attente',
            'moyen_paiement' => 'moneyfusion',
            'agent_id' => auth('busmetro')->id(),
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
        ]);

        if ($result['success']) {
            $transaction->update(['token_paiement' => $result['token'], 'url_paiement' => $result['url']]);
            $achat->update(['token_paiement' => $result['token']]);
            return redirect($result['url']);
        }

        return back()->with('error', 'Erreur paiement: ' . $result['message']);
    }

    private function authorizeAgent(Adherent $adherent): void
    {
        $agent = auth('busmetro')->user();
        if (!$agent->isAdmin() && $adherent->agent_id !== $agent->id) {
            abort(403);
        }
    }
}
