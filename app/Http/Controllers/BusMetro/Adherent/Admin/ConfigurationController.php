<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Kit;
use App\Models\BusMetro\AchatKit;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\CritereScoring;
use App\Models\BusMetro\Profil;
use App\Models\BusMetro\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configs = Configuration::orderBy('groupe')->orderBy('cle')->get()->groupBy('groupe');
        return view('busmetro.admin.configuration.index', compact('configs'));
    }

    public function updateConfigs(Request $request)
    {
        foreach ($request->input('configs', []) as $cle => $valeur) {
            Configuration::set($cle, $valeur);
        }
        return back()->with('success', 'Configuration mise à jour');
    }

    // ===== KITS =====
    public function kits()
    {
        $kits = Kit::withCount('achats')->get();
        return view('busmetro.admin.configuration.kits', compact('kits'));
    }

    public function storeKit(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'contenu' => 'required|array',
            'contenu.*' => 'string',
        ]);
        $validated['actif'] = $request->boolean('actif', true);
        Kit::create($validated);
        return back()->with('success', 'Kit créé');
    }

    public function updateKit(Request $request, Kit $kit)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'contenu' => 'required|array',
        ]);
        $validated['actif'] = $request->boolean('actif');
        $kit->update($validated);
        return back()->with('success', 'Kit mis à jour');
    }

    // ===== TYPES COTISATION =====
    public function typesCotisation()
    {
        $types = TypeCotisation::all();
        return view('busmetro.admin.configuration.types-cotisation', compact('types'));
    }

    public function storeTypeCotisation(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:bm_types_cotisation,code',
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'montant_minimum' => 'required|numeric|min:0',
            'montant_defaut' => 'required|numeric|min:0',
            'frequence' => 'required|in:journalier,hebdomadaire,mensuel',
            'obligatoire' => 'boolean',
            'donne_droit_soutien' => 'boolean',
        ]);
        $validated['obligatoire'] = $request->boolean('obligatoire');
        $validated['donne_droit_soutien'] = $request->boolean('donne_droit_soutien');
        TypeCotisation::create($validated);
        return back()->with('success', 'Type de cotisation créé');
    }

    public function updateTypeCotisation(Request $request, TypeCotisation $type)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'montant_minimum' => 'required|numeric|min:0',
            'montant_defaut' => 'required|numeric|min:0',
            'frequence' => 'required|in:journalier,hebdomadaire,mensuel',
        ]);
        $validated['obligatoire'] = $request->boolean('obligatoire');
        $validated['donne_droit_soutien'] = $request->boolean('donne_droit_soutien');
        $validated['actif'] = $request->boolean('actif');
        $type->update($validated);
        return back()->with('success', 'Type mis à jour');
    }

    // ===== CRITERES SCORING =====
    public function criteresScoring()
    {
        $criteres = CritereScoring::orderBy('ordre')->get();
        return view('busmetro.admin.configuration.criteres-scoring', compact('criteres'));
    }

    public function storeCritereScoring(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:bm_criteres_scoring,code',
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'poids' => 'required|integer|min:1',
            'max_points' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:0',
        ]);
        $validated['actif'] = $request->boolean('actif', true);
        CritereScoring::create($validated);
        return back()->with('success', 'Critère créé');
    }

    public function updateCritereScoring(Request $request, CritereScoring $critere)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'poids' => 'required|integer|min:1',
            'max_points' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:0',
        ]);
        $validated['actif'] = $request->boolean('actif');
        $critere->update($validated);
        return back()->with('success', 'Critère mis à jour');
    }

    // ===== PROFILS =====
    public function profils()
    {
        $profils = Profil::withCount('adherents')->get();
        return view('busmetro.admin.configuration.profils', compact('profils'));
    }

    public function storeProfil(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:bm_profils,code',
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'documents_requis' => 'required|array',
            'plafond_financement' => 'required|numeric|min:0',
        ]);
        $validated['actif'] = $request->boolean('actif', true);
        Profil::create($validated);
        return back()->with('success', 'Profil créé');
    }

    public function updateProfil(Request $request, Profil $profil)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'documents_requis' => 'required|array',
            'plafond_financement' => 'required|numeric|min:0',
        ]);
        $validated['actif'] = $request->boolean('actif');
        $profil->update($validated);
        return back()->with('success', 'Profil mis à jour');
    }
}
