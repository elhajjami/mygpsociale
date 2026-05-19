@extends('admin.layouts.app')

@section('title', $partenaire->nom)

@section('header', $partenaire->nom)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.partenaires.index') }}" class="text-indigo-600 hover:text-indigo-900">
            ← Retour aux partenaires
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.partenaires.edit', $partenaire) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Modifier
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Numéro de Convention</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->numero_convention }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->nom }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type de Structure</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($partenaire->type_structure) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Spécialité</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->specialite ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ville</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->ville }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($partenaire->statut == 'active') bg-green-100 text-green-800
                            @elseif($partenaire->statut == 'expirée') bg-red-100 text-red-800
                            @elseif($partenaire->statut == 'suspendue') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($partenaire->statut) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($partenaire->telephone)
                            <a href="tel:{{ $partenaire->telephone }}" class="text-indigo-600 hover:text-indigo-900">{{ $partenaire->telephone }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($partenaire->email)
                            <a href="mailto:{{ $partenaire->email }}" class="text-indigo-600 hover:text-indigo-900">{{ $partenaire->email }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date Effet</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->date_effet ? $partenaire->date_effet->format('d/m/Y') : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date Fin</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->date_fin ? $partenaire->date_fin->format('d/m/Y') : '-' }}</dd>
                </div>
            </dl>

            @if($partenaire->coordonnees)
            <div class="mt-4">
                <dt class="text-sm font-medium text-gray-500">Coordonnées</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $partenaire->coordonnees }}</dd>
            </div>
            @endif

            @if($partenaire->observations)
            <div class="mt-4">
                <dt class="text-sm font-medium text-gray-500">Observations</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $partenaire->observations }}</dd>
            </div>
            @endif
        </div>
    </div>

    <!-- Informations de facturation -->
    @if($partenaire->adresse || $partenaire->fax || $partenaire->rib || $partenaire->ice || $partenaire->cnss || $partenaire->patente || $partenaire->if || $partenaire->banque || $partenaire->agence)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informations de Facturation</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-2 gap-4">
                @if($partenaire->adresse)
                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $partenaire->adresse }}</dd>
                </div>
                @endif

                @if($partenaire->telephone || $partenaire->fax)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($partenaire->telephone)
                            <a href="tel:{{ $partenaire->telephone }}" class="text-indigo-600 hover:text-indigo-900">{{ $partenaire->telephone }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Fax</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->fax ?? '-' }}</dd>
                </div>
                @endif

                @if($partenaire->ice)
                <div>
                    <dt class="text-sm font-medium text-gray-500">ICE</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->ice }}</dd>
                </div>
                @endif

                @if($partenaire->cnss)
                <div>
                    <dt class="text-sm font-medium text-gray-500">CNSS</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->cnss }}</dd>
                </div>
                @endif

                @if($partenaire->patente)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Patente</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->patente }}</dd>
                </div>
                @endif

                @if($partenaire->if)
                <div>
                    <dt class="text-sm font-medium text-gray-500">IF (Identifiant Fiscal)</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->if }}</dd>
                </div>
                @endif

                @if($partenaire->banque || $partenaire->agence || $partenaire->rib)
                <div class="col-span-2 border-t pt-4 mt-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Coordonnées Bancaires</h4>
                    <div class="grid grid-cols-2 gap-4">
                        @if($partenaire->banque)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Banque</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->banque }}</dd>
                        </div>
                        @endif
                        @if($partenaire->agence)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Agence</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $partenaire->agence }}</dd>
                        </div>
                        @endif
                        @if($partenaire->rib)
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">RIB</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $partenaire->rib }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </dl>
        </div>
    </div>
    @endif

    <!-- Demandes PEC récentes -->
    @if($partenaire->demandesPEC && $partenaire->demandesPEC->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Demandes PEC récentes ({{ $partenaire->demandesPEC->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($partenaire->demandesPEC as $demande)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $demande->numero }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $demande->agent->nom_complet ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($demande->montant_devis, 2, ',', ' ') }} MAD</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($demande->statut == 'Validée') bg-green-100 text-green-800
                                @elseif($demande->statut == 'En attente') bg-yellow-100 text-yellow-800
                                @elseif($demande->statut == 'Rejetée') bg-red-100 text-red-800
                                @elseif($demande->statut == 'Payée') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $demande->statut }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $demande->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Métadonnées -->
    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
        <div class="flex justify-between">
            <span>Créé le : {{ $partenaire->created_at->format('d/m/Y H:i') }}</span>
            @if($partenaire->updated_at != $partenaire->created_at)
            <span>Modifié le : {{ $partenaire->updated_at->format('d/m/Y H:i') }}</span>
            @endif
        </div>
    </div>
</div>
@endsection
