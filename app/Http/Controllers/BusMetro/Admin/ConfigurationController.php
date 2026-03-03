<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\Kit;
use App\Models\BusMetro\TypeCotisation;
use App\Models\BusMetro\CritereScoring;
use App\Models\BusMetro\Profil;
use App\Models\BusMetro\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configs         = Configuration::orderBy('groupe')->orderBy('cle')->get();
        $kits            = Kit::all();
        $typesCotisation = TypeCotisation::all();
        $criteres        = CritereScoring::orderBy('ordre')->get();
        $profils         = Profil::withCount('adherents')->get();

        return view('busmetro.admin.configuration.index', compact(
            'configs', 'kits', 'typesCotisation', 'criteres', 'profils'
        ));
    }

    // ===== CONFIGURATIONS GÉNÉRALES =====
    public function updateConfigs(Request $request)
    {
        foreach ($request->input('configs', []) as $cle => $valeur) {
            Configuration::set($cle, $valeur);
        }
        return back()->with('success', 'Configuration mise à jour.');
    }

    // ===== KITS =====
    public function storeKit(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix'        => 'required|numeric|min:0',
            'contenu'     => 'required|string',
        ]);
        $contenu = array_values(array_filter(array_map('trim', explode("\n", $request->contenu))));
        Kit::create([
            'nom'         => $request->nom,
            'description' => $request->description,
            'prix'        => $request->prix,
            'contenu'     => $contenu,
            'actif'       => $request->boolean('actif', true),
        ]);
        return back()->with('success', 'Kit créé.');
    }

    public function updateKit(Request $request, Kit $kit)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix'        => 'required|numeric|min:0',
            'contenu'     => 'required|string',
        ]);
        $contenu = array_values(array_filter(array_map('trim', explode("\n", $request->contenu))));
        $kit->update([
            'nom'         => $request->nom,
            'description' => $request->description,
            'prix'        => $request->prix,
            'contenu'     => $contenu,
            'actif'       => $request->boolean('actif'),
        ]);
        return back()->with('success', 'Kit mis à jour.');
    }

    public function destroyKit(Kit $kit)
    {
        $kit->delete();
        return back()->with('success', 'Kit supprimé.');
    }

    // ===== TYPES DE COTISATION =====
    public function storeTypeCotisation(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|unique:bm_types_cotisation,code',
            'nom'             => 'required|string|max:255',
            'description'     => 'nullable|string',
            'montant_minimum' => 'required|numeric|min:0',
            'montant_defaut'  => 'required|numeric|min:0',
            'frequence'       => 'required|in:journalier,hebdomadaire,mensuel',
        ]);
        TypeCotisation::create([
            'code'                => strtoupper($request->code),
            'nom'                 => $request->nom,
            'description'         => $request->description,
            'montant_minimum'     => $request->montant_minimum,
            'montant_defaut'      => $request->montant_defaut,
            'frequence'           => $request->frequence,
            'obligatoire'         => $request->boolean('obligatoire'),
            'donne_droit_soutien' => $request->boolean('donne_droit_soutien'),
            'actif'               => $request->boolean('actif', true),
        ]);
        return back()->with('success', 'Type de cotisation créé.');
    }

    public function updateTypeCotisation(Request $request, TypeCotisation $typeCotisation)
    {
        $request->validate([
            'nom'             => 'required|string|max:255',
            'description'     => 'nullable|string',
            'montant_minimum' => 'required|numeric|min:0',
            'montant_defaut'  => 'required|numeric|min:0',
            'frequence'       => 'required|in:journalier,hebdomadaire,mensuel',
        ]);
        $typeCotisation->update([
            'nom'                 => $request->nom,
            'description'         => $request->description,
            'montant_minimum'     => $request->montant_minimum,
            'montant_defaut'      => $request->montant_defaut,
            'frequence'           => $request->frequence,
            'obligatoire'         => $request->boolean('obligatoire'),
            'donne_droit_soutien' => $request->boolean('donne_droit_soutien'),
            'actif'               => $request->boolean('actif'),
        ]);
        return back()->with('success', 'Type de cotisation mis à jour.');
    }

    // ===== CRITÈRES DE SCORING =====
    public function storeCritereScoring(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|unique:bm_criteres_scoring,code',
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'poids'       => 'required|integer|min:1',
            'max_points'  => 'required|integer|min:1',
            'ordre'       => 'required|integer|min:0',
        ]);
        CritereScoring::create([
            'code'        => $request->code,
            'nom'         => $request->nom,
            'description' => $request->description,
            'poids'       => $request->poids,
            'max_points'  => $request->max_points,
            'ordre'       => $request->ordre,
            'actif'       => $request->boolean('actif', true),
        ]);
        return back()->with('success', 'Critère créé.');
    }

    public function updateCritereScoring(Request $request, CritereScoring $critereScoring)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'poids'       => 'required|integer|min:1',
            'max_points'  => 'required|integer|min:1',
            'ordre'       => 'required|integer|min:0',
        ]);
        $critereScoring->update([
            'nom'         => $request->nom,
            'description' => $request->description,
            'poids'       => $request->poids,
            'max_points'  => $request->max_points,
            'ordre'       => $request->ordre,
            'actif'       => $request->boolean('actif'),
        ]);
        return back()->with('success', 'Critère mis à jour.');
    }

    // ===== PROFILS =====
    public function storeProfil(Request $request)
    {
        $request->validate([
            'code'                => 'required|string|unique:bm_profils,code',
            'nom'                 => 'required|string|max:255',
            'description'         => 'nullable|string',
            'documents_requis'    => 'required|string',
            'plafond_financement' => 'required|numeric|min:0',
        ]);
        $docs = array_values(array_filter(array_map('trim', explode("\n", $request->documents_requis))));
        Profil::create([
            'code'                => $request->code,
            'nom'                 => $request->nom,
            'description'         => $request->description,
            'documents_requis'    => $docs,
            'plafond_financement' => $request->plafond_financement,
            'actif'               => $request->boolean('actif', true),
        ]);
        return back()->with('success', 'Profil créé.');
    }

    public function updateProfil(Request $request, Profil $profil)
    {
        $request->validate([
            'nom'                 => 'required|string|max:255',
            'description'         => 'nullable|string',
            'documents_requis'    => 'required|string',
            'plafond_financement' => 'required|numeric|min:0',
        ]);
        $docs = array_values(array_filter(array_map('trim', explode("\n", $request->documents_requis))));
        $profil->update([
            'nom'                 => $request->nom,
            'description'         => $request->description,
            'documents_requis'    => $docs,
            'plafond_financement' => $request->plafond_financement,
            'actif'               => $request->boolean('actif'),
        ]);
        return back()->with('success', 'Profil mis à jour.');
    }
}
