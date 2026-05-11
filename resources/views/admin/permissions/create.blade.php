@extends('admin.layouts.app')

@section('title', 'Nouvelle Permission')

@section('header', 'Nouvelle Permission')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.permissions.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux permissions</a>
    </div>

    <form method="POST" action="{{ route('admin.permissions.store') }}" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la permission *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="ex: créer demandes">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Créer la permission
            </button>
        </div>
    </form>
</div>
@endsection
