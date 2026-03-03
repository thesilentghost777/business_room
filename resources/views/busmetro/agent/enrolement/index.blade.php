@extends('busmetro.layouts.agent')
@section('title', 'Enrôlement')
@section('page-title', 'Enrôlement')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ ($adherents ?? collect())->count() }} adhérent(s) enrôlé(s)</p>
        <a href="{{ route('busmetro.agent.enrolement.create') }}" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-plus mr-1.5"></i>Nouveau</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Membre</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Téléphone</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Statut</th>
                <th class="text-center text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Kit</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($adherents ?? [] as $adh)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $adh->prenom }} {{ $adh->nom }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $adh->telephone }}</td>
                    <td class="px-4 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $adh->statut === 'actif' ? 'bg-bm-100 text-bm-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($adh->statut) }}</span></td>
                    <td class="px-4 py-3 text-center">@if($adh->kit_achete)<i class="fas fa-check-circle text-bm-500"></i>@else<i class="fas fa-times-circle text-gray-300"></i>@endif</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('busmetro.agent.enrolement.show', $adh) }}" class="text-gray-400 hover:text-bm-600"><i class="fas fa-eye text-xs"></i></a>
                        @if(!$adh->kit_achete)
                        <form action="{{ route('busmetro.agent.enrolement.kit', $adh) }}" method="POST" class="inline ml-1">@csrf<button class="px-2 py-1 text-[10px] text-white bg-bm-600 rounded-lg">Vendre kit</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-sm text-gray-400">Aucun adhérent</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
