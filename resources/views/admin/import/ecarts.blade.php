@extends('admin.layouts.app')

@section('title', 'Écarts SAP/CGS')

@section('header', 'Gestion des Écarts SAP/CGS')

@section('content')
<div class="space-y-6">
    <!-- Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
        <form method="POST" action="{{ route('admin.import.ecarts.detecter') }}" class="inline-block">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Détecter les écarts
            </button>
        </form>
    </div>

    <!-- Liste des écarts -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Écarts non traités</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détails</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $ecarts = \App\Models\EcartSapCgs::nonTraites()->limit(50)->get(); @endphp
                    @forelse($ecarts as $ecart)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $ecart->type_ecart }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $ecart->matricule }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ecart->details }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ecart->date_detection->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <form method="POST" action="{{ route('admin.import.ecarts.traiter', $ecart->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800">Marquer traité</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Aucun écart détecté
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
