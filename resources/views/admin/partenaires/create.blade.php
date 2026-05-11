@extends('admin.layouts.app')

@section('title', 'Nouveau Partenaire')

@section('header', 'Nouveau Partenaire')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.partenaires.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Numéro Convention -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de Convention *</label>
                    <input type="text" name="numero_convention" value="{{ old('numero_convention') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('numero_convention')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du Partenaire *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type Structure -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de Structure *</label>
                    <select name="type_structure" id="type_structure" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner...</option>
                        <option value="clinique" {{ old('type_structure') == 'clinique' ? 'selected' : '' }}>Clinique</option>
                        <option value="laboratoire" {{ old('type_structure') == 'laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                        <option value="médecin" {{ old('type_structure') == 'médecin' ? 'selected' : '' }}>Médecin</option>
                        <option value="radiologie" {{ old('type_structure') == 'radiologie' ? 'selected' : '' }}>Radiologie</option>
                    </select>
                    @error('type_structure')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Spécialité (conditionnelle) -->
                <div id="specialite-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Spécialité *</label>
                    <input type="text" name="specialite" value="{{ old('specialite') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('specialite')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ville -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <input type="text" name="ville" value="{{ old('ville') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('ville')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Effet</label>
                        <input type="date" name="date_effet" value="{{ old('date_effet') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Fin</label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="statut" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" {{ old('statut') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expirée" {{ old('statut') == 'expirée' ? 'selected' : '' }}>Expirée</option>
                        <option value="suspendue" {{ old('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                        <option value="résiliée" {{ old('statut') == 'résiliée' ? 'selected' : '' }}>Résiliée</option>
                    </select>
                </div>

                <!-- Coordonnées -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coordonnées</label>
                    <textarea name="coordonnees" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('coordonnees') }}</textarea>
                </div>

                <!-- Observations -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('observations') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('admin.partenaires.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Créer le Partenaire
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type_structure').addEventListener('change', function() {
        const specialiteGroup = document.getElementById('specialite-group');
        if (this.value === 'médecin') {
            specialiteGroup.classList.remove('hidden');
        } else {
            specialiteGroup.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
