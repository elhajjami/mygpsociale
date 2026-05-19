{{-- resources/views/invoice/form.blade.php --}}
@extends('layouts.app')

@section('title', 'Génération de Facture Médicale')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4" x-data="invoiceForm()">

    <div class="max-w-3xl mx-auto">

        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Génération de Facture Médicale</h1>
            <p class="text-gray-500 mt-1 text-sm">Renseignez les informations ci-dessous. L'IA structurera et générera votre PDF.</p>
        </div>

        {{-- Erreurs de validation --}}
        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-700 font-medium text-sm mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('invoice.generate') }}" @submit="loading = true">
            @csrf

            {{-- Étape 1 : Type de formulaire --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h2 class="text-base font-medium text-gray-700 mb-4">Type de prestataire</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach([
                        ['value' => 'medecin',     'label' => 'Médecin',      'icon' => '🩺'],
                        ['value' => 'laboratoire', 'label' => 'Laboratoire',  'icon' => '🔬'],
                        ['value' => 'radiologie',  'label' => 'Radiologie',   'icon' => '📡'],
                        ['value' => 'clinique',    'label' => 'Clinique',     'icon' => '🏥'],
                    ] as $t)
                    <label
                        class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 cursor-pointer transition-all"
                        :class="type === '{{ $t['value'] }}'
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                    >
                        <input type="radio" name="type" value="{{ $t['value'] }}"
                               x-model="type" class="sr-only"
                               {{ old('type') === $t['value'] ? 'checked' : '' }}>
                        <span class="text-2xl">{{ $t['icon'] }}</span>
                        <span class="text-sm font-medium">{{ $t['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @error('type')
                    <p class="mt-2 text-red-600 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- Étape 2 : Informations du prestataire --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h2 class="text-base font-medium text-gray-700 mb-4">
                    Informations du prestataire
                    <span x-show="type === 'clinique'" class="text-gray-400 font-normal text-sm">(Clinique)</span>
                    <span x-show="type && type !== 'clinique'" class="text-gray-400 font-normal text-sm">(Formation Médicale)</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Nom --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">
                            <span x-show="type === 'clinique'">Nom de la clinique</span>
                            <span x-show="type !== 'clinique'">Nom / Raison sociale</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom_formation"
                               value="{{ old('nom_formation') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 @error('nom_formation') border-red-400 @enderror"
                               placeholder="Ex: Cabinet Dr. Alami / Clinique Ibn Sina">
                        @error('nom_formation')
                            <p class="mt-1 text-red-600 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Adresse <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse"
                               value="{{ old('adresse') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 @error('adresse') border-red-400 @enderror"
                               placeholder="Rue, quartier...">
                        @error('adresse') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>

                    {{-- Ville / Tel --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Ville <span class="text-red-500">*</span></label>
                        <input type="text" name="ville" value="{{ old('ville') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('ville') border-red-400 @enderror"
                               placeholder="Casablanca">
                        @error('ville') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" name="tel" value="{{ old('tel') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('tel') border-red-400 @enderror"
                               placeholder="0522 XX XX XX">
                        @error('tel') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>

                    {{-- RIB / Agence --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">RIB (24 positions) <span class="text-red-500">*</span></label>
                        <input type="text" name="rib" value="{{ old('rib') }}" maxlength="24"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('rib') border-red-400 @enderror"
                               placeholder="000000000000000000000000">
                        @error('rib') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Agence bancaire <span class="text-red-500">*</span></label>
                        <input type="text" name="agence" value="{{ old('agence') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('agence') border-red-400 @enderror">
                        @error('agence') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>

                    {{-- Patente / IF / CNSS / ICE --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">N° Patente <span class="text-red-500">*</span></label>
                        <input type="text" name="patente" value="{{ old('patente') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('patente') border-red-400 @enderror">
                        @error('patente') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">N° Identification Fiscale <span class="text-red-500">*</span></label>
                        <input type="text" name="id_fiscale" value="{{ old('id_fiscale') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('id_fiscale') border-red-400 @enderror">
                        @error('id_fiscale') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">N° C.N.S.S <span class="text-red-500">*</span></label>
                        <input type="text" name="cnss" value="{{ old('cnss') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('cnss') border-red-400 @enderror">
                        @error('cnss') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">N° ICE <span class="text-red-500">*</span></label>
                        <input type="text" name="ice" value="{{ old('ice') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('ice') border-red-400 @enderror">
                        @error('ice') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date / Numéro facture --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date de facture <span class="text-red-500">*</span></label>
                        <input type="date" name="date_facture" value="{{ old('date_facture', date('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('date_facture') border-red-400 @enderror">
                        @error('date_facture') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">N° Facture <span class="text-red-500">*</span></label>
                        <input type="text" name="numero_facture" value="{{ old('numero_facture') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('numero_facture') border-red-400 @enderror"
                               placeholder="FAC-2025-001">
                        @error('numero_facture') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- Étape 3 : Champs spécifiques clinique --}}
            <div x-show="type === 'clinique'" x-transition
                 class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h2 class="text-base font-medium text-gray-700 mb-4">Informations patient</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm text-gray-600 mb-1">Nom & Prénom du patient <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_patient" value="{{ old('nom_patient') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('nom_patient') border-red-400 @enderror">
                        @error('nom_patient') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Du (début hospitalisation) <span class="text-red-500">*</span></label>
                        <input type="date" name="date_hospitalisation_debut"
                               value="{{ old('date_hospitalisation_debut') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('date_hospitalisation_debut') border-red-400 @enderror">
                        @error('date_hospitalisation_debut') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Au (fin hospitalisation) <span class="text-red-500">*</span></label>
                        <input type="date" name="date_hospitalisation_fin"
                               value="{{ old('date_hospitalisation_fin') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('date_hospitalisation_fin') border-red-400 @enderror">
                        @error('date_hospitalisation_fin') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 4 : Description libre des prestations --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h2 class="text-base font-medium text-gray-700 mb-1">Description des prestations</h2>
                <p class="text-gray-400 text-xs mb-3">
                    Décrivez librement les actes ou soins effectués. L'IA extraira et structurera automatiquement les lignes de facturation.
                </p>
                <textarea name="description" rows="5"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('description') border-red-400 @enderror"
                    placeholder="Ex: Consultation générale pour M. Karim El Idrissi (matricule 123456), examen clinique complet + ECG. 
Consultation spécialiste pour Mme Fatima Benali (matricule 789012), tarif C2.
Analyse biochimique complète pour M. Ahmed Moussaoui (B25)...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-red-600 text-xs">{{ $message }}</p> @enderror
                <p class="mt-2 text-gray-400 text-xs">
                    💡 Astuce : Mentionnez les noms des patients, matricules CNOPS/CNSS, et types d'actes pour un meilleur résultat.
                </p>
            </div>

            {{-- Bouton soumettre --}}
            <button type="submit"
                    :disabled="!type || loading"
                    class="w-full py-3 px-6 rounded-xl text-sm font-medium transition-all
                           bg-blue-600 text-white hover:bg-blue-700 active:scale-95
                           disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">

                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4z"></path>
                </svg>

                <span x-text="loading ? 'Génération en cours...' : 'Générer le PDF'"></span>
            </button>

            <p x-show="!type" class="mt-2 text-center text-gray-400 text-xs">
                Sélectionnez un type de prestataire pour continuer.
            </p>

        </form>
    </div>
</div>

@push('scripts')
<script>
function invoiceForm() {
    return {
        type: '{{ old('type', '') }}',
        loading: false,
    }
}
</script>
@endpush
@endsection
