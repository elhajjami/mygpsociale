@extends('admin.layouts.app')

@section('title', 'Facture ' . $facture->numero)

@section('header', 'Facture N° ' . $facture->numero)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('dprh.facturation.telecharger', $facture) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Télécharger PDF
        </a>
        @if($facture->peutEtreModifiee())
        <form method="POST" action="{{ route('dprh.facturation.destroy', $facture) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Supprimer
            </button>
        </form>
        @endif
    </div>

    <!-- Informations générales -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $facture->numero }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Créée le {{ $facture->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    @if($facture->type_facture === 'medical')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Formation Médicale
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            Facture Clinique
                        </span>
                    @endif
                    {!! $facture->statut_badge !!}
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3">Informations facture</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date de facturation:</span>
                        <span class="font-medium">{{ $facture->date_facture->format('d/m/Y') }}</span>
                    </div>
                    @if($facture->date_echeance)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date d'échéance:</span>
                        <span class="font-medium">{{ $facture->date_echeance->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($facture->demandePec)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Référence PEC:</span>
                        <span class="font-medium">{{ $facture->demandePec->numero_demande }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3">Partenaire</h4>
                @if($facture->partenaire)
                <div class="space-y-2">
                    <p class="font-medium text-gray-900">{{ $facture->partenaire->nom }}</p>
                    <p class="text-sm text-gray-600">{{ $facture->partenaire->adresse }} {{ $facture->partenaire->ville }}</p>
                    <p class="text-sm text-gray-600">Tél: {{ $facture->partenaire->telephone }}</p>
                    @if($facture->partenaire->ice)
                    <p class="text-sm text-gray-600">ICE: {{ $facture->partenaire->ice }}</p>
                    @endif
                </div>
                @else
                <p class="text-gray-500">Non renseigné</p>
                @endif
            </div>
        </div>

        @if($facture->type_facture === 'clinique')
        <div class="px-6 pb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-3">Hospitalisation</h4>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600">Patient:</p>
                    <p class="font-medium">{{ $facture->nom_patient ?? '-' }}</p>
                </div>
                @if($facture->hospitalisation_du)
                <div>
                    <p class="text-gray-600">Du:</p>
                    <p class="font-medium">{{ $facture->hospitalisation_du->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($facture->hospitalisation_au)
                <div>
                    <p class="text-gray-600">Au:</p>
                    <p class="font-medium">{{ $facture->hospitalisation_au->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Lignes de facturation -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Détail de la facture</h3>

            @if($facture->type_facture === 'medical')
            <!-- Type Medical -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bénéficiaire</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nature acte</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $actes = $facture->lignes->where('type_ligne', 'acte'); @endphp
                        @foreach($actes as $index => $ligne)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $ligne->matricule ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ligne->nom_patient ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ligne->beneficiaire ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ligne->nature_acte }}</td>
                            <td class="px-4 py-2">{{ $ligne->cotation ?? '-' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($ligne->montant, 2) }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <!-- Type Clinique -->
            <div class="space-y-6">
                @php $prestationsClinique = $facture->lignes->where('type_ligne', 'prestation_clinique'); @endphp
                @if($prestationsClinique->count() > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Prestations Clinique</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestationsClinique as $ligne)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $ligne->designation }}</td>
                                    <td class="px-4 py-2">{{ $ligne->cotation ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ number_format($ligne->quantite, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($ligne->montant, 2) }} DH</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-2 text-right font-medium">Total Clinique:</td>
                                    <td class="px-4 py-2 text-right font-bold">{{ number_format($facture->montant_clinique, 2) }} DH</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                @php $honoraires = $facture->lignes->where('type_ligne', 'honoraire'); @endphp
                @if($honoraires->count() > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Honoraires Médecins</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($honoraires as $ligne)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $ligne->designation }}</td>
                                    <td class="px-4 py-2">{{ $ligne->cotation ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ number_format($ligne->quantite, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($ligne->montant, 2) }} DH</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-2 text-right font-medium">Total Honoraires:</td>
                                    <td class="px-4 py-2 text-right font-bold">{{ number_format($facture->montant_honoraires, 2) }} DH</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                @php $autres = $facture->lignes->where('type_ligne', 'autre'); @endphp
                @if($autres->count() > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Autres Prestations</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cotation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autres as $ligne)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $ligne->designation }}</td>
                                    <td class="px-4 py-2">{{ $ligne->cotation ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ number_format($ligne->quantite, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($ligne->montant, 2) }} DH</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-2 text-right font-medium">Total Autres:</td>
                                    <td class="px-4 py-2 text-right font-bold">{{ number_format($facture->montant_autres, 2) }} DH</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Total -->
            <div class="mt-6 flex justify-end">
                <div class="w-80">
                    <div class="bg-blue-50 rounded-lg p-4">
                        @if($facture->type_facture === 'clinique')
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Parts de prise en charge</h4>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Général:</span>
                                <span class="font-medium">{{ number_format($facture->montant_ttc, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Part CNOPS:</span>
                                <span class="font-medium">{{ $facture->part_cnops ? number_format($facture->part_cnops, 2) : '0.00' }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Part Assurance Compl.:</span>
                                <span class="font-medium">{{ $facture->part_assurance ? number_format($facture->part_assurance, 2) : '0.00' }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold border-t pt-2">
                                <span>Part Adhérent:</span>
                                <span>{{ $facture->part_adherent ? number_format($facture->part_adherent, 2) : '0.00' }} DH</span>
                            </div>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t pt-3">
                            <span>Total TTC:</span>
                            <span>{{ number_format($facture->montant_ttc, 2) }} DH</span>
                        </div>
                        <div class="text-sm text-gray-600 mt-2">
                            {{ $facture->montantEnLettres() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conditions et observations -->
    @if($facture->conditions_reglement || $facture->observations)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($facture->conditions_reglement)
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Conditions de règlement</h4>
                <p class="text-gray-700">{{ $facture->conditions_reglement }}</p>
            </div>
            @endif
            @if($facture->observations)
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Observations</h4>
                <p class="text-gray-700">{{ $facture->observations }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <div class="flex justify-between">
        <a href="{{ route('dprh.facturation.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            ← Retour à la liste
        </a>
    </div>
</div>
@endsection
