@extends('busmetro.layouts.direction')
@section('title', 'Sessions de financement')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Sessions de financement</h2>

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

    {{-- Liste des sessions --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="space-y-3">
            @forelse($sessions as $session)
            <div class="border border-gray-100 rounded-xl p-4 hover:border-bm-200 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $session->nom }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            Trimestre {{ $session->trimestre }} — {{ $session->annee }}
                        </p>
                        <div class="flex gap-3 mt-2">
                            <span class="text-[10px] text-gray-500">
                                📋 <span class="font-medium text-gray-700">{{ $session->demandes_count }}</span> demandes
                            </span>
                            <span class="text-[10px] text-gray-500">
                                💰 <span class="font-medium text-gray-700">{{ $session->financements_count }}</span> financements
                            </span>
                        </div>
                        <div class="flex gap-3 mt-1">
                            <span class="text-[10px] text-gray-400">
                                Du {{ \Carbon\Carbon::parse($session->date_debut_candidature)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($session->date_fin_candidature)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full
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
                        <a href="{{ route('busmetro.direction.sessions.analyse', $session) }}"
                           class="text-xs text-bm-600 font-medium hover:underline">
                            Analyser →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-8">Aucune session trouvée</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($sessions->hasPages())
            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
