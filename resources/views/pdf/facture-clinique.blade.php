<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Clinique - SRM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.2;
            background: white;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
        }

        /* Bordure extérieure */
        .border-outer {
            border: 3px solid #000;
            padding: 15px;
            margin: 12mm;
        }

        /* En-tête */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 8px;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .header h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
            text-decoration: underline;
        }

        .header-separator {
            margin: 8px 0;
            font-size: 11px;
            letter-spacing: 2px;
        }

        /* Section titre */
        .section-title {
            background: #e8e8e8;
            padding: 5px 10px;
            font-weight: bold;
            margin: 8px 0 5px 0;
            border: 1px solid #333;
            font-size: 14px;
        }

        /* Tableau principal */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        table td, table th {
            border: 1px solid #333;
            padding: 4px 8px;
            vertical-align: middle;
            font-size: 13px;
            height: 28px;
        }

        table .label {
            background: #f5f5f5;
            font-weight: bold;
            width: 25%;
            white-space: nowrap;
        }

        table .value {
            font-weight: normal;
        }

        /* Tableau prestations */
        table.prestations thead th {
            background: #333;
            color: #fff;
            padding: 5px 8px;
            font-size: 11px;
            text-align: center;
            border: 1px solid #333;
        }

        table.prestations tbody td {
            border: 1px solid #333;
            padding: 4px 6px;
            font-size: 12px;
        }

        table.prestations .td-center {
            text-align: center;
        }

        table.prestations .td-right {
            text-align: right;
        }

        /* Section titre gris */
        .section-subtitle {
            background: #555;
            color: #fff;
            font-weight: bold;
            padding: 4px 8px;
            font-size: 12px;
            margin: 5px 0;
        }

        /* Lignes total */
        .total-inter td {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 13px;
        }

        .total-general td {
            background: #1a1a1a;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }

        /* Arrêté */
        .arrete {
            border: 1px solid #333;
            padding: 8px 10px;
            margin: 10px 0;
            font-size: 13px;
            background: #f9f9f9;
        }

        /* Parts */
        .parts-container {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }

        .parts-box {
            flex: 1;
            border: 1px solid #333;
            padding: 8px 10px;
            font-size: 13px;
        }

        /* Footer */
        .footer {
            margin-top: 12px;
            padding: 10px;
            border: 1px solid #333;
            background: #f9f9f9;
            font-size: 11px;
        }

        .footer-grid {
            display: flex;
            gap: 20px;
        }

        .footer-col {
            flex: 1;
        }

        .footer p {
            margin: 2px 0;
        }

        .footer strong {
            font-weight: bold;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="border-outer">
            {{-- En-tête --}}
            <div class="header">
                <div class="header-logo">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo SRM">
                </div>
                <div class="header-separator">══════════════════════════════════════════════════════════════════════════════</div>
                <h1>FACTURE CLINIQUE</h1>
                <div class="header-separator">══════════════════════════════════════════════════════════════════════════════</div>
            </div>

            {{-- Infos facture --}}
            <table>
                <tr>
                    <td class="label">N° Facture</td>
                    <td class="value"><strong>{{ $numero }}</strong></td>
                    <td class="label">Date Facture</td>
                    <td class="value">{{ $date_facture }}</td>
                </tr>
            </table>

            {{-- Patient --}}
            <div class="section-title">RENSEIGNEMENTS PATIENT</div>
            <table>
                <tr>
                    <td class="label">Nom du patient</td>
                    <td class="value">{{ $nom_patient ?? '' }}</td>
                    <td class="label">Période</td>
                    <td class="value">Du {{ $hospitalisation_du ?? '' }} au {{ $hospitalisation_au ?? '' }}</td>
                </tr>
            </table>

            {{-- PRESTATIONS CLINIQUE --}}
            <div class="section-title">DÉSIGNATION DES PRESTATIONS</div>
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
                    @foreach($lignes_prestations as $ligne)
                    <tr>
                        <td>{{ $ligne->designation }}</td>
                        <td class="td-center">{{ $ligne->cotation ?? '' }}</td>
                        <td class="td-center">{{ number_format($ligne->quantite, 0, '', ' ') }}</td>
                        <td class="td-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }}</td>
                        <td class="td-right">{{ number_format($ligne->montant, 2, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($lignes_prestations->count() > 0)
                <tr class="total-inter">
                    <td colspan="4">Total Clinique</td>
                    <td class="td-right">{{ $montant_clinique ?? '0,00' }} DH</td>
                </tr>
                @endif

                {{-- Honoraires --}}
                @if($lignes_honoraires->count() > 0)
                <tr>
                    <td colspan="5" style="padding:0">
                        <div class="section-subtitle">Honoraires Médecins</div>
                    </td>
                </tr>
                @foreach($lignes_honoraires as $ligne)
                <tr>
                    <td>{{ $ligne->designation }}</td>
                    <td class="td-center">{{ $ligne->cotation ?? '' }}</td>
                    <td class="td-center">{{ number_format($ligne->quantite, 0, '', ' ') }}</td>
                    <td class="td-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }}</td>
                    <td class="td-right">{{ number_format($ligne->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
                <tr class="total-inter">
                    <td colspan="4">Total Honoraires</td>
                    <td class="td-right">{{ $montant_honoraires ?? '0,00' }} DH</td>
                </tr>
                @endif

                {{-- Labo / Anapath / Radio --}}
                @if($lignes_autres->count() > 0)
                <tr>
                    <td colspan="5" style="padding:0">
                        <div class="section-subtitle">Autres Prestations</div>
                    </td>
                </tr>
                @foreach($lignes_autres as $ligne)
                <tr>
                    <td>{{ $ligne->designation }}</td>
                    <td class="td-center">{{ $ligne->cotation ?? '' }}</td>
                    <td class="td-center">{{ number_format($ligne->quantite, 0, '', ' ') }}</td>
                    <td class="td-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }}</td>
                    <td class="td-right">{{ number_format($ligne->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
                <tr class="total-inter">
                    <td colspan="4">Total Autres Prestations</td>
                    <td class="td-right">{{ $montant_autres ?? '0,00' }} DH</td>
                </tr>
                @endif

                <tr class="total-general">
                    <td colspan="4" style="text-align:right; padding-right: 10px;">TOTAL GÉNÉRAL</td>
                    <td class="td-right">{{ $montant_ttc }} DH</td>
                </tr>
            </table>

            {{-- ARRÊTÉ --}}
            <div class="arrete">
                <strong>Arrêtée la présente facture à la somme de :</strong><br>
                {{ $montant_lettres }}
            </div>

            {{-- PARTS --}}
            <div class="parts-container">
                <div class="parts-box">
                    <strong>Part Adhérent :</strong> {{ $part_adherent ?? '..............................' }}
                </div>
                <div class="parts-box">
                    <strong>Part CNOPS :</strong> {{ $part_cnops ?? '..............................' }}
                </div>
            </div>
            @if(isset($part_assurance) && $part_assurance && $part_assurance !== '..............................')
            <div class="parts-container">
                <div class="parts-box" style="text-align:center;">
                    <strong>Part Assurance Complémentaire :</strong> {{ $part_assurance }}
                </div>
            </div>
            @endif

            {{-- PIED DE PAGE --}}
            <div class="footer">
                <div class="footer-grid">
                    <div class="footer-col">
                        <p><strong>{{ $partenaire_nom }}</strong></p>
                        <p><strong>Adresse :</strong> {{ $partenaire_adresse }} {{ $partenaire_ville }}</p>
                        <p><strong>Tél :</strong> {{ $partenaire_tel }}</p>
                        <p><strong>RIB :</strong> {{ $partenaire_rib }}</p>
                        <p><strong>Agence :</strong> {{ $partenaire_agence }}</p>
                    </div>
                    <div class="footer-col">
                        <p><strong>Patente :</strong> {{ $partenaire_patente }}</p>
                        <p><strong>IF :</strong> {{ $partenaire_if }}</p>
                        <p><strong>CNSS :</strong> {{ $partenaire_cnss }}</p>
                        <p><strong>ICE :</strong> {{ $partenaire_ice }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
