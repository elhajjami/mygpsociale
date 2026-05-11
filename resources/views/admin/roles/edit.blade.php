@extends('admin.layouts.app')

@section('title', 'Modifier le Rôle')

@section('header', 'Modifier ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900">← Retour au rôle</a>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle *</label>
            <input type="text" name="name" value="{{ $role->name }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <!-- Permissions -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="space-y-4">
                @forelse($permissionsGrouped as $module => $perms)
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">{{ $module ?? 'Général' }}</h4>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($perms as $permission)
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                @if(in_array($permission->id, $rolePermissions)) checked @endif>
                            <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">Aucune permission disponible.</p>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('admin.roles.show', $role) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
