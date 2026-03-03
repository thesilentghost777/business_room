@extends('busmetro.layouts.adherent')
@section('title', 'Acheter un kit')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Kits d'adhésion</h2>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-xl px-4 py-3">
            {{ session('info') }}
        </div>
    @endif

    {{-- Kit déjà acheté --}}
    @if(auth('adherent')->user()->kit_achete)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-800">Kit déjà acquis</p>
                    <p class="text-xs text-green-600">Vous disposez déjà d'un kit d'adhésion actif</p>
                </div>
            </div>
            @if($achatEnCours && $achatEnCours->kit)
                <div class="bg-white rounded-xl p-3 mt-2">
                    <p class="text-xs text-gray-500">Kit acheté</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $achatEnCours->kit->nom }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ number_format($achatEnCours->montant) }} FCFA</p>
                </div>
            @endif
        </div>

    @else
        {{-- Introduction --}}
        <div class="bg-bm-50 border border-bm-100 rounded-2xl p-4">
            <p class="text-xs text-bm-700 leading-relaxed">
                Le kit d'adhésion est obligatoire pour accéder aux financements.
                Choisissez votre kit ci-dessous et procédez au paiement via Mobile Money.
            </p>
        </div>

        {{-- Liste des kits --}}
        @forelse($kits as $kit)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0 pr-3">
                    <h3 class="text-sm font-bold text-gray-900">{{ $kit->nom }}</h3>
                    @if($kit->description)
                        <p class="text-xs text-gray-400 mt-1">{{ $kit->description }}</p>
                    @endif
                </div>
                <span class="shrink-0 text-base font-bold text-bm-600">
                    {{ number_format($kit->prix) }} F
                </span>
            </div>

            {{-- Contenu du kit --}}
            @if($kit->contenu)
                @php $contenu = is_array($kit->contenu) ? $kit->contenu : json_decode($kit->contenu, true); @endphp
                @if(!empty($contenu))
                <div class="mb-4">
                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide mb-2">Contenu du kit</p>
                    <div class="space-y-1.5">
                        @foreach($contenu as $item)
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-bm-50 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-2.5 h-2.5 text-bm-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif

            {{-- Bouton achat --}}
            <form action="{{ route('busmetro.adherent.kit.acheter') }}" method="POST"
                  onsubmit="handleKitSubmit(this)">
                @csrf
                <input type="hidden" name="kit_id" value="{{ $kit->id }}">
                <button type="submit"
                        class="kit-btn w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                    {{-- État normal --}}
                    <span class="btn-default-content flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Acheter ce kit — {{ number_format($kit->prix) }} F
                    </span>
                    {{-- État loading --}}
                    <span class="btn-loading-content hidden items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Ouverture du portail…
                    </span>
                </button>
            </form>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
            <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-xs text-gray-400">Aucun kit disponible pour le moment</p>
        </div>
        @endforelse

    @endif
</div>

<script>
function handleKitSubmit(form) {
    // Désactiver tous les boutons pour éviter le double-clic
    document.querySelectorAll('.kit-btn').forEach(function(btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-bm-700', 'active:scale-95');
    });

    // Sur le bouton actif : cacher le texte normal, afficher le spinner
    const activeBtn = form.querySelector('.kit-btn');
    activeBtn.querySelector('.btn-default-content').classList.add('hidden');
    activeBtn.querySelector('.btn-default-content').classList.remove('flex');
    activeBtn.querySelector('.btn-loading-content').classList.remove('hidden');
    activeBtn.querySelector('.btn-loading-content').classList.add('flex');
}
</script>
@endsection
