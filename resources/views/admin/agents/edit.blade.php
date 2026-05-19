@extends('admin.layouts.app')

@section('title', 'Modifier Agent ' . $agent->matricule)

@section('header', 'Modifier Agent ' . $agent->matricule)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.agents.show', $agent) }}" class="text-indigo-600 hover:text-indigo-900">← Retour à l'agent</a>
    </div>
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.agents.update', $agent) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations personnelles -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="matricule" :value="__('Matricule')" />
                        <x-input id="matricule" name="matricule" type="text" class="mt-1 block w-full" required value="{{ $agent->matricule }}" />
                        <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cin" :value="__('CIN')" />
                        <x-input id="cin" name="cin" type="text" class="mt-1 block w-full" value="{{ old('cin', $agent->cin) }}" />
                        <x-input-error :messages="$errors->get('cin')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nom" :value="__('Nom')" />
                        <x-input id="nom" name="nom" type="text" class="mt-1 block w-full" required value="{{ old('nom', $agent->nom) }}" />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="prenom" :value="__('Prénom')" />
                        <x-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" value="{{ old('prenom', $agent->prenom) }}" />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_naissance" :value="__('Date de naissance')" />
                        <x-input id="date_naissance" name="date_naissance" type="date" class="mt-1 block w-full" value="{{ old('date_naissance', $agent->date_naissance ? $agent->date_naissance->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Professionnelles -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations professionnelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="date_recrutement" :value="__('Date de recrutement')" />
                        <x-input id="date_recrutement" name="date_recrutement" type="date" class="mt-1 block w-full" value="{{ old('date_recrutement', $agent->date_recrutement ? $agent->date_recrutement->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_recrutement')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="dp_affectation" :value="__('DP / Affectation')" />
                        <x-input id="dp_affectation" name="dp_affectation" type="text" class="mt-1 block w-full" value="{{ old('dp_affectation', $agent->dp_affectation) }}" />
                        <x-input-error :messages="$errors->get('dp_affectation')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="statut" :value="__('Statut')" />
                        <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="Actif" {{ $agent->statut === 'Actif' ? 'selected' : '' }}>Actif</option>
                            <option value="Retraité" {{ $agent->statut === 'Retraité' ? 'selected' : '' }}>Retraité</option>
                            <option value="Sorti" {{ $agent->statut === 'Sorti' ? 'selected' : '' }}>Sorti</option>
                            <option value="Suspendu" {{ $agent->statut === 'Suspendu' ? 'selected' : '' }}>Suspendu</option>
                            <option value="Décédé" {{ $agent->statut === 'Décédé' ? 'selected' : '' }}>Décédé</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Dates importantes -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dates importantes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="date_entree" :value="__('Date d\'entrée')" />
                        <x-input id="date_entree" name="date_entree" type="date" class="mt-1 block w-full" value="{{ old('date_entree', $agent->date_entree ? $agent->date_entree->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_entree')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_sortie" :value="__('Date de sortie')" />
                        <x-input id="date_sortie" name="date_sortie" type="date" class="mt-1 block w-full" value="{{ old('date_sortie', $agent->date_sortie ? $agent->date_sortie->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_sortie')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_retraite" :value="__('Date de retraite')" />
                        <x-input id="date_retraite" name="date_retraite" type="date" class="mt-1 block w-full" value="{{ old('date_retraite', $agent->date_retraite ? $agent->date_retraite->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_retraite')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- CNAMGS -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">CNAMGS</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="numero_immatriculation" :value="__('Numéro d\'immatriculation')" />
                        <x-input id="numero_immatriculation" name="numero_immatriculation" type="text" class="mt-1 block w-full" value="{{ old('numero_immatriculation', $agent->numero_immatriculation) }}" />
                        <x-input-error :messages="$errors->get('numero_immatriculation')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_affiliation" :value="__('Date d\'affiliation')" />
                        <x-input id="date_affiliation" name="date_affiliation" type="date" class="mt-1 block w-full" value="{{ old('date_affiliation', $agent->date_affiliation ? $agent->date_affiliation->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('date_affiliation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Coordonnées bancaires -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Coordonnées bancaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="banque" :value="__('Banque')" />
                        <x-input id="banque" name="banque" type="text" class="mt-1 block w-full" value="{{ old('banque', $agent->banque) }}" />
                        <x-input-error :messages="$errors->get('banque')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="compte_bancaire" :value="__('Compte bancaire')" />
                        <x-input id="compte_bancaire" name="compte_bancaire" type="text" class="mt-1 block w-full" value="{{ old('compte_bancaire', $agent->compte_bancaire) }}" />
                        <x-input-error :messages="$errors->get('compte_bancaire')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cle_bancaire" :value="__('Clé bancaire')" />
                        <x-input id="cle_bancaire" name="cle_bancaire" type="text" class="mt-1 block w-full" value="{{ old('cle_bancaire', $agent->cle_bancaire) }}" />
                        <x-input-error :messages="$errors->get('cle_bancaire')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="info_banque" :value="__('Informations bancaires')" />
                        <textarea id="info_banque" name="info_banque" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('info_banque', $agent->info_banque) }}</textarea>
                        <x-input-error :messages="$errors->get('info_banque')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Observations -->
            <div class="border-t border-gray-200 pt-6">
                <x-input-label for="observations" :value="__('Observations')" />
                <textarea id="observations" name="observations" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('observations', $agent->observations) }}</textarea>
                <x-input-error :messages="$errors->get('observations')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.agents.show', $agent) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Annuler
                </a>
                <x-primary-button>
                    Mettre à jour
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection
