<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\Financement;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\User;
use App\Models\BusMetro\TransactionPaiement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_adherents' => Adherent::count(),
            'adherents_actifs' => Adherent::where('statut', 'actif')->count(),
            'nouveaux_ce_mois' => Adherent::whereMonth('created_at', now()->month)->count(),
            'total_agents' => User::where('role', 'agent')->where('is_active', true)->count(),
            'total_cotisations' => (float) Cotisation::where('statut', 'valide')->sum('montant'),
            'cotisations_ce_mois' => (float) Cotisation::where('statut', 'valide')
                ->whereMonth('date_cotisation', now()->month)->sum('montant'),
            'financements_en_cours' => Financement::where('statut', 'en_cours')->count(),
            'total_finance' => (float) Financement::sum('montant_accorde'),
            'total_rembourse' => (float) Financement::sum('montant_rembourse'),
            'taux_remboursement' => $this->calculerTauxRemboursement(),
            'sessions_actives' => SessionFinancement::whereIn('statut', ['candidature', 'selection', 'validation'])->count(),
        ];

        $adherentsRecents = Adherent::with('profil', 'agent')
            ->orderByDesc('created_at')->limit(10)->get();

        $cotisationsRecentes = Cotisation::with('adherent', 'typeCotisation')
            ->where('statut', 'valide')
            ->orderByDesc('created_at')->limit(10)->get();

        $evolutionCotisations = Cotisation::where('statut', 'valide')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw("DATE_FORMAT(date_cotisation, '%Y-%m') as mois"), DB::raw('SUM(montant) as total'))
            ->groupBy('mois')->orderBy('mois')->get();

        $repartitionProfils = Adherent::select('profil_id', DB::raw('count(*) as total'))
            ->whereNotNull('profil_id')
            ->groupBy('profil_id')
            ->with('profil')
            ->get();

        return view('busmetro.admin.dashboard', compact(
            'stats', 'adherentsRecents', 'cotisationsRecentes',
            'evolutionCotisations', 'repartitionProfils'
        ));
    }

    private function calculerTauxRemboursement(): float
    {
        $totalDu = (float) Financement::sum('montant_total_du');
        $totalRembourse = (float) Financement::sum('montant_rembourse');
        return $totalDu > 0 ? round(($totalRembourse / $totalDu) * 100, 2) : 0;
    }
}
