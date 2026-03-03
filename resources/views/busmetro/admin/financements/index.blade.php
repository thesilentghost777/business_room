@extends('busmetro.layouts.admin')
@section('title', 'Financements')
@section('page-title', 'Financements')

@section('content')
<div class="space-y-4">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">En cours</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $enCours ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Montant total</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($montantTotal ?? 0) }} <span class="text-xs text-gray-400">F</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Remboursé</p>
            <p class="text-xl font-bold text-bm-600 mt-1">{{ number_format($montantRembourse ?? 0) }} <span class="text-xs text-gray-400">F</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Taux remboursement</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $tauxRemboursement ?? 0 }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Bénéficiaire</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Montant</th>
                <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Échéances</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Statut</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Remboursé</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($financements ?? [] as $fin)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $fin->adherent->prenom }} {{ $fin->adherent->nom }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($fin->montant_accorde) }} F</td>
                    <td class="px-4 py-3 text-xs text-gray-600 text-center">{{ $fin->echeanciers->where('statut', 'paye')->count() }}/{{ $fin->echeanciers->count() }}</td>
                    <td class="px-4 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $fin->statut === 'en_cours' ? 'bg-blue-100 text-blue-700' : ($fin->statut === 'rembourse' ? 'bg-bm-100 text-bm-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst(str_replace('_', ' ', $fin->statut)) }}</span></td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-900 text-right">{{ number_format($fin->montant_rembourse) }} F</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('busmetro.admin.financements.show', $fin) }}" class="text-gray-400 hover:text-bm-600"><i class="fas fa-eye text-xs"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-gray-400">Aucun financement</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
