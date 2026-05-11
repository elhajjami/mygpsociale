@extends('admin.layouts.app')

@section('title', 'Agents')

@section('header', 'Agents')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-end mb-4">
        @can('créer agents')
            <a href="{{ route('admin.agents.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                Nouvel Agent
            </a>
        @endcan
    </div>
            <!-- Filtres -->
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <form method="GET" action="{{ route('admin.agents.index') }}" class="flex flex-wrap gap-4">
                    <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                           class="flex-1 min-w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500">

                    <select name="statut" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Tous statuts</option>
                        <option value="Actif" {{ request('statut') === 'Actif' ? 'selected' : '' }}>Actif</option>
                        <option value="Retraité" {{ request('statut') === 'Retraité' ? 'selected' : '' }}>Retraité</option>
                        <option value="Sorti" {{ request('statut') === 'Sorti' ? 'selected' : '' }}>Sorti</option>
                    </select>

                    <select name="categorie" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Toutes catégories</option>
                        <option value="Exécution" {{ request('categorie') === 'Exécution' ? 'selected' : '' }}>Exécution</option>
                        <option value="Maîtrise" {{ request('categorie') === 'Maîtrise' ? 'selected' : '' }}>Maîtrise</option>
                        <option value="Cadre" {{ request('categorie') === 'Cadre' ? 'selected' : '' }}>Cadre</option>
                        <option value="Hors cadre" {{ request('categorie') === 'Hors cadre' ? 'selected' : '' }}>Hors cadre</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">
                        Filtrer
                    </button>

                    <a href="{{ route('admin.agents.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Réinitialiser
                    </a>
                </form>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $statistiques['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Actifs</p>
                    <p class="text-xl font-semibold text-green-600">{{ $statistiques['actifs'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Retraités</p>
                    <p class="text-xl font-semibold text-gray-600">{{ $statistiques['retraites'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Tableau des agents -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ayants droit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $agent->matricule }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $agent->nom_complet }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->categorie }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->dp_affectation }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($agent->statut === 'Actif') bg-green-100 text-green-800
                                        @elseif($agent->statut === 'Retraité') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $agent->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->ayants_droit_count ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.agents.show', $agent) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                    @can('modifier agents')
                                        <a href="{{ route('admin.agents.edit', $agent) }}" class="ml-3 text-indigo-600 hover:text-indigo-900">Modifier</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun agent trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($agents->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $agents->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
@endsection
