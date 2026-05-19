<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Afficher le formulaire de saisie
     */
    public function index()
    {
        return view('invoice.form');
    }

    /**
     * Générer la facture via LLM puis PDF
     */
    public function generate(Request $request)
    {
        $type = $request->input('type'); // 'medecin' | 'laboratoire' | 'radiologie' | 'clinique'

        // Validation commune
        $rules = [
            'type'          => 'required|in:medecin,laboratoire,radiologie,clinique',
            'nom_formation' => 'required|string|max:255',
            'adresse'       => 'required|string',
            'ville'         => 'required|string',
            'tel'           => 'required|string',
            'rib'           => 'required|string|size:24',
            'agence'        => 'required|string',
            'patente'       => 'required|string',
            'id_fiscale'    => 'required|string',
            'cnss'          => 'required|string',
            'ice'           => 'required|string',
            'date_facture'  => 'required|date',
            'numero_facture'=> 'required|string',
            'description'   => 'required|string', // texte libre décrivant les prestations
        ];

        // Champs supplémentaires pour clinique (formulaire 2)
        if ($type === 'clinique') {
            $rules['nom_patient']          = 'required|string';
            $rules['date_hospitalisation_debut'] = 'required|date';
            $rules['date_hospitalisation_fin']   = 'required|date';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Appel LLM pour structurer les données de facturation
        $structuredData = $this->callLLM($request->all());

        if (!$structuredData) {
            return back()->with('error', 'Erreur lors de la génération de la facture par l\'IA. Veuillez réessayer.');
        }

        // Génération du PDF selon le type
        $view   = $type === 'clinique' ? 'invoice.pdf_clinique' : 'invoice.pdf_formation';
        $data   = array_merge($request->all(), $structuredData);
        $pdf    = Pdf::loadView($view, $data)
                     ->setPaper('a4', 'portrait');

        $filename = 'facture_' . $request->input('numero_facture') . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Appel à l'API Anthropic pour structurer les lignes de facturation
     */
    private function callLLM(array $input): ?array
    {
        $type = $input['type'];
        $description = $input['description'];

        if ($type === 'clinique') {
            $prompt = $this->buildClinicPrompt($input);
        } else {
            $prompt = $this->buildFormationPrompt($input, $type);
        }

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-sonnet-4-20250514',
            'max_tokens' => 2000,
            'system'     => 'Tu es un assistant de facturation médicale au Maroc. Tu réponds UNIQUEMENT en JSON valide, sans markdown, sans explication.',
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if (!$response->successful()) {
            \Log::error('Anthropic API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        $content = $response->json('content.0.text');

        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            \Log::error('JSON parse error', ['content' => $content]);
            return null;
        }
    }

    /**
     * Prompt pour Formulaire 1 (médecin / laboratoire / radiologie)
     */
    private function buildFormationPrompt(array $input, string $type): string
    {
        $typeLabel = match($type) {
            'medecin'     => 'Médecin',
            'laboratoire' => 'Laboratoire',
            'radiologie'  => 'Radiologie',
            default       => 'Formation Médicale',
        };

        return <<<PROMPT
Voici les informations d'une facture médicale de type "{$typeLabel}" au Maroc.

Description des prestations fournies par le praticien :
"{$input['description']}"

À partir de cette description, génère un JSON structuré avec les lignes de facturation.
Chaque prestation doit avoir une cotation (code acte) et un tarif TTC en DH.

Réponds UNIQUEMENT avec ce JSON (sans markdown) :
{
  "lignes": [
    {
      "matricule": "string (matricule CNOPS/CNSS de l'assuré)",
      "nom_prenom": "string",
      "beneficiaire": "string (assuré ou ayant droit)",
      "nature_examen": "string (description de l'acte)",
      "cotation": "string (ex: C2, B10, Z5...)",
      "tarif_ttc": "number (en DH)"
    }
  ],
  "total": "number (somme totale en DH)",
  "total_en_lettres": "string (montant en toutes lettres en français, ex: Deux mille trois cents dirhams)"
}
PROMPT;
    }

    /**
     * Prompt pour Formulaire 2 (clinique)
     */
    private function buildClinicPrompt(array $input): string
    {
        return <<<PROMPT
Voici les informations d'une facture de clinique au Maroc.

Description des prestations fournies durant l'hospitalisation :
"{$input['description']}"

Nom du patient : {$input['nom_patient']}
Période d'hospitalisation : du {$input['date_hospitalisation_debut']} au {$input['date_hospitalisation_fin']}

À partir de cette description, génère un JSON structuré pour la facture de clinique.
Respecte les catégories de prestations CNOPS marocaines.

Réponds UNIQUEMENT avec ce JSON (sans markdown) :
{
  "sejour": { "nbre": "number", "prix_unitaire": "number", "montant": "number" },
  "bloc_operatoire": { "lettre_cle": "K", "nbre": "number", "prix_unitaire": "number", "montant": "number" },
  "prestations_diverses": [
    { "designation": "string", "lettre_cle": "string", "nbre": "number", "prix_unitaire": "number", "montant": "number" }
  ],
  "pharmacie": { "nbre": 1, "prix_unitaire": "number", "montant": "number" },
  "total_clinique": "number",
  "honoraires": {
    "chirurgien":    { "lettre_cle": "K", "nbre": "number", "prix_unitaire": "number", "montant": "number" },
    "anesthesiste":  { "lettre_cle": "K", "nbre": "number", "prix_unitaire": "number", "montant": "number" },
    "autres":        { "lettre_cle": "K", "nbre": "number", "prix_unitaire": "number", "montant": "number" }
  },
  "laboratoire":  { "lettre_cle": "B",   "nbre": "number", "prix_unitaire": "number", "montant": "number" },
  "anapath":      { "lettre_cle": "P",   "nbre": "number", "prix_unitaire": "number", "montant": "number" },
  "radiologie":   { "lettre_cle": "K/Z", "nbre": "number", "prix_unitaire": "number", "montant": "number" },
  "total_autres_prestations": "number",
  "total_general": "number",
  "total_en_lettres": "string",
  "part_adherent": "number",
  "part_cnops": "number"
}
PROMPT;
    }
}
