<?php

namespace App\Http\Controllers\BusMetro\Agent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\Remboursement;

class DashboardController extends Controller
{
    public function index()
    {
        $agent = auth('busmetro')->user();

        $enrolements = Adherent::where('agent_id', $agent->id)
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalAdherents = Adherent::where('agent_id', $agent->id)->count();

        $collectes = (float) Cotisation::where('agent_id', $agent->id)
            ->where('statut', 'valide')
            ->whereMonth('date_cotisation', now()->month)
            ->sum('montant');

        $remboursements = (float) Remboursement::where('agent_id', $agent->id)
            ->where('statut', 'valide')
            ->whereMonth('created_at', now()->month)
            ->sum('montant');

        $mesAdherentsRecents = Adherent::where('agent_id', $agent->id)
            ->orderByDesc('created_at')->limit(10)->get();

        $cotisationsRecentes = Cotisation::with('adherent', 'typeCotisation')
            ->where('agent_id', $agent->id)
            ->where('statut', 'valide')
            ->orderByDesc('created_at')->limit(10)->get();

        return view('busmetro.agent.dashboard', compact(
            'enrolements',
            'totalAdherents',
            'collectes',
            'remboursements',
            'mesAdherentsRecents',
            'cotisationsRecentes'
        ));
    }
}
