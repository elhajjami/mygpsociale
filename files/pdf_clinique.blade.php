{{-- resources/views/invoice/pdf_clinique.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 10px;
        color: #1a1a1a;
        background: #fff;
        padding: 20px 28px;
    }

    /* En-tête */
    .header {
        display: table; width: 100%;
        border-bottom: 2px solid #1a1a1a;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .header-left, .header-right {
        display: table-cell; vertical-align: middle;
    }
    .header-right { text-align: right; }
    .clinique-nom {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    .clinique-ice {
        font-size: 9px;
        color: #555;
        margin-top: 2px;
    }
    .facture-badge {
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #1a1a1a;
    }

    /* Tableau patient */
    .table-patient {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }
    .table-patient th {
        background: #1a1a1a;
        color: #fff;
        padding: 5px 8px;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid #1a1a1a;
        text-align: center;
    }
    .table-patient td {
        border: 1px solid #ccc;
        padding: 5px 8px;
        font-size: 9.5px;
        text-align: center;
    }

    /* Section titre */
    .section-titre {
        background: #1a1a1a;
        color: #fff;
        font-weight: bold;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 6px;
        margin-top: 6px;
    }
    .section-titre-gray {
        background: #555;
        color: #fff;
        font-weight: bold;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 6px;
    }

    /* Tableau prestations */
    table.prestations {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    table.prestations th {
        background: #333;
        color: #fff;
        padding: 4px 5px;
        font-size: 8.5px;
        text-transform: uppercase;
        border: 1px solid #333;
        text-align: center;
    }
    table.prestations td {
        border: 1px solid #ddd;
        padding: 4px 5px;
        font-size: 9.5px;
        vertical-align: middle;
    }
    .td-center { text-align: center; }
    .td-right  { text-align: right; font-weight: 500; }

    /* Lignes total intermédiaires */
    .total-inter td {
        background: #e8e8e8;
        font-weight: bold;
        font-size: 10px;
        padding: 5px 5px;
        border: 1px solid #bbb;
        text-align: right;
    }
    .total-general td {
        background: #1a1a1a;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        padding: 6px 5px;
        border: 1px solid #1a1a1a;
        text-align: right;
    }

    /* Arrêté */
    .arrete {
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 8px 10px;
        margin: 10px 0;
        font-size: 9.5px;
        background: #fafafa;
    }

    /* Parts */
    .parts {
        display: table;
        width: 100%;
        margin: 8px 0;
    }
    .part-cell {
        display: table-cell;
        width: 50%;
        padding: 5px 8px;
        border: 1px solid #ccc;
        font-size: 9.5px;
    }

    /* Pied de page */
    .footer {
        margin-top: 12px;
        border-top: 1px solid #ccc;
        padding-top: 8px;
        font-size: 8.5px;
        color: #555;
    }
    .footer-grid { display: table; width: 100%; }
    .footer-col { display: table-cell; vertical-align: top; width: 50%; }
    .footer-col:last-child { text-align: right; }
</style>
</head>
<body>

{{-- EN-TÊTE --}}
<div class="header">
    <div class="header-left">
        <div class="clinique-nom">{{ $nom_formation }}</div>
        <div class="clinique-ice">ICE : {{ $ice }}</div>
    </div>
    <div class="header-right">
        <div class="facture-badge">Facture</div>
    </div>
</div>

{{-- TABLEAU PATIENT --}}
<table class="table-patient">
    <thead>
        <tr>
            <th>Numéro Facture</th>
            <th>Date Facture</th>
            <th>Nom du patient</th>
            <th colspan="2">Période d'hospitalisation</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>{{ $numero_facture }}</strong></td>
            <td>{{ \Carbon\Carbon::parse($date_facture)->format('d/m/Y') }}</td>
            <td>{{ $nom_patient ?? '' }}</td>
            <td>Du : {{ isset($date_hospitalisation_debut) ? \Carbon\Carbon::parse($date_hospitalisation_debut)->format('d/m/Y') : '' }}</td>
            <td>Au : {{ isset($date_hospitalisation_fin) ? \Carbon\Carbon::parse($date_hospitalisation_fin)->format('d/m/Y') : '' }}</td>
        </tr>
    </tbody>
</table>

{{-- PRESTATIONS CLINIQUE --}}
<div class="section-titre">Désignation des prestations</div>
<table class="prestations">
    <thead>
        <tr>
            <th style="width:40%">Désignation</th>
            <th style="width:12%">Lettre Clé</th>
            <th style="width:12%">Nbre</th>
            <th style="width:18%">Prix Unitaire</th>
            <th style="width:18%">Montant DH</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Séjour</td>
            <td class="td-center"></td>
            <td class="td-center">{{ $sejour['nbre'] ?? '' }}</td>
            <td class="td-right">{{ isset($sejour['prix_unitaire']) ? number_format($sejour['prix_unitaire'], 2, ',', ' ') : '' }}</td>
            <td class="td-right">{{ isset($sejour['montant']) ? number_format($sejour['montant'], 2, ',', ' ') : '' }}</td>
        </tr>
        <tr>
            <td>Bloc Opératoire</td>
            <td class="td-center">{{ $bloc_operatoire['lettre_cle'] ?? 'K' }}</td>
            <td class="td-center">{{ $bloc_operatoire['nbre'] ?? '' }}</td>
            <td class="td-right">{{ isset($bloc_operatoire['prix_unitaire']) ? number_format($bloc_operatoire['prix_unitaire'], 2, ',', ' ') : '' }}</td>
            <td class="td-right">{{ isset($bloc_operatoire['montant']) ? number_format($bloc_operatoire['montant'], 2, ',', ' ') : '' }}</td>
        </tr>
        @if(!empty($prestations_diverses))
            @foreach($prestations_diverses as $p)
            <tr>
                <td>{{ $p['designation'] ?? '' }}</td>
                <td class="td-center">{{ $p['lettre_cle'] ?? '' }}</td>
                <td class="td-center">{{ $p['nbre'] ?? '' }}</td>
                <td class="td-right">{{ isset($p['prix_unitaire']) ? number_format($p['prix_unitaire'], 2, ',', ' ') : '' }}</td>
                <td class="td-right">{{ isset($p['montant']) ? number_format($p['montant'], 2, ',', ' ') : '' }}</td>
            </tr>
            @endforeach
        @endif
        <tr>
            <td>Pharmacie (Médicale/Chirurgicale)</td>
            <td class="td-center"></td>
            <td class="td-center">{{ $pharmacie['nbre'] ?? '' }}</td>
            <td class="td-right">{{ isset($pharmacie['prix_unitaire']) ? number_format($pharmacie['prix_unitaire'], 2, ',', ' ') : '' }}</td>
            <td class="td-right">{{ isset($pharmacie['montant']) ? number_format($pharmacie['montant'], 2, ',', ' ') : '' }}</td>
        </tr>
    </tbody>
    <tr class="total-inter">
        <td colspan="4">Total Clinique</td>
        <td>{{ isset($total_clinique) ? number_format($total_clinique, 2, ',', ' ') . ' DH' : '' }}</td>
    </tr>

    {{-- Honoraires --}}
    <tr><td colspan="5" style="padding:0"><div class="section-titre-gray">Honoraires Médecins</div></td></tr>
    <tr>
        <td>Médecin (Chirurgien)</td>
        <td class="td-center">{{ $honoraires['chirurgien']['lettre_cle'] ?? 'K' }}</td>
        <td class="td-center">{{ $honoraires['chirurgien']['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($honoraires['chirurgien']['prix_unitaire']) ? number_format($honoraires['chirurgien']['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($honoraires['chirurgien']['montant']) ? number_format($honoraires['chirurgien']['montant'], 2, ',', ' ') : '' }}</td>
    </tr>
    <tr>
        <td>Médecin (Anesthésiste)</td>
        <td class="td-center">{{ $honoraires['anesthesiste']['lettre_cle'] ?? 'K' }}</td>
        <td class="td-center">{{ $honoraires['anesthesiste']['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($honoraires['anesthesiste']['prix_unitaire']) ? number_format($honoraires['anesthesiste']['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($honoraires['anesthesiste']['montant']) ? number_format($honoraires['anesthesiste']['montant'], 2, ',', ' ') : '' }}</td>
    </tr>
    <tr>
        <td>Autres Médecins</td>
        <td class="td-center">{{ $honoraires['autres']['lettre_cle'] ?? 'K' }}</td>
        <td class="td-center">{{ $honoraires['autres']['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($honoraires['autres']['prix_unitaire']) ? number_format($honoraires['autres']['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($honoraires['autres']['montant']) ? number_format($honoraires['autres']['montant'], 2, ',', ' ') : '' }}</td>
    </tr>

    {{-- Labo / Anapath / Radio --}}
    <tr><td colspan="5" style="padding:0"><div class="section-titre-gray">Autres Prestations</div></td></tr>
    <tr>
        <td>Laboratoire (Analyses)</td>
        <td class="td-center">{{ $laboratoire['lettre_cle'] ?? 'B' }}</td>
        <td class="td-center">{{ $laboratoire['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($laboratoire['prix_unitaire']) ? number_format($laboratoire['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($laboratoire['montant']) ? number_format($laboratoire['montant'], 2, ',', ' ') : '' }}</td>
    </tr>
    <tr>
        <td>Anapath</td>
        <td class="td-center">{{ $anapath['lettre_cle'] ?? 'P' }}</td>
        <td class="td-center">{{ $anapath['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($anapath['prix_unitaire']) ? number_format($anapath['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($anapath['montant']) ? number_format($anapath['montant'], 2, ',', ' ') : '' }}</td>
    </tr>
    <tr>
        <td>Radiologie (Examens)</td>
        <td class="td-center">{{ $radiologie['lettre_cle'] ?? 'K/Z' }}</td>
        <td class="td-center">{{ $radiologie['nbre'] ?? '' }}</td>
        <td class="td-right">{{ isset($radiologie['prix_unitaire']) ? number_format($radiologie['prix_unitaire'], 2, ',', ' ') : '' }}</td>
        <td class="td-right">{{ isset($radiologie['montant']) ? number_format($radiologie['montant'], 2, ',', ' ') : '' }}</td>
    </tr>

    <tr class="total-inter">
        <td colspan="4">Total Autres Prestations</td>
        <td>{{ isset($total_autres_prestations) ? number_format($total_autres_prestations, 2, ',', ' ') . ' DH' : '' }}</td>
    </tr>
    <tr class="total-general">
        <td colspan="4">TOTAL GÉNÉRAL</td>
        <td>{{ isset($total_general) ? number_format($total_general, 2, ',', ' ') . ' DH' : '' }}</td>
    </tr>
</table>

{{-- ARRÊTÉ --}}
<div class="arrete">
    <strong>Arrêtée la présente facture à la somme de :</strong><br>
    {{ $total_en_lettres ?? '' }}
</div>

{{-- PARTS --}}
<div class="parts">
    <div class="part-cell">
        <strong>Part Adhérent :</strong> {{ isset($part_adherent) ? number_format($part_adherent, 2, ',', ' ') . ' DH' : '..............................' }}
    </div>
    <div class="part-cell" style="text-align:right">
        <strong>Part CNOPS :</strong> {{ isset($part_cnops) ? number_format($part_cnops, 2, ',', ' ') . ' DH' : '..............................' }}
    </div>
</div>

{{-- PIED DE PAGE --}}
<div class="footer">
    <div class="footer-grid">
        <div class="footer-col">
            <strong>Adresse :</strong> {{ $adresse }}, {{ $ville }}<br>
            <strong>Tél :</strong> {{ $tel }}<br>
            <strong>Compte bancaire (RIB) :</strong> {{ $rib }}<br>
            <strong>Agence :</strong> {{ $agence }}
        </div>
        <div class="footer-col">
            <strong>Patente :</strong> {{ $patente }}<br>
            <strong>Identification Fiscale :</strong> {{ $id_fiscale }}<br>
            <strong>C.N.S.S :</strong> {{ $cnss }}<br>
            <strong>ICE :</strong> {{ $ice }}
        </div>
    </div>
</div>

</body>
</html>
