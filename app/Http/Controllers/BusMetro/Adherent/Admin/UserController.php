<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\User;
use App\Models\BusMetro\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                ->orWhere('prenom', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }

        $users = $query->orderByDesc('created_at')->paginate(20);
        return view('busmetro.admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('busmetro.admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:bm_users,email',
            'telephone' => 'required|string|unique:bm_users,telephone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,agent,direction',
            'zone_affectation' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        AuditLog::log('create', $user);

        return redirect()->route('busmetro.admin.users.index')->with('success', 'Utilisateur créé');
    }

    public function edit(User $user)
    {
        return view('busmetro.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:bm_users,email,' . $user->id,
            'telephone' => 'required|string|unique:bm_users,telephone,' . $user->id,
            'role' => 'required|in:admin,agent,direction',
            'zone_affectation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('busmetro.admin.users.index')->with('success', 'Utilisateur modifié');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Statut modifié');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('busmetro.admin.users.index')->with('success', 'Utilisateur supprimé');
    }
}
