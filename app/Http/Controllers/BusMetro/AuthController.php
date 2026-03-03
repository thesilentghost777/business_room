<?php

namespace App\Http\Controllers\BusMetro;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\User;
use App\Models\BusMetro\Adherent;
use App\Models\BusMetro\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ========================
    // CONNEXION STAFF (Admin/Agent/Direction)
    // ========================
    public function showLoginForm()
    {
        return view('busmetro.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Identifiants incorrects')->withInput();
        }

        if (!$user->is_active) {
            return back()->with('error', 'Votre compte est désactivé')->withInput();
        }

        Auth::guard('busmetro')->login($user, $request->boolean('remember'));
        $user->update(['derniere_connexion' => now()]);
        AuditLog::log('login', $user);

        return match ($user->role) {
            'admin' => redirect()->route('busmetro.admin.dashboard'),
            'agent' => redirect()->route('busmetro.agent.dashboard'),
            'direction' => redirect()->route('busmetro.direction.dashboard'),
        };
    }

    public function logout()
    {
        AuditLog::log('logout');
        Auth::guard('busmetro')->logout();
        return redirect()->route('busmetro.login');
    }

    // ========================
    // CONNEXION ADHERENTS
    // ========================
    public function showAdherentLoginForm()
    {
        return view('busmetro.auth.adherent-login');
    }

    public function adherentLogin(Request $request)
    {
        $request->validate([
            'telephone' => 'required',
            'password' => 'required',
        ]);

        $adherent = Adherent::where('telephone', $request->telephone)->first();

        if (!$adherent || !Hash::check($request->password, $adherent->password)) {
            return back()->with('error', 'Identifiants incorrects')->withInput();
        }

        if ($adherent->statut === 'radie') {
            return back()->with('error', 'Votre compte a été radié');
        }

        Auth::guard('adherent')->login($adherent, $request->boolean('remember'));

        return redirect()->route('busmetro.adherent.dashboard');
    }

    public function adherentLogout()
    {
        Auth::guard('adherent')->logout();
        return redirect()->route('busmetro.adherent.login');
    }

    // ========================
    // INSCRIPTION ADHERENT (par l'adhérent lui-même ou via agent)
    // ========================
    public function showRegisterForm()
    {
        return view('busmetro.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:bm_adherents,telephone',
            'email' => 'nullable|email|unique:bm_adherents,email',
            'password' => 'required|string|min:6|confirmed',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:M,F',
            'ville' => 'nullable|string',
            'quartier' => 'nullable|string',
            'code_parrainage' => 'nullable|string',
        ]);

        $parrainId = null;
        if (!empty($validated['code_parrainage'])) {
            $parrain = Adherent::where('code_parrainage', $validated['code_parrainage'])
                ->where('statut', 'actif')
                ->first();
            $parrainId = $parrain?->id;
        }

        $adherent = Adherent::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'telephone' => $validated['telephone'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'],
            'date_naissance' => $validated['date_naissance'] ?? null,
            'sexe' => $validated['sexe'] ?? null,
            'ville' => $validated['ville'] ?? null,
            'quartier' => $validated['quartier'] ?? null,
            'parrain_id' => $parrainId,
            'statut' => 'en_attente',
        ]);

        // Créer le parrainage si parrain trouvé
        if ($parrainId) {
            \App\Models\BusMetro\Parrainage::create([
                'parrain_id' => $parrainId,
                'filleul_id' => $adherent->id,
                'date_parrainage' => now(),
                'statut' => 'actif',
                'bonus_points' => (float) \App\Models\BusMetro\Configuration::get('bonus_parrainage', 2),
            ]);
        }

        Auth::guard('adherent')->login($adherent);

        return redirect()->route('busmetro.adherent.dashboard')
            ->with('success', 'Inscription réussie ! Veuillez acheter votre kit d\'adhésion.');
    }
}
