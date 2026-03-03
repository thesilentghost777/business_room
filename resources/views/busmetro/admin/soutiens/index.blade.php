@extends('busmetro.layouts.admin')
@section('title', 'Soutiens NKH')
@section('page-title', 'Soutiens NKH')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Adhérent</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Type</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Montant</th>
                <th class="text-left text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Statut</th>
                <th class="text-right text-[10px] font-semibold text-gray-400 uppercase px-4 py-3">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($soutiens ?? [] as $s)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $s->adherent->prenom }} {{ $s->adherent->nom }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst($s->type_evenement) }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($s->montant) }} F</td>
                    <td class="px-4 py-3"><span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $s->statut === 'verse' ? 'bg-bm-100 text-bm-700' : ($s->statut === 'approuve' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($s->statut) }}</span></td>
                    <td class="px-4 py-3 text-right space-x-1">
                        @if($s->statut === 'en_attente')
                        <form action="{{ route('busmetro.admin.soutiens.traiter', $s) }}" method="POST" class="inline">@csrf<input type="hidden" name="decision" value="approuve"><button class="px-2 py-1 text-[10px] text-white bg-bm-600 rounded-lg">Approuver</button></form>
                        @endif
                        @if($s->statut === 'approuve')
                        <form action="{{ route('busmetro.admin.soutiens.verser', $s) }}" method="POST" class="inline">@csrf<button class="px-2 py-1 text-[10px] text-white bg-blue-600 rounded-lg">Verser</button></form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-sm text-gray-400">Aucun soutien</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
