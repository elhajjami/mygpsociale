@extends('admin.layouts.app')

@section('title', 'Modifier Partenaire')

@section('header', 'Modifier Partenaire')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.partenaires.update', $partenaire) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Numéro Convention -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de Convention *</label>
                    <input type="text" name="numero_convention" value="{{ old('numero_convention', $partenaire->numero_convention) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('numero_convention')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du Partenaire *</label>
                    <input type="text" name="nom" value="{{ old('nom', $partenaire->nom) }}" required
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
                        <option value="clinique" {{ old('type_structure', $partenaire->type_structure) == 'clinique' ? 'selected' : '' }}>Clinique</option>
                        <option value="laboratoire" {{ old('type_structure', $partenaire->type_structure) == 'laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                        <option value="médecin" {{ old('type_structure', $partenaire->type_structure) == 'médecin' ? 'selected' : '' }}>Médecin</option>
                        <option value="radiologie" {{ old('type_structure', $partenaire->type_structure) == 'radiologie' ? 'selected' : '' }}>Radiologie</option>
                    </select>
                    @error('type_structure')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Spécialité (conditionnelle) -->
                <div id="specialite-group" class="{{ in_array(old('type_structure', $partenaire->type_structure), ['clinique', 'laboratoire', 'médecin']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Spécialité</label>
                    <select name="specialite" id="specialite"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner...</option>
                    </select>
                    @error('specialite')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ville -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <select name="ville" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner...</option>
                        <option value="Fès" {{ old('ville', $partenaire->ville) == 'Fès' ? 'selected' : '' }}>Fès</option>
                        <option value="Meknès" {{ old('ville', $partenaire->ville) == 'Meknès' ? 'selected' : '' }}>Meknès</option>
                    </select>
                    @error('ville')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $partenaire->telephone) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 0612345678">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $partenaire->email) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: contact@exemple.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Effet</label>
                        <input type="date" name="date_effet" value="{{ old('date_effet', $partenaire->date_effet ? $partenaire->date_effet->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Fin</label>
                        <input type="date" name="date_fin" value="{{ old('date_fin', $partenaire->date_fin ? $partenaire->date_fin->format('Y-m-d') : '') }}"
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
                        <option value="active" {{ old('statut', $partenaire->statut) == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expirée" {{ old('statut', $partenaire->statut) == 'expirée' ? 'selected' : '' }}>Expirée</option>
                        <option value="suspendue" {{ old('statut', $partenaire->statut) == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                        <option value="résiliée" {{ old('statut', $partenaire->statut) == 'résiliée' ? 'selected' : '' }}>Résiliée</option>
                    </select>
                </div>

                <!-- Coordonnées -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coordonnées</label>
                    <textarea name="coordonnees" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('coordonnees', $partenaire->coordonnees) }}</textarea>
                </div>

                <!-- Observations -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('observations', $partenaire->observations) }}</textarea>
                </div>

                <!-- Section Facturation -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de Facturation</h3>

                    <div class="space-y-4">
                        <!-- Adresse -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <textarea name="adresse" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Adresse complète">{{ old('adresse', $partenaire->adresse) }}</textarea>
                            @error('adresse')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Fax -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fax</label>
                                <input type="text" name="fax" value="{{ old('fax', $partenaire->fax) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ex: 051234567">
                                @error('fax')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- CNSS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CNSS</label>
                                <input type="text" name="cnss" value="{{ old('cnss', $partenaire->cnss) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Numéro CNSS">
                                @error('cnss')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ICE -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ICE (Identifiant Commun de l'Entreprise)</label>
                            <input type="text" name="ice" value="{{ old('ice', $partenaire->ice) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ex: 001234567891234">
                            @error('ice')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Patente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patente</label>
                                <input type="text" name="patente" value="{{ old('patente', $partenaire->patente) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Numéro de patente">
                                @error('patente')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- IF (Identifiant Fiscal) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IF (Identifiant Fiscal)</label>
                                <input type="text" name="if" value="{{ old('if', $partenaire->if) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Numéro IF">
                                @error('if')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Coordonnées bancaires -->
                        <div class="border-t pt-4">
                            <h4 class="text-md font-medium text-gray-800 mb-3">Coordonnées Bancaires</h4>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Banque -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Banque</label>
                                    <input type="text" name="banque" value="{{ old('banque', $partenaire->banque) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Nom de la banque">
                                    @error('banque')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Agence -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Agence</label>
                                    <input type="text" name="agence" value="{{ old('agence', $partenaire->agence) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Agence bancaire">
                                    @error('agence')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- RIB -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">RIB (Relevé d'Identité Bancaire)</label>
                                <input type="text" name="rib" value="{{ old('rib', $partenaire->rib) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="24 caractères"
                                    maxlength="24">
                                @error('rib')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('admin.partenaires.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Définition des spécialités par type
    const specialitesParType = {
        clinique: [
            { value: 'Multidisciplinaire', label: 'Multidisciplinaire' }
        ],
        laboratoire: [
            { value: 'Biologie médicale', label: 'Biologie médicale' }
        ],
        médecin: [
            { value: 'Cardiologie', label: 'Cardiologie' },
            { value: 'Dermatologie', label: 'Dermatologie' },
            { value: 'Gastro-entérologie', label: 'Gastro-entérologie' },
            { value: 'Gynécologie', label: 'Gynécologie' },
            { value: 'Médecine générale', label: 'Médecine générale' },
            { value: 'Médecine interne', label: 'Médecine interne' },
            { value: 'Néphrologie', label: 'Néphrologie' },
            { value: 'Neurologie', label: 'Neurologie' },
            { value: 'Ophtalmologie', label: 'Ophtalmologie' },
            { value: 'ORL', label: 'ORL' },
            { value: 'Pédiatrie', label: 'Pédiatrie' },
            { value: 'Pneumologie', label: 'Pneumologie' },
            { value: 'Psychiatrie', label: 'Psychiatrie' },
            { value: 'Radiologie', label: 'Radiologie' },
            { value: 'Rhumatologie', label: 'Rhumatologie' },
            { value: 'Stomatologie', label: 'Stomatologie' },
            { value: 'Chirurgie générale', label: 'Chirurgie générale' },
            { value: 'Chirurgie orthopédique', label: 'Chirurgie orthopédique' }
        ]
    };

    // Sauvegarder la valeur actuelle de spécialité
    const currentSpecialite = "{{ old('specialite', $partenaire->specialite) }}";

    function updateSpecialites() {
        const typeSelect = document.getElementById('type_structure');
        const specialiteGroup = document.getElementById('specialite-group');
        const specialiteSelect = document.getElementById('specialite');

        const selectedType = typeSelect.value;
        const selectedSpecialite = specialiteSelect.value;

        // Cacher le groupe
        specialiteGroup.classList.add('hidden');

        // Réinitialiser le select
        specialiteSelect.innerHTML = '<option value="">Sélectionner...</option>';

        if (specialitesParType[selectedType]) {
            // Afficher le groupe
            specialiteGroup.classList.remove('hidden');

            // Ajouter les options
            specialitesParType[selectedType].forEach(function(spec) {
                const option = document.createElement('option');
                option.value = spec.value;
                option.textContent = spec.label;
                if (spec.value === currentSpecialite) {
                    option.selected = true;
                }
                specialiteSelect.appendChild(option);
            });
        }
    }

    document.getElementById('type_structure').addEventListener('change', updateSpecialites);

    // Initialiser au chargement
    window.addEventListener('DOMContentLoaded', updateSpecialites);
</script>
@endpush
@endsection
