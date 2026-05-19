@extends('admin.layouts.app')

@section('title', 'Demandes PEC')

@section('header', 'Demandes de Prise en Charge')

@section('content')
<div class="space-y-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">En attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statistiques['en_attente'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Validées</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiques['validees'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Rejetées</p>
            <p class="text-2xl font-bold text-red-600">{{ $statistiques['rejetees'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-sm text-gray-500">Payées</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statistiques['payees'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filtres et Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('dprh.demandes.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tous statuts</option>
                    <option value="En attente de validation" {{ request('statut') === 'En attente de validation' ? 'selected' : '' }}>En attente</option>
                    <option value="Validée" {{ request('statut') === 'Validée' ? 'selected' : '' }}>Validée</option>
                    <option value="Rejetée" {{ request('statut') === 'Rejetée' ? 'selected' : '' }}>Rejetée</option>
                    <option value="Payée" {{ request('statut') === 'Payée' ? 'selected' : '' }}>Payée</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filtrer
            </button>

            <a href="{{ route('dprh.demandes.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                Réinitialiser
            </a>

            <div class="ml-auto">
                <a href="{{ route('dprh.demandes.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Nouvelle Demande
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des demandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partenaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date soin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($demandes as $demande)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600">
                                {{ $demande->numero_demande }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $demande->agent->nom_complet ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->partenaire->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->type_prestation ?? $demande->type_soin ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $demande->date_soin?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ number_format($demande->montant_devis, 2) }} DH
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($demande->statut === 'Validée') bg-green-100 text-green-800
                                    @elseif($demande->statut === 'En attente de validation') bg-yellow-100 text-yellow-800
                                    @elseif($demande->statut === 'Rejetée') bg-red-100 text-red-800
                                    @elseif($demande->statut === 'Payée') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $demande->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-3">
                                <a href="{{ route('dprh.demandes.show', $demande) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center justify-center" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if($demande->statut === 'En attente de validation')
                                    <a href="{{ route('dprh.demandes.edit', $demande) }}" class="text-yellow-600 hover:text-yellow-800 inline-flex items-center justify-center" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('dprh.demandes.valider', $demande) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 inline-flex items-center justify-center" title="Valider" onclick="return confirm('Valider cette demande ?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                    <button type="button" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center" title="Rejeter" onclick="openRejetModal({{ $demande->id }}, '{{ $demande->numero_demande }}')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                                @if(in_array($demande->statut, ['Validée', 'Payée']))
                                    <a href="{{ route('dprh.demandes.imprimer-bon', $demande) }}" class="text-purple-600 hover:text-purple-800 inline-flex items-center justify-center" title="Imprimer Bon PEC">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                Aucune demande trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($demandes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $demandes->appends(request()->all())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modale de rejet -->
<div id="rejetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rejeter la demande PEC</h3>
            <p class="text-sm text-gray-600 mb-4">Demande n° <span id="rejetDemandeNumero"></span></p>

            <form method="POST" id="rejetForm">
                @csrf
                <input type="hidden" name="demande_id" id="rejetDemandeId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif du rejet *</label>
                    <textarea name="motif_rejet" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Veuillez indiquer le motif du rejet..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejetModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejetModal(demandeId, demandeNumero) {
    document.getElementById('rejetDemandeId').value = demandeId;
    document.getElementById('rejetDemandeNumero').textContent = demandeNumero;
    document.getElementById('rejetModal').classList.remove('hidden');
    document.getElementById('rejetModal').classList.add('flex');
}

function closeRejetModal() {
    document.getElementById('rejetModal').classList.add('hidden');
    document.getElementById('rejetModal').classList.remove('flex');
    document.getElementById('rejetForm').reset();
}

document.getElementById('rejetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const demandeId = document.getElementById('rejetDemandeId').value;
    this.action = '{{ route('dprh.demandes.rejeter', ':id') }}'.replace(':id', demandeId);
    this.submit();
});
</script>
@endsection
