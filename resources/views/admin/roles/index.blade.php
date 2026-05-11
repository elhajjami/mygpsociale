@extends('admin.layouts.app')

@section('title', 'Gestion des Rôles')

@section('header', 'Gestion des Rôles')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div class="flex gap-4">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-900">Administration</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-700">Rôles</span>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Nouveau Rôle
        </a>
    </div>

    <!-- Tableau des rôles -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateurs</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $role->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $role->permissions_count }} permission(s)
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $role->users_count }} utilisateur(s)
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="ml-3 text-indigo-600 hover:text-indigo-900">Modifier</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                        Aucun rôle trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($roles->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
