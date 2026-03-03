<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\SoutienNkh;
use Illuminate\Http\Request;

class SoutienController extends Controller
{
    public function index(Request $request)
    {
        $query = SoutienNkh::with(['adherent', 'approbateur']);

        if ($request->filled('statut')) $query->where('statut', $request->statut);

        $soutiens = $query->orderByDesc('created_at')->paginate(20);
        return view('busmetro.admin.soutiens.index', compact('soutiens'));
    }

    public function traiter(Request $request, SoutienNkh $soutien)
    {
        $request->validate([
            'action' => 'required|in:approuver,rejeter',
            'montant' => 'nullable|numeric|min:0',
        ]);

        if ($request->action === 'approuver') {
            $soutien->update([
                'statut' => 'approuve',
                'montant' => $request->montant ?? $soutien->montant,
                'approuve_par' => auth('busmetro')->id(),
            ]);
        } else {
            $soutien->update(['statut' => 'rejete']);
        }

        return back()->with('success', 'Soutien ' . ($request->action === 'approuver' ? 'approuvé' : 'rejeté'));
    }

    public function verser(SoutienNkh $soutien)
    {
        if ($soutien->statut !== 'approuve') {
            return back()->with('error', 'Ce soutien n\'est pas approuvé');
        }

        $soutien->update(['statut' => 'verse', 'date_versement' => now()]);

        \App\Models\BusMetro\Notification::envoyer(
            'adherent', $soutien->adherent_id,
            'Soutien NKH versé',
            "Votre soutien de {$soutien->montant} FCFA a été versé.",
            'success'
        );

        return back()->with('success', 'Soutien versé');
    }
}
