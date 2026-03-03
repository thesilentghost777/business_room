@extends('busmetro.layouts.adherent')
@section('title', 'Remboursement')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('busmetro.adherent.financement') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-lg font-bold text-gray-900">Remboursement</h2>
    </div>

    {{-- Résumé du financement --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Mon financement</h3>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">En cours</span>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div class="text-center">
                <p class="text-lg font-bold text-gray-900">{{ number_format($financement->montant_accorde) }}</p>
                <p class="text-[9px] text-gray-400">Accordé (F)</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-bm-600">{{ number_format($financement->montant_rembourse) }}</p>
                <p class="text-[9px] text-gray-400">Remboursé (F)</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-red-600">{{ number_format($financement->montant_accorde - $financement->montant_rembourse) }}</p>
                <p class="text-[9px] text-gray-400">Reste (F)</p>
            </div>
        </div>
        @php
            $pct = $financement->montant_accorde > 0
                ? round(($financement->montant_rembourse / $financement->montant_accorde) * 100) : 0;
        @endphp
        <div class="mt-4">
            <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                <span>Progression</span><span>{{ $pct }}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-bm-600 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Formulaire de paiement --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Effectuer un paiement</h3>
        <form action="{{ route('busmetro.adherent.financement.payer') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="financement_id" value="{{ $financement->id }}">
            <div>
                <label class="text-[11px] text-gray-500 mb-1 block">Échéance à régler (optionnel)</label>
                <select name="echeancier_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white">
                    <option value="">Paiement libre</option>
                    @foreach($financement->echeanciers->whereIn('statut', ['a_venir','en_attente','retard','partiel']) as $ech)
                    <option value="{{ $ech->id }}">
                        Échéance #{{ $ech->numero_echeance }} — {{ number_format($ech->montant_du) }} F
                        ({{ \Carbon\Carbon::parse($ech->date_echeance)->format('d/m/Y') }})
                        @if($ech->statut === 'retard') ⚠ Retard @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <input type="number" name="montant" min="1" placeholder="Montant à payer (FCFA)" required
                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
            <button type="submit"
                class="w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700">
                Payer via Mobile Money
            </button>
        </form>
    </div>

    {{-- Échéancier --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Échéancier</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($financement->echeanciers->sortBy('numero_echeance') as $ech)
            @php
                $colors = ['paye'=>'bg-green-100 text-green-700','en_attente'=>'bg-yellow-100 text-yellow-700',
                    'retard'=>'bg-red-100 text-red-700','impaye'=>'bg-red-100 text-red-700',
                    'partiel'=>'bg-orange-100 text-orange-700','a_venir'=>'bg-gray-100 text-gray-500'];
                $labels = ['paye'=>'Payé','en_attente'=>'En attente','retard'=>'Retard',
                    'impaye'=>'Impayé','partiel'=>'Partiel','a_venir'=>'À venir'];
            @endphp
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800">#{{ $ech->numero_echeance }} — {{ number_format($ech->montant_du) }} F</p>
                    <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($ech->date_echeance)->format('d M Y') }}</p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $colors[$ech->statut] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ $labels[$ech->statut] ?? $ech->statut }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <i class="fas fa-check-circle text-gray-200 text-2xl mb-2"></i>
                <p class="text-sm text-gray-400">Aucune échéance disponible.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
