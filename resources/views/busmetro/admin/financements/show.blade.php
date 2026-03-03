@extends('busmetro.layouts.admin')
@section('title', 'Financement #'.$financement->id)
@section('page-title')
<div class="flex items-center space-x-2"><a href="{{ route('busmetro.admin.financements.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a><span>Financement de {{ $financement->adherent->prenom }}</span></div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-500">Montant accordé</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($financement->montant_accorde) }} <span class="text-sm text-gray-400">FCFA</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-500">Montant remboursé</p>
            <p class="text-2xl font-bold text-bm-600 mt-1">{{ number_format($financement->montant_rembourse) }} <span class="text-sm text-gray-400">FCFA</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-500">Reste à payer</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($financement->montant_accorde - $financement->montant_rembourse) }} <span class="text-sm text-gray-400">FCFA</span></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Échéancier</h3>
        <div class="space-y-2">
            @foreach($financement->echeanciers as $ech)
            <div class="flex items-center justify-between py-3 px-4 rounded-xl {{ $ech->statut === 'paye' ? 'bg-bm-50' : ($ech->statut === 'en_retard' ? 'bg-red-50' : 'bg-gray-50') }}">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full {{ $ech->statut === 'paye' ? 'bg-bm-200' : ($ech->statut === 'en_retard' ? 'bg-red-200' : 'bg-gray-200') }} flex items-center justify-center">
                        <i class="fas {{ $ech->statut === 'paye' ? 'fa-check text-bm-700' : ($ech->statut === 'en_retard' ? 'fa-exclamation text-red-700' : 'fa-clock text-gray-500') }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">Échéance {{ $ech->numero }}</p>
                        <p class="text-[10px] text-gray-500">{{ $ech->date_echeance->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-900">{{ number_format($ech->montant) }} F</p>
                    @if($ech->penalite > 0)<p class="text-[10px] text-red-500">+{{ number_format($ech->penalite) }} F pénalité</p>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
