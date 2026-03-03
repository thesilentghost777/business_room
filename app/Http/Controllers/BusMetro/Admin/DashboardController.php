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
        // --- Stats principales ---
        $totalAdherents     = Adherent::where('statut', 'actif')->count();
        $nouveauxAdherents  = Adherent::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count();
        $totalCotisations   = (float) Cotisation::where('statut', 'valide')
                                      ->whereMonth('date_cotisation', now()->month)
                                      ->whereYear('date_cotisation', now()->year)
                                      ->sum('montant');
        $totalFinancements  = (float) Financement::where('statut', 'en_cours')->sum('montant_accorde');
        $tauxRemboursement  = $this->calculerTauxRemboursement();

        // --- Derniers adhérents (5) ---
        $derniersAdherents = Adherent::orderByDesc('created_at')->limit(5)->get();

        // --- Dernières transactions (5) ---
        $dernieresTransactions = TransactionPaiement::orderByDesc('created_at')->limit(5)->get();

        // --- Activité récente (combinaison adhérents + cotisations) ---
        $activites = collect();

        Adherent::orderByDesc('created_at')->limit(3)->get()->each(function ($adh) use (&$activites) {
            $activites->push([
                'icon'  => 'user-plus',
                'titre' => "Nouvel adhérent : {$adh->prenom} {$adh->nom}",
                'date'  => $adh->created_at->diffForHumans(),
            ]);
        });

        Cotisation::with('adherent')->where('statut', 'valide')
            ->orderByDesc('created_at')->limit(3)->get()
            ->each(function ($c) use (&$activites) {
                $nom = $c->adherent ? "{$c->adherent->prenom} {$c->adherent->nom}" : 'Adhérent';
                $activites->push([
                    'icon'  => 'coins',
                    'titre' => "Cotisation de {$nom} : " . number_format($c->montant) . " F",
                    'date'  => $c->created_at->diffForHumans(),
                ]);
            });

        $activites = $activites->sortByDesc(fn($a) => $a['date'])->values()->take(6);

        // --- Données graphique (6 derniers mois) ---
        $evolution = Cotisation::where('statut', 'valide')
            ->where('date_cotisation', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(date_cotisation, '%Y-%m') as mois"),
                DB::raw('SUM(montant) as total')
            )
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');

        // Construire les 6 derniers mois même si certains sont vides
        $chartLabels = [];
        $chartData   = [];
        $moisFr      = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $key   = $date->format('Y-m');
            $chartLabels[] = $moisFr[$date->month - 1];
            $chartData[]   = $evolution->has($key) ? (float) $evolution[$key]->total : 0;
        }

        return view('busmetro.admin.dashboard', compact(
            'totalAdherents',
            'nouveauxAdherents',
            'totalCotisations',
            'totalFinancements',
            'tauxRemboursement',
            'derniersAdherents',
            'dernieresTransactions',
            'activites',
            'chartLabels',
            'chartData'
        ));
    }

    private function calculerTauxRemboursement(): float
    {
        $totalDu       = (float) Financement::sum('montant_total_du');
        $totalRembourse = (float) Financement::sum('montant_rembourse');
        return $totalDu > 0 ? round(($totalRembourse / $totalDu) * 100, 2) : 0;
    }
}
