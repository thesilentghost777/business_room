@extends('busmetro.layouts.agent')
@section('title', 'Collecte cotisation')
@section('page-title', 'Collecter une cotisation')

@section('content')
<div class="max-w-md mx-auto">

    {{-- Alertes --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Recherche --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Rechercher l'adhérent</h3>
        <form action="{{ route('busmetro.agent.collecte.rechercher') }}" method="POST" class="flex space-x-2">
            @csrf
            {{-- name="telephone" doit correspondre au validate() du controller --}}
            <input type="text" name="telephone"
                value="{{ session('search', old('telephone')) }}"
                placeholder="Téléphone ou matricule" required
                class="flex-1 px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-300 focus:outline-none">
            <button type="submit" class="px-4 py-2.5 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    @if(isset($adherent))
        {{-- Carte adhérent --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-bm-100 flex items-center justify-center">
                    <span class="text-bm-700 font-bold text-lg">{{ substr($adherent->prenom, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $adherent->prenom }} {{ $adherent->nom }}</p>
                    <p class="text-xs text-gray-500">{{ $adherent->matricule }} · Score : {{ $adherent->score_actuel ?? 0 }}/100</p>
                    <p class="text-xs text-gray-400">{{ $adherent->telephone }}</p>
                </div>
            </div>
        </div>

        {{-- Formulaire cotisation --}}
        <form action="{{ route('busmetro.agent.collecte.cotisation.store') }}" method="POST"
              class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            @csrf
            <input type="hidden" name="adherent_id" value="{{ $adherent->id }}">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Type de cotisation</label>
                <select name="type_cotisation_id" required
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-300 focus:outline-none">
                    {{-- ← variable correcte : $typesCotisation --}}
                    @forelse($typesCotisation ?? [] as $tc)
                        <option value="{{ $tc->id }}">{{ $tc->code }} — {{ $tc->nom }} (min. {{ number_format($tc->montant_minimum) }} F)</option>
                    @empty
                        <option disabled>Aucun type disponible</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                <input type="number" name="montant" required min="1"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-300 focus:outline-none"
                    placeholder="Ex: 500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mode de paiement</label>
                <select name="mode_paiement"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-300 focus:outline-none">
                    <option value="especes">Espèces</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                <textarea name="commentaire" rows="2"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-300 focus:outline-none"
                    placeholder="Remarque éventuelle..."></textarea>
            </div>

            <button type="submit"
                class="w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700 transition">
                <i class="fas fa-check mr-2"></i>Enregistrer la cotisation
            </button>
        </form>
    @endif

</div>
@endsection
