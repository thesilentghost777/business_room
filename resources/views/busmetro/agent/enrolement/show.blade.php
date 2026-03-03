@extends('busmetro.layouts.agent')

@section('title', 'Fiche Adhérent — ' . $adherent->nom_complet)
@section('page-title', 'Fiche Adhérent')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('busmetro.agent.enrolement.index') }}" class="text-sm text-gray-400 hover:text-bm-600">
            <i class="fas fa-arrow-left mr-1"></i>Retour
        </a>
        @if(!$adherent->kit_achete)
            <button onclick="document.getElementById('kitModal').classList.remove('hidden')"
                class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-shopping-bag mr-1.5"></i>Vendre un kit
            </button>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Identity card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-bm-100 flex items-center justify-center text-bm-700 font-semibold text-sm flex-shrink-0">
                @if($adherent->photo_identite_url)
                    <img src="{{ $adherent->photo_identite_url }}" class="w-12 h-12 rounded-full object-cover" alt="">
                @else
                    {{ strtoupper(substr($adherent->prenom,0,1)) }}{{ strtoupper(substr($adherent->nom,0,1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $adherent->prenom }} {{ $adherent->nom }}</p>
                <p class="text-xs text-gray-500">{{ $adherent->telephone }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                    {{ $adherent->statut === 'actif' ? 'bg-bm-100 text-bm-700' :
                       ($adherent->statut === 'suspendu' ? 'bg-red-100 text-red-700' :
                       ($adherent->statut === 'radie' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700')) }}">
                    {{ ucfirst(str_replace('_',' ', $adherent->statut)) }}
                </span>
                @if($adherent->kit_achete)
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium bg-bm-100 text-bm-700">
                        <i class="fas fa-check-circle mr-0.5"></i>Kit
                    </span>
                @else
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-400">
                        Sans kit
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    @php
        $cotisationsValides = $adherent->cotisationsValides ?? collect();
        $totalCotise = $cotisationsValides->sum('montant');
        $nbFilleuls = $adherent->filleuls()->count() ?? 0;
        $score = $adherent->score_actuel ?? 0;
        $dateAdhesion = $adherent->date_adhesion ? \Carbon\Carbon::parse($adherent->date_adhesion)->format('d/m/Y') : '—';
    @endphp

    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Total cotisé</p>
            <p class="text-base font-semibold text-gray-900">{{ number_format($totalCotise, 0, ',', ' ') }} <span class="text-xs text-gray-400">FCFA</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Cotisations</p>
            <p class="text-base font-semibold text-gray-900">{{ $cotisationsValides->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Filleuls</p>
            <p class="text-base font-semibold text-gray-900">{{ $nbFilleuls }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Score</p>
            <p class="text-base font-semibold {{ $score >= 70 ? 'text-bm-600' : ($score >= 40 ? 'text-amber-500' : 'text-red-500') }}">{{ $score }}</p>
        </div>
    </div>

    {{-- Infos personnelles --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <p class="text-[10px] font-semibold text-gray-400 uppercase">Informations personnelles</p>
        </div>
        <div class="divide-y divide-gray-50">
            @php
                $fields = [
                    ['Matricule', $adherent->matricule],
                    ['Email', $adherent->email ?? null],
                    ['Date de naissance', $adherent->date_naissance ? \Carbon\Carbon::parse($adherent->date_naissance)->format('d/m/Y') : null],
                    ['Sexe', $adherent->sexe === 'M' ? 'Masculin' : ($adherent->sexe === 'F' ? 'Féminin' : null)],
                    ['Ville', $adherent->ville ?? null],
                    ['Quartier', $adherent->quartier ?? null],
                    ['Adresse', $adherent->adresse ?? null],
                    ['Type pièce', $adherent->piece_identite_type ?? null],
                    ['N° pièce', $adherent->piece_identite_numero ?? null],
                    ['Adhésion', $dateAdhesion],
                ];
            @endphp
            @foreach($fields as [$label, $value])
                <div class="flex justify-between items-center px-4 py-2.5">
                    <span class="text-[11px] text-gray-400 uppercase font-medium">{{ $label }}</span>
                    <span class="text-xs {{ $value ? 'text-gray-900' : 'text-gray-300 italic' }}">{{ $value ?? 'Non renseigné' }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Profil économique --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <p class="text-[10px] font-semibold text-gray-400 uppercase">Profil économique</p>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="flex justify-between items-center px-4 py-2.5">
                <span class="text-[11px] text-gray-400 uppercase font-medium">Catégorie</span>
                @if($adherent->profil)
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium bg-blue-50 text-blue-600">{{ $adherent->profil->nom }}</span>
                @else
                    <span class="text-xs text-gray-300 italic">Non assignée</span>
                @endif
            </div>
            <div class="flex justify-between items-center px-4 py-2.5">
                <span class="text-[11px] text-gray-400 uppercase font-medium">Activité</span>
                <span class="text-xs {{ $adherent->activite_economique ? 'text-gray-900' : 'text-gray-300 italic' }}">{{ $adherent->activite_economique ?? 'Non renseignée' }}</span>
            </div>
            <div class="flex justify-between items-center px-4 py-2.5">
                <span class="text-[11px] text-gray-400 uppercase font-medium">Revenu mensuel</span>
                <span class="text-xs {{ $adherent->revenu_mensuel ? 'text-bm-600 font-medium' : 'text-gray-300 italic' }}">
                    {{ $adherent->revenu_mensuel ? number_format($adherent->revenu_mensuel, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Parrainage --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <p class="text-[10px] font-semibold text-gray-400 uppercase">Parrainage</p>
        </div>
        <div class="px-4 py-3 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-[11px] text-gray-400 uppercase font-medium">Code de parrainage</span>
                <code class="text-xs px-2 py-0.5 bg-gray-50 border border-gray-100 rounded text-bm-600 font-mono cursor-pointer select-all"
                    onclick="navigator.clipboard.writeText(this.textContent.trim());this.textContent='Copié !';setTimeout(()=>this.textContent='{{ $adherent->code_parrainage }}',1500)">
                    {{ $adherent->code_parrainage }}
                </code>
            </div>
            @if($adherent->parrain)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-3 py-2">
                    <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center text-bm-700 text-xs font-semibold flex-shrink-0">
                        {{ strtoupper(substr($adherent->parrain->prenom,0,1)) }}{{ strtoupper(substr($adherent->parrain->nom,0,1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $adherent->parrain->nom_complet }}</p>
                        <p class="text-[11px] text-gray-400">{{ $adherent->parrain->matricule }}</p>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-300 italic">Aucun parrain</p>
            @endif
        </div>
    </div>

    {{-- Kit --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
            <p class="text-[10px] font-semibold text-gray-400 uppercase">Kit d'adhésion</p>
            @if(!$adherent->kit_achete)
                <button onclick="document.getElementById('kitModal').classList.remove('hidden')"
                    class="px-3 py-1 bg-bm-600 text-white rounded-lg text-[10px] font-medium hover:bg-bm-700">
                    <i class="fas fa-plus mr-1"></i>Acquérir
                </button>
            @endif
        </div>
        <div class="px-4 py-3">
            @if($adherent->kit_achete && $adherent->achatKit)
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-bm-500"></i>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-900">{{ $adherent->achatKit->kit->nom }}</p>
                        <p class="text-[11px] text-gray-400">
                            {{ number_format($adherent->achatKit->montant, 0, ',', ' ') }} FCFA
                            &bull; {{ ucfirst($adherent->achatKit->moyen_paiement ?? 'N/A') }}
                            &bull; {{ $adherent->achatKit->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium bg-bm-100 text-bm-700">Payé</span>
                </div>
            @else
                <p class="text-xs text-gray-400 text-center py-4">Aucun kit acquis pour le moment.</p>
            @endif
        </div>
    </div>

    {{-- Cotisations récentes --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
            <p class="text-[10px] font-semibold text-gray-400 uppercase">Cotisations récentes</p>
            <a href="#" class="text-[10px] text-bm-600 hover:underline">Voir tout</a>
        </div>
        @if($cotisationsValides->count() > 0)
            <table class="w-full">
                <tbody class="divide-y divide-gray-50">
                    @foreach($cotisationsValides->take(6) as $cotisation)
                        <tr>
                            <td class="px-4 py-2.5 text-xs text-gray-900">{{ $cotisation->typeCotisation->nom ?? 'N/A' }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-400">{{ \Carbon\Carbon::parse($cotisation->date_cotisation)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-xs font-medium text-bm-600 text-right">+{{ number_format($cotisation->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-xs text-gray-400 text-center py-6">Aucune cotisation enregistrée.</p>
        @endif
    </div>

    {{-- Documents --}}
    @if($adherent->piece_identite_url || $adherent->document_activite_url || $adherent->photo_identite_url)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-50">
                <p class="text-[10px] font-semibold text-gray-400 uppercase">Documents</p>
            </div>
            <div class="px-4 py-3 flex gap-2 flex-wrap">
                @if($adherent->piece_identite_url)
                    <a href="{{ $adherent->piece_identite_url }}" target="_blank"
                        class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-100 rounded-xl text-xs text-gray-600 hover:border-bm-300 hover:text-bm-600">
                        <i class="fas fa-id-card text-[10px]"></i> Pièce d'identité
                    </a>
                @endif
                @if($adherent->document_activite_url)
                    <a href="{{ $adherent->document_activite_url }}" target="_blank"
                        class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-100 rounded-xl text-xs text-gray-600 hover:border-bm-300 hover:text-bm-600">
                        <i class="fas fa-file text-[10px]"></i> Doc. activité
                    </a>
                @endif
                @if($adherent->photo_identite_url)
                    <a href="{{ $adherent->photo_identite_url }}" target="_blank"
                        class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-100 rounded-xl text-xs text-gray-600 hover:border-bm-300 hover:text-bm-600">
                        <i class="fas fa-image text-[10px]"></i> Photo
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>

{{-- Modal Kit --}}
<div id="kitModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-5">
        <p class="text-sm font-semibold text-gray-900 mb-4">Acquérir un kit d'adhésion</p>
        <form method="POST" action="{{ route('busmetro.agent.enrolement.kit', $adherent) }}">
            @csrf
            <div class="mb-3">
                <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-1">Kit</label>
                <select name="kit_id" required id="kitSelect" onchange="updateKitPrice()"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-bm-400">
                    <option value="">— Sélectionner —</option>
                    @foreach($kits as $kit)
                        <option value="{{ $kit->id }}" data-prix="{{ $kit->prix }}">
                            {{ $kit->nom }} — {{ number_format($kit->prix, 0, ',', ' ') }} FCFA
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="kitDetails" class="hidden mb-3 flex justify-between items-center px-3 py-2 bg-bm-50 rounded-xl">
                <span class="text-xs text-gray-500">Montant</span>
                <span id="kitPrice" class="text-sm font-semibold text-bm-700"></span>
            </div>

            <div class="mb-4">
                <label class="text-[10px] font-semibold text-gray-400 uppercase block mb-2">Paiement</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 px-3 py-2.5 border border-gray-100 rounded-xl cursor-pointer has-[:checked]:border-bm-400 has-[:checked]:bg-bm-50">
                        <input type="radio" name="mode_paiement" value="especes" required class="accent-bm-600">
                        <span class="text-sm">💵</span>
                        <span class="text-xs font-medium text-gray-900">Espèces</span>
                    </label>
                    <label class="flex items-center gap-3 px-3 py-2.5 border border-gray-100 rounded-xl cursor-pointer has-[:checked]:border-bm-400 has-[:checked]:bg-bm-50">
                        <input type="radio" name="mode_paiement" value="moneyfusion" class="accent-bm-600">
                        <span class="text-sm">📱</span>
                        <span class="text-xs font-medium text-gray-900">MoneyFusion</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                    Confirmer
                </button>
                <button type="button" onclick="document.getElementById('kitModal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-100 rounded-xl text-sm text-gray-500 hover:bg-gray-50">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateKitPrice() {
        const sel = document.getElementById('kitSelect');
        const prix = sel.options[sel.selectedIndex]?.dataset?.prix;
        const details = document.getElementById('kitDetails');
        if (prix) {
            document.getElementById('kitPrice').textContent =
                parseFloat(prix).toLocaleString('fr-FR') + ' FCFA';
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }
    }
    document.getElementById('kitModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endsection
