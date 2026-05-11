<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Agents -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Agents Actifs</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['agents']['actifs'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Demandes en attente -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">En attente</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['demandes']['en_attente'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Demandes validées -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Validées</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['demandes']['validees'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Écarts non traités -->
                @role('admin')
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Écarts</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['ecarts']['non_trites'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                @endrole
            </div>

            <!-- Alertes de plafond -->
            @if(isset($alertesPlafond) && count($alertesPlafond) > 0)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Alertes de plafond</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ count($alertesPlafond) }} agent(s) ont atteint ou dépassé 80% de leur plafond annuel.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Demandes récentes -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Demandes récentes</h3>
                    </div>
                    <div class="p-4">
                        @if(isset($demandesRecentes) && $demandesRecentes->count() > 0)
                            <div class="space-y-3">
                                @foreach($demandesRecentes as $demande)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $demande->numero_demande }}</p>
                                            <p class="text-xs text-gray-500">{{ $demande->agent->nom_complet }} - {{ $demande->partenaire->nom ?? 'N/A' }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($demande->statut === 'Validée') bg-green-100 text-green-800
                                            @elseif($demande->statut === 'En attente') bg-yellow-100 text-yellow-800
                                            @elseif($demande->statut === 'Rejetée') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $demande->statut }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">Aucune demande récente</p>
                        @endif
                    </div>
                </div>

                <!-- Écarts récents -->
                @role('admin')
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Écarts non traités</h3>
                    </div>
                    <div class="p-4">
                        @if(isset($ecartsRecents) && $ecartsRecents->count() > 0)
                            <div class="space-y-3">
                                @foreach($ecartsRecents as $ecart)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $ecart->type_ecart }}</p>
                                            <span class="text-xs text-gray-500">{{ $ecart->date_detection->format('d/m/Y') }}</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">Matricule: {{ $ecart->matricule }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">Aucun écart non traité</p>
                        @endif
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
