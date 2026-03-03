@extends('busmetro.layouts.agent')
@section('title', 'Carnets de recettes')
@section('page-title', 'Carnets de recettes')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Date</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Adhérent</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Montant</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Source</th>
                <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Validé</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recettes ?? [] as $r)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $r->date_recette->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $r->adherent->prenom }} {{ $r->adherent->nom }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($r->montant) }} F</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $r->source_revenu }}</td>
                    <td class="px-4 py-3 text-center">@if($r->valide_par_agent)<i class="fas fa-check-circle text-bm-500"></i>@else<i class="fas fa-clock text-amber-400"></i>@endif</td>
                    <td class="px-4 py-3 text-right">
                        @if(!$r->valide_par_agent)
                        <form action="{{ route('busmetro.agent.carnets.valider', $r) }}" method="POST" class="inline">@csrf<button class="px-2 py-1 text-[10px] text-white bg-bm-600 rounded-lg">Valider</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-gray-400">Aucune recette</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
