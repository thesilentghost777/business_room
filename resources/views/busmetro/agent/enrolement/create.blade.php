@extends('busmetro.layouts.agent')
@section('title', 'Nouvel adhérent')
@section('page-title', 'Enrôler un nouvel adhérent')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('busmetro.agent.enrolement.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- INFORMATIONS PERSONNELLES --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Informations personnelles</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm @error('nom') border-red-400 @enderror">
                    @error('nom')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm @error('prenom') border-red-400 @enderror">
                    @error('prenom')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Téléphone <span class="text-red-500">*</span></label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm @error('telephone') border-red-400 @enderror">
                    @error('telephone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-gray-400">(optionnel)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sexe</label>
                    <select name="sexe" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">-- Choisir --</option>
                        <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- PROFIL & ACTIVITÉ --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Profil & Activité économique</h3>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Profil</label>
                <select name="profil_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                    <option value="">-- Choisir un profil --</option>
                    @foreach($profils ?? [] as $p)
                        <option value="{{ $p->id }}" {{ old('profil_id') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Activité économique / Métier</label>
                <input type="text" name="activite_economique" value="{{ old('activite_economique') }}"
                    placeholder="Ex: Commerce de détail, Couture, Menuiserie..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description de l'activité</label>
                <textarea name="description_activite" rows="3"
                    placeholder="Décrivez brièvement l'activité exercée..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm resize-none">{{ old('description_activite') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Revenu mensuel estimé (FCFA)</label>
                <input type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel', 0) }}" min="0" step="500"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
            </div>
        </div>

        {{-- LOCALISATION --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Localisation</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quartier</label>
                    <input type="text" name="quartier" value="{{ old('quartier') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Adresse complète</label>
                <input type="text" name="adresse" value="{{ old('adresse') }}"
                    placeholder="Rue, numéro, bâtiment..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
            </div>
        </div>

        {{-- PIÈCE D'IDENTITÉ --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Pièce d'identité</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type de pièce</label>
                    <select name="piece_identite_type" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">-- Choisir --</option>
                        <option value="CNI" {{ old('piece_identite_type') === 'CNI' ? 'selected' : '' }}>CNI</option>
                        <option value="Passeport" {{ old('piece_identite_type') === 'Passeport' ? 'selected' : '' }}>Passeport</option>
                        <option value="Permis de conduire" {{ old('piece_identite_type') === 'Permis de conduire' ? 'selected' : '' }}>Permis de conduire</option>
                        <option value="Carte de séjour" {{ old('piece_identite_type') === 'Carte de séjour' ? 'selected' : '' }}>Carte de séjour</option>
                        <option value="Autre" {{ old('piece_identite_type') === 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Numéro de pièce</label>
                    <input type="text" name="piece_identite_numero" value="{{ old('piece_identite_numero') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
            </div>
        </div>

        {{-- PARRAINAGE --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Parrainage <span class="text-gray-400 font-normal">(optionnel)</span></h3>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Code de parrainage</label>
                <input type="text" name="code_parrainage" value="{{ old('code_parrainage') }}"
                    placeholder="Entrer le code du parrain si applicable"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                <p class="text-xs text-gray-400 mt-1">Laissez vide si l'adhérent n'a pas de parrain.</p>
            </div>
        </div>

        {{-- SÉCURITÉ --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Accès & Sécurité</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" value="000000" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm @error('password') border-red-400 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Par défaut : 000000</p>
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end space-x-3 pb-6">
            <a href="{{ route('busmetro.agent.enrolement.index') }}"
                class="px-4 py-2 text-sm border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</a>
            <button type="submit"
                class="px-6 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700 font-medium">
                Enrôler l'adhérent
            </button>
        </div>
    </form>
</div>
@endsection
