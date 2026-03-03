@extends('busmetro.layouts.direction')
@section('title', 'Analyse — ' . $session->nom)

@section('content')
<div class="space-y-6">

    {{-- En-tête session --}}
    <div>
        <a href="{{ route('busmetro.direction.sessions') }}"
           class="text-xs text-bm-600 font-medium hover:underline">← Retour aux sessions</a>
        <h2 class="text-lg font-bold text-gray-900 mt-2">{{ $session->nom }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">
            Trimestre {{ $session->trimestre }} — {{ $session->annee }}
            · Score minimum : <span class="font-semibold text-gray-600">{{ $session->score_minimum }} pts</span>
        </p>
    </div>

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

    {{-- Résumé session --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-blue-50 rounded-2xl p-4">
            <p class="text-[10px] text-blue-600 font-medium uppercase tracking-wide">Demandes</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $demandes->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-2xl p-4">
            <p class="text-[10px] text-green-600 font-medium uppercase tracking-wide">Budget total</p>
            <p class="text-base font-bold text-green-700 mt-1">{{ number_format($session->budget_total) }} F</p>
        </div>
    </div>

    {{-- Statut session --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500">Statut actuel</p>
            <p class="text-sm font-bold text-gray-900 mt-0.5">{{ ucfirst($session->statut) }}</p>
        </div>
        <span class="text-[10px] font-semibold px-3 py-1.5 rounded-full
            @switch($session->statut)
                @case('preparation') bg-gray-100 text-gray-600 @break
                @case('candidature') bg-blue-100 text-blue-700 @break
                @case('selection')   bg-amber-100 text-amber-700 @break
                @case('validation')  bg-purple-100 text-purple-700 @break
                @case('financement') bg-green-100 text-green-700 @break
                @case('cloturee')    bg-gray-100 text-gray-400 @break
                @default             bg-gray-100 text-gray-500
            @endswitch">
            {{ ucfirst($session->statut) }}
        </span>
    </div>

    {{-- Liste des demandes --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">
            Demandes classées par score
        </h3>

        <div class="space-y-3">
            @forelse($demandes as $i => $demande)
            <div class="border border-gray-100 rounded-xl p-4">
                {{-- En-tête demande --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-400 w-5">{{ $i + 1 }}</span>
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $demande->adherent->prenom }} {{ $demande->adherent->nom }}
                            </p>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5 ml-7">
                            {{ $demande->adherent->matricule }}
                            @if($demande->adherent->profil)
                                · {{ $demande->adherent->profil->nom }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-sm font-bold
                            @if($demande->score_total >= 80) text-green-600
                            @elseif($demande->score_total >= 60) text-bm-600
                            @else text-red-500 @endif">
                            {{ number_format($demande->score_total, 1) }} pts
                        </span>
                    </div>
                </div>

                {{-- Infos demande --}}
                <div class="ml-7 space-y-1 mb-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Montant demandé</span>
                        <span class="font-medium text-gray-800">{{ number_format($demande->montant_demande) }} F</span>
                    </div>
                    @if($demande->motif)
                    <div class="text-[10px] text-gray-400 italic line-clamp-2">
                        « {{ $demande->motif }} »
                    </div>
                    @endif
                </div>

                {{-- Statut --}}
                <div class="ml-7 flex items-center justify-between">
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full
                        @switch($demande->statut)
                            @case('en_attente')       bg-gray-100 text-gray-500 @break
                            @case('pre_selectionnee') bg-amber-100 text-amber-700 @break
                            @case('selectionnee')     bg-blue-100 text-blue-700 @break
                            @case('validee')          bg-green-100 text-green-700 @break
                            @case('rejetee')          bg-red-100 text-red-700 @break
                            @case('financee')         bg-purple-100 text-purple-700 @break
                            @default                  bg-gray-100 text-gray-500
                        @endswitch">
                        {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                    </span>

                    {{-- Actions selon statut --}}
                    @if(in_array($session->statut, ['validation', 'selection']))
                        @if(in_array($demande->statut, ['selectionnee', 'pre_selectionnee', 'en_attente']))
                        <button
                            onclick="document.getElementById('modal-valider-{{ $demande->id }}').classList.remove('hidden')"
                            class="text-xs font-medium text-bm-600 hover:underline">
                            Traiter →
                        </button>
                        @endif
                    @endif

                    @if($session->statut === 'financement' && $demande->statut === 'validee')
                        <button
                            onclick="document.getElementById('modal-financement-{{ $demande->id }}').classList.remove('hidden')"
                            class="text-xs font-medium text-green-600 hover:underline">
                            Accorder →
                        </button>
                    @endif
                </div>

                {{-- Commentaire direction --}}
                @if($demande->commentaire_direction)
                <div class="ml-7 mt-2 text-[10px] text-gray-400 bg-gray-50 rounded-lg px-3 py-2">
                    {{ $demande->commentaire_direction }}
                </div>
                @endif
            </div>

            {{-- Modal : Valider / Rejeter --}}
            <div id="modal-valider-{{ $demande->id }}"
                 class="hidden fixed inset-0 bg-black/40 z-50 flex items-end justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4">
                    <h4 class="text-sm font-bold text-gray-900">
                        Traiter la demande de {{ $demande->adherent->prenom }}
                    </h4>
                    <form action="{{ route('busmetro.direction.demandes.valider', $demande) }}" method="POST"
                          class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Action *</label>
                            <select name="action" required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white">
                                <option value="valider">✅ Valider</option>
                                <option value="rejeter">❌ Rejeter</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Commentaire</label>
                            <textarea name="commentaire" rows="2"
                                      placeholder="Motif ou commentaire..."
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="button"
                                    onclick="document.getElementById('modal-valider-{{ $demande->id }}').classList.add('hidden')"
                                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="flex-1 py-2.5 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700">
                                Confirmer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal : Accorder financement --}}
            <div id="modal-financement-{{ $demande->id }}"
                 class="hidden fixed inset-0 bg-black/40 z-50 flex items-end justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4">
                    <h4 class="text-sm font-bold text-gray-900">
                        Accorder le financement — {{ $demande->adherent->prenom }}
                    </h4>
                    <p class="text-xs text-gray-400">
                        Demande : <span class="font-semibold text-gray-700">{{ number_format($demande->montant_demande) }} F</span>
                    </p>
                    <form action="{{ route('busmetro.direction.demandes.financement', $demande) }}" method="POST"
                          class="space-y-3">
                        @csrf
                        @method('POST')
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Montant accordé (FCFA) *</label>
                            <input type="number" name="montant_accorde" required min="1"
                                   value="{{ $demande->montant_demande }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Durée (mois) *</label>
                            <input type="number" name="duree_mois" required min="1" max="36"
                                   placeholder="Ex: 12"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Taux d'intérêt (%) *</label>
                            <input type="number" name="taux_interet" required min="0" step="0.01"
                                   placeholder="Ex: 5.00"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="button"
                                    onclick="document.getElementById('modal-financement-{{ $demande->id }}').classList.add('hidden')"
                                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="flex-1 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700">
                                Accorder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @empty
            <p class="text-xs text-gray-400 text-center py-8">Aucune demande pour cette session</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
