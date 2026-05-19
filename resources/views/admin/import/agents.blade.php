@extends('admin.layouts.app')

@section('title', 'Import Agents')

@section('header', 'Import des Agents')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Section 1: Import CGS -->
       <!--
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <h4 class="font-semibold text-green-900 mb-2">📊 Import CGS (Base de données principale)</h4>

    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
     <h3 class="text-lg font-semibold text-gray-900 mb-4">Importer les données CGS</h3>

        <form method="POST" action="{{ route('admin.import.cgs.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>

                <input type="file" name="file" accept=".xlsx,.xls" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">

            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    📥 Importer CGS
                </button>
            </div>
        </form>

        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h5 class="font-medium text-gray-900 mb-2">Colonnes du fichier CGS.xlsx :</h5>
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <p class="font-medium text-gray-700 mb-1">Informations agent :</p>
                    <ul class="space-y-1">
                        <li>• Mat. → Matricule</li>
                        <li>• Nom de l'Agent, Prénom de l'Agent</li>
                        <li>• Date naissance → date_naissance</li>
                        <li>• Date recrutement → date_recrutement</li>
                        <li>• Date Affiliation Agent → date_affiliation</li>
                        <li>• CIN</li>
                        <li>• Etat → Statut (Actif/Retraité/Sorti)</li>
                    </ul>
                </div>
                <div>
                    <p class="font-medium text-gray-700 mb-1">Coordonnées bancaires :</p>
                    <ul class="space-y-1">
                        <li>• BANQUE → banque</li>
                        <li>• Compte Bancaire → compte_bancaire</li>
                        <li>• Clé bancaire → cle_bancaire</li>
                        <li>• Info banque → info_banque</li>
                    </ul>
                    <p class="font-medium text-gray-700 mb-1 mt-3">Ayants droit :</p>
                    <ul class="space-y-1">
                        <li>• Nom bénéficiaire, Qualité</li>
                        <li>• Date naissance Bénéficiaire</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
-->




    <div class="bg-white rounded-lg shadow p-6">
            <!-- Section 2: Import SAP -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <h4 class="font-semibold text-yellow-900 mb-2">🔍 Import SAP (Données de comparaison)</h4>
    </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Importer les données SAP (Agents + Ayants droit)</h3>

        <form method="POST" action="{{ route('admin.import.agents-ayants-droits.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier Excel (xlsx, xls, csv) *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Taille maximale: 10MB</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    📥 Importer SAP
                </button>
                <a href="{{ route('admin.import.modele-agents-combine') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    📥 Modèle
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-3 gap-6 mt-8">
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

    <!-- Info Carrière
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-8">
        <h4 class="font-semibold text-blue-900 mb-2">ℹ️ Informations sur les données carrière</h4>
        <p class="text-blue-800 text-sm">
            Les informations de carrière (catégorie, niveau, degré) sont gérées dans la table <strong>carrieres</strong>
            et sont accessibles via la relation <code>carrieres()</code> de chaque agent.
        </p>
    </div> -->
</div>
@endsection
