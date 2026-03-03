@extends('busmetro.layouts.admin')
@section('title', 'Configuration')
@section('page-title', 'Configuration système')

@section('content')
<div class="space-y-6" x-data="{ tab: 'general' }">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex space-x-1 bg-gray-100 rounded-xl p-1 max-w-fit">
        <button @click="tab='general'" :class="tab==='general' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all">Général</button>
        <button @click="tab='kits'" :class="tab==='kits' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all">Kits</button>
        <button @click="tab='cotisations'" :class="tab==='cotisations' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all">Cotisations</button>
        <button @click="tab='scoring'" :class="tab==='scoring' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all">Scoring</button>
        <button @click="tab='profils'" :class="tab==='profils' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all">Profils</button>
    </div>

    {{-- ===== GÉNÉRAL ===== --}}
    <div x-show="tab==='general'" class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Paramètres généraux</h3>
        <form action="{{ route('busmetro.admin.configuration.update') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            @foreach($configs->groupBy('groupe') as $groupe => $groupeConfigs)
            <div class="mb-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-3">{{ ucfirst($groupe) }}</p>
                @foreach($groupeConfigs as $config)
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        {{ ucfirst(str_replace('_', ' ', $config->cle)) }}
                    </label>
                    <input
                        type="{{ in_array($config->type, ['integer','float']) ? 'number' : 'text' }}"
                        step="{{ $config->type === 'float' ? '0.01' : '1' }}"
                        name="configs[{{ $config->cle }}]"
                        value="{{ $config->valeur }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"
                    >
                    @if($config->description)
                    <p class="text-[10px] text-gray-400 mt-1">{{ $config->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
            <button type="submit" class="px-6 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700 font-medium">Enregistrer</button>
        </form>
    </div>

    {{-- ===== KITS ===== --}}
    <div x-show="tab==='kits'" class="space-y-4">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">Kits d'adhésion disponibles</p>
            <button @click="$dispatch('open-modal', 'kit-create')" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($kits as $kit)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-900">{{ $kit->nom }}</h4>
                    <span class="text-xs font-bold text-bm-600">{{ number_format($kit->prix) }} F</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $kit->description }}</p>
                @if(is_array($kit->contenu))
                <ul class="text-[10px] text-gray-400 mb-3 space-y-0.5">
                    @foreach($kit->contenu as $item)
                    <li><i class="fas fa-check text-bm-500 mr-1"></i>{{ $item }}</li>
                    @endforeach
                </ul>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-[10px] {{ $kit->actif ? 'text-green-600' : 'text-gray-400' }}">{{ $kit->actif ? 'Actif' : 'Inactif' }}</span>
                    <div class="flex space-x-3">
                        <button @click="$dispatch('open-modal', 'kit-edit-{{ $kit->id }}')" class="text-xs text-gray-400 hover:text-blue-600">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('busmetro.admin.configuration.kit.destroy', $kit) }}" method="POST" onsubmit="return confirm('Supprimer ce kit ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 col-span-3">Aucun kit disponible.</p>
            @endforelse
        </div>

        {{-- Modals édition kits --}}
        @foreach($kits as $kit)
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'kit-edit-{{ $kit->id }}')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Modifier le kit</h3>
                <form action="{{ route('busmetro.admin.configuration.kit.update', $kit) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ $kit->nom }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ $kit->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prix (F CFA)</label>
                        <input type="number" name="prix" value="{{ $kit->prix }}" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Contenu (une ligne par élément)</label>
                        <textarea name="contenu" rows="4" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ is_array($kit->contenu) ? implode("\n", $kit->contenu) : $kit->contenu }}</textarea>
                    </div>
                    <label class="flex items-center space-x-2 text-xs text-gray-700">
                        <input type="hidden" name="actif" value="0">
                        <input type="checkbox" name="actif" value="1" {{ $kit->actif ? 'checked' : '' }}>
                        <span>Actif</span>
                    </label>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Modal création kit --}}
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'kit-create')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Nouveau kit</h3>
                <form action="{{ route('busmetro.admin.configuration.kit.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prix (F CFA)</label>
                        <input type="number" name="prix" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Contenu (une ligne par élément)</label>
                        <textarea name="contenu" rows="4" required placeholder="Carte CASS&#10;Cahier de recettes&#10;Guide du membre" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <label class="flex items-center space-x-2 text-xs text-gray-700">
                        <input type="hidden" name="actif" value="0">
                        <input type="checkbox" name="actif" value="1" checked>
                        <span>Actif</span>
                    </label>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== COTISATIONS ===== --}}
    <div x-show="tab==='cotisations'" class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Types de cotisation</h3>
            <button @click="$dispatch('open-modal', 'cotisation-create')" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
        </div>
        <div class="space-y-3">
            @forelse($typesCotisation as $tc)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <span class="text-xs font-bold {{ $tc->code === 'NKD' ? 'text-blue-600' : 'text-purple-600' }}">{{ $tc->code }}</span>
                    <span class="text-xs text-gray-600 ml-2">{{ $tc->nom }}</span>
                    @if($tc->description)<p class="text-[10px] text-gray-400 mt-0.5">{{ $tc->description }}</p>@endif
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-900">Min : {{ number_format($tc->montant_minimum) }} F</p>
                        <p class="text-[10px] text-gray-500">Défaut : {{ number_format($tc->montant_defaut) }} F</p>
                        <p class="text-[10px] text-gray-400">{{ ucfirst($tc->frequence) }}</p>
                    </div>
                    <button @click="$dispatch('open-modal', 'cotisation-edit-{{ $tc->id }}')" class="text-gray-400 hover:text-blue-600">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Aucun type de cotisation.</p>
            @endforelse
        </div>

        {{-- Modals édition cotisations --}}
        @foreach($typesCotisation as $tc)
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'cotisation-edit-{{ $tc->id }}')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Modifier {{ $tc->code }}</h3>
                <form action="{{ route('busmetro.admin.configuration.cotisation.update', $tc) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ $tc->nom }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ $tc->description }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant minimum (F)</label>
                            <input type="number" name="montant_minimum" value="{{ $tc->montant_minimum }}" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant défaut (F)</label>
                            <input type="number" name="montant_defaut" value="{{ $tc->montant_defaut }}" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Fréquence</label>
                        <select name="frequence" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                            <option value="journalier"    {{ $tc->frequence==='journalier'    ? 'selected' : '' }}>Journalier</option>
                            <option value="hebdomadaire"  {{ $tc->frequence==='hebdomadaire'  ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel"       {{ $tc->frequence==='mensuel'       ? 'selected' : '' }}>Mensuel</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 text-xs text-gray-700">
                            <input type="hidden" name="obligatoire" value="0">
                            <input type="checkbox" name="obligatoire" value="1" {{ $tc->obligatoire ? 'checked' : '' }}>
                            <span>Obligatoire</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs text-gray-700">
                            <input type="hidden" name="donne_droit_soutien" value="0">
                            <input type="checkbox" name="donne_droit_soutien" value="1" {{ $tc->donne_droit_soutien ? 'checked' : '' }}>
                            <span>Droit au soutien</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs text-gray-700">
                            <input type="hidden" name="actif" value="0">
                            <input type="checkbox" name="actif" value="1" {{ $tc->actif ? 'checked' : '' }}>
                            <span>Actif</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Modal création cotisation --}}
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'cotisation-create')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Nouveau type de cotisation</h3>
                <form action="{{ route('busmetro.admin.configuration.cotisation.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Code (ex: NKD)</label>
                            <input type="text" name="code" required style="text-transform:uppercase" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fréquence</label>
                            <select name="frequence" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                                <option value="journalier">Journalier</option>
                                <option value="hebdomadaire">Hebdomadaire</option>
                                <option value="mensuel">Mensuel</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant minimum (F)</label>
                            <input type="number" name="montant_minimum" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant défaut (F)</label>
                            <input type="number" name="montant_defaut" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2 text-xs text-gray-700">
                            <input type="hidden" name="obligatoire" value="0">
                            <input type="checkbox" name="obligatoire" value="1">
                            <span>Obligatoire</span>
                        </label>
                        <label class="flex items-center space-x-2 text-xs text-gray-700">
                            <input type="hidden" name="donne_droit_soutien" value="0">
                            <input type="checkbox" name="donne_droit_soutien" value="1">
                            <span>Droit au soutien</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== SCORING ===== --}}
    <div x-show="tab==='scoring'" class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Critères de scoring</h3>
            <button @click="$dispatch('open-modal', 'scoring-create')" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Critère</th>
                        <th class="text-center text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Poids</th>
                        <th class="text-center text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Max pts</th>
                        <th class="text-center text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Ordre</th>
                        <th class="text-right text-[10px] font-semibold text-gray-400 uppercase pb-2 px-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($criteres as $c)
                    <tr>
                        <td class="py-2.5 px-2">
                            <p class="text-xs font-medium text-gray-900">{{ $c->nom }}</p>
                            <p class="text-[10px] text-gray-400">{{ $c->description }}</p>
                        </td>
                        <td class="py-2.5 px-2 text-center text-xs font-bold text-gray-900">{{ $c->poids }}</td>
                        <td class="py-2.5 px-2 text-center text-xs font-bold text-gray-900">{{ $c->max_points }}</td>
                        <td class="py-2.5 px-2 text-center text-xs text-gray-500">{{ $c->ordre }}</td>
                        <td class="py-2.5 px-2 text-right">
                            <button @click="$dispatch('open-modal', 'scoring-edit-{{ $c->id }}')" class="text-gray-400 hover:text-blue-600">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-sm text-gray-400 py-4 text-center">Aucun critère.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modals édition critères --}}
        @foreach($criteres as $c)
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'scoring-edit-{{ $c->id }}')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Modifier le critère</h3>
                <form action="{{ route('busmetro.admin.configuration.scoring.update', $c) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ $c->nom }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ $c->description }}</textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Poids</label>
                            <input type="number" name="poids" value="{{ $c->poids }}" min="1" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Max points</label>
                            <input type="number" name="max_points" value="{{ $c->max_points }}" min="1" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ordre</label>
                            <input type="number" name="ordre" value="{{ $c->ordre }}" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                    </div>
                    <label class="flex items-center space-x-2 text-xs text-gray-700">
                        <input type="hidden" name="actif" value="0">
                        <input type="checkbox" name="actif" value="1" {{ $c->actif ? 'checked' : '' }}>
                        <span>Actif</span>
                    </label>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Modal création critère --}}
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'scoring-create')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Nouveau critère de scoring</h3>
                <form action="{{ route('busmetro.admin.configuration.scoring.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Code unique (ex: regularite)</label>
                        <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Poids</label>
                            <input type="number" name="poids" value="1" min="1" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Max points</label>
                            <input type="number" name="max_points" value="20" min="1" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ordre</label>
                            <input type="number" name="ordre" value="0" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== PROFILS ===== --}}
    <div x-show="tab==='profils'" class="space-y-4">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">Profils adhérents</p>
            <button @click="$dispatch('open-modal', 'profil-create')" class="px-4 py-2 bg-bm-600 text-white rounded-xl text-sm font-medium hover:bg-bm-700">
                <i class="fas fa-plus mr-1"></i>Ajouter
            </button>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            @forelse($profils as $profil)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="text-sm font-semibold text-gray-900">{{ $profil->nom }}</h4>
                    <button @click="$dispatch('open-modal', 'profil-edit-{{ $profil->id }}')" class="text-gray-400 hover:text-blue-600 ml-2">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $profil->description }}</p>
                @if(is_array($profil->documents_requis))
                <ul class="text-[10px] text-gray-400 mb-3 space-y-0.5">
                    @foreach($profil->documents_requis as $doc)
                    <li><i class="fas fa-file text-gray-300 mr-1"></i>{{ $doc }}</li>
                    @endforeach
                </ul>
                @endif
                <div class="flex items-center justify-between text-[10px] text-gray-400">
                    <span>Plafond : <b class="text-gray-900">{{ number_format($profil->plafond_financement) }} F</b></span>
                    <span>{{ $profil->adherents_count ?? 0 }} membres</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Aucun profil.</p>
            @endforelse
        </div>

        {{-- Modals édition profils --}}
        @foreach($profils as $profil)
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'profil-edit-{{ $profil->id }}')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Modifier le profil</h3>
                <form action="{{ route('busmetro.admin.configuration.profil.update', $profil) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ $profil->nom }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ $profil->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Documents requis (un par ligne)</label>
                        <textarea name="documents_requis" rows="4" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">{{ is_array($profil->documents_requis) ? implode("\n", $profil->documents_requis) : $profil->documents_requis }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Plafond financement (F)</label>
                        <input type="number" name="plafond_financement" value="{{ $profil->plafond_financement }}" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <label class="flex items-center space-x-2 text-xs text-gray-700">
                        <input type="hidden" name="actif" value="0">
                        <input type="checkbox" name="actif" value="1" {{ $profil->actif ? 'checked' : '' }}>
                        <span>Actif</span>
                    </label>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Modal création profil --}}
        <div x-data="{ open: false }"
             x-on:open-modal.window="open = ($event.detail === 'profil-create')"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="open=false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Nouveau profil</h3>
                <form action="{{ route('busmetro.admin.configuration.profil.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Code unique (ex: salarie)</label>
                        <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Documents requis (un par ligne)</label>
                        <textarea name="documents_requis" rows="4" required placeholder="CNI&#10;Photo 4x4&#10;Justificatif d'activité" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Plafond financement (F)</label>
                        <input type="number" name="plafond_financement" min="0" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bm-500">
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="open=false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
