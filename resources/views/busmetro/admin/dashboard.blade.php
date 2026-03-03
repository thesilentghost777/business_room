@extends('busmetro.layouts.admin')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-bm-100 flex items-center justify-center"><i class="fas fa-users text-bm-600"></i></div>
                <span class="text-xs text-bm-600 font-medium bg-bm-50 px-2 py-0.5 rounded-full">+{{ $nouveauxAdherents ?? 12 }}</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAdherents ?? 1247) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Adhérents actifs</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center"><i class="fas fa-coins text-blue-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCotisations ?? 4520000) }} <span class="text-sm font-normal text-gray-400">F</span></p>
            <p class="text-xs text-gray-500 mt-0.5">Cotisations ce mois</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-hand-holding-usd text-amber-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalFinancements ?? 15000000) }} <span class="text-sm font-normal text-gray-400">F</span></p>
            <p class="text-xs text-gray-500 mt-0.5">Financements en cours</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center"><i class="fas fa-percentage text-purple-600"></i></div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $tauxRemboursement ?? 87 }}<span class="text-sm font-normal text-gray-400">%</span></p>
            <p class="text-xs text-gray-500 mt-0.5">Taux de remboursement</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Évolution des cotisations</h3>
                <select class="text-xs border border-gray-200 rounded-lg px-2 py-1 text-gray-600">
                    <option>6 derniers mois</option>
                    <option>12 derniers mois</option>
                </select>
            </div>
            <canvas id="cotisationsChart" height="200"></canvas>
        </div>

        <!-- Recent activity -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Activité récente</h3>
            <div class="space-y-3">
                @forelse($activites ?? [] as $activite)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-{{ $activite['icon'] ?? 'circle' }} text-bm-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-900 font-medium">{{ $activite['titre'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $activite['date'] }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                    <p class="text-xs text-gray-400">Aucune activité récente</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent tables -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Derniers adhérents -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Derniers adhérents</h3>
                <a href="{{ route('busmetro.admin.adherents.index') }}" class="text-xs text-bm-600 hover:text-bm-700 font-medium">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="border-b border-gray-100">
                        <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Membre</th>
                        <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Statut</th>
                        <th class="text-right text-[10px] font-semibold text-gray-400 uppercase tracking-wider pb-2">Score</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($derniersAdherents ?? [] as $adh)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-bm-100 flex items-center justify-center"><span class="text-bm-700 text-[10px] font-bold">{{ substr($adh->prenom, 0, 1) }}</span></div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">{{ $adh->prenom }} {{ $adh->nom }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $adh->telephone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                                    {{ $adh->statut === 'actif' ? 'bg-bm-100 text-bm-700' : ($adh->statut === 'en_attente' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $adh->statut)) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right"><span class="text-xs font-semibold text-gray-900">{{ $adh->score_global ?? 0 }}/100</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-6 text-center text-xs text-gray-400">Aucun adhérent</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dernières transactions -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Dernières transactions</h3>
                <a href="{{ route('busmetro.admin.transactions.index') }}" class="text-xs text-bm-600 hover:text-bm-700 font-medium">Voir tout</a>
            </div>
            <div class="space-y-2.5">
                @forelse($dernieresTransactions ?? [] as $tx)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-full {{ $tx->statut === 'completed' ? 'bg-bm-100' : 'bg-amber-100' }} flex items-center justify-center">
                            <i class="fas fa-{{ $tx->statut === 'completed' ? 'check' : 'clock' }} {{ $tx->statut === 'completed' ? 'text-bm-600' : 'text-amber-600' }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-900">{{ ucfirst($tx->type) }}</p>
                            <p class="text-[10px] text-gray-400">{{ $tx->created_at->format('d/m H:i') }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">{{ number_format($tx->montant) }} F</span>
                </div>
                @empty
                <div class="text-center py-6"><p class="text-xs text-gray-400">Aucune transaction</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('cotisationsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? ['Jan','Fév','Mar','Avr','Mai','Jun']) !!},
                datasets: [{
                    label: 'Cotisations (FCFA)',
                    data: {!! json_encode($chartData ?? [800000,920000,1100000,980000,1250000,1400000]) !!},
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHitRadius: 20,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }},
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }}},
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => (v/1000000).toFixed(1)+'M' }}
                }
            }
        });
    }
</script>
@endpush
@endsection
