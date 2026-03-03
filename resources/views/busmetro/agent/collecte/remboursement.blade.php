@extends('busmetro.layouts.agent')
@section('title', 'Collecte remboursement')
@section('page-title', 'Collecter un remboursement')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Rechercher le financement</h3>
        <form action="{{ route('busmetro.agent.collecte.remboursement.rechercher') }}" method="POST" class="flex space-x-2">
            @csrf
            <input type="text" name="telephone" placeholder="Téléphone de l'adhérent" required class="flex-1 px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
            <button type="submit" class="px-4 py-2.5 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-search"></i></button>
        </form>
    </div>

    @if(isset($financement))
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-gray-900">{{ $financement->adherent->prenom }} {{ $financement->adherent->nom }}</p>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">En cours</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-gray-900">{{ number_format($financement->montant_accorde) }}</p>
                <p class="text-[9px] text-gray-400">Montant accordé (F)</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-lg font-bold text-red-600">{{ number_format($financement->montant_accorde - $financement->montant_rembourse) }}</p>
                <p class="text-[9px] text-gray-400">Reste à payer (F)</p>
            </div>
        </div>
    </div>

    <form action="{{ route('busmetro.agent.collecte.remboursement.store') }}" method="POST" class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
        @csrf
        <input type="hidden" name="financement_id" value="{{ $financement->id }}">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Montant (FCFA)</label>
            <input type="number" name="montant" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Mode</label>
            <select name="mode_paiement" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                <option value="especes">Espèces</option>
                <option value="mobile_money">Mobile Money</option>
            </select>
        </div>
        <button type="submit" class="w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700">Enregistrer le remboursement</button>
    </form>
    @endif
</div>
@endsection
