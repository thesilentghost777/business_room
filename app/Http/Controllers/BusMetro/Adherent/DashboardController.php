<?php

namespace App\Http\Controllers\BusMetro\Adherent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\Notification;
use App\Models\BusMetro\SessionFinancement;

class DashboardController extends Controller
{
    public function index()
    {
        $adherent = auth('adherent')->user();
        $adherent->load(['profil', 'financementEnCours.echeanciers', 'achatKit.kit']);

        $stats = [
            'score' => $adherent->score_actuel,
            'total_cotisations' => (float) $adherent->cotisationsValides()->sum('montant'),
            'cotisations_ce_mois' => (float) $adherent->cotisationsValides()
                ->whereMonth('date_cotisation', now()->month)->count(),
            'filleuls' => $adherent->filleuls()->where('statut', 'actif')->count(),
            'financement_en_cours' => $adherent->financementEnCours,
        ];

        $dernieresCotisations = $adherent->cotisationsValides()
            ->with('typeCotisation')
            ->orderByDesc('date_cotisation')
            ->limit(10)->get();

        $notifications = Notification::where('destinataire_type', 'adherent')
            ->where('destinataire_id', $adherent->id)
            ->where('lu', false)
            ->orderByDesc('created_at')
            ->limit(5)->get();

        $sessionOuverte = SessionFinancement::where('statut', 'candidature')
            ->where('date_fin_candidature', '>=', now())
            ->first();

        return view('busmetro.adherent.dashboard', compact(
    'adherent', 'stats', 'dernieresCotisations', 'notifications', 'sessionOuverte'
));
    }

    public function profil()
    {
        $adherent = auth('adherent')->user();
        $adherent->load(['profil', 'parrain', 'filleuls', 'achatKit.kit']);

        return view('busmetro.adherent.profil', compact('adherent'));
    }

    public function updateProfil(\Illuminate\Http\Request $request)
{
    $adherent = auth('adherent')->user();

    $validated = $request->validate([
        'nom'                   => 'nullable|string|max:100',
        'prenom'                => 'nullable|string|max:100',
        'email'                 => 'nullable|email|unique:bm_adherents,email,' . $adherent->id,
        'ville'                 => 'nullable|string',
        'quartier'              => 'nullable|string',
        'adresse'               => 'nullable|string',
        'activite_economique'   => 'nullable|string',
        'description_activite'  => 'nullable|string',
        'revenu_mensuel'        => 'nullable|numeric|min:0',
        'password'              => 'nullable|string|min:6',
    ]);

    // Gérer le mot de passe séparément
    if (!empty($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $adherent->update($validated);

    return back()->with('success', 'Profil mis à jour avec succès');
}

    public function notifications()
    {
        $adherent = auth('adherent')->user();

        $notifications = Notification::where('destinataire_type', 'adherent')
            ->where('destinataire_id', $adherent->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('busmetro.adherent.notifications', compact('notifications'));
    }

    public function lireNotification(Notification $notification)
    {
        $notification->marquerCommeLu();
        return $notification->lien ? redirect($notification->lien) : back();
    }
}
