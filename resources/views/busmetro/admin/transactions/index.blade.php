@extends('busmetro.layouts.admin')
@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Token, téléphone..." class="flex-1 min-w-[150px] px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
            <select name="statut" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                <option value="">Tous</option>
                <option value="completed" {{ request('statut') == 'completed' ? 'selected' : '' }}>Complété</option>
                <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="failed" {{ request('statut') == 'failed' ? 'selected' : '' }}>Échoué</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-search mr-1"></i>Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Date</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Type</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Adhérent</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Montant</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Statut</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Moyen</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transactions ?? [] as $tx)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ ucfirst($tx->type) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $tx->adherent->prenom ?? '—' }} {{ $tx->adherent->nom ?? '' }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($tx->montant) }} F</td>
                    <td class="px-4 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $tx->statut === 'completed' ? 'bg-bm-100 text-bm-700' : ($tx->statut === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($tx->statut) }}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $tx->moyen_paiement ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-gray-400">Aucune transaction</td></tr>
                @endforelse
            </tbody>
        </table>
        @if(method_exists($transactions ?? collect(), 'links'))
        <div class="px-4 py-3 border-t border-gray-100">{{ $transactions->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
