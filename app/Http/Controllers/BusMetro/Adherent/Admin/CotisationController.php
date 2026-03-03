<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Cotisation;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\TransactionPaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotisationController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotisation::with(['adherent', 'typeCotisation', 'agent']);

        if ($request->filled('type_id')) $query->where('type_cotisation_id', $request->type_id);
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('date_debut')) $query->whereDate('date_cotisation', '>=', $request->date_debut);
        if ($request->filled('date_fin')) $query->whereDate('date_cotisation', '<=', $request->date_fin);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('adherent', fn($q) => $q->where('nom', 'like', "%$s%")
                ->orWhere('prenom', 'like', "%$s%")
                ->orWhere('matricule', 'like', "%$s%"));
        }

        $cotisations = $query->orderByDesc('date_cotisation')->paginate(30);
        $types = TypeCotisation::where('actif', true)->get();

        $stats = [
            'total_valide' => (float) Cotisation::where('statut', 'valide')->sum('montant'),
            'total_nkd' => (float) Cotisation::where('statut', 'valide')
                ->whereHas('typeCotisation', fn($q) => $q->where('code', 'NKD'))->sum('montant'),
            'total_nkh' => (float) Cotisation::where('statut', 'valide')
                ->whereHas('typeCotisation', fn($q) => $q->where('code', 'NKH'))->sum('montant'),
            'ce_mois' => (float) Cotisation::where('statut', 'valide')
                ->whereMonth('date_cotisation', now()->month)->sum('montant'),
            'aujourdhui' => (float) Cotisation::where('statut', 'valide')
                ->whereDate('date_cotisation', today())->sum('montant'),
        ];

        return view('busmetro.admin.cotisations.index', compact('cotisations', 'types', 'stats'));
    }

    public function rapport(Request $request)
    {
        $mois = $request->input('mois', now()->month);
        $annee = $request->input('annee', now()->year);

        $parJour = Cotisation::where('statut', 'valide')
            ->whereMonth('date_cotisation', $mois)
            ->whereYear('date_cotisation', $annee)
            ->select(
                DB::raw('DATE(date_cotisation) as date'),
                DB::raw('SUM(montant) as total'),
                DB::raw('COUNT(*) as nombre')
            )->groupBy('date')->orderBy('date')->get();

        $parType = Cotisation::where('statut', 'valide')
            ->whereMonth('date_cotisation', $mois)
            ->whereYear('date_cotisation', $annee)
            ->join('bm_types_cotisation', 'bm_cotisations.type_cotisation_id', '=', 'bm_types_cotisation.id')
            ->select('bm_types_cotisation.nom as type_nom', DB::raw('SUM(bm_cotisations.montant) as total'), DB::raw('COUNT(*) as nombre'))
            ->groupBy('type_nom')->get();

        $total = Cotisation::where('statut', 'valide')
            ->whereMonth('date_cotisation', $mois)
            ->whereYear('date_cotisation', $annee)
            ->sum('montant');

        return view('busmetro.admin.cotisations.rapport', compact('parJour', 'parType', 'total', 'mois', 'annee'));
    }
}
