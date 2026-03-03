@extends('busmetro.layouts.adherent')
@section('title', 'Carnet de recettes')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Carnet de recettes</h2>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- Résumé --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-green-50 rounded-2xl p-4">
            <p class="text-[10px] text-green-600 font-medium uppercase tracking-wide">Total recettes</p>
            <p class="text-base font-bold text-green-700 mt-1">{{ number_format($totalRecettes ?? 0) }} F</p>
        </div>
        <div class="bg-red-50 rounded-2xl p-4">
            <p class="text-[10px] text-red-600 font-medium uppercase tracking-wide">Total dépenses</p>
            <p class="text-base font-bold text-red-700 mt-1">{{ number_format($totalDepenses ?? 0) }} F</p>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Ajouter une recette</h3>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-4 py-3 mb-4">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('busmetro.adherent.carnet.ajouter') }}" method="POST" class="space-y-3">
            @csrf

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Date de la recette *</label>
                <input type="date"
                       name="date_recette"
                       value="{{ old('date_recette', date('Y-m-d')) }}"
                       required
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('date_recette') border-red-400 @enderror">
            </div>

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Montant reçu (FCFA) *</label>
                <input type="number"
                       name="montant_recette"
                       placeholder="Ex: 15000"
                       value="{{ old('montant_recette') }}"
                       required
                       min="0"
                       step="1"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('montant_recette') border-red-400 @enderror">
            </div>

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Montant dépensé (FCFA)</label>
                <input type="number"
                       name="montant_depense"
                       placeholder="Ex: 5000"
                       value="{{ old('montant_depense', 0) }}"
                       min="0"
                       step="1"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('montant_depense') border-red-400 @enderror">
            </div>

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Catégorie</label>
                <select name="categorie"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white @error('categorie') border-red-400 @enderror">
                    <option value="">-- Choisir --</option>
                    <option value="vente"   {{ old('categorie') == 'vente'   ? 'selected' : '' }}>Vente</option>
                    <option value="service" {{ old('categorie') == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="autre"   {{ old('categorie') == 'autre'   ? 'selected' : '' }}>Autre</option>
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-500 mb-1 block">Description</label>
                <textarea name="description"
                          rows="2"
                          placeholder="Ex: Vente de poisson au marché..."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700 transition-colors">
                Enregistrer la recette
            </button>
        </form>
    </div>

    {{-- Liste des recettes --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Mes recettes</h3>
        <div class="space-y-2">
            @forelse($recettes ?? [] as $r)
            <div class="flex items-start justify-between py-2.5 border-b border-gray-50 last:border-0">
                <div class="flex-1 min-w-0 pr-3">
                    <p class="text-xs font-medium text-gray-900 truncate">
                        {{ $r->description ?? ucfirst($r->categorie ?? 'Recette') }}
                    </p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($r->date_recette)->format('d/m/Y') }}
                        @if($r->categorie)
                            · <span class="capitalize">{{ $r->categorie }}</span>
                        @endif
                        ·
                        @if($r->valide)
                            <span class="text-bm-600 font-medium">Validé</span>
                        @else
                            <span class="text-amber-500 font-medium">En attente</span>
                        @endif
                    </p>
                    @if($r->montant_depense > 0)
                        <p class="text-[10px] text-red-400 mt-0.5">
                            Dépense : {{ number_format($r->montant_depense) }} F
                        </p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <span class="text-xs font-bold text-green-700">+{{ number_format($r->montant_recette) }} F</span>
                    @if($r->montant_depense > 0)
                        <p class="text-[10px] font-semibold text-gray-500">
                            Net : {{ number_format($r->montant_recette - $r->montant_depense) }} F
                        </p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-6">Aucune recette enregistrée</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($recettes) && $recettes->hasPages())
            <div class="mt-4">
                {{ $recettes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
