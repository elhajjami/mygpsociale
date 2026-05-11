@extends('admin.layouts.app')

@section('title', 'Rôle ' . $role->name)

@section('header', $role->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux rôles</a>
    </div>

    <!-- Informations du rôle -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du rôle</h3>
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-sm text-gray-500">Nom</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Utilisateurs</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->users->count() }}</dd>
            </div>
        </dl>
        <div class="mt-4 pt-4 border-t">
            <a href="{{ route('admin.roles.edit', $role) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Modifier
            </a>
        </div>
    </div>

    <!-- Permissions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Permissions ({{ $role->permissions->count() }})</h3>
        </div>
        <div class="p-4">
            @if($role->permissions->count() > 0)
                <div class="grid grid-cols-3 gap-2">
                    @foreach($role->permissions->sortBy('name') as $permission)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                        {{ $permission->name }}
                    </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Aucune permission assignée</p>
            @endif
        </div>
    </div>

    <!-- Utilisateurs avec ce rôle -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Utilisateurs avec ce rôle</h3>
        </div>
        <div class="p-4">
            @if($role->users->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($role->users as $user)
                    <li class="py-2 flex justify-between">
                        <span class="text-sm text-gray-900">{{ $user->name }}</span>
                        <span class="text-sm text-gray-500">{{ $user->email }}</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">Aucun utilisateur avec ce rôle</p>
            @endif
        </div>
    </div>
</div>
@endsection
