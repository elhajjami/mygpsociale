@extends('admin.layouts.app')

@section('title', 'Facturation')

@section('header', 'Gestion des Factures')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Factures</h2>
        <div class="flex gap-3">
            <a href="{{ route('dprh.facturation.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle Facture
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $statistiques['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Brouillons</p>
            <p class="text-2xl font-bold text-gray-600">{{ $statistiques['brouillon'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Générées</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statistiques['generee'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Envoyées</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statistiques['envoyee'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Payées</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiques['payee'] }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('dprh.facturation.index') }}" class="flex flex-wrap gap-4">
            <div>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les types</option>
                    <option value="medical" {{ request('type') == 'medical' ? 'selected' : '' }}>Formation Médicale</option>
                    <option value="clinique" {{ request('type') == 'clinique' ? 'selected' : '' }}>Clinique</option>
                </select>
            </div>
            <div>
                <select name="statut" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="generee" {{ request('statut') == 'generee' ? 'selected' : '' }}>Générée</option>
                    <option value="envoyee" {{ request('statut') == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                    <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Payée</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Date début">
            </div>
            <div>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Date fin">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Filtrer
            </button>
            <a href="{{ route('dprh.facturation.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Réinitialiser
            </a>
        </form>
    </div>

    <!-- Tableau des factures -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Facture</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partenaire</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant TTC</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($factures as $facture)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $facture->numero }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $facture->date_facture->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        @if($facture->type_facture === 'medical')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Médical
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                Clinique
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $facture->partenaire->nom ?? '-' }}
                        @if($facture->demande_pec_id)
                        <span class="text-xs text-gray-400">(PEC: {{ $facture->demandePec->numero_demande }})</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ number_format($facture->montant_ttc, 2) }} DH</td>
                    <td class="px-4 py-3">{!! $facture->statut_badge !!}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('dprh.facturation.show', $facture) }}" class="text-blue-600 hover:text-blue-800" title="Voir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('dprh.facturation.telecharger', $facture) }}" class="text-green-600 hover:text-green-800" title="Télécharger PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            @if($facture->peutEtreModifiee())
                            <form method="POST" action="{{ route('dprh.facturation.destroy', $facture) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">Aucune facture trouvée</p>
                        <a href="{{ route('dprh.facturation.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Créer une facture
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($factures->hasPages())
    <div class="mt-4">
        {{ $factures->links() }}
    </div>
    @endif
</div>
@endsection
