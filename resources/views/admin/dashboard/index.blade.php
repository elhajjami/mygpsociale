@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('header', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Agents -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Agents</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistiques['agents']['total']) }}</p>
                    <p class="text-sm text-green-600">{{ number_format($statistiques['agents']['actifs']) }} actifs</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Demandes en attente -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistiques['demandes']['en_attente']) }}</p>
                    <p class="text-sm text-yellow-600">{{ number_format($statistiques['demandes']['expirees']) }} expirées</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Demandes validées -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Validées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistiques['demandes']['validees']) }}</p>
                    <p class="text-sm text-red-600">{{ number_format($statistiques['demandes']['rejetees']) }} rejetées</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Écarts non traités -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Écarts SAP/CGS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistiques['ecarts']['non_trites']) }}</p>
                    <p class="text-sm text-orange-600">À traiter</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Consommation des plafonds -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Plafond Annuel ({{ $annee }})</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Plafond Total</span>
                    <span class="font-semibold">{{ number_format($statistiques['montants']['engage'] + $statistiques['montants']['consome'], 2) }} DH</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Montant Engagé</span>
                    <span class="font-semibold text-blue-600">{{ number_format($statistiques['montants']['engage'], 2) }} DH</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Montant Consommé</span>
                    <span class="font-semibold text-green-600">{{ number_format($statistiques['montants']['consome'], 2) }} DH</span>
                </div>
                @if($statistiques['montants']['engage'] + $statistiques['montants']['consome'] > 0)
                <div class="pt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, ($statistiques['montants']['engage'] + $statistiques['montants']['consome']) / max(1, $statistiques['montants']['engage'] + $statistiques['montants']['consome']) * 100) }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Consommation par catégorie -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Consommation par Catégorie</h3>
            <div class="space-y-3">
                @foreach($consommationParCategorie as $categorie => $data)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $categorie }}</span>
                        <span class="font-medium">{{ number_format($data['pourcentage'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $data['pourcentage']) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($data['consome'] + $data['engage'], 2) }} / {{ number_format($data['plafond_total'], 2) }} DH</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Alertes et Top Partenaires -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Alertes de plafond -->
        @if(count($alertesPlafond) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Alertes Plafond ({{ count($alertesPlafond) }})
            </h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($alertesPlafond as $alerte)
                <div class="p-3 rounded-lg {{ $alerte['niveau'] === 'critique' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">{{ $alerte['agent']['nom'] }} {{ $alerte['agent']['prenom'] }}</p>
                            <p class="text-sm text-gray-600">Mat: {{ $alerte['agent']['matricule'] }} | {{ $alerte['agent']['categorie'] }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $alerte['niveau'] === 'critique' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800' }}">
                            {{ number_format($alerte['pourcentage'], 1) }}%
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ number_format($alerte['reste'], 2) }} DH restants</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Top 5 Partenaires -->
        @if(count($topPartenaires) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Partenaires ({{ $annee }})</h3>
            <div class="space-y-3">
                @foreach($topPartenaires as $index => $partenaire)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <span class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded-full font-bold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <p class="font-medium text-gray-900">{{ $partenaire['nom'] }}</p>
                            <p class="text-sm text-gray-600">{{ $partenaire['nb_demandes'] }} demandes</p>
                        </div>
                    </div>
                    <span class="font-semibold text-blue-600">{{ number_format($partenaire['montant_total'], 2) }} DH</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Demandes et Écarts récents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Demandes récentes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Demandes Récentes</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($demandesRecentes as $demande)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-gray-900">{{ $demande->agent->nom ?? 'N/A' }} {{ $demande->agent->prenom ?? '' }}</p>
                                <p class="text-xs text-gray-500">{{ $demande->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($demande->montant_devis, 2) }} DH</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($demande->statut === 'Validée') bg-green-100 text-green-800
                                    @elseif($demande->statut === 'En attente') bg-yellow-100 text-yellow-800
                                    @elseif($demande->statut === 'Rejetée') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $demande->statut }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Écarts récents -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Écarts Non Traités</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ecartsRecents as $ecart)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ecart->type_ecart }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ecart->matricule }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $ecart->date_detection->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Auto-refresh des alertes toutes les 30 secondes
    setInterval(() => {
        fetch('{{ route('api.alertes') }}')
            .then(r => r.json())
            .then(data => {
                if (data.total > 0) {
                    console.log('Alertes:', data);
                }
            });
    }, 30000);
</script>
@endpush
@endsection
