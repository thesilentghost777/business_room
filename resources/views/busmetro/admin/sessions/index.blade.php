@extends('busmetro.layouts.admin')
@section('title', 'Sessions de financement')
@section('page-title', 'Sessions de financement')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ ($sessions ?? collect())->total() }} session(s)</p>
        <a href="{{ route('busmetro.admin.sessions.create') }}" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
            <i class="fas fa-plus mr-1.5"></i>Nouvelle session
        </a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($sessions ?? [] as $session)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                    {{ $session->statut === 'preparation' ? 'bg-gray-100 text-gray-700' :
                      ($session->statut === 'candidature' ? 'bg-bm-100 text-bm-700' :
                      ($session->statut === 'selection'   ? 'bg-amber-100 text-amber-700' :
                      ($session->statut === 'validation'  ? 'bg-blue-100 text-blue-700' :
                      ($session->statut === 'financement' ? 'bg-green-100 text-green-700' :
                                                            'bg-red-100 text-red-700')))) }}">
                    {{ ucfirst(str_replace('_', ' ', $session->statut)) }}
                </span>
                <span class="text-[10px] text-gray-400">{{ $session->created_at->format('d/m/Y') }}</span>
            </div>

            <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ $session->nom }}</h4>
            <p class="text-xs text-gray-500 mb-3">T{{ $session->trimestre }} {{ $session->annee }}</p>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($session->budget_total, 0, ',', ' ') }}</p>
                    <p class="text-[9px] text-gray-400">Budget (FCFA)</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $session->demandes_count ?? 0 }}</p>
                    <p class="text-[9px] text-gray-400">Demandes</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('busmetro.admin.sessions.show', $session) }}"
                   class="flex-1 text-center py-2 text-xs font-medium text-bm-600 border border-bm-200 rounded-xl hover:bg-bm-50">
                    Détails
                </a>
                @if(in_array($session->statut, ['candidature', 'selection']))
                <form action="{{ route('busmetro.admin.sessions.scoring', $session) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="py-2 px-3 text-xs font-medium text-white bg-bm-600 rounded-xl hover:bg-bm-700">
                        Scoring
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center">
            <i class="fas fa-calendar text-gray-200 text-4xl mb-3"></i>
            <p class="text-sm text-gray-400">Aucune session de financement</p>
        </div>
        @endforelse
    </div>

    @if(isset($sessions) && $sessions->hasPages())
        <div class="pt-2">{{ $sessions->links() }}</div>
    @endif
</div>
@endsection
