<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Financement;
use App\Models\BusMetro\Remboursement;
use App\Models\BusMetro\Echeancier;
use App\Services\BusMetro\FinancementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancementController extends Controller
{
    public function index(Request $request)
    {
        $query = Financement::with(['adherent', 'session']);

        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('reference', 'like', "%$s%")
                ->orWhereHas('adherent', fn($q2) => $q2->where('nom', 'like', "%$s%")
                    ->orWhere('prenom', 'like', "%$s%")
                    ->orWhere('matricule', 'like', "%$s%")));
        }

        $financements = $query->orderByDesc('created_at')->paginate(20);

        $stats = [
            'total_accorde' => (float) Financement::sum('montant_accorde'),
            'total_rembourse' => (float) Financement::sum('montant_rembourse'),
            'en_cours' => Financement::where('statut', 'en_cours')->count(),
            'soldes' => Financement::where('statut', 'solde')->count(),
            'defaut' => Financement::where('statut', 'defaut')->count(),
        ];

        return view('busmetro.admin.financements.index', compact('financements', 'stats'));
    }

    public function show(Financement $financement)
    {
        $financement->load(['adherent.profil', 'session', 'demande',
            'echeanciers', 'remboursements.agent', 'approbateur']);

        return view('busmetro.admin.financements.show', compact('financement'));
    }

    public function appliquerPenalites(FinancementService $financementService)
    {
        $financementService->appliquerPenalites();
        return back()->with('success', 'Pénalités appliquées');
    }

    public function statistiques(FinancementService $financementService)
    {
        $stats = $financementService->getStatistiquesGlobales();

        $parMois = Financement::select(
            DB::raw("DATE_FORMAT(date_debut, '%Y-%m') as mois"),
            DB::raw('SUM(montant_accorde) as total_accorde'),
            DB::raw('COUNT(*) as nombre')
        )->groupBy('mois')->orderBy('mois')->get();

        return view('busmetro.admin.financements.statistiques', compact('stats', 'parMois'));
    }
}
