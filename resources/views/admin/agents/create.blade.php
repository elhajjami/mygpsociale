@extends('admin.layouts.app')

@section('title', 'Nouvel Agent')

@section('header', 'Nouvel Agent')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.agents.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux agents</a>
    </div>
            <div class="bg-white rounded-lg shadow">
                <form method="POST" action="{{ route('admin.agents.store') }}" class="p-6 space-y-6">
                    @csrf

                    <!-- Informations personnelles -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="matricule" :value="__('Matricule')" />
                                <x-input id="matricule" name="matricule" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cin" :value="__('CIN')" />
                                <x-input id="cin" name="cin" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('cin')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nom" :value="__('Nom')" />
                                <x-input id="nom" name="nom" type="text" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="prenom" :value="__('Prénom')" />
                                <x-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date_naissance" :value="__('Date de naissance')" />
                                <x-input id="date_naissance" name="date_naissance" type="date" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Professionnelles -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations professionnelles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="categorie" :value="__('Catégorie')" />
                                <select id="categorie" name="categorie" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Exécution">Exécution</option>
                                    <option value="Maîtrise">Maîtrise</option>
                                    <option value="Cadre">Cadre</option>
                                    <option value="Hors cadre">Hors cadre</option>
                                </select>
                                <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="niveau" :value="__('Niveau')" />
                                <x-input id="niveau" name="niveau" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('niveau')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="degre" :value="__('Degré')" />
                                <x-input id="degre" name="degre" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('degre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="dp_affectation" :value="__('DP / Affectation')" />
                                <x-input id="dp_affectation" name="dp_affectation" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('dp_affectation')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="population" :value="__('Population')" />
                                <select id="population" name="population" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="autre">Autre</option>
                                    <option value="BO">BO</option>
                                </select>
                                <x-input-error :messages="$errors->get('population')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="statut" :value="__('Statut')" />
                                <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Actif">Actif</option>
                                    <option value="Retraité">Retraité</option>
                                    <option value="Sorti">Sorti</option>
                                    <option value="Suspendu">Suspendu</option>
                                    <option value="Décédé">Décédé</option>
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
                                <x-input id="date_entree" name="date_entree" type="date" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('date_entree')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date_sortie" :value="__('Date de sortie')" />
                                <x-input id="date_sortie" name="date_sortie" type="date" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('date_sortie')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date_retraite" :value="__('Date de retraite')" />
                                <x-input id="date_retraite" name="date_retraite" type="date" class="mt-1 block w-full" />
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
                                <x-input id="numero_immatriculation" name="numero_immatriculation" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('numero_immatriculation')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="numero_affiliation" :value="__('Numéro d\'affiliation')" />
                                <x-input id="numero_affiliation" name="numero_affiliation" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('numero_affiliation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="border-t border-gray-200 pt-6">
                        <x-input-label for="observations" :value="__('Observations')" />
                        <textarea id="observations" name="observations" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        <x-input-error :messages="$errors->get('observations')" class="mt-2" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                        <a href="{{ route('admin.agents.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Annuler
                        </a>
                        <x-primary-button>
                            Créer l'agent
                        </x-primary-button>
                    </div>
                </form>
            </div>
@endsection
