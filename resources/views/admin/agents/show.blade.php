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
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                            @if(isset($correspond))
                                @if($correspond)
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">✓ Correspond SAP</span>
                                @elseif($agentSap)
                                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">⚠ Diffère de SAP</span>
                                @else
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">✗ Absent dans SAP</span>
                                @endif
                            @endif
                        </div>
                        <div class="p-4">
                            @if(isset($differences) && $differences)
                            <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                <h4 class="text-sm font-medium text-orange-900 mb-2">⚠ Différences détectées avec SAP</h4>
                                <dl class="space-y-1 text-sm">
                                    @foreach($differences as $champ => $vals)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ ucfirst($champ) }} :</span>
                                            <span>
                                                <span class="line-through text-red-600">{{ $vals['cgs'] ?? 'N/A' }}</span>
                                                <span class="mx-1">→</span>
                                                <span class="text-green-600 font-medium">{{ $vals['sap'] ?? 'N/A' }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                            @endif
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
                                    <dt class="text-sm text-gray-500">Nom complet</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $agent->nom_complet }}
                                        @if(isset($differences['nom']))
                                            <span class="text-xs text-orange-600 ml-2">(SAP: {{ $differences['nom']['sap'] }})</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Date de naissance</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $agent->date_naissance?->format('d/m/Y') ?? 'N/A' }}
                                        @if(isset($differences['date_naissance']))
                                            <span class="text-xs text-orange-600 ml-2">(SAP: {{ is_string($differences['date_naissance']['sap']) ? $differences['date_naissance']['sap'] : $differences['date_naissance']['sap']->format('d/m/Y') }})</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Âge</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->age ?? 'N/A' }} ans</dd>
                                </div>
                            </dl>
                            @if(isset($agentSap))
                            <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                <p>Dernier import SAP: {{ $agentSap->date_import_sap?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                <p>Fichier: {{ $agentSap->fichier_import ?? 'N/A' }}</p>
                            </div>
                            @endif
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
                                    <dt class="text-sm text-gray-500">Date de recrutement</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->date_recrutement?->format('d/m/Y') ?? 'N/A' }}</dd>
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
                            </dl>
                        </div>
                    </div>

                    <!-- Coordonnées bancaires -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Coordonnées bancaires</h3>
                        </div>
                        <div class="p-4">
                            <dl class="grid grid-cols-2 gap-4">
                                @if($agent->banque)
                                <div>
                                    <dt class="text-sm text-gray-500">Banque</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->banque }}</dd>
                                </div>
                                @endif
                                @if($agent->compte_bancaire)
                                <div>
                                    <dt class="text-sm text-gray-500">Compte bancaire</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->compte_bancaire }}</dd>
                                </div>
                                @endif
                                @if($agent->cle_bancaire)
                                <div>
                                    <dt class="text-sm text-gray-500">Clé bancaire</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->cle_bancaire }}</dd>
                                </div>
                                @endif
                                @if($agent->info_banque)
                                <div class="col-span-2">
                                    <dt class="text-sm text-gray-500">Informations bancaires</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $agent->info_banque }}</dd>
                                </div>
                                @endif
                            </dl>
                            @if(!$agent->banque && !$agent->compte_bancaire && !$agent->cle_bancaire && !$agent->info_banque)
                            <p class="text-sm text-gray-500 text-center py-2">Aucune information bancaire enregistrée</p>
                            @endif
                        </div>
                    </div>

                    <!-- Ayants droit -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Ayants droit</h3>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-500">{{ $agent->ayantsDroit->count() }} inscrit(s)</span>
                                <a href="{{ route('admin.agents.ayants-droit.create', $agent) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                    + Ajouter
                                </a>
                            </div>
                        </div>
                        <div class="p-4">
                            @if($agent->ayantsDroit->count() > 0)
                                <div class="space-y-2">
                                    @foreach($agent->ayantsDroit as $ayantDroit)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $ayantDroit->nom_prenom }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ ucfirst($ayantDroit->type) }}
                                                    @if($ayantDroit->date_naissance) - {{ $ayantDroit->date_naissance->format('d/m/Y') }} ({{ $ayantDroit->age ?? '?' }} ans) @endif
                                                    @if($ayantDroit->cin) - CIN: {{ $ayantDroit->cin }} @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($ayantDroit->statut === 'Validé') bg-green-100 text-green-800
                                                    @elseif($ayantDroit->statut === 'En attente') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $ayantDroit->statut }}
                                                </span>
                                                <!-- Actions rapides -->
                                                <div class="flex items-center gap-1">
                                                    @if($ayantDroit->statut !== 'Validé')
                                                    <form method="POST" action="{{ route('admin.ayants-droit.valider', $ayantDroit) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 text-green-600 hover:bg-green-100 rounded" title="Valider">
                                                            ✓
                                                        </button>
                                                    </form>
                                                    @endif
                                                    @if($ayantDroit->statut !== 'En attente')
                                                    <form method="POST" action="{{ route('admin.ayants-droit.mettre-en-attente', $ayantDroit) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 text-yellow-600 hover:bg-yellow-100 rounded" title="Mettre en attente">
                                                            ⏸
                                                        </button>
                                                    </form>
                                                    @endif
                                                    @if($ayantDroit->statut !== 'Rejeté')
                                                    <form method="POST" action="{{ route('admin.ayants-droit.rejeter', $ayantDroit) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Rejeter">
                                                            ✗
                                                        </button>
                                                    </form>
                                                    @endif
                                                    <a href="{{ route('admin.ayants-droit.edit', $ayantDroit) }}" class="p-1 text-blue-600 hover:bg-blue-100 rounded" title="Modifier">
                                                        ✎
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.ayants-droit.destroy', $ayantDroit) }}" class="inline" onsubmit="return confirm('Supprimer cet ayant droit ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Supprimer">
                                                            🗑
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-sm text-gray-500 mb-3">Aucun ayant droit enregistré</p>
                                    <a href="{{ route('admin.agents.ayants-droit.create', $agent) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                        + Ajouter un ayant droit
                                    </a>
                                </div>
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
