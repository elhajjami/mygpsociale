@extends('admin.layouts.app')

@section('title', 'Agent ' . $agent->matricule)

@section('header', $agent->matricule . ' - ' . $agent->nom_complet)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.agents.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour aux agents</a>
    </div>
    <div class="flex justify-end mb-4">
        @can('modifier agents')
            <a href="{{ route('admin.agents.edit', $agent) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                Modifier
            </a>
        @endcan
    </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations principales -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informations personnelles -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                        </div>
                        <div class="p-4">
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-gray-500">Matricule</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->matricule }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">CIN</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->cin ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Nom</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Prénom</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->prenom ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Date de naissance</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->date_naissance?->format('d/m/Y') ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Âge</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->age ?? 'N/A' }} ans</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Informations professionnelles -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informations professionnelles</h3>
                        </div>
                        <div class="p-4">
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-gray-500">Catégorie</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->categorie }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Niveau</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->niveau ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Degré</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->degre ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">DP / Affectation</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->dp_affectation ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Statut</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($agent->statut === 'Actif') bg-green-100 text-green-800
                                            @elseif($agent->statut === 'Retraité') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $agent->statut }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Population</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->population ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Ayants droit -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Ayants droit</h3>
                            <span class="text-sm text-gray-500">{{ $agent->ayantsDroit->count() }} inscrit(s)</span>
                        </div>
                        <div class="p-4">
                            @if($agent->ayantsDroit->count() > 0)
                                <div class="space-y-2">
                                    @foreach($agent->ayantsDroit as $ayantDroit)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $ayantDroit->nom_prenom }}</p>
                                                <p class="text-xs text-gray-500">{{ $ayantDroit->type }} - {{ $ayantDroit->date_naissance?->format('d/m/Y') ?? 'N/A' }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $ayantDroit->statut === 'Validé' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $ayantDroit->statut }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Aucun ayant droit enregistré</p>
                            @endif
                        </div>
                    </div>

                    <!-- Historique des carrières -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Historique des carrières</h3>
                            <span class="text-sm text-gray-500">{{ $agent->carrieres->count() }} enregistrement(s)</span>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            @if($agent->carrieres->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Degré</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($agent->carrieres->take(10) as $carriere)
                                        <tr @if($carriere->date_fin === null) class="bg-green-50" @endif>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $carriere->date_debut?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $carriere->categorie_complete }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $carriere->niv ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $carriere->deg ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $carriere->motif_modification ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($agent->carrieres->count() > 10)
                                <p class="text-xs text-gray-500 mt-2 text-center">... et {{ $agent->carrieres->count() - 10 }} autres enregistrements</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Aucun historique de carrière</p>
                            @endif
                        </div>
                    </div>

                    <!-- Mesures disciplinaires -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Mesures disciplinaires</h3>
                            <span class="text-sm text-gray-500">{{ $agent->mesures->count() }} mesure(s)</span>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            @if($agent->mesures->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($agent->mesures->take(10) as $mesure)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $mesure->date_debut?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $mesure->categorie ?? 'N/A' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($mesure->motif, 80) ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($agent->mesures->count() > 10)
                                <p class="text-xs text-gray-500 mt-2 text-center">... et {{ $agent->mesures->count() - 10 }} autres mesures</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Aucune mesure enregistrée</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">
                    <!-- Plafond annuel -->
                    @if(isset($plafondAnneeCourante) && $plafondAnneeCourante)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Plafond {{ $plafondAnneeCourante->annee }}</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500">Plafond annuel</span>
                                        <span class="font-medium">{{ number_format($plafondAnneeCourante->plafond_annuel, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500">Consommé</span>
                                        <span class="font-medium text-red-600">{{ number_format($plafondAnneeCourante->montant_consome, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($plafondAnneeCourante->montant_consome / $plafondAnneeCourante->plafond_annuel) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500">Engagé</span>
                                        <span class="font-medium text-yellow-600">{{ number_format($plafondAnneeCourante->montant_engage, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($plafondAnneeCourante->montant_engage / $plafondAnneeCourante->plafond_annuel) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Reste disponible</span>
                                        <span class="font-medium text-green-600">{{ number_format($plafondAnneeCourante->reste_disponible, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Demandes récentes -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Demandes récentes</h3>
                        </div>
                        <div class="p-4">
                            @if(isset($agent->demandesPEC) && $agent->demandesPEC->count() > 0)
                                <div class="space-y-2">
                                    @foreach($agent->demandesPEC as $demande)
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-medium text-gray-900">{{ $demande->numero_demande }}</p>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs text-gray-500">{{ $demande->created_at->format('d/m/Y') }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($demande->statut === 'Validée') bg-green-100 text-green-800
                                                    @elseif($demande->statut === 'En attente') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $demande->statut }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Aucune demande</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
