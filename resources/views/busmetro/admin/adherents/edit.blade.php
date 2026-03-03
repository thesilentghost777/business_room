@extends('busmetro.layouts.admin')
@section('title', 'Modifier adhérent')
@section('page-title')
<div class="flex items-center space-x-2">
    <a href="{{ route('busmetro.admin.adherents.show', $adherent) }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
    <span>Modifier {{ $adherent->prenom }} {{ $adherent->nom }}</span>
</div>
@endsection

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('busmetro.admin.adherents.update', $adherent) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-900">Informations personnelles</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Nom</label><input type="text" name="nom" value="{{ old('nom', $adherent->nom) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Prénom</label><input type="text" name="prenom" value="{{ old('prenom', $adherent->prenom) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Téléphone</label><input type="tel" name="telephone" value="{{ old('telephone', $adherent->telephone) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $adherent->email) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Sexe</label><select name="sexe" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"><option value="M" {{ $adherent->sexe === 'M' ? 'selected' : '' }}>M</option><option value="F" {{ $adherent->sexe === 'F' ? 'selected' : '' }}>F</option></select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Ville</label><input type="text" name="ville" value="{{ old('ville', $adherent->ville) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Quartier</label><input type="text" name="quartier" value="{{ old('quartier', $adherent->quartier) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            </div>

            {{-- ✅ CORRECTION : profil_id était présent mais statut manquait (requis par le contrôleur) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Profil</label>
                    <select name="profil_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">Aucun</option>
                        @foreach($profils ?? [] as $p)
                            <option value="{{ $p->id }}" {{ old('profil_id', $adherent->profil_id) == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="en_attente" {{ old('statut', $adherent->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="actif"      {{ old('statut', $adherent->statut) === 'actif'      ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu"   {{ old('statut', $adherent->statut) === 'suspendu'   ? 'selected' : '' }}>Suspendu</option>
                        <option value="radie"      {{ old('statut', $adherent->statut) === 'radie'      ? 'selected' : '' }}>Radié</option>
                    </select>
                </div>
            </div>

            {{-- ✅ CORRECTION : activite_economique et revenu_mensuel étaient dans la validation mais absents du formulaire --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Activité économique</label>
                <input type="text" name="activite_economique" value="{{ old('activite_economique', $adherent->activite_economique) }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Revenu mensuel (F)</label>
                <input type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel', $adherent->revenu_mensuel) }}" min="0" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
            </div>
        </div>

        {{-- Affichage des erreurs de validation --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex justify-end space-x-3">
            <a href="{{ route('busmetro.admin.adherents.show', $adherent) }}" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700 font-medium">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
