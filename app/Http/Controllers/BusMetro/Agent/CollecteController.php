<?php
namespace App\Http\Controllers\BusMetro\Agent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\Financement;
use App\Services\BusMetro\CotisationService;
use App\Services\BusMetro\FinancementService;
use Illuminate\Http\Request;

class CollecteController extends Controller
{
    // ===== COTISATIONS =====

    public function cotisationForm()
    {
        $typesCotisation = TypeCotisation::where('actif', true)->get();
        return view('busmetro.agent.collecte.cotisation', compact('typesCotisation'));
    }

    public function rechercherAdherent(Request $request)
    {
        $request->validate(['telephone' => 'required|string']); // ← était 'search'

        $s = trim($request->telephone); // ← était $request->search

        $adherent = Adherent::where('statut', 'actif')
            // ← suppression du filtre kit_achete qui bloquait les résultats
            ->where(function ($q) use ($s) {
                $q->where('telephone', $s)
                  ->orWhere('matricule', $s);
            })
            ->first();

        $typesCotisation = TypeCotisation::where('actif', true)->get(); // ← toujours repasser la variable

        if (!$adherent) {
            return back()
                ->with('error', 'Adhérent non trouvé ou inactif')
                ->with('search', $s);
        }

        return view('busmetro.agent.collecte.cotisation', compact('adherent', 'typesCotisation'));
    }

    public function enregistrerCotisation(Request $request, CotisationService $cotisationService)
    {
        $request->validate([
            'adherent_id'        => 'required|exists:bm_adherents,id',
            'type_cotisation_id' => 'required|exists:bm_types_cotisation,id',
            'montant'            => 'required|numeric|min:1',
            'mode_paiement'      => 'nullable|string',
            'commentaire'        => 'nullable|string',
        ]);

        $adherent = Adherent::findOrFail($request->adherent_id);
        $type     = TypeCotisation::findOrFail($request->type_cotisation_id);

        try {
            $cotisationService->enregistrerEspeces(
                $adherent,
                $type,
                $request->montant,
                auth('busmetro')->id(),
                $request->commentaire
            );

            return redirect()->route('busmetro.agent.collecte.cotisation')
                ->with('success', "Cotisation de {$request->montant} FCFA enregistrée pour {$adherent->prenom} {$adherent->nom}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ===== REMBOURSEMENTS =====

    public function remboursementForm()
    {
        return view('busmetro.agent.collecte.remboursement');
    }

    public function rechercherFinancement(Request $request)
    {
        $request->validate(['telephone' => 'required|string']); // ← était 'search'

        $s = trim($request->telephone); // ← était $request->search

        $adherent = Adherent::where(function ($q) use ($s) {
            $q->where('telephone', $s)
              ->orWhere('matricule', $s);
        })->first();

        if (!$adherent) {
            return back()->with('error', 'Adhérent non trouvé');
        }

        $financement = $adherent->financementEnCours;

        if (!$financement) {
            return back()->with('error', 'Aucun financement en cours pour cet adhérent');
        }

        $financement->load('echeanciers');

        return view('busmetro.agent.collecte.remboursement', compact('adherent', 'financement'));
    }

    public function enregistrerRemboursement(Request $request, FinancementService $financementService)
    {
        $request->validate([
            'financement_id' => 'required|exists:bm_financements,id',
            'montant'        => 'required|numeric|min:1',
            'echeancier_id'  => 'nullable|exists:bm_echeanciers,id',
            'commentaire'    => 'nullable|string',
        ]);

        $financement = Financement::findOrFail($request->financement_id);

        try {
            $financementService->enregistrerRemboursementEspeces(
                $financement,
                $request->montant,
                auth('busmetro')->id(),
                $request->echeancier_id,
                $request->commentaire
            );

            return redirect()->route('busmetro.agent.collecte.remboursement')
                ->with('success', "Remboursement de {$request->montant} FCFA enregistré");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
