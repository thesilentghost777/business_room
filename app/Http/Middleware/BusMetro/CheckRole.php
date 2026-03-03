<?php

namespace App\Http\Middleware\BusMetro;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth('busmetro')->user();

        if (!$user || !$user->is_active) {
            return redirect()->route('busmetro.login')->with('error', 'Accès non autorisé');
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
