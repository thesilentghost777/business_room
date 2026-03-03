@extends('busmetro.layouts.admin')
@section('title', $session->nom)
@section('page-title')
<div class="flex items-center space-x-2">
    <a href="{{ route('busmetro.admin.sessions.index') }}" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-arrow-left"></i>
    </a>
    <span>{{ $session->nom }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header session --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                        {{ $session->statut === 'preparation' ? 'bg-gray-100 text-gray-700' :
                          ($session->statut === 'candidature' ? 'bg-bm-100 text-bm-700' :
                          ($session->statut === 'selection'   ? 'bg-amber-100 text-amber-700' :
                          ($session->statut === 'validation'  ? 'bg-blue-100 text-blue-700' :
                          ($session->statut === 'financement' ? 'bg-green-100 text-green-700' :
                                                                'bg-red-100 text-red-700')))) }}">
                        {{ ucfirst($session->statut) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500">
                    Budget : <b>{{ number_format($session->budget_total, 0, ',', ' ') }} FCFA</b>
                    &nbsp;·&nbsp; T{{ $session->trimestre }} {{ $session->annee }}
                    &nbsp;·&nbsp; Créée le {{ $session->created_at->format('d/m/Y') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Candidatures : {{ $session->date_debut_candidature?->format('d/m/Y') }} → {{ $session->date_fin_candidature?->format('d/m/Y') }}
                    &nbsp;·&nbsp; Score min : <b>{{ $session->score_minimum }}/100</b>
                    &nbsp;·&nbsp; Max bénéficiaires : <b>{{ $session->nombre_beneficiaires_max }}</b>
                </p>
            </div>

            <div class="flex items-center space-x-2">
                @if($session->statut !== 'cloturee')
                <form action="{{ route('busmetro.admin.sessions.statut', $session) }}" method="POST" class="inline">
                    @csrf
                    <select name="statut" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-gray-200 rounded-xl">
                        @foreach(['preparation','candidature','selection','validation','financement','cloturee'] as $s)
                            <option value="{{ $s }}" {{ $session->statut === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif

                @if(in_array($session->statut, ['candidature', 'selection']))
                <form action="{{ route('busmetro.admin.sessions.scoring', $session) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-bm-600 rounded-xl hover:bg-bm-700">
                        <i class="fas fa-calculator mr-1"></i>Lancer scoring
                    </button>
                </form>
                @endif

                @if($session->statut === 'selection')
                <form action="{{ route('busmetro.admin.sessions.selectionner', $session) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-amber-500 rounded-xl hover:bg-amber-600">
                        <i class="fas fa-check-double mr-1"></i>Sélectionner bénéficiaires
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Demandes --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">
                Demandes de financement ({{ $demandes->count() }})
            </h3>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Adhérent</th>
                    <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Montant demandé</th>
                    <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Score</th>
                    <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Rang</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Statut</th>
                    <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($demandes as $demande)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">
                        {{ $demande->adherent->prenom }} {{ $demande->adherent->nom }}
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">
                        {{ number_format($demande->montant_demande, 0, ',', ' ') }} F
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-bold {{ ($demande->score_total ?? 0) >= $session->score_minimum ? 'text-bm-600' : 'text-amber-600' }}">
                            {{ $demande->score_total ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                        {{ $demande->rang ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                            {{ $demande->statut === 'validee'          ? 'bg-bm-100 text-bm-700' :
                              ($demande->statut === 'rejetee'          ? 'bg-red-100 text-red-700' :
                              ($demande->statut === 'pre_selectionnee' ? 'bg-blue-100 text-blue-700' :
                              ($demande->statut === 'financee'         ? 'bg-green-100 text-green-700' :
                                                                         'bg-amber-100 text-amber-700'))) }}">
                            {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-1">
                        @if(in_array($demande->statut, ['en_attente', 'pre_selectionnee']))
                        <form action="{{ route('busmetro.admin.demandes.valider', $demande) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="valider">
                            <button class="px-2 py-1 text-[10px] text-white bg-bm-600 rounded-lg hover:bg-bm-700">
                                Valider
                            </button>
                        </form>
                        <form action="{{ route('busmetro.admin.demandes.valider', $demande) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="rejeter">
                            <button class="px-2 py-1 text-[10px] text-white bg-red-500 rounded-lg hover:bg-red-600">
                                Rejeter
                            </button>
                        </form>
                        @endif

                        @if($demande->statut === 'validee')
                        <button onclick="document.getElementById('modal-financement-{{ $demande->id }}').classList.remove('hidden')"
                                class="px-2 py-1 text-[10px] text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Accorder financement
                        </button>
                        {{-- Modal financement --}}
                        <div id="modal-financement-{{ $demande->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
                            <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
                                <h4 class="text-sm font-semibold mb-4">Accorder un financement</h4>
                                <form action="{{ route('busmetro.admin.demandes.financement', $demande) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Montant accordé (FCFA)</label>
                                        <input type="number" name="montant_accorde" value="{{ $demande->montant_demande }}" required
                                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Durée (mois)</label>
                                        <input type="number" name="duree_mois" value="12" min="1" max="36" required
                                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Taux d'intérêt (%)</label>
                                        <input type="number" name="taux_interet" value="5" min="0" step="0.1" required
                                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                                    </div>
                                    <div class="flex justify-end space-x-2 pt-2">
                                        <button type="button"
                                                onclick="document.getElementById('modal-financement-{{ $demande->id }}').classList.add('hidden')"
                                                class="px-4 py-2 text-xs border border-gray-200 rounded-xl hover:bg-gray-50">
                                            Annuler
                                        </button>
                                        <button type="submit" class="px-4 py-2 text-xs text-white bg-green-600 rounded-xl hover:bg-green-700">
                                            Confirmer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-xs text-gray-400">Aucune demande</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
