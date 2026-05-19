@extends('admin.layouts.app')

@section('title', 'Nouvelle Facture')

@section('header', 'Créer une Facture')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto" x-data="facturationForm()">

    <!-- Messages d'erreur/succès -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Erreur</h3>
                    <div class="mt-2 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Message d'erreur AJAX dynamique -->
    <div x-show="ajaxError" x-cloak
         class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800">Erreur de validation</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <template x-for="error in ajaxErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>
            </div>
            <button @click="ajaxError = false; ajaxErrors = []"
                    class="ml-auto flex-shrink-0 text-red-400 hover:text-red-600">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
    <!-- Sélection du type de facture -->
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Type de facturation</h3>
        <div class="grid grid-cols-2 gap-4">
            <button
                type="button"
                @click="type = 'medical'; typeChanged()"
                :class="type === 'medical' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                class="p-4 border-2 rounded-lg text-left transition hover:border-blue-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Formation Médicale</p>
                        <p class="text-sm text-gray-500">Médecin, Laboratoire, Radiologie</p>
                    </div>
                </div>
            </button>
            <button
                type="button"
                @click="type = 'clinique'; typeChanged()"
                :class="type === 'clinique' ? 'border-purple-500 bg-purple-50' : 'border-gray-300'"
                class="p-4 border-2 rounded-lg text-left transition hover:border-purple-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Clinique</p>
                        <p class="text-sm text-gray-500">Hospitalisation, Bloc opératoire</p>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('dprh.facturation.store') }}">
        @csrf

        <!-- Champs cachés -->
        <input type="hidden" name="type_facture" :value="type">
        <input type="hidden" name="demande_pec_id" :value="selectedPec?.id">

        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de facturation *</label>
                    <input type="date" name="date_facture" required value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
                    <input type="date" name="date_echeance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Partenaire
                        <span class="text-xs text-gray-500" x-show="type">(<span x-text="type === 'medical' ? 'Formation médicale' : 'Clinique'"></span>)</span>
                    </label>
                    <select name="partenaire_id" x-model.number="partenaireId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner...</option>
                        <template x-for="partenaire in partenairesFiltres" :key="partenaire.id">
                            <option :value="partenaire.id" x-text="partenaire.nom + ' (' + partenaire.ville + ')'"></option>
                        </template>
                    </select>
                    <p x-show="partenairesFiltres.length === 0 && type" class="mt-1 text-xs text-amber-600">
                        Aucun partenaire disponible pour ce type.
                    </p>
                </div>
            </div>
        </div>

        <!-- Informations Clinique (si type clinique) -->
        <div x-show="type === 'clinique'" class="bg-white rounded-lg shadow p-6 mb-6" x-cloak>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Demande PEC associée</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lier à une demande PEC (requis pour Clinique)</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            x-model="pecSearch"
                            @input="searchPec()"
                            placeholder="Rechercher par N° PEC ou Matricule..."
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <button type="button" @click="showRecherchePecClinique = !showRecherchePecClinique" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Rechercher
                        </button>
                    </div>
                    <!-- PEC sélectionnée -->
                    <div x-show="selectedPec" class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200" x-cloak>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">PEC : <span x-text="selectedPec?.numero"></span></p>
                                <p class="text-sm text-gray-600" x-text="selectedPec?.agent_nom + ' ' + selectedPec?.agent_prenom"></p>
                                <p class="text-xs text-gray-500" x-text="'Matricule: ' + (selectedPec?.matricule || '')"></p>
                            </div>
                            <button type="button" @click="selectedPec = null; pecSearch = ''; plafondInfo = null" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Plafond annuel -->
                        <div x-show="plafondInfo" class="mt-3 pt-3 border-t border-purple-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Plafond Annuel {{ now()->year }}</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-500">Plafond annuel:</span>
                                    <span class="font-medium" x-text="formatMontant(plafondInfo?.plafond_annuel) + ' DH'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Reste disponible:</span>
                                    <span class="font-medium" :class="plafondInfo?.reste < 0 ? 'text-red-600' : 'text-green-600'" x-text="formatMontant(plafondInfo?.reste) + ' DH'"></span>
                                </div>
                            </div>
                            <!-- Alerte dépassement -->
                            <div x-show="plafondDepasse" class="mt-2 p-2 bg-red-100 border border-red-300 rounded text-sm text-red-800">
                                <strong>Attention:</strong> La part adhérent (<span x-text="formatMontant(partAdherentCalcule)"></span> DH) dépasse le reste disponible !
                            </div>
                        </div>
                    </div>
                    <!-- Résultats de recherche -->
                    <div x-show="pecResults.length > 0" class="mt-3 border border-purple-300 rounded-lg overflow-hidden" x-cloak>
                        <template x-for="pec in pecResults" :key="pec.id">
                            <div
                                @click="selectPec(pec)"
                                class="px-4 py-3 hover:bg-purple-100 cursor-pointer border-b border-purple-200 last:border-b-0 flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="pec.numero"></p>
                                    <p class="text-sm text-gray-600" x-text="pec.agent"></p>
                                    <p class="text-xs text-gray-500" x-text="'Matricule: ' + pec.matricule + ' | ' + pec.partenaire"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-purple-700" x-text="pec.montant + ' DH'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails Hospitalisation (si type clinique) -->
        <div x-show="type === 'clinique'" class="bg-white rounded-lg shadow p-6 mb-6" x-cloak>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Hospitalisation</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du patient *</label>
                    <input type="text" name="nom_patient" :value="selectedPec ? ((selectedPec.agent_nom || '') + ' ' + (selectedPec.agent_prenom || '')).trim() : ''" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                    <input type="date" name="hospitalisation_du" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                    <input type="date" name="hospitalisation_au" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Lignes de facturation - MEDICAL -->
        <div x-show="type === 'medical'" class="bg-white rounded-lg shadow p-6 mb-6" x-cloak>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Actes médicaux</h3>
                <div class="flex gap-2">
                    <button type="button" @click="showRecherchePec = !showRecherchePec" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Rechercher PEC
                    </button>
                    <button type="button" @click="addLigneMedical()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        + Ajouter un acte
                    </button>
                </div>
            </div>

            <!-- Recherche PEC pour ajouter des actes -->
            <div x-show="showRecherchePec" x-transition class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher une demande PEC (par N° PEC ou Matricule)</label>
                <div class="flex gap-2">
                    <input
                        type="text"
                        x-model="pecMedicalSearch"
                        @input="searchPecMedical"
                        placeholder="N° PEC ou Matricule..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <!-- Résultats de recherche PEC -->
                <div x-show="pecMedicalResults.length > 0" class="mt-3 border border-green-300 rounded-lg overflow-hidden" x-cloak>
                    <template x-for="pec in pecMedicalResults" :key="pec.id">
                        <div
                            @click="ajouterLignesDepuisPec(pec)"
                            class="px-4 py-3 hover:bg-green-100 cursor-pointer border-b border-green-200 last:border-b-0 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900" x-text="pec.numero"></p>
                                <p class="text-sm text-gray-600" x-text="pec.agent"></p>
                                <p class="text-xs text-gray-500" x-text="'Matricule: ' + pec.matricule + ' | ' + pec.partenaire"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-700" x-text="pec.montant + ' DH'"></p>
                                <p class="text-xs text-gray-500">Cliquez pour ajouter</p>
                            </div>
                        </div>
                    </template>
                </div>
                <p x-show="pecMedicalSearch.length >= 2 && pecMedicalResults.length === 0" class="mt-2 text-sm text-gray-500" x-cloak>
                    Aucune demande PEC trouvée
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bénéficiaire</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nature acte</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(ligne, index) in lignesMedical" :key="index">
                            <tr class="border-b">
                                <td class="px-3 py-2">
                                    <input type="text" :name="'lignes[' + index + '][matricule]'" x-model="ligne.matricule" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'lignes[' + index + '][nom_patient]'" x-model="ligne.nom_patient" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                </td>
                                <td class="px-3 py-2">
                                    <select :name="'lignes[' + index + '][beneficiaire]'" x-model="ligne.beneficiaire" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        <option value="">Choisir...</option>
                                        <option value="Agent">Agent</option>
                                        <option value="Conjoint">Conjoint</option>
                                        <option value="Enfant">Enfant</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'lignes[' + index + '][nature_acte]'" x-model="ligne.nature_acte" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'lignes[' + index + '][cotation]'" x-model="ligne.cotation" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="K, B, P...">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="hidden" :name="'lignes[' + index + '][type_ligne]'" value="acte">
                                    <input type="hidden" :name="'lignes[' + index + '][quantite]'" value="1">
                                    <input type="number" step="0.01" :name="'lignes[' + index + '][prix_unitaire]'" x-model="ligne.prix_unitaire" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                </td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="removeLigneMedical(index)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-right">
                <span class="text-gray-600">Total:</span>
                <span class="text-xl font-bold text-gray-900" x-text="totalMedical + ' DH'"></span>
            </div>
        </div>

        <!-- Lignes de facturation - CLINIQUE -->
        <div x-show="type === 'clinique'" class="space-y-6" x-cloak>
            <!-- Prestations Clinique -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Prestations Clinique</h3>
                    <button type="button" @click="addLignePrestation()" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                        + Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ligne, index) in lignesPrestations" :key="index">
                                <tr class="border-b">
                                    <td class="px-3 py-2">
                                        <input type="hidden" :name="'lignes[' + index + '][type_ligne]'" value="prestation_clinique">
                                        <input type="text" :name="'lignes[' + index + '][designation]'" x-model="ligne.designation" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Séjour, Bloc, Pharmacie...">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="'lignes[' + index + '][cotation]'" x-model="ligne.cotation" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + index + '][quantite]'" x-model="ligne.quantite" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + index + '][prix_unitaire]'" x-model="ligne.prix_unitaire" class="w-28 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button" @click="removeLignePrestation(index)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Honoraires -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Honoraires Médecins</h3>
                    <button type="button" @click="addLigneHonoraire()" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                        + Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ligne, index) in lignesHonoraires" :key="index">
                                <tr class="border-b">
                                    <td class="px-3 py-2">
                                        <input type="hidden" :name="'lignes[' + (index + 100) + '][type_ligne]'" value="honoraire">
                                        <input type="text" :name="'lignes[' + (index + 100) + '][designation]'" x-model="ligne.designation" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Chirurgien, Anesthésiste...">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="'lignes[' + (index + 100) + '][cotation]'" x-model="ligne.cotation" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + (index + 100) + '][quantite]'" x-model="ligne.quantite" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + (index + 100) + '][prix_unitaire]'" x-model="ligne.prix_unitaire" class="w-28 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button" @click="removeLigneHonoraire(index)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Autres Prestations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Autres Prestations (Labo, Radio...)</h3>
                    <button type="button" @click="addLigneAutre()" class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        + Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ligne, index) in lignesAutres" :key="index">
                                <tr class="border-b">
                                    <td class="px-3 py-2">
                                        <input type="hidden" :name="'lignes[' + (index + 200) + '][type_ligne]'" value="autre">
                                        <input type="text" :name="'lignes[' + (index + 200) + '][designation]'" x-model="ligne.designation" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Laboratoire, Anapath...">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="'lignes[' + (index + 200) + '][cotation]'" x-model="ligne.cotation" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + (index + 200) + '][quantite]'" x-model="ligne.quantite" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" :name="'lignes[' + (index + 200) + '][prix_unitaire]'" x-model="ligne.prix_unitaire" class="w-28 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button" @click="removeLigneAutre(index)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Clinique -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2">
                            <span>Total Clinique:</span>
                            <span x-text="totalClinic + ' DH'"></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span>Total Honoraires:</span>
                            <span x-text="totalHonoraires + ' DH'"></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span>Total Autres:</span>
                            <span x-text="totalAutres + ' DH'"></span>
                        </div>
                        <div class="flex justify-between py-2 border-t-2 font-bold text-lg">
                            <span>TOTAL GÉNÉRAL:</span>
                            <span x-text="totalGeneral + ' DH'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parts de prise en charge (Clinique) -->
        <div x-show="type === 'clinique'" class="bg-white rounded-lg shadow p-6 mb-6" x-cloak>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Parts de prise en charge</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Part Adhérent (DH)
                        <span class="text-xs text-gray-500">(calculé automatiquement)</span>
                    </label>
                    <input type="text" :value="partAdherentCalcule" readonly
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium"
                           placeholder="Calculé automatiquement">
                    <input type="hidden" name="part_adherent" :value="partAdherentCalcule">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part CNOPS (DH)</label>
                    <input type="number" step="0.01" name="part_cnops" x-model="partCnops" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Part Assurance Complémentaire (DH)</label>
                    <input type="number" step="0.01" name="part_assurance" x-model="partAssurance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                </div>
            </div>
            <div class="mt-3 text-sm text-gray-600 bg-blue-50 p-2 rounded">
                <strong>Calcul :</strong>
                Total Général (<span x-text="totalGeneral"></span> DH)
                - CNOPS (<span x-text="partCnops || '0'"></span> DH)
                - Assurance (<span x-text="partAssurance || '0'"></span> DH)
                = <strong x-text="partAdherentCalcule"></strong> DH
            </div>
        </div>

        <!-- Conditions et observations -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conditions de règlement</label>
                    <textarea name="conditions_reglement" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">Paiement à réception de facture</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('dprh.facturation.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit" name="submit_facture" value="1"
                    :disabled="plafondDepasse"
                    :class="plafondDepasse ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                    class="px-6 py-3 text-white rounded-lg">
                Créer la facture
            </button>
        </div>
    </form>
</div>

<script>
function facturationForm() {
    return {
        type: 'medical',
        // État de soumission
        isSubmitting: false,
        // Gestion des erreurs AJAX
        ajaxError: false,
        ajaxErrors: [],
        // Stocker les requêtes AJAX en cours pour pouvoir les annuler
        pendingRequests: [],
        // Recherche PEC dans les actes médicaux
        showRecherchePec: false,
        pecMedicalSearch: '',
        pecMedicalResults: [],
        // Recherche PEC pour clinique
        pecSearch: '',
        pecResults: [],
        selectedPec: null,
        // Plafond
        plafondInfo: null,
        plafondDepasse: false,
        agentId: null,
        partenaireId: null,  // AJOUT: Initialisation par défaut

        // Données partenaires (tous)
        tousPartenaires: @json($partenairesJs),

        // Lignes Medical
        lignesMedical: [{ matricule: '', nom_patient: '', beneficiaire: '', nature_acte: '', cotation: '', prix_unitaire: '' }],

        // Lignes Clinique
        lignesPrestations: [],
        lignesHonoraires: [],
        lignesAutres: [],

        // Parts de prise en charge (initialisées à 0)
        partCnops: 0,
        partAssurance: 0,

        init() {
            // Watcher pour vérifier le plafond lors du changement des montants
            this.$watch('partCnops', () => this.checkPlafondDepasse());
            this.$watch('partAssurance', () => this.checkPlafondDepasse());
            this.$watch('totalMedical', () => this.checkPlafondDepasse());
            this.$watch('totalGeneral', () => this.checkPlafondDepasse());
            // Réinitialiser les erreurs lors du changement de type
            this.$watch('type', () => {
                this.ajaxError = false;
                this.ajaxErrors = [];
            });

            @if(isset($demandePec) && $demandePec)
                this.type = {{ $demandePec->type_prestation === 'chirurgie' ? "'clinique'" : "'medical'" }};
                this.partenaireId = {{ $demandePec->partenaire_id }};
                this.agentId = {{ $demandePec->agent_id }};
                // Charger les infos de plafond
                this.loadPlafondInfo({{ $demandePec->agent_id }});

                // Déterminer le bénéficiaire depuis la PEC
                let beneficiairePec = 'Agent';
                let nomPatientPec = {{ json_encode($demandePec->agent->nom . ' ' . $demandePec->agent->prenom) }};
                const matriculePec = {{ json_encode($demandePec->agent->matricule) }};

                @if($demandePec->beneficiaire_type === 'ayant_droit' && $demandePec->ayantDroit)
                    beneficiairePec = {{ $demandePec->ayantDroit->type === 'conjoint' ? "'Conjoint'" : "'Enfant'" }};
                    nomPatientPec = {{ json_encode($demandePec->ayantDroit->nom_prenom) }};
                @endif

                // Pour type medical, pré-ajouter la ligne depuis la PEC
                @if($demandePec->type_prestation !== 'chirurgie')
                this.lignesMedical = [{
                    pec_id: {{ $demandePec->id }},
                    matricule: matriculePec,
                    nom_patient: nomPatientPec,
                    beneficiaire: beneficiairePec,
                    nature_acte: {{ json_encode($demandePec->nature_examens ?: 'Acte médical') }},
                    cotation: '',
                    prix_unitaire: {{ $demandePec->montant_devis }}
                }];
                @else
                this.addLignePrestation();
                this.addLigneHonoraire();
                this.addLigneAutre();
                @endif
            @else
                this.addLignePrestation();
                this.addLigneHonoraire();
                this.addLigneAutre();
            @endif
        },

        searchPec() {
            if (this.pecSearch.length < 2) {
                this.pecResults = [];
                return;
            }

            // Clinique: uniquement les PEC de type clinique
            fetch('{{ route('dprh.facturation.api.pec-search') }}?q=' + encodeURIComponent(this.pecSearch) + '&type_structure=clinique')
                .then(r => r.json())
                .then(data => {
                    this.pecResults = data;
                });
        },

        selectPec(pec) {
            this.selectedPec = pec;
            this.partenaireId = pec.partenaire_id;
            this.pecSearch = pec.numero;
            this.pecResults = [];
            this.agentId = pec.agent_id || null;

            // Charger les infos de plafond de l'agent
            this.loadPlafondInfo(pec.agent_id);

            // Mettre à jour le nom du patient avec le bénéficiaire correct
            if (pec.beneficiaire_nom) {
                const patientInput = document.querySelector('input[name="nom_patient"]');
                if (patientInput) {
                    patientInput.value = pec.beneficiaire_nom;
                }
            }

            // Pré-remplir les lignes clinique avec les données de la PEC
            if (this.type === 'clinique' && pec.lignes) {
                // Vider les lignes existantes
                this.lignesPrestations = [];
                this.lignesHonoraires = [];
                this.lignesAutres = [];

                // Ajouter les lignes depuis la PEC si disponibles
                pec.lignes.forEach(ligne => {
                    if (ligne.type_ligne === 'prestation_clinique') {
                        this.lignesPrestations.push({
                            designation: ligne.designation || 'SEJOUR',
                            cotation: ligne.cotation || '',
                            quantite: ligne.quantite || 1,
                            prix_unitaire: ligne.prix_unitaire || ''
                        });
                    } else if (ligne.type_ligne === 'honoraire') {
                        this.lignesHonoraires.push({
                            designation: ligne.designation || 'MEDECIN',
                            cotation: ligne.cotation || 'K',
                            quantite: ligne.quantite || 1,
                            prix_unitaire: ligne.prix_unitaire || ''
                        });
                    } else if (ligne.type_ligne === 'autre') {
                        this.lignesAutres.push({
                            designation: ligne.designation || '',
                            cotation: ligne.cotation || '',
                            quantite: ligne.quantite || 1,
                            prix_unitaire: ligne.prix_unitaire || ''
                        });
                    }
                });

                // Si aucune ligne, ajouter des lignes vides par défaut
                if (this.lignesPrestations.length === 0) this.addLignePrestation();
                if (this.lignesHonoraires.length === 0) this.addLigneHonoraire();
                if (this.lignesAutres.length === 0) this.addLigneAutre();
            }
        },

        // Rechercher des PEC pour ajouter aux actes médicaux
        searchPecMedical() {
            if (this.pecMedicalSearch.length < 2) {
                this.pecMedicalResults = [];
                return;
            }

            // Medical: exclure clinique (afficher médecin, laboratoire, radiologie)
            fetch('{{ route('dprh.facturation.api.pec-search') }}?q=' + encodeURIComponent(this.pecMedicalSearch) + '&exclure_type=clinique')
                .then(r => r.json())
                .then(data => {
                    this.pecMedicalResults = data;
                });
        },

        // Ajouter des lignes depuis une PEC sélectionnée
        ajouterLignesDepuisPec(pec) {
            // Vérifier si cette PEC est déjà ajoutée
            const dejaAjoute = this.lignesMedical.some(l => l.pec_id === pec.id);
            if (dejaAjoute) {
                alert('Cette PEC est déjà ajoutée aux actes.');
                return;
            }

            // Utiliser le bénéficiaire depuis la PEC (Agent, Conjoint, ou Enfant)
            const beneficiaire = pec.beneficiaire || 'Agent';

            // Ajouter une nouvelle ligne avec les données de la PEC
            this.lignesMedical.push({
                pec_id: pec.id,
                matricule: pec.matricule || '',
                nom_patient: pec.beneficiaire_nom || (pec.agent_nom + ' ' + pec.agent_prenom),
                beneficiaire: beneficiaire,
                nature_acte: pec.nature_examens || 'Acte médical',
                cotation: '',
                prix_unitaire: pec.montant || 0
            });

            // Fermer la recherche et réinitialiser
            this.showRecherchePec = false;
            this.pecMedicalSearch = '';
            this.pecMedicalResults = [];
        },

        // Watcher pour réinitialiser le partenaire et la PEC si le type change
        typeChanged() {
            // Réinitialiser la PEC car le type a changé
            this.selectedPec = null;
            this.pecSearch = '';
            this.pecResults = [];
            this.showRecherchePec = false;
            this.pecMedicalSearch = '';
            this.pecMedicalResults = [];

            // Vérifier si le partenaire actuel correspond au nouveau type
            if (this.partenaireId) {
                const partenaireActuel = this.tousPartenaires.find(p => p.id === this.partenaireId);
                if (partenaireActuel) {
                    const typesMedical = ['médecin', 'laboratoire', 'radiologie'];
                    const typesClinique = ['clinique'];
                    const typesAutorises = this.type === 'medical' ? typesMedical : typesClinique;

                    if (!typesAutorises.includes(partenaireActuel.type_structure)) {
                        // Le partenaire ne correspond pas au type, on réinitialise
                        this.partenaireId = '';
                    }
                }
            }
        },

        addLigneMedical() {
            this.lignesMedical.push({ matricule: '', nom_patient: '', beneficiaire: '', nature_acte: '', cotation: '', prix_unitaire: '' });
        },

        removeLigneMedical(index) {
            if (this.lignesMedical.length > 1) {
                this.lignesMedical.splice(index, 1);
            }
        },

        addLignePrestation() {
            this.lignesPrestations.push({ designation: 'SEJOUR', cotation: '', quantite: 1, prix_unitaire: '' });
        },

        removeLignePrestation(index) {
            this.lignesPrestations.splice(index, 1);
        },

        addLigneHonoraire() {
            this.lignesHonoraires.push({ designation: 'MEDECIN chirurgien', cotation: 'K', quantite: 1, prix_unitaire: '' });
        },

        removeLigneHonoraire(index) {
            this.lignesHonoraires.splice(index, 1);
        },

        addLigneAutre() {
            this.lignesAutres.push({ designation: '', cotation: '', quantite: 1, prix_unitaire: '' });
        },

        removeLigneAutre(index) {
            this.lignesAutres.splice(index, 1);
        },

        // Récupère toutes les lignes à soumettre avec des indices séquentiels
        get submitData() {
            let data = {
                lignes: []
            };

            if (this.type === 'medical') {
                this.lignesMedical.forEach((ligne, idx) => {
                    // Ignorer les lignes vides (sans prix et sans nature_acte)
                    if (!ligne.prix_unitaire && !ligne.nature_acte) return;

                    data.lignes.push({
                        index: data.lignes.length,
                        type_ligne: 'acte',
                        matricule: ligne.matricule || '',
                        nom_patient: ligne.nom_patient || '',
                        beneficiaire: ligne.beneficiaire || '',
                        nature_acte: ligne.nature_acte || '',
                        cotation: ligne.cotation || '',
                        quantite: 1,
                        prix_unitaire: ligne.prix_unitaire || 0
                    });
                });
            } else {
                let ligneIdx = 0;
                this.lignesPrestations.forEach(ligne => {
                    if (!ligne.prix_unitaire) return;
                    data.lignes.push({
                        index: ligneIdx++,
                        type_ligne: 'prestation_clinique',
                        designation: ligne.designation || '',
                        cotation: ligne.cotation || '',
                        quantite: ligne.quantite || 1,
                        prix_unitaire: ligne.prix_unitaire || 0
                    });
                });
                this.lignesHonoraires.forEach(ligne => {
                    if (!ligne.prix_unitaire) return;
                    data.lignes.push({
                        index: ligneIdx++,
                        type_ligne: 'honoraire',
                        designation: ligne.designation || '',
                        cotation: ligne.cotation || '',
                        quantite: ligne.quantite || 1,
                        prix_unitaire: ligne.prix_unitaire || 0
                    });
                });
                this.lignesAutres.forEach(ligne => {
                    if (!ligne.prix_unitaire) return;
                    data.lignes.push({
                        index: ligneIdx++,
                        type_ligne: 'autre',
                        designation: ligne.designation || '',
                        cotation: ligne.cotation || '',
                        quantite: ligne.quantite || 1,
                        prix_unitaire: ligne.prix_unitaire || 0
                    });
                });
            }

            return data;
        },

        submitForm() {
            // Empêcher les soumissions multiples
            if (this.isSubmitting) {
                return;
            }

            console.log('submitForm appelé, type:', this.type);

            // Vérifier le plafond avant soumission
            if (this.plafondDepasse) {
                alert('Le plafond annuel de l\'agent est insuffisant pour cette facturation.\n\n' +
                      'Reste disponible: ' + this.formatMontant(this.plafondInfo?.reste) + ' DH\n' +
                      'Part adhérent: ' + this.formatMontant(this.partAdherentCalcule) + ' DH\n' +
                      'Dépassement: ' + this.formatMontant(parseFloat(this.partAdherentCalcule) - (this.plafondInfo?.reste || 0)) + ' DH');
                return;
            }

            // Valider qu'il y a au moins une ligne avec un prix
            let hasValidLine = false;

            if (this.type === 'medical') {
                hasValidLine = this.lignesMedical.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0);
            } else {
                hasValidLine = (this.lignesPrestations.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                               this.lignesHonoraires.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                               this.lignesAutres.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0));
            }

            if (!hasValidLine) {
                alert('Veuillez ajouter au moins une ligne de facturation avec un prix.');
                return;
            }

            console.log('Validation OK, soumission en cours...');
            this.isSubmitting = true;

            // Soumettre directement sans vérification de session
            return this.submitFormData();
        },

        submitFormData() {
            // Soumettre le formulaire via fetch pour éviter la perte de session
            const form = document.querySelector('form');
            const formData = new FormData(form);

            // Ajouter les lignes depuis Alpine.js
            let ligneIndex = 0;
            if (this.type === 'medical') {
                this.lignesMedical.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        formData.append(`lignes[${ligneIndex}][type_ligne]`, 'acte');
                        formData.append(`lignes[${ligneIndex}][matricule]`, ligne.matricule || '');
                        formData.append(`lignes[${ligneIndex}][nom_patient]`, ligne.nom_patient || '');
                        formData.append(`lignes[${ligneIndex}][beneficiaire]`, ligne.beneficiaire || '');
                        formData.append(`lignes[${ligneIndex}][nature_acte]`, ligne.nature_acte || '');
                        formData.append(`lignes[${ligneIndex}][cotation]`, ligne.cotation || '');
                        formData.append(`lignes[${ligneIndex}][quantite]`, '1');
                        formData.append(`lignes[${ligneIndex}][prix_unitaire]`, ligne.prix_unitaire || '0');
                        ligneIndex++;
                    }
                });
            } else {
                // Clinique - Prestations
                this.lignesPrestations.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        formData.append(`lignes[${ligneIndex}][type_ligne]`, 'prestation_clinique');
                        formData.append(`lignes[${ligneIndex}][designation]`, ligne.designation || '');
                        formData.append(`lignes[${ligneIndex}][cotation]`, ligne.cotation || '');
                        formData.append(`lignes[${ligneIndex}][quantite]`, ligne.quantite || '1');
                        formData.append(`lignes[${ligneIndex}][prix_unitaire]`, ligne.prix_unitaire || '0');
                        ligneIndex++;
                    }
                });
                // Honoraires
                this.lignesHonoraires.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        formData.append(`lignes[${ligneIndex}][type_ligne]`, 'honoraire');
                        formData.append(`lignes[${ligneIndex}][designation]`, ligne.designation || '');
                        formData.append(`lignes[${ligneIndex}][cotation]`, ligne.cotation || '');
                        formData.append(`lignes[${ligneIndex}][quantite]`, ligne.quantite || '1');
                        formData.append(`lignes[${ligneIndex}][prix_unitaire]`, ligne.prix_unitaire || '0');
                        ligneIndex++;
                    }
                });
                // Autres
                this.lignesAutres.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        formData.append(`lignes[${ligneIndex}][type_ligne]`, 'autre');
                        formData.append(`lignes[${ligneIndex}][designation]`, ligne.designation || '');
                        formData.append(`lignes[${ligneIndex}][cotation]`, ligne.cotation || '');
                        formData.append(`lignes[${ligneIndex}][quantite]`, ligne.quantite || '1');
                        formData.append(`lignes[${ligneIndex}][prix_unitaire]`, ligne.prix_unitaire || '0');
                        ligneIndex++;
                    }
                });
            }

            console.log('Nombre de lignes ajoutées:', ligneIndex);

            // Récupérer le token CSRF ACTUEL depuis le champ caché du formulaire
            const csrfInput = form.querySelector('input[name="_token"]');
            const currentCsrfToken = csrfInput ? csrfInput.value : '';

            console.log('Soumission du formulaire, token CSRF:', currentCsrfToken ? 'présent' : 'manquant');

            // Ajouter l'en-tête pour indiquer qu'on attend du JSON
            return fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': currentCsrfToken  // Utiliser le token actuel du formulaire
                },
                // Garder les cookies de session
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Réponse reçue, status:', response.status, 'redirected:', response.redirected);

                if (response.status === 419) {
                    // CSRF token expired - page expirée
                    this.isSubmitting = false;
                    alert('Votre session a expiré. Veuillez rafraîchir la page et réessayer.');
                    window.location.reload();
                    throw new Error('CSRF expired');
                }

                if (response.status === 401) {
                    // Non authentifié
                    this.isSubmitting = false;
                    window.location.href = '/login';
                    throw new Error('Unauthorized');
                }

                if (response.redirected) {
                    // Le serveur a redirigé (probablement vers login = session perdue)
                    this.isSubmitting = false;
                    window.location.href = response.url;
                    throw new Error('Redirected');
                }
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(result => {
                this.isSubmitting = false;
                if (!result) return; // Déjà redirigé

                if (result.status === 422 || !result.data.success) {
                    // Erreur de validation ou autre erreur
                    if (result.data.errors) {
                        this.ajaxErrors = Object.values(result.data.errors).flat();
                        this.ajaxError = true;
                        // Scroll vers le haut pour voir l'erreur
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (result.data.message) {
                        this.ajaxErrors = [result.data.message];
                        this.ajaxError = true;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (result.data.error) {
                        this.ajaxErrors = ['Erreur: ' + result.data.error];
                        this.ajaxError = true;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } else if (result.data.success && result.data.redirect) {
                    // Succès - rediriger vers la page de la facture
                    window.location.href = result.data.redirect;
                }
            })
            .catch(error => {
                // Ignore les erreurs de redirection déjà gérées
                if (error.message === 'CSRF expired' || error.message === 'Unauthorized' || error.message === 'Redirected') {
                    return;
                }
                this.isSubmitting = false;
                console.error('Erreur lors de la soumission:', error);
                this.ajaxErrors = ['Une erreur est survenue lors de la création de la facture. Veuillez réessayer.'];
                this.ajaxError = true;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        },

        get totalMedical() {
            return this.lignesMedical.reduce((sum, l) => sum + (parseFloat(l.prix_unitaire) || 0), 0).toFixed(2);
        },

        get totalClinic() {
            return this.lignesPrestations.reduce((sum, l) => sum + (parseFloat(l.prix_unitaire) * parseFloat(l.quantite) || 0), 0).toFixed(2);
        },

        get totalHonoraires() {
            return this.lignesHonoraires.reduce((sum, l) => sum + (parseFloat(l.prix_unitaire) * parseFloat(l.quantite) || 0), 0).toFixed(2);
        },

        get totalAutres() {
            return this.lignesAutres.reduce((sum, l) => sum + (parseFloat(l.prix_unitaire) * parseFloat(l.quantite) || 0), 0).toFixed(2);
        },

        get totalGeneral() {
            return (parseFloat(this.totalClinic) + parseFloat(this.totalHonoraires) + parseFloat(this.totalAutres)).toFixed(2);
        },

        get partenairesFiltres() {
            if (!this.type) return this.tousPartenaires;

            if (this.type === 'clinique') {
                // Pour clinique : uniquement les partenaires de type 'clinique'
                return this.tousPartenaires.filter(p => p.type_structure === 'clinique');
            } else {
                // Pour medical : tous les partenaires SAUF 'clinique'
                return this.tousPartenaires.filter(p => p.type_structure !== 'clinique');
            }
        },

        get partAdherentCalcule() {
            const total = parseFloat(this.totalGeneral) || 0;
            const cnops = parseFloat(this.partCnops) || 0;
            const assurance = parseFloat(this.partAssurance) || 0;
            const resultat = total - cnops - assurance;
            return resultat > 0 ? resultat.toFixed(2) : '0.00';
        },

        // Charger les informations de plafond d'un agent
        loadPlafondInfo(agentId) {
            if (!agentId) return;

            fetch('{{ route('dprh.demandes.api.plafond-agent') }}?agent_id=' + agentId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                this.plafondInfo = data;
                this.checkPlafondDepasse();
            })
            .catch(error => {
                console.error('Erreur chargement plafond:', error);
            });
        },

        // Formater un montant
        formatMontant(value) {
            if (!value) return '0,00';
            return new Intl.NumberFormat('fr-MA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        },

        // Vérifier si le plafond est dépassé
        checkPlafondDepasse() {
            if (!this.plafondInfo) {
                this.plafondDepasse = false;
                return;
            }

            let montantAConsommer = 0;
            if (this.type === 'medical') {
                montantAConsommer = parseFloat(this.totalMedical) || 0;
            } else {
                montantAConsommer = parseFloat(this.partAdherentCalcule) || 0;
            }

            const resteDisponible = this.plafondInfo.reste || 0;
            this.plafondDepasse = montantAConsommer > resteDisponible;
        },

        // Soumettre la facture
        soumettreFacture() {
            // Vérifier le plafond
            if (this.plafondDepasse) {
                alert('Plafond insuffisant !');
                return;
            }

            // Vérifier qu'il y a des lignes
            let hasValidLine = false;
            if (this.type === 'medical') {
                hasValidLine = this.lignesMedical.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0);
            } else {
                hasValidLine = (this.lignesPrestations.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                               this.lignesHonoraires.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                               this.lignesAutres.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0));
            }

            if (!hasValidLine) {
                alert('Veuillez ajouter au moins une ligne avec un prix.');
                return;
            }

            // Ajouter les lignes au formulaire HTML et soumettre
            const form = document.querySelector('form');

            // Supprimer les anciens champs lignes s'ils existent
            form.querySelectorAll('input[name^="lignes"]').forEach(el => el.remove());

            // Ajouter les nouvelles lignes
            let ligneIndex = 0;
            if (this.type === 'medical') {
                this.lignesMedical.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        this.addLigneField(form, ligneIndex, 'acte', ligne);
                        ligneIndex++;
                    }
                });
            } else {
                this.lignesPrestations.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        this.addLigneField(form, ligneIndex, 'prestation_clinique', ligne);
                        ligneIndex++;
                    }
                });
                this.lignesHonoraires.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        this.addLigneField(form, ligneIndex, 'honoraire', ligne);
                        ligneIndex++;
                    }
                });
                this.lignesAutres.forEach(ligne => {
                    if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                        this.addLigneField(form, ligneIndex, 'autre', ligne);
                        ligneIndex++;
                    }
                });
            }

            // Soumettre le formulaire normalement
            form.submit();
        },

        // Ajouter un champ ligne au formulaire
        addLigneField(form, index, typeLigne, ligne) {
            const createField = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `lignes[${index}][${name}]`;
                input.value = value || '';
                form.appendChild(input);
            };

            createField('type_ligne', typeLigne);

            if (typeLigne === 'acte') {
                createField('matricule', ligne.matricule);
                createField('nom_patient', ligne.nom_patient);
                createField('beneficiaire', ligne.beneficiaire);
                createField('nature_acte', ligne.nature_acte);
                createField('cotation', ligne.cotation);
                createField('quantite', '1');
            } else {
                createField('designation', ligne.designation);
                createField('cotation', ligne.cotation);
                createField('quantite', ligne.quantite);
            }
            createField('prix_unitaire', ligne.prix_unitaire);
        }
    };
}
</script>

<script>
// Fonction globale pour soumettre la facture
function soumettreFacture() {
    // Récupérer l'instance Alpine
    const alpineData = document.querySelector('[x-data]')._x_dataStack[0];

    if (!alpineData) {
        alert('Erreur: Formulaire non initialisé');
        return;
    }

    // Vérifier le plafond
    if (alpineData.plafondDepasse) {
        alert('Plafond insuffisant !');
        return;
    }

    // Vérifier qu'il y a des lignes
    let hasValidLine = false;
    if (alpineData.type === 'medical') {
        hasValidLine = alpineData.lignesMedical.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0);
    } else {
        hasValidLine = (alpineData.lignesPrestations.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                       alpineData.lignesHonoraires.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0) ||
                       alpineData.lignesAutres.some(l => l.prix_unitaire && parseFloat(l.prix_unitaire) > 0));
    }

    if (!hasValidLine) {
        alert('Veuillez ajouter au moins une ligne avec un prix.');
        return;
    }

    // Ajouter les lignes au formulaire HTML et soumettre
    const form = document.querySelector('form');

    // Supprimer les anciens champs lignes s'ils existent
    form.querySelectorAll('input[name^="lignes"]').forEach(el => el.remove());

    // Ajouter les nouvelles lignes
    let ligneIndex = 0;
    if (alpineData.type === 'medical') {
        alpineData.lignesMedical.forEach(ligne => {
            if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                addLigneField(form, ligneIndex, 'acte', ligne);
                ligneIndex++;
            }
        });
    } else {
        alpineData.lignesPrestations.forEach(ligne => {
            if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                addLigneField(form, ligneIndex, 'prestation_clinique', ligne);
                ligneIndex++;
            }
        });
        alpineData.lignesHonoraires.forEach(ligne => {
            if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                addLigneField(form, ligneIndex, 'honoraire', ligne);
                ligneIndex++;
            }
        });
        alpineData.lignesAutres.forEach(ligne => {
            if (ligne.prix_unitaire && parseFloat(ligne.prix_unitaire) > 0) {
                addLigneField(form, ligneIndex, 'autre', ligne);
                ligneIndex++;
            }
        });
    }

    console.log('Soumission du formulaire avec', ligneIndex, 'lignes');

    // Soumettre le formulaire normalement
    form.submit();
}

// Ajouter un champ ligne au formulaire
function addLigneField(form, index, typeLigne, ligne) {
    const createField = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `lignes[${index}][${name}]`;
        input.value = value || '';
        form.appendChild(input);
    };

    createField('type_ligne', typeLigne);

    if (typeLigne === 'acte') {
        createField('matricule', ligne.matricule);
        createField('nom_patient', ligne.nom_patient);
        createField('beneficiaire', ligne.beneficiaire);
        createField('nature_acte', ligne.nature_acte);
        createField('cotation', ligne.cotation);
        createField('quantite', '1');
    } else {
        createField('designation', ligne.designation);
        createField('cotation', ligne.cotation);
        createField('quantite', ligne.quantite);
    }
    createField('prix_unitaire', ligne.prix_unitaire);
}
</script>
@endsection

@push('scripts')
<script>
// Récupérer le token CSRF depuis le meta tag
const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

// Configuration globale pour fetch avec CSRF
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const [url, options = {}] = args;

    // Ajouter le token CSRF à toutes les requêtes
    if (options.headers === undefined) {
        options.headers = {};
    }

    // Convertir Headers object to plain object if needed
    if (options.headers instanceof Headers) {
        const headersObj = {};
        options.headers.forEach((value, key) => {
            headersObj[key] = value;
        });
        options.headers = headersObj;
    }

    // Ajouter X-CSRF-TOKEN pour les requêtes non-GET - utiliser le token À JOUR
    if (options.method && options.method !== 'GET' && options.method !== 'HEAD') {
        options.headers['X-CSRF-TOKEN'] = window.csrfToken || csrfToken;
    }

    return originalFetch(url, options);
};
</script>
@endpush
