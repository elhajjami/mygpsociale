# Génération de Factures Médicales — Laravel + Alpine.js + Anthropic

## Structure des fichiers

```
app/Http/Controllers/
    InvoiceController.php          ← Controller principal

resources/views/invoice/
    form.blade.php                 ← Formulaire de saisie (Alpine.js)
    pdf_formation.blade.php        ← Template PDF Formulaire 1 (médecin/labo/radio)
    pdf_clinique.blade.php         ← Template PDF Formulaire 2 (clinique)

routes/
    web.php                        ← 2 routes à ajouter
```

---

## 1. Dépendances à installer

### Package PDF (DomPDF pour Laravel)
```bash
composer require barryvdh/laravel-dompdf
```

Publier la config :
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Dans `config/dompdf.php`, activer le support UTF-8 :
```php
'font_cache' => storage_path('fonts/'),
'default_font' => 'dejavu sans',
```

---

## 2. Clé API Anthropic

Dans `.env` :
```
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxx
```

Dans `config/services.php` :
```php
'anthropic' => [
    'key' => env('ANTHROPIC_API_KEY'),
],
```

---

## 3. Alpine.js

Si vous n'utilisez pas encore Alpine.js, ajoutez dans votre layout `<head>` :
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 4. Tailwind CSS

Le formulaire utilise les classes Tailwind. Si vous utilisez le CDN (développement) :
```html
<script src="https://cdn.tailwindcss.com"></script>
```

---

## 5. Routes (routes/web.php)

```php
use App\Http\Controllers\InvoiceController;

Route::get('/facture', [InvoiceController::class, 'index'])->name('invoice.form');
Route::post('/facture/generer', [InvoiceController::class, 'generate'])->name('invoice.generate');
```

---

## 6. Layout attendu

Le fichier `form.blade.php` étend `layouts.app` et utilise :
- `@section('content')` pour le contenu
- `@push('scripts')` pour le JS Alpine

Adaptez selon votre layout existant.

---

## Flux de fonctionnement

```
Utilisateur remplit le formulaire
        ↓
POST /facture/generer
        ↓
InvoiceController::generate()
  ├── Validation des données
  ├── Appel API Anthropic (structuration des lignes)
  │     ├── Formulaire 1 → buildFormationPrompt()
  │     └── Formulaire 2 → buildClinicPrompt()
  ├── Réception JSON structuré
  └── Génération PDF via DomPDF
        ├── type != clinique → pdf_formation.blade.php
        └── type == clinique → pdf_clinique.blade.php
              ↓
        Téléchargement PDF
```

---

## Types supportés

| Type       | Formulaire | Template PDF          |
|------------|------------|-----------------------|
| medecin    | 1          | pdf_formation.blade   |
| laboratoire| 1          | pdf_formation.blade   |
| radiologie | 1          | pdf_formation.blade   |
| clinique   | 2          | pdf_clinique.blade    |

---

## Personnalisation

### Ajouter le logo de la clinique/cabinet
Dans `pdf_formation.blade.php` ou `pdf_clinique.blade.php`, ajoutez dans l'en-tête :
```html
<img src="{{ public_path('images/logo.png') }}" style="height: 40px;">
```
Note : DomPDF nécessite des chemins absolus pour les images → utiliser `public_path()`.

### Modifier le nombre de lignes vides (Formulaire 1)
Dans `pdf_formation.blade.php`, cherchez `$i < 20` et modifiez le nombre.
