@extends('admin.layouts.app')

@section('title', 'Agents')

@section('header', 'Agents')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Info comparaison SAP -->
    @if($statistiques['total_sap'] ?? 0 > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-blue-800">
                <strong>Données SAP disponibles :</strong> {{ $statistiques['total_sap'] }} agents SAP importés
            </p>
        </div>
        <a href="{{ route('admin.import.agents') }}" class="text-sm text-blue-600 hover:underline">
            Importer d'autres données SAP
        </a>
    </div>
    @endif

    <div class="flex justify-end mb-4">
        @can('créer agents')
            <a href="{{ route('admin.agents.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                Nouvel Agent
            </a>
        @endcan
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('admin.agents.index') }}" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                   class="flex-1 min-w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500">

            <select name="statut" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Tous statuts</option>
                <option value="Actif" {{ request('statut') === 'Actif' ? 'selected' : '' }}>Actif</option>
                <option value="Retraité" {{ request('statut') === 'Retraité' ? 'selected' : '' }}>Retraité</option>
                <option value="Sorti" {{ request('statut') === 'Sorti' ? 'selected' : '' }}>Sorti</option>
            </select>

            @if(($statistiques['total_sap'] ?? 0) > 0)
            <select name="comparaison" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="tous" {{ request('comparaison') === 'tous' || !request('comparaison') ? 'selected' : '' }}>Tous (CGS)</option>
                <option value="correspond" {{ request('comparaison') === 'correspond' ? 'selected' : '' }}>✓ Correspond SAP</option>
                <option value="different" {{ request('comparaison') === 'different' ? 'selected' : '' }}>⚠ Diffère de SAP</option>
                <option value="absent_sap" {{ request('comparaison') === 'absent_sap' ? 'selected' : '' }}>✗ Absent dans SAP</option>
                <option value="absent_cgs" {{ request('comparaison') === 'absent_cgs' ? 'selected' : '' }}>+ Absent dans CGS</option>
            </select>
            @endif

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">
                Filtrer
            </button>

            <a href="{{ route('admin.agents.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                Réinitialiser
            </a>
        </form>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total CGS</p>
            <p class="text-xl font-semibold text-gray-900">{{ $statistiques['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Actifs</p>
            <p class="text-xl font-semibold text-green-600">{{ $statistiques['actifs'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Retraités</p>
            <p class="text-xl font-semibold text-gray-600">{{ $statistiques['retraites'] ?? 0 }}</p>
        </div>
    </div>

    @if(($statistiques['total_sap'] ?? 0) > 0)
    <!-- Statistiques comparaison -->
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">✓ Corresp. SAP</p>
            <p class="text-xl font-semibold text-green-600">{{ $statistiques['correspondent'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">⚠ Diffèrent</p>
            <p class="text-xl font-semibold text-orange-600">{{ $statistiques['different'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">✗ Absent SAP</p>
            <p class="text-xl font-semibold text-red-600">{{ $statistiques['absent_sap'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">+ Absent CGS</p>
            <p class="text-xl font-semibold text-blue-600">{{ $statistiques['absent_cgs'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total SAP</p>
            <p class="text-xl font-semibold text-blue-600">{{ $statistiques['total_sap'] ?? 0 }}</p>
        </div>
    </div>
    @endif

            <!-- Tableau des agents -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SAP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ayants droit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-gray-50 @if(isset($agent->is_sap_only)) bg-blue-50 @endif">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if(isset($agent->is_sap_only))
                                        <span class="text-blue-600" title="Absent dans CGS (SAP uniquement)">+</span>
                                    @elseif(isset($agent->statut_comparaison))
                                        @if($agent->statut_comparaison === 'correspond')
                                            <span class="text-green-600" title="Correspond aux données SAP">✓</span>
                                        @elseif($agent->statut_comparaison === 'different')
                                            <a href="#" onclick="showDifference({{ $agent->id }}); return false;" class="text-orange-600" title="Diffère des données SAP">⚠</a>
                                        @else
                                            <span class="text-red-400" title="Absent dans SAP">✗</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">−</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $agent->matricule }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($agent->is_sap_only))
                                        {{ $agent->nom_prenom ?? trim(($agent->nom ?? '') . ' ' . ($agent->prenom ?? '')) }}
                                        <span class="text-xs text-blue-600 ml-1">(SAP)</span>
                                    @else
                                        {{ $agent->nom_complet }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->dp_affectation ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($agent->statut === 'Actif') bg-green-100 text-green-800
                                        @elseif($agent->statut === 'Retraité') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $agent->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($agent->is_sap_only)) -
                                    @else {{ $agent->ayants_droit_count ?? 0 }} @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(isset($agent->is_sap_only))
                                        <span class="text-gray-400">N/A</span>
                                    @else
                                        <div class="flex justify-end space-x-3">
                                            <a href="{{ route('admin.agents.show', $agent) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center" title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @can('modifier agents')
                                                <a href="{{ route('admin.agents.edit', $agent) }}" class="text-yellow-600 hover:text-yellow-800 inline-flex items-center" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endcan
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun agent trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($agents->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $agents->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>

    <!-- Modal des différences SAP/CGS -->
    <div id="modalDifferences" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">⚠ Différences SAP / CGS</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="differencesContent"></div>

                <div class="mt-6 flex justify-end gap-3">
                    <a id="linkVoirAgent" href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Voir l'agent
                    </a>
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Données des agents pour la modal -->
    <script>
        const agentsShowUrlBase = '{{ url('admin/agents') }}';
        const agentsData = [
            @foreach($agents as $agent)
            {
                id: {{ $agent->id }},
                matricule: '{{ $agent->matricule }}',
                nom: '{{ $agent->nom }}',
                prenom: '{{ $agent->prenom ?? '' }}',
                statut: '{{ $agent->statut }}',
                date_naissance: '{{ $agent->date_naissance ? $agent->date_naissance->format('d/m/Y') : '' }}',
                @if(isset($agent->donnees_sap) && $agent->donnees_sap)
                donnees_sap: {
                    nom_prenom: '{{ $agent->donnees_sap->nom_prenom ?? trim(($agent->donnees_sap->nom ?? '') . ' ' . ($agent->donnees_sap->prenom ?? '')) }}',
                    nom: '{{ $agent->donnees_sap->nom ?? '' }}',
                    prenom: '{{ $agent->donnees_sap->prenom ?? '' }}',
                    statut: '{{ $agent->donnees_sap->statut }}',
                    date_naissance: '{{ $agent->donnees_sap->date_naissance ? $agent->donnees_sap->date_naissance->format('d/m/Y') : '' }}',
                    date_import_sap: '{{ $agent->donnees_sap->date_import_sap ? $agent->donnees_sap->date_import_sap->format('d/m/Y H:i') : '' }}',
                    fichier_import: '{{ $agent->donnees_sap->fichier_import ?? '' }}'
                },
                @else
                donnees_sap: null,
                @endif
            }@if(!$loop->last),@endif
            @endforeach
        ];

        const comparaisonUrlBase = '{{ url('admin/agents/comparaison') }}';

        async function showDifference(agentId) {
            // Afficher un indicateur de chargement
            document.getElementById('differencesContent').innerHTML = '<p class="text-sm text-gray-500">Chargement...</p>';
            document.getElementById('modalDifferences').classList.remove('hidden');
            document.getElementById('modalDifferences').classList.add('flex');

            try {
                const response = await fetch(`${comparaisonUrlBase}/${agentId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Response not OK:', response.status, text.substring(0, 200));
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                let html = `
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600"><strong>Agent:</strong> ${data.agent.nom} ${data.agent.prenom || ''}</p>
                        <p class="text-sm text-gray-600"><strong>Matricule:</strong> ${data.agent.matricule}</p>
                    </div>
                `;

                if (data.sap) {
                    const differences = data.differences || {};

                    if (Object.keys(differences).length > 0) {
                        html += '<table class="min-w-full divide-y divide-gray-200">';
                        html += '<thead class="bg-gray-50"><tr>';
                        html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Champ</th>';
                        html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">CGS</th>';
                        html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">SAP</th>';
                        html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';

                        for (const [champ, vals] of Object.entries(differences)) {
                            html += '<tr>';
                            html += `<td class="px-4 py-3 text-sm font-medium text-gray-900">${champ.charAt(0).toUpperCase() + champ.slice(1)}</td>`;
                            html += `<td class="px-4 py-3 text-sm text-red-600 line-through">${vals.cgs}</td>`;
                            html += `<td class="px-4 py-3 text-sm text-green-600 font-medium">${vals.sap}</td>`;
                            html += '</tr>';
                        }

                        html += '</tbody></table>';

                        if (data.sap.date_import_sap) {
                            html += `
                                <div class="mt-4 p-3 bg-blue-50 rounded-lg text-xs text-blue-800">
                                    <p><strong>Dernier import SAP:</strong> ${data.sap.date_import_sap}</p>
                                    <p><strong>Fichier:</strong> ${data.sap.fichier_import || 'N/A'}</p>
                                </div>
                            `;
                        }
                    } else {
                        html += '<p class="text-sm text-green-600">✓ Les données correspondent</p>';
                    }
                } else {
                    html += '<p class="text-sm text-red-600">Aucune donnée SAP disponible pour cet agent</p>';
                }

                document.getElementById('differencesContent').innerHTML = html;
                document.getElementById('linkVoirAgent').href = agentsShowUrlBase + '/' + agentId;

            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('differencesContent').innerHTML = '<p class="text-sm text-red-600">Erreur: ' + error.message + '</p>';
            }
        }

        function closeModal() {
            document.getElementById('modalDifferences').classList.add('hidden');
            document.getElementById('modalDifferences').classList.remove('flex');
        }

        // Fermer la modal en cliquant à l'extérieur
        document.getElementById('modalDifferences').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection
