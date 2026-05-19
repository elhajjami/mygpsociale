@extends('admin.layouts.app')

@section('title', 'Ajouter un ayant droit - ' . $agent->nom_complet)

@section('header', 'Ajouter un ayant droit')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.agents.show', $agent) }}" class="text-indigo-600 hover:text-indigo-900">
            ← Retour à l'agent {{ $agent->matricule }}
        </a>
    </div>

    <!-- Infos agent -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-blue-900">Agent : {{ $agent->matricule }} - {{ $agent->nom_complet }}</h3>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.agents.ayants-droit.store', $agent) }}">
            @csrf

            <div class="space-y-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de lien *</label>
                    <select name="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner...</option>
                        <option value="conjoint" {{ old('type') == 'conjoint' ? 'selected' : '' }}>Conjoint(e)</option>
                        <option value="enfant" {{ old('type') == 'enfant' ? 'selected' : '' }}>Enfant</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom et Prénom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom et Prénom *</label>
                    <input type="text" name="nom_prenom" value="{{ old('nom_prenom') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Benali Mohamed">
                    @error('nom_prenom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de naissance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('date_naissance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CIN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                    <input type="text" name="cin" value="{{ old('cin') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: AB123456">
                    @error('cin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="statut" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="En attente" {{ old('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                        <option value="Validé" {{ old('statut') == 'Validé' ? 'selected' : '' }}>Validé</option>
                        <option value="Rejeté" {{ old('statut') == 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Observations -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('observations') }}</textarea>
                    @error('observations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('admin.agents.show', $agent) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Ajouter l'ayant droit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
