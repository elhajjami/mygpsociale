@extends('admin.layouts.app')

@section('title', 'Modifier Demande PEC')

@section('header', 'Modifier la Demande de Prise en Charge')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('dprh.demandes.update', $demande->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Infos actuelles -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Demande #{{ $demande->id }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Agent:</span>
                            <span class="font-medium">{{ $demande->agent->nom ?? 'N/A' }} {{ $demande->agent->prenom ?? '' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Partenaire:</span>
                            <span class="font-medium">{{ $demande->partenaire->nom ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Date soin:</span>
                            <span class="font-medium">{{ $demande->date_soin->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Montant:</span>
                            <span class="font-medium">{{ number_format($demande->montant_devis, 2) }} DH</span>
                        </div>
                    </div>
                </div>

                <!-- Section Modification -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier la demande</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date du soin</label>
                            <input type="date" name="date_soin" value="{{ $demande->date_soin->format('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('date_soin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant du devis (DH)</label>
                            <input type="number" name="montant_devis" step="0.01" min="0" value="{{ $demande->montant_devis }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('montant_devis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de prestation</label>
                            <select name="type_prestation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="consultation" {{ $demande->type_prestation == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                <option value="analyse" {{ $demande->type_prestation == 'analyse' ? 'selected' : '' }}>Analyse</option>
                                <option value="radiologie" {{ $demande->type_prestation == 'radiologie' ? 'selected' : '' }}>Radiologie</option>
                                <option value="medicament" {{ $demande->type_prestation == 'medicament' ? 'selected' : '' }}>Médicament</option>
                                <option value="chirurgie" {{ $demande->type_prestation == 'chirurgie' ? 'selected' : '' }}>Chirurgie</option>
                                <option value="autre" {{ $demande->type_prestation == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Partenaire</label>
                            <select name="partenaire_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                @foreach(\App\Models\Partenaire::orderBy('nom')->get() as $p)
                                <option value="{{ $p->id }}" {{ $demande->partenaire_id == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description / Diagnostic</label>
                            <textarea name="description" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $demande->description ?? '' }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remplacer le document</label>
                            <input type="file" name="fichier_devis"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">
                                @if($demande->fichier_devis)
                                    Document actuel: <a href="{{ Storage::url($demande->fichier_devis) }}" target="_blank" class="text-blue-600 hover:underline">Voir</a>
                                @else
                                    Aucun document attaché
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('dprh.demandes.show', $demande->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Actions rapides -->
    @if($demande->statut === 'En attente')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Validation</h3>
        <div class="flex gap-4">
            <form method="POST" action="{{ route('dprh.demandes.valider', $demande->id) }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Valider la demande
                </button>
            </form>
            <form method="POST" action="{{ route('dprh.demandes.rejeter', $demande->id) }}"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?');">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Rejeter la demande
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
