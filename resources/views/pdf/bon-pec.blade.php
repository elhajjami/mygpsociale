<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Prise en Charge - SRM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            line-height: 1.2;
            background: white;
        }

        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        /* En-tête */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header p {
            font-size: 13px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 8px 0;
        }

        .header h2 {
            font-size: 28px;
            font-weight: bold;
            margin: 8px 0;
            text-decoration: underline;
        }

        /* Section renseignements */
        .section-title {
            background: #e8e8e8;
            padding: 5px 10px;
            font-weight: bold;
            margin: 8px 0 5px 0;
            border: 1px solid #333;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .numero-inline {
            font-weight: bold;
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
            font-size: 15px;
            height: 32px;
        }

        table .label {
            background: #f5f5f5;
            font-weight: bold;
            width: 14%;
            white-space: nowrap;
            padding: 4px 6px;
        }

        table .value {
            font-weight: normal;
        }

        /* Section formation médicale */
        .formation-section {
            margin: 12px 0;
            border: 1px solid #333;
            padding: 10px;
        }

        .formation-section h4 {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        /* Tableau montants */
        .montants-table {
            width: 100%;
            border-collapse: collapse;
        }

        .montants-table td {
            border: 1px solid #333;
            padding: 8px 10px;
            font-size: 15px;
            height: 35px;
        }

        .montants-table .montant-label {
            text-align: right;
            padding-right: 15px;
            width: 60%;
        }

        .montants-table .montant-value {
            font-weight: bold;
            text-align: center;
            width: 120px;
        }

        /* Section visa */
        .visa-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .visa-box {
            width: 45%;
        }

        .visa-box h4 {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
        }

        .visa-row {
            display: flex;
            margin: 4px 0;
        }

        .visa-row .label {
            width: 60px;
            flex-shrink: 0;
            font-size: 14px;
        }

        .visa-row .input {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 24px;
            display: inline-block;
        }

        .visa-row .input.signature {
            height: 130px;
            min-height: 130px;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #000;
            background: #f9f9f9;
        }

        .footer p {
            margin: 3px 0;
            text-align: justify;
            font-size: 12px;
        }

        .footer-number {
            margin-top: 10px;
            font-size: 10px;
            color: #666;
        }

        /* Bordure */
        .border-outer {
            border: 3px solid #000;
            padding: 15px;
            margin: 12mm;
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
            <!-- En-tête -->
            <div class="header">
                <div class="header-logo">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo SRM">
                </div>
                <h1>Société Régionale Multi Services Fès-Meknès</h1>
                @if(isset($partenaire) && $partenaire->type_structure === 'clinique')
                    <h2>LETTRE DE PRISE EN CHARGE</h2>
                @else
                    <h2>BON DE PRISE EN CHARGE</h2>
                @endif
                <p>Renseignements individuels</p>
            </div>

            <!-- Section Agent -->
            <div class="section-title">
                DEMANDE DE PRISE EN CHARGE-
                <span class="numero-inline">N° {{ $numero_demande }}</span>
            </div>

            <table>
                <tr>
                    <td class="label">Type demande</td>
                    <td class="value">{{ $beneficiaire_type ?? 'Agent' }}</td>
                    <td class="label">N° Immatriculation</td>
                    <td class="value">{{ $agent->matricule ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">Nom et Prénom</td>
                    <td class="value">{{ $agent->nom ?? '' }} {{ $agent->prenom ?? '' }}</td>
                    <td class="label">N° Affiliation</td>
                    <td class="value">{{ $agent->matricule ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">Mle</td>
                    <td class="value">{{ $agent->matricule ?? '' }}</td>
                    <td class="label">Date de naissance</td>
                    <td class="value">{{ $agent_date_naissance }}</td>
                </tr>
                <tr>
                    <td class="label">Service</td>
                    <td class="value" colspan="3">{{ $agent->dp_affectation ?? '-' }}</td>
                </tr>
            </table>

            <!-- Section Patient -->
            <div class="section-title">BÉNÉFICIAIRE</div>

            <table>
                <tr>
                    <td class="label">Nom et Prénom du patient</td>
                    <td class="value">{{ $beneficiaire_nom }}</td>
                    <td class="label">N° CIN</td>
                    <td class="value">{{ $agent->cin ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Lien de parenté avec l'agent</td>
                    <td class="value">{{ $lien_parente }}</td>
                    <td class="label">Date de naissance</td>
                    <td class="value">{{ $beneficiaire_date_naissance ?? '-' }}</td>
                </tr>
            </table>

            <!-- Section Prestation -->
            <div class="section-title">PRESTATION MEDICALE</div>

            <table>
                <tr>
                    <td class="label">Nature des examens</td>
                    <td class="value" colspan="3">
                        <strong>{{ $type_soin }}</strong><br>
                        {{ $description ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Partenaire Médical</td>
                    <td class="value" colspan="3">
                        <strong>{{ $partenaire->nom }}</strong><br>
                        {{ $partenaire->type_structure }} - {{ $partenaire->ville }}
                        @if($partenaire->specialite)
                            <br>Spécialité: {{ $partenaire->specialite }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Date du soin</td>
                    <td class="value">{{ $date_soin }}</td>
                    <td class="label">Montant Devis</td>
                    <td class="value">{{ $montant_devis }} DH</td>
                </tr>
            </table>

            <!-- Case formation médicale -->
            <div class="formation-section">
                <h4>CASE RÉSERVÉE À LA FORMATION MÉDICALE</h4>
                <table class="montants-table">
                    <tr>
                        <td class="montant-label">Montant brut de la Prestation:</td>
                        <td class="montant-value">{{ $montant_devis }} DH</td>
                    </tr>
                    <tr>
                        <td class="montant-label">Déduction (<span id="deduction-pct">0</span>%):</td>
                        <td class="montant-value">
                            <span style="border-bottom: 1px solid #333; display: inline-block; width: 100px; min-height: 20px;"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="montant-label" style="background: #e8e8e8; font-size: 14px;">
                            <strong>Montant net à prendre en charge par SRM:</strong>
                        </td>
                        <td class="montant-value" style="background: #e8e8e8; font-size: 14px;">
                            <strong>{{ $montant_devis }} DH</strong>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Section Visa -->
            <div class="visa-section">
                <div class="visa-box">
                    <h4>VISA FORMATION MÉDICALE</h4>
                    <div class="visa-row">
                        <span class="label">Date:</span>
                        <span class="input"></span>
                    </div>
                    <div class="visa-row">
                        <span class="label">Signature:</span>
                        <span class="input signature"></span>
                    </div>
                </div>

                <div class="visa-box">
                    <h4>VISA RESPONSABLE SRM</h4>
                    <div class="visa-row">
                        <span class="label">Date:</span>
                        <span class="input">{{ $date_generation }}</span>
                    </div>
                    <div class="visa-row">
                        <span class="label">Signature:</span>
                        <span class="input signature"></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>IMPORTANT:</strong></p>
                <p>1. La vérification par le cabinet de l'identité du bénéficiaire de la présente prise en charge est exigée.</p>
                <p>2. La présente prise en charge doit obligatoirement accompagner la facture, établie en 4 exemplaires originaux signés et cachetés, qui sera adressée pour paiement à SRM.</p>
                <p>3. L'octroi de cette prise en charge ne signifie nullement la gratuité de la prestation. Il s'agit seulement d'une substitution de l'Employeur au paiement dans l'immédiat. La régularisation se fera ultérieurement.</p>
                <p>4. Toute prise en charge non utilisée doit être retournée à l'Employeur pour annulation.</p>
            </div>

            <div class="footer-number">
                Bon émis le: {{ $date_generation }}
            </div>
        </div>
    </div>
</body>
</html>
