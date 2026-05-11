@extends('admin.layouts.app')

@section('title', 'Utilisateur ' . $user->name)

@section('header', $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux utilisateurs</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Informations utilisateur -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nom</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($user->email_verified_at) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $user->email_verified_at ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Rôles -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Rôles</h3>
                    <span class="text-sm text-gray-500">{{ $user->roles->count() }} rôle(s)</span>
                </div>
                <div class="p-4">
                    @if($user->roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun rôle assigné</p>
                    @endif
                </div>
            </div>

            <!-- Permissions via rôles -->
            @if($user->hasRole('Admin CGS'))
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-500">Accès complet à toutes les fonctionnalités</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Modifier
                    </a>

                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                            Supprimer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
