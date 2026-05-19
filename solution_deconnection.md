# Solution Déconnexion Facturation

## Problème rencontré

Lors de la création d'une facture, l'utilisateur était redirigé vers la page de login au lieu de créer la facture.

## Causes identifiées

### 1. Session lifetime trop court (PRINCIPAL)
**Fichier**: `.env` ligne 32
```env
SESSION_LIFETIME=10  # 10 minutes seulement
```

La session expirait après seulement 10 minutes, ce qui causait une déconnexion automatique si l'utilisateur prenait plus de 10 minutes à remplir le formulaire.

### 2. Variable JavaScript non définie
**Fichier**: `resources/views/dprh/facturation/create.blade.php`

La variable `partenaireId` était utilisée dans le template Alpine.js mais n'était pas initialisée par défaut, causant une erreur JavaScript.

## Solutions appliquées

### 1. Augmenter la durée de session

**Modifier** `.env`:
```env
SESSION_LIFETIME=1440  # 24 heures au lieu de 10 minutes
```

**Puis vider le cache**:
```bash
php artisan config:clear
php artisan cache:clear
```

### 2. Corriger la variable JavaScript Alpine.js

**Fichier**: `resources/views/dprh/facturation/create.blade.php` (ligne 628)

Ajouter dans la fonction `facturationForm()`:
```javascript
partenaireId: null,  // AJOUT: Initialisation par défaut
```

### 3. Simplifier le bouton de soumission

**Avant**:
```html
<button type="button" onclick="soumettreFacture()" ...>
```

**Après**:
```html
<button type="submit" name="submit_facture" value="1" ...>
```

## Fichiers modifiés

1. `.env` - SESSION_LIFETIME changé de 10 à 1440
2. `bootstrap/app.php` - Ajout du middleware de logs (optionnel)
3. `app/Http/Middleware/LogCsrfAndSessionErrors.php` - Créé (optionnel)
4. `app/Http/Controllers/DpRh/FacturationController.php` - Logs détaillés ajoutés (optionnel)
5. `resources/views/dprh/facturation/create.blade.php` - Correction partenaireId + bouton simplifié

## Vérification

Après correction, la création de facture fonctionne correctement sans déconnexion.

## Note importante

⚠️ **Si SESSION_LIFETIME revient à 10**, le problème réapparaîtra. Vérifiez toujours que `.env` contient `SESSION_LIFETIME=1440`.

---

*Document créé le 19/05/2026*
