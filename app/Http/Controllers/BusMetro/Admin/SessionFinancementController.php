<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\DemandeFinancement;
use App\Models\BusMetro\Financement;
use App\Services\BusMetro\ScoringService;
use App\Services\BusMetro\FinancementService;
use Illuminate\Http\Request;

class SessionFinancementController extends Controller
{
    public function index()
    {
        $sessions = SessionFinancement::withCount('demandes', 'financements')
            ->orderByDesc('annee')->orderByDesc('trimestre')
            ->paginate(15);

        return view('busmetro.admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('busmetro.admin.sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'trimestre' => 'required|integer|min:1|max:4',
            'annee' => 'required|integer|min:2024',
            'date_debut_candidature' => 'required|date',
            'date_fin_candidature' => 'required|date|after:date_debut_candidature',
            'budget_total' => 'required|numeric|min:0',
            'nombre_beneficiaires_max' => 'required|integer|min:1',
            'score_minimum' => 'required|numeric|min:0|max:100',
        ]);

        $validated['statut'] = 'preparation';
        $validated['creee_par'] = auth('busmetro')->id();

        SessionFinancement::create($validated);

        return redirect()->route('busmetro.admin.sessions.index')->with('success', 'Session créée');
    }

    public function show(SessionFinancement $session)
    {
        $session->load(['demandes.adherent.profil', 'financements.adherent']);

        $demandes = $session->demandes()->with('adherent')
            ->orderByDesc('score_total')->get();

        return view('busmetro.admin.sessions.show', compact('session', 'demandes'));
    }

    public function edit(SessionFinancement $session)
    {
        return view('busmetro.admin.sessions.edit', compact('session'));
    }

    public function update(Request $request, SessionFinancement $session)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'trimestre' => 'required|integer|min:1|max:4',
            'annee' => 'required|integer',
            'date_debut_candidature' => 'required|date',
            'date_fin_candidature' => 'required|date',
            'date_selection' => 'nullable|date',
            'date_debut_financement' => 'nullable|date',
            'budget_total' => 'required|numeric|min:0',
            'nombre_beneficiaires_max' => 'required|integer|min:1',
            'score_minimum' => 'required|numeric|min:0|max:100',
        ]);

        $session->update($validated);

        return redirect()->route('busmetro.admin.sessions.show', $session)->with('success', 'Session mise à jour');
    }

    public function changerStatut(Request $request, SessionFinancement $session)
    {
        $request->validate(['statut' => 'required|in:preparation,candidature,selection,validation,financement,cloturee']);
        $session->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut de la session mis à jour');
    }

    public function lancerScoring(SessionFinancement $session, ScoringService $scoringService)
    {
        $scoringService->calculerScoresSession($session);

        return back()->with('success', 'Scoring calculé pour tous les adhérents');
    }

    public function selectionnerBeneficiaires(SessionFinancement $session, ScoringService $scoringService)
    {
        $beneficiaires = $scoringService->selectionnerBeneficiaires($session);

        // Pré-sélectionner les demandes des bénéficiaires
        foreach ($beneficiaires as $index => $beneficiaire) {
            $demande = DemandeFinancement::where('adherent_id', $beneficiaire['id'])
                ->where('session_id', $session->id)
                ->first();

            if ($demande) {
                $demande->update([
                    'statut' => 'pre_selectionnee',
                    'rang' => $index + 1,
                    'score_total' => $beneficiaire['score_actuel'],
                ]);
            }
        }

        return back()->with('success', count($beneficiaires) . ' bénéficiaires pré-sélectionnés');
    }

    public function validerDemande(Request $request, DemandeFinancement $demande)
    {
        $request->validate(['action' => 'required|in:valider,rejeter', 'commentaire' => 'nullable|string']);

        $demande->update([
            'statut' => $request->action === 'valider' ? 'validee' : 'rejetee',
            'commentaire_direction' => $request->commentaire,
            'validee_par' => auth('busmetro')->id(),
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Demande ' . ($request->action === 'valider' ? 'validée' : 'rejetée'));
    }

    public function accorderFinancement(
        Request $request,
        DemandeFinancement $demande,
        FinancementService $financementService
    ) {
        $request->validate([
            'montant_accorde' => 'required|numeric|min:1',
            'duree_mois' => 'required|integer|min:1|max:36',
            'taux_interet' => 'required|numeric|min:0',
        ]);

        try {
            $financementService->accorderFinancement(
                $demande,
                $request->montant_accorde,
                $request->duree_mois,
                $request->taux_interet,
                auth('busmetro')->id()
            );

            return back()->with('success', 'Financement accordé');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
