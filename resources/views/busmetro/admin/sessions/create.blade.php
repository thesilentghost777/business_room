@extends('busmetro.layouts.admin')
@section('title', 'Nouvelle session')
@section('page-title', 'Nouvelle session de financement')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('busmetro.admin.sessions.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">

            {{-- Nom --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nom de la session</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"
                    placeholder="Session T1 2025">
            </div>

            {{-- Trimestre + Année --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Trimestre</label>
                    <select name="trimestre" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach([1,2,3,4] as $t)
                            <option value="{{ $t }}" {{ old('trimestre') == $t ? 'selected' : '' }}>T{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Année</label>
                    <input type="number" name="annee" value="{{ old('annee', date('Y')) }}" required
                        min="2024"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"
                        placeholder="{{ date('Y') }}">
                </div>
            </div>

            {{-- Dates de candidature --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Début candidature</label>
                    <input type="date" name="date_debut_candidature" value="{{ old('date_debut_candidature') }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Fin candidature</label>
                    <input type="date" name="date_fin_candidature" value="{{ old('date_fin_candidature') }}" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
            </div>

            {{-- Budget + Nb bénéficiaires --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Budget total (FCFA)</label>
                    <input type="number" name="budget_total" value="{{ old('budget_total') }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"
                        placeholder="5000000">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nb max bénéficiaires</label>
                    <input type="number" name="nombre_beneficiaires_max" value="{{ old('nombre_beneficiaires_max') }}" required min="1"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"
                        placeholder="20">
                </div>
            </div>

            {{-- Score minimum --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Score minimum (/100)</label>
                <input type="number" name="score_minimum" value="{{ old('score_minimum', 60) }}" required
                    min="0" max="100" step="0.01"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm"
                    placeholder="60">
            </div>

        </div>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-end space-x-3">
            <a href="{{ route('busmetro.admin.sessions.index') }}"
                class="px-4 py-2 text-sm border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</a>
            <button type="submit"
                class="px-6 py-2 text-sm text-white bg-bm-600 rounded-xl hover:bg-bm-700 font-medium">Créer la session</button>
        </div>
    </form>
</div>
@endsection
