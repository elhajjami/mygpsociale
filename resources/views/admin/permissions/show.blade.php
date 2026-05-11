@extends('admin.layouts.app')

@section('title', 'Permission ' . $permission->name)

@section('header', $permission->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.permissions.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux permissions</a>
        <a href="{{ route('admin.permissions.edit', $permission) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Modifier
        </a>
    </div>

    <!-- Informations de la permission -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nom</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $permission->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Rôles avec cette permission</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $permission->roles->count() }} rôle(s)</dd>
            </div>
        </dl>
    </div>

    <!-- Rôles ayant cette permission -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Rôles ayant cette permission ({{ $permission->roles->count() }})</h3>
        </div>
        <div class="p-4">
            @if($permission->roles->count() > 0)
                <div class="grid grid-cols-2 gap-2">
                    @foreach($permission->roles as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ $role->name }}
                    </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Aucun rôle n'a cette permission</p>
            @endif
        </div>
    </div>
</div>
@endsection
