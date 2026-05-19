<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Formation Médicale - SRM</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .numero-inline {
            font-weight: bold;
            font-size: 13px;
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

        /* Ligne total */
        .total-row td {
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

        .arrete-titre {
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Signature */
        .signature-section {
            margin-top: 20px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            border: 1px solid #333;
            padding: 6px 20px;
            min-width: 140px;
            min-height: 50px;
            text-align: center;
            font-size: 11px;
            color: #666;
            vertical-align: top;
        }

        /* Footer partenaire */
        .partenaire-info {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #333;
            background: #f9f9f9;
            font-size: 11px;
        }

        .partenaire-grid {
            display: flex;
            gap: 20px;
        }

        .partenaire-col {
            flex: 1;
        }

        .partenaire-col p {
            margin: 2px 0;
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
                <h1>FACTURE FORMATION MÉDICALE</h1>
                <div class="header-separator">══════════════════════════════════════════════════════════════════════════════</div>
            </div>

            {{-- Infos facture --}}
            <div class="section-title">
                FACTURE
                <span class="numero-inline">N° {{ $numero }}</span>
            </div>

            <table>
                <tr>
                    <td class="label">Date Facture</td>
                    <td class="value">{{ $date_facture }}</td>
                    <td class="label">Partenaire</td>
                    <td class="value"><strong>{{ $partenaire_nom }}</strong></td>
                </tr>
            </table>

            {{-- TABLEAU DES PRESTATIONS --}}
            <div class="section-title">DÉTAIL DES ACTES</div>
            <table class="prestations">
                <thead>
                    <tr>
                        <th style="width:10%">Matricule</th>
                        <th style="width:18%">Nom & Prénom</th>
                        <th style="width:13%">Bénéficiaire</th>
                        <th style="width:32%">Nature d'examen</th>
                        <th style="width:10%">Cotation</th>
                        <th style="width:12%">Tarif TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lignes_actes as $ligne)
                    <tr>
                        <td class="td-center">{{ $ligne->matricule ?? '' }}</td>
                        <td>{{ $ligne->nom_patient ?? '' }}</td>
                        <td class="td-center">{{ $ligne->beneficiaire ?? '' }}</td>
                        <td>{{ $ligne->nature_acte }}</td>
                        <td class="td-center">{{ $ligne->cotation ?? '' }}</td>
                        <td class="td-right">{{ number_format($ligne->montant, 2, ',', ' ') }} DH</td>
                    </tr>
                    @endforeach

                    {{-- Lignes vides pour compléter --}}
                    @php $remainingLines = max(5, 15 - $lignes_actes->count()); @endphp
                    @for($i = 0; $i < $remainingLines; $i++)
                    <tr style="height: 20px;">
                        <td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                </tbody>
                <tr class="total-row">
                    <td colspan="5" style="text-align:right; padding-right: 10px;">TOTAL</td>
                    <td class="td-right">{{ $montant_ttc }} DH</td>
                </tr>
            </table>

            {{-- ARRÊTÉ --}}
            <div class="arrete">
                <div class="arrete-titre">Arrêtée la présente facture à la somme de :</div>
                <div>{{ $montant_lettres }}</div>
            </div>

            {{-- SIGNATURE --}}
            <div class="signature-section">
                <div style="font-size: 12px; margin-bottom: 5px;">Signature et cachet</div>
                <div class="signature-box">Signature & Cachet</div>
            </div>

            {{-- INFO PARTENAIRE --}}
            <div class="partenaire-info">
                <div class="partenaire-grid">
                    <div class="partenaire-col">
                        <p><strong>{{ $partenaire_nom }}</strong></p>
                        <p><strong>Adresse :</strong> {{ $partenaire_adresse }} {{ $partenaire_ville }}</p>
                        <p><strong>Tél :</strong> {{ $partenaire_tel }}</p>
                    </div>
                    <div class="partenaire-col">
                        <p><strong>RIB :</strong> {{ $partenaire_rib }}</p>
                        <p><strong>Agence :</strong> {{ $partenaire_agence }}</p>
                        <p><strong>ICE :</strong> {{ $partenaire_ice }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
