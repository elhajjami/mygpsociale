@extends('admin.layouts.app')

@section('title', 'Partenaires')

@section('header', 'Gestion des Partenaires')

@section('content')
<div class="space-y-6">
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.partenaires.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou N° Convention..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                <select name="ville" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Toutes les villes</option>
                    <option value="Fès" {{ request('ville') == 'Fès' ? 'selected' : '' }}>Fès</option>
                    <option value="Meknès" {{ request('ville') == 'Meknès' ? 'selected' : '' }}>Meknès</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type_structure" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les types</option>
                    <option value="clinique" {{ request('type_structure') == 'clinique' ? 'selected' : '' }}>Clinique</option>
                    <option value="laboratoire" {{ request('type_structure') == 'laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                    <option value="médecin" {{ request('type_structure') == 'médecin' ? 'selected' : '' }}>Médecin</option>
                    <option value="radiologie" {{ request('type_structure') == 'radiologie' ? 'selected' : '' }}>Radiologie</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="expirée" {{ request('statut') == 'expirée' ? 'selected' : '' }}>Expirée</option>
                    <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                    <option value="résiliée" {{ request('statut') == 'résiliée' ? 'selected' : '' }}>Résiliée</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Filtrer
                </button>
                <a href="{{ route('admin.partenaires.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des partenaires -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                Partenaires ({{ $partenaires->total() }})
            </h3>
            <a href="{{ route('admin.partenaires.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau Partenaire
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase uppercase">N° Convention</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ville</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Spécialité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($partenaires as $partenaire)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $partenaire->numero_convention }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.partenaires.show', $partenaire->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                {{ $partenaire->nom }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($partenaire->type_structure) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $partenaire->ville }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $partenaire->specialite ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $partenaire->telephone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($partenaire->email)
                                <a href="mailto:{{ $partenaire->email }}" class="text-blue-600 hover:text-blue-800">{{ $partenaire->email }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($partenaire->statut == 'active') bg-green-100 text-green-800
                                @elseif($partenaire->statut == 'expirée') bg-red-100 text-red-800
                                @elseif($partenaire->statut == 'suspendue') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($partenaire->statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.partenaires.show', $partenaire->id) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.partenaires.edit', $partenaire->id) }}" class="text-yellow-600 hover:text-yellow-800 inline-flex items-center" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="mt-2">Aucun partenaire trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($partenaires->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $partenaires->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
