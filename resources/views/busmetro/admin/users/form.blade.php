@extends('busmetro.layouts.admin')
@section('title', isset($user) ? 'Modifier utilisateur' : 'Nouvel utilisateur')
@section('page-title', isset($user) ? 'Modifier '.$user->prenom : 'Nouvel utilisateur')

@section('content')
<div class="max-w-lg">
    <form action="{{ isset($user) ? route('busmetro.admin.users.update', $user) : route('busmetro.admin.users.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Nom</label><input type="text" name="nom" value="{{ old('nom', $user->nom ?? '') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Prénom</label><input type="text" name="prenom" value="{{ old('prenom', $user->prenom ?? '') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Téléphone</label><input type="tel" name="telephone" value="{{ old('telephone', $user->telephone ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Rôle</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agent" {{ old('role', $user->role ?? '') === 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="direction" {{ old('role', $user->role ?? '') === 'direction' ? 'selected' : '' }}>Direction</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Zone</label><input type="text" name="zone_affectation" value="{{ old('zone_affectation', $user->zone_affectation ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            @if(!isset($user))
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Mot de passe</label><input type="password" name="password" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            @endif
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('busmetro.admin.users.index') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700 font-medium">{{ isset($user) ? 'Mettre à jour' : 'Créer' }}</button>
        </div>
    </form>
</div>
@endsection
