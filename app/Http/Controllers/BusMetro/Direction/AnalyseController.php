<?php

namespace App\Http\Controllers\BusMetro\Direction;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\DemandeFinancement;
use App\Services\BusMetro\FinancementService;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function sessions()
    {
        $sessions = SessionFinancement::withCount('demandes', 'financements')
            ->orderByDesc('annee')->orderByDesc('trimestre')
            ->paginate(15);

        return view('busmetro.direction.sessions', compact('sessions'));
    }

    public function analyserSession(SessionFinancement $session)
    {
        $session->load(['demandes.adherent.profil']);

        $demandes = $session->demandes()
            ->with('adherent.profil')
            ->orderByDesc('score_total')
            ->get();

        return view('busmetro.direction.sessions.analyse', compact('session', 'demandes'));
    }

    public function validerDemande(Request $request, DemandeFinancement $demande)
    {
        $request->validate([
            'action' => 'required|in:valider,rejeter',
            'commentaire' => 'nullable|string',
        ]);

        $demande->update([
            'statut' => $request->action === 'valider' ? 'validee' : 'rejetee',
            'commentaire_direction' => $request->commentaire,
            'validee_par' => auth('busmetro')->id(),
            'date_validation' => now(),
        ]);

        \App\Models\BusMetro\Notification::envoyer(
            'adherent', $demande->adherent_id,
            $request->action === 'valider' ? 'Demande validée' : 'Demande rejetée',
            $request->action === 'valider'
                ? 'Votre demande de financement a été validée !'
                : 'Votre demande de financement a été rejetée. ' . ($request->commentaire ?? ''),
            $request->action === 'valider' ? 'success' : 'danger'
        );

        return back()->with('success', 'Demande traitée');
    }

    public function accorderFinancement(Request $request, DemandeFinancement $demande, FinancementService $service)
    {
        $request->validate([
            'montant_accorde' => 'required|numeric|min:1',
            'duree_mois' => 'required|integer|min:1|max:36',
            'taux_interet' => 'required|numeric|min:0',
        ]);

        try {
            $service->accorderFinancement(
                $demande,
                $request->montant_accorde,
                $request->duree_mois,
                $request->taux_interet,
                auth('busmetro')->id()
            );

            return back()->with('success', 'Financement accordé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
