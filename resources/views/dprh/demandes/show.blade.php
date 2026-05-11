@extends('admin.layouts.app')

@section('title', 'Demande PEC #' . $demande->id)

@section('header', 'Demande PEC : ' . $demande->numero_demande)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-end space-x-2">
        @if($demande->statut === 'En attente')
            <a href="{{ route('dprh.demandes.edit', $demande) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Modifier
            </a>
        @endif
        @if(in_array($demande->statut, ['Validée', 'Payée']))
            <a href="{{ route('dprh.demandes.imprimer-bon', $demande) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Imprimer Bon PEC
            </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow">
        <!-- En-tête avec statut -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $demande->numero_demande }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Créée le {{ $demande->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-4 py-2 text-sm font-medium rounded-full
                    @if($demande->statut === 'Validée') bg-green-100 text-green-800
                    @elseif($demande->statut === 'En attente') bg-yellow-100 text-yellow-800
                    @elseif($demande->statut === 'Rejetée') bg-red-100 text-red-800
                    @elseif($demande->statut === 'Payée') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $demande->statut }}
                </span>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Agent et bénéficiaire -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Agent</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium text-gray-900">{{ $demande->agent->nom ?? 'N/A' }} {{ $demande->agent->prenom ?? '' }}</p>
                        <p class="text-sm text-gray-600">Matricule: {{ $demande->agent->matricule }}</p>
                        <p class="text-sm text-gray-600">{{ $demande->agent->categorie ?? '-' }} - {{ $demande->agent->dp_affectation ?? '-' }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Bénéficiaire</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium text-gray-900">{{ $demande->beneficiaire?->nom ?? $demande->agent->nom }} {{ $demande->beneficiaire?->prenom ?? $demande->agent->prenom }}</p>
                        <p class="text-sm text-gray-600">
                            @if($demande->beneficiaire)
                                Ayant droit
                            @else
                                Agent titulaire
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Partenaire et soin -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Partenaire</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium text-gray-900">{{ $demande->partenaire->nom }}</p>
                        <p class="text-sm text-gray-600">{{ $demande->partenaire->type_structure }} - {{ $demande->partenaire->ville }}</p>
                        @if($demande->partenaire->specialite)
                            <p class="text-sm text-gray-600">Spécialité: {{ $demande->partenaire->specialite }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Détails du soin</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium text-gray-900">{{ $demande->type_prestation ?? $demande->type_soin ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">Date: {{ $demande->date_soin?->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Montants -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Montant devis</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($demande->montant_devis, 2) }} DH</p>
                    </div>
                    @if($demande->montant_regle)
                    <div>
                        <p class="text-sm text-gray-500">Montant réglé</p>
                        <p class="text-lg font-semibold text-green-600">{{ number_format($demande->montant_regle, 2) }} DH</p>
                    </div>
                    @endif
                    @if($demande->date_paiement)
                    <div>
                        <p class="text-sm text-gray-500">Date paiement</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $demande->date_paiement->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            @if($demande->description)
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Description</h4>
                <p class="text-sm text-gray-700">{{ $demande->description }}</p>
            </div>
            @endif

            <!-- Validation -->
            @if($demande->date_validation || $demande->motif_rejet)
            <div class="border-t border-gray-200 pt-6">
                @if($demande->statut === 'Validée' || $demande->statut === 'Payée')
                    <div class="flex items-center text-green-700">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">Validée le {{ $demande->date_validation->format('d/m/Y H:i') }}</span>
                    </div>
                @endif

                @if($demande->statut === 'Rejetée')
                    <div class="flex items-center text-red-700">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">Demande rejetée</span>
                    </div>
                    <div class="bg-red-50 rounded-md p-3 mt-2 ml-7">
                        <p class="text-sm text-red-800"><strong>Motif:</strong> {{ $demande->motif_rejet }}</p>
                    </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Actions -->
        @if($demande->statut === 'En attente')
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <form method="POST" action="{{ route('dprh.demandes.valider', $demande) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Valider
                    </button>
                </form>

                <form method="POST" action="{{ route('dprh.demandes.rejeter', $demande) }}"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Rejeter
                    </button>
                </form>

                <form method="POST" action="{{ route('dprh.demandes.destroy', $demande) }}"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Supprimer
                    </button>
                </form>
            </div>
        @endif

        @if($demande->statut === 'Validée')
            <div class="px-6 py-4 bg-blue-50 border-t border-blue-200">
                <form method="POST" action="{{ route('dprh.demandes.marquer-payee', $demande) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant réglé</label>
                            <input type="number" name="montant_regle" step="0.01" value="{{ $demande->montant_devis }}" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date paiement</label>
                            <input type="date" name="date_paiement" value="{{ now()->format('Y-m-d') }}" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Marquer comme payée
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
