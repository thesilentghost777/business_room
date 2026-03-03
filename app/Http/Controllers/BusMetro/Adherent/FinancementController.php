<?php

namespace App\Http\Controllers\BusMetro\Adherent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\DemandeFinancement;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\CarnetRecette;
use App\Services\BusMetro\FinancementService;
use Illuminate\Http\Request;

class FinancementController extends Controller
{
    public function index()
{
    $adherent = auth('adherent')->user();
    $adherent->load(['financements.echeanciers', 'demandes.session']);

    $sessionOuverte = SessionFinancement::where('statut', 'candidature')
        ->where('date_fin_candidature', '>=', now())
        ->first();

    $dejaPostule = false;
    if ($sessionOuverte) {
        $dejaPostule = DemandeFinancement::where('adherent_id', $adherent->id)
            ->where('session_id', $sessionOuverte->id)->exists();
    }

    // ✅ Récupérer le financement actif de l'adhérent
    $financementActif = $adherent->financements()
        ->where('statut', 'en_cours')
        ->latest()
        ->first();

    return view('busmetro.adherent.financement', compact(
        'adherent', 'sessionOuverte', 'dejaPostule', 'financementActif'
    ));
}

    public function postuler(Request $request, FinancementService $financementService)
    {
        $request->validate([
            'session_id' => 'required|exists:bm_sessions_financement,id',
            'montant_demande' => 'required|numeric|min:1',
            'motif' => 'required|string|max:1000',
            'description_projet' => 'nullable|string|max:2000',
        ]);

        $adherent = auth('adherent')->user();
        $session = SessionFinancement::findOrFail($request->session_id);

        try {
            $financementService->soumettreDemandeFinancement(
                $adherent, $session,
                $request->montant_demande,
                $request->motif,
                $request->description_projet
            );

            return back()->with('success', 'Votre demande de financement a été soumise.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function remboursement(FinancementService $financementService)
    {
        $adherent = auth('adherent')->user();
        $financement = $adherent->financementEnCours;

        if (!$financement) {
            return back()->with('info', 'Aucun financement en cours');
        }

        $financement->load('echeanciers');

        return view('busmetro.adherent.remboursement', compact('financement'));
    }

    public function payerRemboursement(Request $request, FinancementService $financementService)
    {
        $request->validate([
            'financement_id' => 'required|exists:bm_financements,id',
            'montant' => 'required|numeric|min:1',
            'echeancier_id' => 'nullable|exists:bm_echeanciers,id',
        ]);

        $financement = \App\Models\BusMetro\Financement::findOrFail($request->financement_id);

        try {
            $result = $financementService->initierRemboursementMobile(
                $financement, $request->montant, $request->echeancier_id
            );
            return redirect($result['url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ===== CARNET DE RECETTES =====
    // ===== CARNET DE RECETTES =====
public function carnet()
{
    $adherent = auth('adherent')->user();
    $recettes = $adherent->carnetsRecettes()->orderByDesc('date_recette')->paginate(20);

    $totalRecettes = (float) $adherent->carnetsRecettes()->sum('montant_recette');
    $totalDepenses = (float) $adherent->carnetsRecettes()->sum('montant_depense');

    return view('busmetro.adherent.carnet', compact('recettes', 'totalRecettes', 'totalDepenses'));
}

public function ajouterRecette(Request $request)
{
    $request->validate([
        'date_recette'    => 'required|date|before_or_equal:today',
        'montant_recette' => 'required|numeric|min:0',
        'montant_depense' => 'nullable|numeric|min:0',
        'description'     => 'nullable|string|max:500',
        'categorie'       => 'nullable|in:vente,service,autre',
    ]);

    $adherent = auth('adherent')->user();

    CarnetRecette::create([
        'adherent_id'    => $adherent->id,
        'date_recette'   => $request->date_recette,
        'montant_recette'=> $request->montant_recette,
        'montant_depense'=> $request->montant_depense ?? 0,
        'description'    => $request->description,
        'categorie'      => $request->categorie,
    ]);

    return back()->with('success', 'Recette ajoutée avec succès !');
}
}
