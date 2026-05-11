@extends('admin.layouts.app')

@section('title', 'Demandes PEC')

@section('header', 'Demandes de Prise en Charge')

@section('content')
<div class="space-y-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">En attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statistiques['en_attente'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Validées</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiques['validees'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Rejetées</p>
            <p class="text-2xl font-bold text-red-600">{{ $statistiques['rejetees'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Payées</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statistiques['payees'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filtres et Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('dprh.demandes.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tous statuts</option>
                    <option value="En attente" {{ request('statut') === 'En attente' ? 'selected' : '' }}>En attente</option>
                    <option value="Validée" {{ request('statut') === 'Validée' ? 'selected' : '' }}>Validée</option>
                    <option value="Rejetée" {{ request('statut') === 'Rejetée' ? 'selected' : '' }}>Rejetée</option>
                    <option value="Payée" {{ request('statut') === 'Payée' ? 'selected' : '' }}>Payée</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filtrer
            </button>

            <a href="{{ route('dprh.demandes.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                Réinitialiser
            </a>

            <div class="ml-auto">
                <a href="{{ route('dprh.demandes.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Nouvelle Demande
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des demandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partenaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date soin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($demandes as $demande)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600">
                                {{ $demande->numero_demande }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $demande->agent->nom_complet ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->partenaire->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->type_prestation ?? $demande->type_soin ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->date_soin?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ number_format($demande->montant_devis, 2) }} DH
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($demande->statut === 'Validée') bg-green-100 text-green-800
                                    @elseif($demande->statut === 'En attente') bg-yellow-100 text-yellow-800
                                    @elseif($demande->statut === 'Rejetée') bg-red-100 text-red-800
                                    @elseif($demande->statut === 'Payée') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $demande->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('dprh.demandes.show', $demande) }}" class="text-blue-600 hover:text-blue-800">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                Aucune demande trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($demandes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $demandes->appends(request()->all())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
