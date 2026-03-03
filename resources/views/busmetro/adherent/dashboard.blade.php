@extends('busmetro.layouts.adherent')
@section('title', 'Mon Tableau de bord')
@section('page-title', 'Mon Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- ===================== STATS ===================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Score --}}
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-bm-100 flex items-center justify-center">
                    <i class="fas fa-star text-bm-600"></i>
                </div>
                <span class="text-xs text-bm-600 font-medium bg-bm-50 px-2 py-0.5 rounded-full">Score</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $stats['score'] ?? 0 }}<span class="text-sm font-normal text-gray-400">/100</span>
            </p>
            <p class="text-xs text-gray-500 mt-0.5">Score de fidélité</p>
        </div>

        {{-- Total cotisations --}}
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-coins text-blue-600"></i>
                </div>
                <span class="text-xs text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-full">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ number_format($stats['total_cotisations'] ?? 0) }}
                <span class="text-sm font-normal text-gray-400">F</span>
            </p>
            <p class="text-xs text-gray-500 mt-0.5">Cotisations cumulées</p>
        </div>

        {{-- Cotisations ce mois --}}
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-amber-600"></i>
                </div>
                <span class="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">Ce mois</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $stats['cotisations_ce_mois'] ?? 0 }}
            </p>
            <p class="text-xs text-gray-500 mt-0.5">Cotisations ce mois</p>
        </div>

        {{-- Filleuls actifs --}}
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-user-friends text-purple-600"></i>
                </div>
                <span class="text-xs text-purple-600 font-medium bg-purple-50 px-2 py-0.5 rounded-full">Réseau</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['filleuls'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Filleuls actifs</p>
        </div>

    </div>

    {{-- ===================== FINANCEMENT EN COURS ===================== --}}
    @if($stats['financement_en_cours'])
    @php $financement = $stats['financement_en_cours']; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Mon financement en cours</h3>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-bm-100 text-bm-700">
                {{ ucfirst(str_replace('_', ' ', $financement->statut)) }}
            </span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Montant accordé</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ number_format($financement->montant_accorde ?? 0) }} F</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Montant remboursé</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ number_format($financement->montant_rembourse ?? 0) }} F</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Reste à rembourser</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5">
                    {{ number_format(($financement->montant_accorde ?? 0) - ($financement->montant_rembourse ?? 0)) }} F
                </p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Échéances</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $financement->echeanciers->count() }}</p>
            </div>
        </div>

        {{-- Barre de progression remboursement --}}
        @if(($financement->montant_accorde ?? 0) > 0)
        @php
            $progression = round(($financement->montant_rembourse / $financement->montant_accorde) * 100);
        @endphp
        <div class="mt-4">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] text-gray-400">Progression remboursement</span>
                <span class="text-[10px] font-semibold text-bm-600">{{ $progression }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-bm-500 h-1.5 rounded-full transition-all duration-700" style="width: {{ min($progression, 100) }}%"></div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ===================== SESSION OUVERTE ===================== --}}
    @if($sessionOuverte)
    <div class="bg-bm-50 border border-bm-200 rounded-2xl p-5 flex items-start space-x-4">
        <div class="w-10 h-10 rounded-xl bg-bm-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-door-open text-bm-600"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-bm-800">Session de financement ouverte</p>
            <p class="text-xs text-bm-600 mt-0.5">
                Candidatures ouvertes jusqu'au
                {{ \Carbon\Carbon::parse($sessionOuverte->date_fin_candidature)->format('d/m/Y') }}
            </p>
        </div>

    </div>
    @endif

    {{-- ===================== COTISATIONS + NOTIFICATIONS ===================== --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Dernières cotisations --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Mes dernières cotisations</h3>
                <a href="{{ route('busmetro.adherent.cotisations') }}"
                   class="text-xs text-bm-600 hover:text-bm-700 font-medium">Voir tout</a>
            </div>

            @if($dernieresCotisations->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                <p class="text-xs text-gray-400">Aucune cotisation enregistrée</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Type</th>
                            <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Date</th>
                            <th class="text-right text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($dernieresCotisations as $cotisation)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5">
                                <span class="text-xs font-medium text-gray-900">
                                    {{ $cotisation->typeCotisation->nom ?? '—' }}
                                </span>
                            </td>
                            <td class="py-2.5">
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($cotisation->date_cotisation)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right">
                                <span class="text-xs font-semibold text-gray-900">
                                    {{ number_format($cotisation->montant) }} F
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                <a href="{{ route('busmetro.adherent.notifications') }}"
                   class="text-xs text-bm-600 hover:text-bm-700 font-medium">Tout voir</a>
            </div>

            @if($notifications->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-bell-slash text-gray-300 text-3xl mb-3"></i>
                <p class="text-xs text-gray-400">Aucune nouvelle notification</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($notifications as $notif)
                <a href="{{ route('busmetro.adherent.notifications.lire', $notif->id) }}"
                   class="flex items-start space-x-3 p-2 rounded-xl hover:bg-gray-50 transition-colors block">
                    <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-bell text-bm-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-900 font-medium leading-snug">{{ $notif->titre }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-bm-500 flex-shrink-0 mt-1.5"></span>
                </a>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    {{-- ===================== PROFIL RAPIDE ===================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Mon profil</h3>
            <a href="{{ route('busmetro.adherent.profil') }}"
               class="text-xs text-bm-600 hover:text-bm-700 font-medium">Modifier</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Nom complet</p>
                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $adherent->prenom }} {{ $adherent->nom }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Téléphone</p>
                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $adherent->telephone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Statut</p>
                <span class="inline-flex mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-medium
                    {{ $adherent->statut === 'actif' ? 'bg-bm-100 text-bm-700' : ($adherent->statut === 'en_attente' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst(str_replace('_', ' ', $adherent->statut)) }}
                </span>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Membre depuis</p>
                <p class="text-sm font-medium text-gray-900 mt-0.5">
                    {{ $adherent->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
