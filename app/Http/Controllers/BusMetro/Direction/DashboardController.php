<?php

namespace App\Http\Controllers\BusMetro\Direction;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Financement;
use App\Models\BusMetro\SessionFinancement;
use App\Models\BusMetro\DemandeFinancement;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\Adherent;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'sessions_en_cours' => SessionFinancement::whereIn('statut', ['candidature', 'selection', 'validation', 'financement'])->count(),
            'demandes_en_attente' => DemandeFinancement::whereIn('statut', ['en_attente', 'pre_selectionnee'])->count(),
            'financements_actifs' => Financement::where('statut', 'en_cours')->count(),
            'total_finance' => (float) Financement::sum('montant_accorde'),
            'total_rembourse' => (float) Financement::sum('montant_rembourse'),
            'taux_remboursement' => $this->tauxRemboursement(),
            'adherents_eligibles' => Adherent::where('statut', 'actif')
                ->where('kit_achete', true)
                ->where('score_actuel', '>=', 60)
                ->doesntHave('financementEnCours')
                ->count(),
        ];

        $sessionsRecentes = SessionFinancement::withCount('demandes', 'financements')
            ->orderByDesc('annee')->orderByDesc('trimestre')
            ->limit(5)->get();

        $topScores = Adherent::where('statut', 'actif')
            ->where('score_actuel', '>', 0)
            ->orderByDesc('score_actuel')
            ->limit(10)->get();

        return view('busmetro.direction.dashboard', compact('stats', 'sessionsRecentes', 'topScores'));
    }

    private function tauxRemboursement(): float
    {
        $du = (float) Financement::sum('montant_total_du');
        $rembourse = (float) Financement::sum('montant_rembourse');
        return $du > 0 ? round(($rembourse / $du) * 100, 2) : 0;
    }
}
