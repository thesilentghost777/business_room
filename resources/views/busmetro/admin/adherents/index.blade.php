@extends('busmetro.layouts.admin')
@section('title', 'Adhérents')
@section('page-title', 'Gestion des adhérents')

@section('content')
<div class="space-y-4">
    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un adhérent..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500">
            </div>
            <select name="statut" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="suspendu" {{ request('statut') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                <option value="radie" {{ request('statut') == 'radie' ? 'selected' : '' }}>Radié</option>
            </select>
            <select name="profil" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                <option value="">Tous les profils</option>
                @foreach($profils ?? [] as $p)
                <option value="{{ $p->id }}" {{ request('profil') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700"><i class="fas fa-search mr-1.5"></i>Filtrer</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Membre</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Téléphone</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Profil</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Statut</th>
                    <th class="text-center text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Score</th>
                    <th class="text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Kit</th>
                    <th class="text-right text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($adherents ?? [] as $adh)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-full bg-bm-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-bm-700 text-[10px] font-bold">{{ substr($adh->prenom, 0, 1) }}{{ substr($adh->nom, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-900">{{ $adh->prenom }} {{ $adh->nom }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $adh->code_adherent }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $adh->telephone }}</td>
                        <td class="px-4 py-3"><span class="text-xs text-gray-600">{{ $adh->profil->nom ?? '—' }}</span></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $adh->statut === 'actif' ? 'bg-bm-100 text-bm-700' : ($adh->statut === 'en_attente' ? 'bg-amber-100 text-amber-700' : ($adh->statut === 'suspendu' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst(str_replace('_', ' ', $adh->statut)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-semibold {{ ($adh->score_global ?? 0) >= 70 ? 'text-bm-600' : (($adh->score_global ?? 0) >= 40 ? 'text-amber-600' : 'text-red-600') }}">{{ $adh->score_global ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($adh->kit_achete)<i class="fas fa-check-circle text-bm-500 text-sm"></i>@else<i class="fas fa-times-circle text-gray-300 text-sm"></i>@endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('busmetro.admin.adherents.show', $adh) }}" class="p-1.5 text-gray-400 hover:text-bm-600 rounded-lg hover:bg-bm-50"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('busmetro.admin.adherents.edit', $adh) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50"><i class="fas fa-edit text-xs"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center"><i class="fas fa-users text-gray-200 text-3xl mb-2"></i><p class="text-sm text-gray-400">Aucun adhérent trouvé</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($adherents ?? collect(), 'links'))
        <div class="px-4 py-3 border-t border-gray-100">{{ $adherents->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
