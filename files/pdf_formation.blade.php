{{-- resources/views/invoice/pdf_formation.blade.php --}}
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

    /* En-tête deux colonnes */
    .header { display: table; width: 100%; margin-bottom: 14px; }
    .header-left, .header-right { display: table-cell; vertical-align: top; width: 50%; }
    .header-right { text-align: right; }

    .titre-formation {
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        border-bottom: 2px solid #1a1a1a;
        padding-bottom: 4px;
        display: inline-block;
    }

    .info-ligne { margin-bottom: 3px; }
    .info-label { color: #555; }

    /* Titre facture */
    .facture-titre {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: 1.5px solid #1a1a1a;
        padding: 6px 0;
        margin: 14px 0 4px 0;
    }
    .facture-meta {
        display: table;
        width: 100%;
        margin-bottom: 12px;
    }
    .facture-meta-left, .facture-meta-right {
        display: table-cell;
        font-size: 10px;
    }
    .facture-meta-right { text-align: right; }

    /* Tableau des prestations */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }
    thead tr {
        background-color: #1a1a1a;
        color: #fff;
    }
    thead th {
        padding: 5px 4px;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid #1a1a1a;
    }
    tbody tr { background: #fff; }
    tbody tr:nth-child(even) { background: #f7f7f7; }
    tbody td {
        padding: 4px;
        border: 1px solid #ccc;
        font-size: 9.5px;
        vertical-align: middle;
    }
    .td-center { text-align: center; }
    .td-right  { text-align: right; font-weight: 500; }

    /* Ligne total */
    .total-row td {
        background: #1a1a1a;
        color: #fff;
        font-weight: bold;
        font-size: 10px;
        padding: 5px 4px;
        border: 1px solid #1a1a1a;
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
    .arrete-titre { font-weight: bold; margin-bottom: 3px; }

    /* Signature */
    .signature {
        text-align: right;
        margin-top: 24px;
        font-size: 9.5px;
        color: #555;
    }
    .signature-box {
        display: inline-block;
        border: 1px solid #ccc;
        padding: 6px 20px;
        min-width: 140px;
        min-height: 50px;
        text-align: center;
        font-size: 9px;
        color: #888;
        margin-top: 6px;
        vertical-align: top;
    }
</style>
</head>
<body>

{{-- EN-TÊTE --}}
<div class="header">
    <div class="header-left">
        <div class="titre-formation">{{ strtoupper($nom_formation) }}</div>
        <div class="info-ligne"><span class="info-label">Adresse :</span> {{ $adresse }}, {{ $ville }}</div>
        <div class="info-ligne"><span class="info-label">Tél :</span> {{ $tel }}</div>
        <div class="info-ligne"><span class="info-label">N° C.N.S.S :</span> {{ $cnss }}</div>
        <div class="info-ligne"><span class="info-label">N° ICE :</span> {{ $ice }}</div>
    </div>
    <div class="header-right">
        <div class="info-ligne"><span class="info-label">Compte Bancaire (RIB) :</span><br><strong>{{ $rib }}</strong></div>
        <div class="info-ligne"><span class="info-label">Agence bancaire :</span> {{ $agence }}</div>
        <div class="info-ligne"><span class="info-label">N° Patente :</span> {{ $patente }}</div>
        <div class="info-ligne"><span class="info-label">N° Identification Fiscale :</span> {{ $id_fiscale }}</div>
    </div>
</div>

{{-- TITRE FACTURE --}}
<div class="facture-titre">Facture</div>
<div class="facture-meta">
    <div class="facture-meta-left">
        <strong>FACTURE N°</strong> {{ $numero_facture }}
    </div>
    <div class="facture-meta-right">
        <strong>DATE :</strong> {{ \Carbon\Carbon::parse($date_facture)->format('d/m/Y') }}
    </div>
</div>

{{-- TABLEAU DES PRESTATIONS --}}
<table>
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
        @foreach($lignes as $ligne)
        <tr>
            <td class="td-center">{{ $ligne['matricule'] ?? '' }}</td>
            <td>{{ $ligne['nom_prenom'] ?? '' }}</td>
            <td class="td-center">{{ $ligne['beneficiaire'] ?? '' }}</td>
            <td>{{ $ligne['nature_examen'] ?? '' }}</td>
            <td class="td-center">{{ $ligne['cotation'] ?? '' }}</td>
            <td class="td-right">{{ number_format($ligne['tarif_ttc'] ?? 0, 2, ',', ' ') }} DH</td>
        </tr>
        @endforeach

        {{-- Lignes vides pour compléter --}}
        @for($i = count($lignes); $i < 20; $i++)
        <tr style="height: 16px;">
            <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        @endfor
    </tbody>
    <tr class="total-row">
        <td colspan="5" style="text-align:right; padding-right: 8px;">TOTAL</td>
        <td class="td-right">{{ number_format($total ?? 0, 2, ',', ' ') }} DH</td>
    </tr>
</table>

{{-- ARRÊTÉ --}}
<div class="arrete">
    <div class="arrete-titre">Arrêtée la présente facture à la somme de :</div>
    <div>{{ $total_en_lettres ?? '' }}</div>
</div>

{{-- SIGNATURE --}}
<div class="signature">
    Signature et cachet
    <div class="signature-box">Signature &amp; Cachet</div>
</div>

</body>
</html>
