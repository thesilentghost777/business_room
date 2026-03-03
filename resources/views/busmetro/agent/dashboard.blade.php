@extends('busmetro.layouts.agent')
@section('title', 'Tableau de bord Agent')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-bm-100 flex items-center justify-center mb-3"><i class="fas fa-user-plus text-bm-600"></i></div>
            <p class="text-2xl font-bold text-gray-900">{{ $enrolements ?? 0 }}</p>
            <p class="text-xs text-gray-500">Enrôlements ce mois</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3"><i class="fas fa-coins text-blue-600"></i></div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($collectes ?? 0) }} <span class="text-sm text-gray-400">F</span></p>
            <p class="text-xs text-gray-500">Cotisations collectées</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-3"><i class="fas fa-undo text-amber-600"></i></div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($remboursements ?? 0) }} <span class="text-sm text-gray-400">F</span></p>
            <p class="text-xs text-gray-500">Remboursements collectés</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-3"><i class="fas fa-users text-purple-600"></i></div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalAdherents ?? 0 }}</p>
            <p class="text-xs text-gray-500">Mes adhérents</p>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ route('busmetro.agent.enrolement.create') }}" class="bg-white rounded-2xl border border-gray-100 p-5 hover:border-bm-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-2xl bg-bm-100 flex items-center justify-center mb-3 group-hover:bg-bm-200"><i class="fas fa-user-plus text-bm-600 text-lg"></i></div>
            <h4 class="text-sm font-semibold text-gray-900">Nouveau membre</h4>
            <p class="text-xs text-gray-500 mt-1">Enrôler un adhérent</p>
        </a>
        <a href="{{ route('busmetro.agent.collecte.cotisation') }}" class="bg-white rounded-2xl border border-gray-100 p-5 hover:border-bm-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center mb-3 group-hover:bg-blue-200"><i class="fas fa-coins text-blue-600 text-lg"></i></div>
            <h4 class="text-sm font-semibold text-gray-900">Collecter cotisation</h4>
            <p class="text-xs text-gray-500 mt-1">NKD / NKH</p>
        </a>
        <a href="{{ route('busmetro.agent.collecte.remboursement') }}" class="bg-white rounded-2xl border border-gray-100 p-5 hover:border-bm-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center mb-3 group-hover:bg-amber-200"><i class="fas fa-undo text-amber-600 text-lg"></i></div>
            <h4 class="text-sm font-semibold text-gray-900">Remboursement</h4>
            <p class="text-xs text-gray-500 mt-1">Collecter un remboursement</p>
        </a>
    </div>
</div>
@endsection
