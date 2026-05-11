@extends('admin.layouts.app')

@section('title', 'Import Ayants Droit')

@section('header', 'Import des Ayants Droit')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Formulaire d'import -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Importer les ayants droit depuis SAP</h3>

        <form method="POST" action="{{ route('admin.import.ayants-droit.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier Excel (xlsx, xls, csv) *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Importer le fichier
                </button>
                <a href="{{ route('admin.import.modele-ayants-droit') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Télécharger le modèle
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
