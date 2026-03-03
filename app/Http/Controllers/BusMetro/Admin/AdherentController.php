<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\Profil;
use App\Models\BusMetro\User;
use App\Models\BusMetro\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdherentController extends Controller
{
    public function index(Request $request)
    {
        $query = Adherent::with(['profil', 'agent', 'parrain']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                ->orWhere('prenom', 'like', "%$s%")
                ->orWhere('telephone', 'like', "%$s%")
                ->orWhere('matricule', 'like', "%$s%"));
        }

        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('profil_id')) $query->where('profil_id', $request->profil_id);
        if ($request->filled('kit')) {
            $query->where('kit_achete', $request->kit === 'oui');
        }

        $adherents = $query->orderByDesc('created_at')->paginate(25);
        $profils = Profil::where('actif', true)->get();

        return view('busmetro.admin.adherents.index', compact('adherents', 'profils'));
    }

    public function show(Adherent $adherent)
    {
        $adherent->load([
            'profil', 'agent', 'parrain', 'filleuls',
            'cotisationsValides.typeCotisation', 'financements.echeanciers',
            'carnetsRecettes', 'achatKit.kit'
        ]);

        return view('busmetro.admin.adherents.show', compact('adherent'));
    }

    public function edit(Adherent $adherent)
    {
        $profils = Profil::where('actif', true)->get();
        $agents = User::where('role', 'agent')->where('is_active', true)->get();

        return view('busmetro.admin.adherents.edit', compact('adherent', 'profils', 'agents'));
    }

    public function update(Request $request, Adherent $adherent)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:bm_adherents,telephone,' . $adherent->id,
            'email' => 'nullable|email|unique:bm_adherents,email,' . $adherent->id,
            'profil_id' => 'nullable|exists:bm_profils,id',
            'activite_economique' => 'nullable|string',
            'revenu_mensuel' => 'nullable|numeric|min:0',
            'ville' => 'nullable|string',
            'quartier' => 'nullable|string',
            'statut' => 'required|in:en_attente,actif,suspendu,radie',
        ]);

        $ancien = $adherent->toArray();
        $adherent->update($validated);
        AuditLog::log('update', $adherent, $ancien, $validated);

        return redirect()->route('busmetro.admin.adherents.show', $adherent)
            ->with('success', 'Adhérent mis à jour');
    }

    public function changerStatut(Request $request, Adherent $adherent)
    {
        $request->validate(['statut' => 'required|in:actif,suspendu,radie']);
        $adherent->update(['statut' => $request->statut]);
        AuditLog::log('changement_statut', $adherent);

        return back()->with('success', 'Statut modifié');
    }

    public function resetPassword(Request $request, Adherent $adherent)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $adherent->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe réinitialisé');
    }

    public function destroy(Adherent $adherent)
    {
        AuditLog::log('delete', $adherent);
        $adherent->delete();
        return redirect()->route('busmetro.admin.adherents.index')->with('success', 'Adhérent supprimé');
    }
}
