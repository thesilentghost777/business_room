@extends('busmetro.layouts.adherent')
@section('title', 'Mon profil')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Mon profil</h2>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('busmetro.adherent.profil.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Nom</label><input type="text" name="nom" value="{{ $adherent->nom }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Prénom</label><input type="text" name="prenom" value="{{ $adherent->prenom }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ $adherent->email }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Ville</label><input type="text" name="ville" value="{{ $adherent->ville }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Quartier</label><input type="text" name="quartier" value="{{ $adherent->quartier }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            <hr class="border-gray-100">
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Nouveau mot de passe</label><input type="password" name="password" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm" placeholder="Laisser vide pour ne pas changer"></div>
            <button type="submit" class="px-6 py-2.5 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">Mettre à jour</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Informations du compte</h3>
        <dl class="space-y-2">
            <div class="flex justify-between"><dt class="text-xs text-gray-500">Code adhérent</dt><dd class="text-xs font-mono font-bold text-gray-900">{{ $adherent->code_adherent }}</dd></div>
            <div class="flex justify-between"><dt class="text-xs text-gray-500">Code parrainage</dt><dd class="text-xs font-mono font-bold text-bm-600">{{ $adherent->code_parrainage }}</dd></div>
            <div class="flex justify-between"><dt class="text-xs text-gray-500">Profil</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->profil->nom ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-xs text-gray-500">Membre depuis</dt><dd class="text-xs text-gray-900">{{ $adherent->created_at->format('d/m/Y') }}</dd></div>
        </dl>
    </div>
</div>
@endsection
