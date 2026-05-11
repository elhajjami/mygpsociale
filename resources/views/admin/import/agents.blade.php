@extends('admin.layouts.app')

@section('title', 'Import Agents SAP')

@section('header', 'Import des Agents SAP')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Formulaire d'import -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Importer les agents depuis SAP</h3>

        <form method="POST" action="{{ route('admin.import.agents.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier Excel (xlsx, xls, csv) *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Taille maximale: 10MB</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Importer le fichier
                </button>
                <a href="{{ route('admin.import.modele-agents') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Télécharger le modèle
                </a>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="font-semibold text-blue-900 mb-3">Instructions</h4>
        <ul class="list-disc list-inside space-y-2 text-blue-800">
            <li>Téléchargez d'abord le modèle de fichier</li>
            <li>Remplissez le fichier avec les données des agents SAP</li>
            <li>Les colonnes requises : Matricule, Nom, Prénom, Date Naissance, Sexe, Catégorie, Statut, Date Embauche</li>
            <li>Importez le fichier rempli</li>
        </ul>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Agent::count() }}</p>
            <p class="text-gray-600">Total Agents</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">{{ \App\Models\Agent::where('statut', 'Actif')->count() }}</p>
            <p class="text-gray-600">Agents Actifs</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-gray-600">{{ \App\Models\Agent::where('statut', 'Retraité')->count() }}</p>
            <p class="text-gray-600">Retraités</p>
        </div>
    </div>
</div>
@endsection
