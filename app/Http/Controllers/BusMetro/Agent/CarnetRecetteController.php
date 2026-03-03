<?php

namespace App\Http\Controllers\BusMetro\Agent;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\CarnetRecette;
use App\Models\BusMetro\Adherent;
use Illuminate\Http\Request;

class CarnetRecetteController extends Controller
{
    public function index(Request $request)
    {
        $agent = auth('busmetro')->user();

        $query = CarnetRecette::whereHas('adherent', fn($q) => $q->where('agent_id', $agent->id))
            ->with('adherent');

        if ($request->filled('adherent_id')) $query->where('adherent_id', $request->adherent_id);
        if ($request->filled('valide')) $query->where('valide', $request->valide === 'oui');

        $recettes = $query->orderByDesc('date_recette')->paginate(20);

        return view('busmetro.agent.carnets.index', compact('recettes'));
    }

    public function valider(CarnetRecette $recette)
    {
        $recette->update(['valide' => true, 'valide_par' => auth('busmetro')->id()]);
        return back()->with('success', 'Recette validée');
    }
}
