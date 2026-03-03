@extends('busmetro.layouts.admin')
@section('title', 'Cotisations')
@section('page-title', 'Suivi des cotisations')

@section('content')
<div class="space-y-4">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total NKD ce mois</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($totalNkd ?? 0) }} <span class="text-xs text-gray-400">F</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total NKH ce mois</p>
            <p class="text-xl font-bold text-purple-600 mt-1">{{ number_format($totalNkh ?? 0) }} <span class="text-xs text-gray-400">F</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Nb cotisations</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $nbCotisations ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Taux régularité</p>
            <p class="text-xl font-bold text-bm-600 mt-1">{{ $tauxRegularite ?? 0 }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="flex-1 min-w-[150px] px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
            <select name="type" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                <option value="">Tous types</option>
                <option value="NKD" {{ request('type') == 'NKD' ? 'selected' : '' }}>NKD</option>
                <option value="NKH" {{ request('type') == 'NKH' ? 'selected' : '' }}>NKH</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
            <button type="submit" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-search mr-1"></i>Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Date</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Adhérent</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Type</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Montant</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Mode</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Agent</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cotisations ?? [] as $cot)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $cot->date_cotisation->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $cot->adherent->prenom }} {{ $cot->adherent->nom }}</td>
                    <td class="px-4 py-3"><span class="text-xs font-bold {{ $cot->typeCotisation->code === 'NKD' ? 'text-blue-600' : 'text-purple-600' }}">{{ $cot->typeCotisation->code }}</span></td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($cot->montant) }} F</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($cot->mode_paiement) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $cot->agent->nom_complet ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-gray-400">Aucune cotisation trouvée</td></tr>
                @endforelse
            </tbody>
        </table>
        @if(method_exists($cotisations ?? collect(), 'links'))
        <div class="px-4 py-3 border-t border-gray-100">{{ $cotisations->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
