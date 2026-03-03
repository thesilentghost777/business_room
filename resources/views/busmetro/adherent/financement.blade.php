@extends('busmetro.layouts.adherent')
@section('title', 'Financement')

@section('content')
<div class="space-y-6">
    <h2 class="text-lg font-bold text-gray-900">Financement</h2>

    {{-- ✅ Financement en cours --}}
    @if($financementActif)
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Financement en cours</h3>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">En cours</span>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="text-center">
                <p class="text-lg font-bold text-gray-900">{{ number_format($financementActif->montant_accorde) }}</p>
                <p class="text-[9px] text-gray-400">Accordé (F)</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-bm-600">{{ number_format($financementActif->montant_rembourse) }}</p>
                <p class="text-[9px] text-gray-400">Remboursé (F)</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-red-600">
                    {{ number_format($financementActif->montant_accorde - $financementActif->montant_rembourse) }}
                </p>
                <p class="text-[9px] text-gray-400">Reste (F)</p>
            </div>
        </div>

        {{-- Barre de progression du remboursement --}}
        @php
            $progression = $financementActif->montant_accorde > 0
                ? round(($financementActif->montant_rembourse / $financementActif->montant_accorde) * 100)
                : 0;
        @endphp
        <div class="mb-4">
            <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                <span>Progression</span>
                <span>{{ $progression }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-bm-600 h-2 rounded-full transition-all" style="width: {{ $progression }}%"></div>
            </div>
        </div>

        {{-- Prochaine échéance --}}
        @php
            $prochaineEcheance = $financementActif->echeanciers
                ->whereIn('statut', ['a_venir', 'en_attente', 'retard'])
                ->sortBy('date_echeance')
                ->first();
        @endphp
        @if($prochaineEcheance)
        <div class="bg-gray-50 rounded-xl p-3 mb-4 flex justify-between items-center">
            <div>
                <p class="text-[10px] text-gray-400">Prochaine échéance</p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ number_format($prochaineEcheance->montant_du) }} F
                </p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-400">Date</p>
                <p class="text-sm font-medium {{ $prochaineEcheance->statut === 'retard' ? 'text-red-600' : 'text-gray-700' }}">
                    {{ \Carbon\Carbon::parse($prochaineEcheance->date_echeance)->format('d/m/Y') }}
                </p>
            </div>
        </div>
        @endif

        <a href="{{ route('busmetro.adherent.financement.remboursement') }}"
           class="block text-center py-2.5 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700 transition">
            Effectuer un remboursement
        </a>
    </div>

    {{-- ✅ Historique des échéances --}}
    @if($financementActif->echeanciers->count())
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Échéancier</h3>
        <div class="space-y-2">
            @foreach($financementActif->echeanciers->sortBy('numero_echeance') as $echeance)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-medium text-gray-400 w-5">#{{ $echeance->numero_echeance }}</span>
                    <div>
                        <p class="text-xs font-medium text-gray-800">{{ number_format($echeance->montant_du) }} F</p>
                        <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</p>
                    </div>
                </div>
                @php
                    $badgeClass = match($echeance->statut) {
                        'paye'       => 'bg-green-100 text-green-700',
                        'retard'     => 'bg-red-100 text-red-700',
                        'partiel'    => 'bg-orange-100 text-orange-700',
                        'en_attente' => 'bg-yellow-100 text-yellow-700',
                        default      => 'bg-gray-100 text-gray-500',
                    };
                    $badgeLabel = match($echeance->statut) {
                        'paye'       => 'Payé',
                        'retard'     => 'Retard',
                        'partiel'    => 'Partiel',
                        'en_attente' => 'En attente',
                        'a_venir'    => 'À venir',
                        default      => ucfirst($echeance->statut),
                    };
                @endphp
                <span class="text-[9px] px-2 py-0.5 rounded-full font-medium {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @else
    {{-- ✅ Demande déjà soumise pour la session ouverte --}}
    @if($dejaPostule && $sessionOuverte)
    <div class="bg-blue-50 rounded-2xl p-5 text-center border border-blue-100">
        <i class="fas fa-clock text-blue-400 text-2xl mb-2"></i>
        <p class="text-sm font-semibold text-blue-700">Demande soumise</p>
        <p class="text-xs text-blue-500 mt-1">
            Votre candidature pour la session <b>{{ $sessionOuverte->nom }}</b> est en cours d'examen.
        </p>
    </div>

    {{-- ✅ Session ouverte, pas encore postulé --}}
    @elseif($sessionOuverte)
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
            <h3 class="text-sm font-semibold text-gray-900">Session ouverte : {{ $sessionOuverte->nom }}</h3>
        </div>
        <p class="text-[10px] text-gray-400 mb-1">
            Candidatures jusqu'au {{ \Carbon\Carbon::parse($sessionOuverte->date_fin_candidature)->format('d/m/Y') }}
        </p>
        <p class="text-xs text-gray-500 mb-4">
            Votre score actuel :
            <b class="text-bm-600">{{ auth()->guard('adherent')->user()->score_actuel ?? 0 }}/100</b>
            (minimum requis : {{ $sessionOuverte->score_minimum }})
        </p>
        <form action="{{ route('busmetro.adherent.financement.postuler') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="session_id" value="{{ $sessionOuverte->id }}">
            <div>
                <label class="text-[10px] text-gray-500 block mb-1">Montant souhaité (FCFA)</label>
                <input type="number" name="montant_demande" placeholder="Ex: 150000" min="1"
                       required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500 focus:border-transparent outline-none">
            </div>
            <div>
                <label class="text-[10px] text-gray-500 block mb-1">Motif de la demande</label>
                <textarea name="motif" rows="2" placeholder="Pourquoi avez-vous besoin de ce financement ?"
                          required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500 outline-none resize-none"></textarea>
            </div>
            <div>
                <label class="text-[10px] text-gray-500 block mb-1">Description du projet (optionnel)</label>
                <textarea name="description_projet" rows="3" placeholder="Décrivez votre projet en détail..."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500 outline-none resize-none"></textarea>
            </div>
            <button type="submit"
                    class="w-full py-3 bg-bm-600 text-white rounded-xl text-sm font-semibold hover:bg-bm-700 transition">
                Soumettre ma candidature
            </button>
        </form>
    </div>

    {{-- ✅ Aucune session ouverte --}}
    @else
    <div class="bg-gray-50 rounded-2xl p-8 text-center">
        <i class="fas fa-calendar-times text-gray-300 text-3xl mb-3"></i>
        <p class="text-sm font-semibold text-gray-600">Aucune session ouverte</p>
        <p class="text-xs text-gray-400 mt-1">Les sessions de financement sont trimestrielles. Revenez prochainement.</p>
    </div>
    @endif

    {{-- Historique des demandes passées --}}
    @if($adherent->demandes->count())
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Mes demandes passées</h3>
        <div class="space-y-2">
            @foreach($adherent->demandes->sortByDesc('created_at') as $demande)
            <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-xs font-medium text-gray-800">{{ $demande->session->nom ?? '—' }}</p>
                    <p class="text-[10px] text-gray-400">{{ number_format($demande->montant_demande) }} F demandés</p>
                </div>
                @php
                    $dClass = match($demande->statut) {
                        'financee'        => 'bg-green-100 text-green-700',
                        'validee'         => 'bg-blue-100 text-blue-700',
                        'selectionnee'    => 'bg-indigo-100 text-indigo-700',
                        'pre_selectionnee'=> 'bg-purple-100 text-purple-700',
                        'rejetee'         => 'bg-red-100 text-red-700',
                        default           => 'bg-yellow-100 text-yellow-700',
                    };
                    $dLabel = match($demande->statut) {
                        'financee'        => 'Financée',
                        'validee'         => 'Validée',
                        'selectionnee'    => 'Sélectionnée',
                        'pre_selectionnee'=> 'Pré-sélectionnée',
                        'rejetee'         => 'Rejetée',
                        default           => 'En attente',
                    };
                @endphp
                <span class="text-[9px] px-2 py-0.5 rounded-full font-medium {{ $dClass }}">{{ $dLabel }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
