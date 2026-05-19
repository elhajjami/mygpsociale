<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de la facturation
    |--------------------------------------------------------------------------
    */

    // Préfixe pour les numéros de facture
    'prefixe' => env('FACTURE_PREFIXE', 'FAC-'),

    // Début de la série numérotique
    'debut_serie' => env('FACTURE_DEBUT_SERIE', 1000),

    // Nombre de jours pour l'échéance par défaut
    'echeance_jours' => env('FACTURE_ECHEANCE_JOURS', 30),

    // Conditions de paiement par défaut
    'conditions_paiement' => env('FACTURE_CONDITIONS', 'Paiement à réception de facture'),

    // Informations de l'entreprise émettrice
    'entreprise' => [
        'nom' => env('FACTURE_ENTREPRISE_NOM', 'CGS - Caisse Générale de Sécurité Sociale'),
        'adresse' => env('FACTURE_ENTREPRISE_ADRESSE', ''),
        'ville' => env('FACTURE_ENTREPRISE_VILLE', ''),
        'telephone' => env('FACTURE_ENTREPRISE_TEL', ''),
        'fax' => env('FACTURE_ENTREPRISE_FAX', ''),
        'email' => env('FACTURE_ENTREPRISE_EMAIL', ''),
        'ice' => env('FACTURE_ENTREPRISE_ICE', ''),
        'rc' => env('FACTURE_ENTREPRISE_RC', ''),
        'patente' => env('FACTURE_ENTREPRISE_PATENTE', ''),
        'if' => env('FACTURE_ENTREPRISE_IF', ''),
        'cnss' => env('FACTURE_ENTREPRISE_CNSS', ''),
        'rib' => env('FACTURE_ENTREPRISE_RIB', ''),
        'banque' => env('FACTURE_ENTREPRISE_BANQUE', ''),
        'agence' => env('FACTURE_ENTREPRISE_AGENCE', ''),
    ],

    // Configuration TVA (si applicable)
    'tva' => [
        'taux' => env('FACTURE_TVA_TAUX', 0), // 0 = pas de TVA pour les soins médicaux
        'applicable' => env('FACTURE_TVA_APPLICABLE', false),
    ],

    // Options PDF
    'pdf' => [
        'logo' => public_path('images/logo.png'),
        'font' => 'Times New Roman',
        'dpi' => 150,
    ],
];
