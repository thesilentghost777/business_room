@extends('busmetro.layouts.admin')
@section('title', 'Détail adhérent')
@section('page-title')
<div class="flex items-center space-x-2">
    <a href="{{ route('busmetro.admin.adherents.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
    <span>{{ $adherent->prenom }} {{ $adherent->nom }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header card -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-bm-100 flex items-center justify-center">
                    <span class="text-bm-700 text-xl font-bold">{{ substr($adherent->prenom, 0, 1) }}{{ substr($adherent->nom, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $adherent->prenom }} {{ $adherent->nom }}</h2>
                    <p class="text-sm text-gray-500">{{ $adherent->code_adherent }} · {{ $adherent->telephone }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $adherent->statut === 'actif' ? 'bg-bm-100 text-bm-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($adherent->statut) }}</span>
                        <span class="text-xs text-gray-400">Score: <b class="text-gray-900">{{ $adherent->score_global ?? 0 }}/100</b></span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <form action="{{ route('busmetro.admin.adherents.statut', $adherent) }}" method="POST" class="inline">
                    @csrf
                    <select name="statut" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-gray-200 rounded-xl bg-white">
                        <option value="actif" {{ $adherent->statut === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu" {{ $adherent->statut === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="radie" {{ $adherent->statut === 'radie' ? 'selected' : '' }}>Radié</option>
                    </select>
                </form>
                <form action="{{ route('busmetro.admin.adherents.reset-password', $adherent) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs border border-gray-200 rounded-xl hover:bg-gray-50" onclick="return confirm('Réinitialiser le mot de passe ?')">
                        <i class="fas fa-key mr-1"></i>Reset MDP
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Info grid -->
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Informations personnelles</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Email</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Sexe</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->sexe === 'M' ? 'Masculin' : ($adherent->sexe === 'F' ? 'Féminin' : '—') }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Naissance</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->date_naissance ? $adherent->date_naissance->format('d/m/Y') : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Ville</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->ville ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Quartier</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->quartier ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Profil</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->profil->nom ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Inscription</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->created_at->format('d/m/Y') }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Statistiques financières</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Kit acheté</dt><dd class="text-xs font-medium">@if($adherent->kit_achete)<span class="text-bm-600"><i class="fas fa-check"></i> Oui</span>@else<span class="text-red-500">Non</span>@endif</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Total cotisations</dt><dd class="text-xs font-bold text-gray-900">{{ number_format($adherent->cotisations->sum('montant') ?? 0) }} F</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Cotisations NKD</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->cotisations->where('type_cotisation.code', 'NKD')->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Financements</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->financements->count() ?? 0 }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Parrainage</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->code_parrainage }}</dd></div>
                <div class="flex justify-between"><dt class="text-xs text-gray-500">Filleuls</dt><dd class="text-xs font-medium text-gray-900">{{ $adherent->filleuls->count() ?? 0 }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Score détaillé</h3>
            @if($adherent->dernierScore)
            <div class="space-y-3">
                @foreach($adherent->dernierScore->details ?? [] as $critere => $val)
                <div>
                    <div class="flex justify-between mb-1"><span class="text-[10px] text-gray-500">{{ ucfirst($critere) }}</span><span class="text-[10px] font-bold">{{ $val }}/{{ $adherent->dernierScore->details_max[$critere] ?? 20 }}</span></div>
                    <div class="w-full h-1.5 bg-gray-100 rounded-full"><div class="h-1.5 bg-bm-500 rounded-full" style="width: {{ min(100, ($val / max(1, $adherent->dernierScore->details_max[$critere] ?? 20)) * 100) }}%"></div></div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-400 text-center py-4">Aucun score calculé</p>
            @endif
        </div>
    </div>

    <!-- Cotisations history -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Historique des cotisations</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-gray-100">
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Date</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Type</th>
                    <th class="text-right text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Montant</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Mode</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($adherent->cotisations->take(10) as $cot)
                    <tr>
                        <td class="py-2 px-2 text-xs text-gray-600">{{ $cot->date_cotisation->format('d/m/Y') }}</td>
                        <td class="py-2 px-2"><span class="text-xs font-medium {{ $cot->typeCotisation->code === 'NKD' ? 'text-blue-600' : 'text-purple-600' }}">{{ $cot->typeCotisation->code }}</span></td>
                        <td class="py-2 px-2 text-xs font-semibold text-gray-900 text-right">{{ number_format($cot->montant) }} F</td>
                        <td class="py-2 px-2 text-xs text-gray-500">{{ ucfirst($cot->mode_paiement) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-6 text-center text-xs text-gray-400">Aucune cotisation</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
