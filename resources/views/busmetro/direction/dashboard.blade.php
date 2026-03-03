@extends('busmetro.layouts.direction')
@section('title', 'Tableau de bord Direction')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Tableau de bord</h2>

    {{-- KPIs principaux --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-blue-50 rounded-2xl p-4">
            <p class="text-[10px] text-blue-600 font-medium uppercase tracking-wide">Sessions en cours</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['sessions_en_cours'] }}</p>
        </div>
        <div class="bg-amber-50 rounded-2xl p-4">
            <p class="text-[10px] text-amber-600 font-medium uppercase tracking-wide">Demandes en attente</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['demandes_en_attente'] }}</p>
        </div>
        <div class="bg-green-50 rounded-2xl p-4">
            <p class="text-[10px] text-green-600 font-medium uppercase tracking-wide">Financements actifs</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $stats['financements_actifs'] }}</p>
        </div>
        <div class="bg-purple-50 rounded-2xl p-4">
            <p class="text-[10px] text-purple-600 font-medium uppercase tracking-wide">Adhérents éligibles</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['adherents_eligibles'] }}</p>
        </div>
    </div>

    {{-- Bloc financier --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">Situation financière globale</h3>

        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Total financé</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_finance']) }} F</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Total remboursé</span>
                <span class="text-sm font-bold text-green-600">{{ number_format($stats['total_rembourse']) }} F</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Taux de remboursement</span>
                <span class="text-sm font-bold
                    @if($stats['taux_remboursement'] >= 80) text-green-600
                    @elseif($stats['taux_remboursement'] >= 50) text-amber-600
                    @else text-red-600 @endif">
                    {{ $stats['taux_remboursement'] }} %
                </span>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full
                @if($stats['taux_remboursement'] >= 80) bg-green-500
                @elseif($stats['taux_remboursement'] >= 50) bg-amber-500
                @else bg-red-500 @endif"
                style="width: {{ min($stats['taux_remboursement'], 100) }}%">
            </div>
        </div>
    </div>

    {{-- Sessions récentes --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Sessions récentes</h3>
            <a href="{{ route('busmetro.direction.sessions') }}"
               class="text-xs text-bm-600 font-medium hover:underline">Voir tout</a>
        </div>

        <div class="space-y-2">
            @forelse($sessionsRecentes as $session)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <div class="flex-1 min-w-0 pr-3">
                    <p class="text-xs font-medium text-gray-900 truncate">{{ $session->nom }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        T{{ $session->trimestre }} {{ $session->annee }}
                        · {{ $session->demandes_count }} demande(s)
                        · {{ $session->financements_count }} financement(s)
                    </p>
                </div>
                <span class="shrink-0 text-[10px] font-semibold px-2 py-1 rounded-full
                    @switch($session->statut)
                        @case('candidature') bg-blue-100 text-blue-700 @break
                        @case('selection')   bg-amber-100 text-amber-700 @break
                        @case('validation')  bg-purple-100 text-purple-700 @break
                        @case('financement') bg-green-100 text-green-700 @break
                        @case('cloturee')    bg-gray-100 text-gray-500 @break
                        @default             bg-gray-100 text-gray-500
                    @endswitch">
                    {{ ucfirst($session->statut) }}
                </span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-6">Aucune session trouvée</p>
            @endforelse
        </div>
    </div>

    {{-- Top scores --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Top 10 adhérents — Score</h3>

        <div class="space-y-2">
            @forelse($topScores as $i => $adherent)
            <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                <span class="text-xs font-bold text-gray-400 w-5 text-center">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">
                        {{ $adherent->prenom }} {{ $adherent->nom }}
                    </p>
                    <p class="text-[10px] text-gray-400">{{ $adherent->matricule }}</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-sm font-bold
                        @if($adherent->score_actuel >= 80) text-green-600
                        @elseif($adherent->score_actuel >= 60) text-bm-600
                        @else text-amber-600 @endif">
                        {{ number_format($adherent->score_actuel, 1) }}
                    </span>
                    <span class="text-[10px] text-gray-400"> pts</span>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-6">Aucun adhérent actif</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
