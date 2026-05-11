@extends('admin.layouts.app')

@section('title', 'Nouvelle Demande PEC')

@section('header', 'Nouvelle Demande de Prise en Charge')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Informations -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p>Remplissez les informations pour créer une nouvelle demande de prise en charge.</p>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('dprh.demandes.store') }}" id="demandeForm">
            @csrf

            <div class="space-y-6">
                <!-- Section Agent -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Agent / Bénéficiaire</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de bénéficiaire *</label>
                            <select name="type_beneficiaire" id="type_beneficiaire" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="agent">Agent</option>
                                <option value="ayant_droit">Ayant droit</option>
                            </select>
                        </div>

                        <div id="agent-search-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher un agent *</label>
                            <input type="text" id="agent_search" placeholder="Matricule ou nom..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <input type="hidden" name="agent_id" id="agent_id">
                            <div id="agent_results" class="mt-1 bg-white border rounded-lg hidden max-h-40 overflow-y-auto"></div>
                        </div>

                        <div id="ayant_droit_group" class="hidden md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ayant droit</label>
                            <select name="ayant_droit_id" id="ayant_droit_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner d'abord un agent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Infos agent affichées -->
                    <div id="agent_info" class="mt-4 hidden p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <span id="info_matricule" class="font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Nom:</span>
                                <span id="info_nom" class="font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Catégorie:</span>
                                <span id="info_categorie" class="font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Plafond disponible:</span>
                                <span id="info_plafond" class="font-medium text-green-600">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Partenaire -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Partenaire Médical</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                            <select name="ville" id="ville" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner...</option>
                                @foreach($villes as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de structure *</label>
                            <select name="type_structure" id="type_structure" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner...</option>
                                <option value="clinique">Clinique</option>
                                <option value="laboratoire">Laboratoire</option>
                                <option value="médecin">Médecin</option>
                                <option value="radiologie">Radiologie</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Partenaire *</label>
                            <select name="partenaire_id" id="partenaire_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner d'abord ville et type</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section Détails de la prestation -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la Prestation</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date du soin *</label>
                            <input type="date" name="date_soin" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant du devis (DH) *</label>
                            <input type="number" name="montant_devis" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de prestation</label>
                            <select name="type_prestation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="consultation">Consultation</option>
                                <option value="analyse">Analyse</option>
                                <option value="radiologie">Radiologie</option>
                                <option value="medicament">Médicament</option>
                                <option value="chirurgie">Chirurgie</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description / Diagnostic</label>
                            <textarea name="description" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Description de la prestation..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section Documents -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Documents</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Devis / Facture (PDF, Image)</label>
                        <input type="file" name="fichier_devis"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('dprh.demandes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Créer la demande
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Type de bénéficiaire
    document.getElementById('type_beneficiaire').addEventListener('change', function() {
        const havingDroitGroup = document.getElementById('ayant_droit_group');
        if (this.value === 'ayant_droit') {
            havingDroitGroup.classList.remove('hidden');
        } else {
            havingDroitGroup.classList.add('hidden');
        }
    });

    // Recherche agent avec délai
    let searchTimeout;
    document.getElementById('agent_search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        const resultsDiv = document.getElementById('agent_results');

        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.agents.autocomplete') }}?search=${encodeURIComponent(query)}`, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(r => r.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length === 0) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    data.forEach(agent => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                        div.textContent = `${agent.matricule} - ${agent.nom_complet}`;
                        div.addEventListener('click', () => {
                            document.getElementById('agent_id').value = agent.id;
                            document.getElementById('agent_search').value = `${agent.matricule} - ${agent.nom_complet}`;
                            resultsDiv.classList.add('hidden');

                            // Afficher infos agent
                            document.getElementById('info_matricule').textContent = agent.matricule;
                            document.getElementById('info_nom').textContent = agent.nom_complet;
                            document.getElementById('info_categorie').textContent = agent.categorie || '-';
                            document.getElementById('info_plafond').textContent = agent.plafond_restant || '-';
                            document.getElementById('agent_info').classList.remove('hidden');

                            // Charger ayants droit
                            loadAyantsDroit(agent.id);
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                });
        }, 300);
    });

    // Charger les ayants droit
    function loadAyantsDroit(agentId) {
        console.log('Chargement ayants droit pour agent ID:', agentId);

        const url = `{{ route('dprh.demandes.api.ayants-droit') }}?agent_id=${agentId}`;
        console.log('URL appelée:', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => {
            console.log('Response status:', r.status);
            if (!r.ok) {
                return r.text().then(text => {
                    console.error('Erreur réponse:', text);
                    throw new Error(`HTTP ${r.status}: ${text}`);
                });
            }
            return r.json();
        })
        .then(data => {
            console.log('Ayants droit reçus:', data);
            const select = document.getElementById('ayant_droit_id');
            select.innerHTML = '<option value="">Sélectionner un ayant droit</option>';
            if (data.length === 0) {
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "Aucun ayant droit";
                option.disabled = true;
                select.appendChild(option);
            } else {
                data.forEach(ad => {
                    const option = document.createElement('option');
                    option.value = ad.id;
                    option.textContent = `${ad.nom_complet} (${ad.lien_parente})`;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Erreur chargement ayants droit:', error);
            const select = document.getElementById('ayant_droit_id');
            select.innerHTML = '<option value="">Erreur lors du chargement</option>';
        });
    }

    // Chargement des partenaires
    function loadPartenaires() {
        const ville = document.getElementById('ville').value;
        const type = document.getElementById('type_structure').value;

        if (!ville || !type) {
            document.getElementById('partenaire_id').innerHTML = '<option value="">Sélectionner d\'abord ville et type</option>';
            return;
        }

        fetch(`{{ route('admin.partenaires.api.par-ville-type') }}?ville=${encodeURIComponent(ville)}&type=${encodeURIComponent(type)}`)
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('partenaire_id');
                select.innerHTML = '<option value="">Sélectionner un partenaire</option>';
                data.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.id;
                    option.textContent = p.nom;
                    select.appendChild(option);
                });
            });
    }

    document.getElementById('ville').addEventListener('change', loadPartenaires);
    document.getElementById('type_structure').addEventListener('change', loadPartenaires);

    // Fermer résultats si clic ailleurs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#agent_search') && !e.target.closest('#agent_results')) {
            document.getElementById('agent_results').classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
