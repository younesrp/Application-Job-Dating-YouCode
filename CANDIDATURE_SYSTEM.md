# Système de Candidature Amélioré avec Upload CV

## 📋 Résumé des Changements

Implémentation d'un système de candidature professionnel avec upload de CV (PDF), validation côté serveur, et protection CSRF.

---

## ✅ Fonctionnalités Implémentées

### 1. **Modal de Candidature Interactif**
- **Fichier**: `app/views/front/dashboard/modal-candidature.twig`
- **Fonctionnalités**:
  - Formulaire modal avec fields:
    - Message de motivation (textarea, 50-2000 caractères)
    - Upload CV (PDF uniquement, max 5MB)
    - CSRF token caché
    - Boutons Annuler/Envoyer
  - Validation JavaScript côté client
  - Gestion du nom du fichier avec affichage
  - Fermeture via bouton, clic externe, ou Échap

### 2. **Contrôleur CandidatureController Amélioré**
- **Fichier**: `app/controllers/front/CandidatureController.php`
- **Nouvelles méthodes**:
  - `apply()`: Traite les soumissions de candidature
  - `validateCandidatureData()`: Validation côté serveur
  - `uploadCV()`: Upload et validation du fichier
  - `deleteCV()`: Suppression sécurisée du fichier

- **Validations Implémentées**:
  - ✓ Token CSRF
  - ✓ Authentification requise
  - ✓ Message obligatoire (50-2000 caractères)
  - ✓ CV PDF uniquement (vérification MIME type)
  - ✓ Taille max 5 MB
  - ✓ Pas de doublons (hasApplied check)
  - ✓ Offre valide

- **Sécurité**:
  - Noms de fichier uniques: `candidature_{user_id}_{annonce_id}_{timestamp}.pdf`
  - Vérification MIME type (pas d'extension seule)
  - Permissions fichier: 0644 (lecture seule)
  - Suppression du fichier en cas d'erreur BD

### 3. **Modèle Candidature Mis à Jour**
- **Fichier**: `app/models/Candidature.php`
- **Modification**: 
  - `createCandidature($apprentId, $annonceId, $message, $cvPath = null)`
  - Nouveau paramètre `$cvPath` pour stocker le chemin du PDF

### 4. **Système de Messages Flash**
- **Fichier**: `app/views/components/flash-messages.twig`
- **Affichage**:
  - Messages de succès (vert)
  - Messages d'erreur (rouge)
  - Messages d'avertissement (orange)
  - Messages informatifs (bleu)
- **Auto-fermeture**: 5 secondes
- **Animations**: Slide-in et fade-out

### 5. **Intégration au Dashboard**
- **Fichier**: `app/views/front/dashboard/index.twig`
- **Changements**:
  - Bouton "Postuler" convertis en calls JavaScript
  - Ouverture du modal avec `openCandidatureModal(id, titre)`
  - Badge "Postulé" pour candidatures existantes
  - Inclusion du modal: `{% include "front/dashboard/modal-candidature.twig" %}`

### 6. **Contrôleur Dashboard Amélioré**
- **Fichier**: `app/controllers/front/DashboardController.php`
- **Changements**:
  - Génération du token CSRF
  - Récupération des messages flash
  - Passage des données au template Twig

### 7. **Layout Mis à Jour**
- **Fichier**: `app/views/front/layout.twig`
- **Ajouts**:
  - Inclusion du composant flash-messages
  - Supportera tous les messages d'erreur/succès

---

## 🗄️ Changements Base de Données

### Migration
```sql
ALTER TABLE candidatures ADD COLUMN cv_path VARCHAR(255) NULL AFTER message;
```

### Structure Mise à Jour
```
candidatures table:
- id
- apprenant_id (FK)
- annonce_id (FK)
- message (TEXT)
- cv_path (VARCHAR(255) - NEW) ← Chemin vers le fichier PDF
- date_candidature (DATETIME)
- statut (ENUM)
```

---

## 📁 Arborescence des Fichiers

```
public/uploads/cvs/
├── candidature_1_5_1704067200.pdf
├── candidature_2_3_1704067250.pdf
└── (autres fichiers...)

app/views/
├── front/
│   ├── dashboard/
│   │   ├── index.twig (modifié)
│   │   └── modal-candidature.twig (NOUVEAU)
│   └── layout.twig (modifié)
├── components/
│   └── flash-messages.twig (NOUVEAU)

app/controllers/front/
├── CandidatureController.php (amélioré)
└── DashboardController.php (modifié)

app/core/
└── Session.php (ajout méthode getFlash)

app/models/
└── Candidature.php (modifié)
```

---

## 🔐 Sécurité

### Protections Implémentées
1. **CSRF Token**: Génération et vérification systématique
2. **Vérification MIME Type**: Pas seulement l'extension
3. **Noms Uniques**: Timestamp + IDs pour éviter les collisions
4. **Permissions Fichiers**: 0644 (lecture seule)
5. **Validation Côté Serveur**: Toutes les données validées
6. **Limite de Taille**: 5 MB maximum
7. **Suppression Sécurisée**: Fichiers supprimés en cas d'erreur

---

## 🧪 Test du Système

### Vérification des Composants
```bash
php test-candidature.php
```

### Flow de Test Manuel
1. **Connexion** comme apprenant
2. **Dashboard** → Voir les annonces
3. **Clic "Postuler"** → Modal s'ouvre
4. **Remplir le formulaire**:
   - Message: "Je suis très intéressé par ce poste..."
   - CV: Sélectionner un PDF
5. **Clic "Envoyer"**
6. **Vérifier**:
   - Message de succès
   - Badge "Postulé" apparaît
   - Fichier enregistré dans `/public/uploads/cvs/`
   - Entrée en BD avec `cv_path` défini

---

## 📋 Validations Côté Client

```javascript
- PDF uniquement (accept=".pdf")
- Vérification MIME au changement de fichier
- Taille max 5 MB
- Affichage du nom et taille du fichier
- Confirmation avant envoi
```

---

## 📋 Validations Côté Serveur

```php
- Token CSRF valide
- Utilisateur authentifié
- Message 50-2000 caractères
- File type = application/pdf
- File extension = .pdf
- File size <= 5 MB
- Pas d'application dupliquée
- Offre valide (id > 0)
```

---

## 🎯 Flux Complet d'une Candidature

```
1. Utilisateur clique "Postuler"
   ↓
2. Modal s'ouvre avec openCandidatureModal()
   ↓
3. Utilisateur remplit message + CV
   ↓
4. Validation JavaScript
   ↓
5. Submit POST /candidature
   ↓
6. CandidatureController::apply()
   ├─ Vérifier CSRF token
   ├─ Valider les données
   ├─ Vérifier pas de doublon (hasApplied)
   ├─ Uploader le CV
   │  ├─ Vérifier MIME type
   │  ├─ Vérifier extension
   │  ├─ Vérifier taille
   │  └─ Sauvegarder avec nom unique
   ├─ Créer l'enregistrement BD
   └─ Flash success message
   ↓
7. Redirection /dashboard
   ↓
8. Affichage du message de succès
   ↓
9. Badge "Postulé" visible
```

---

## 💾 Fichiers Générés

| Fichier | Type | Contenu |
|---------|------|---------|
| `modal-candidature.twig` | Template | Modal, formulaire, JS validation |
| `flash-messages.twig` | Composant | Système de notifications |
| `CandidatureController.php` | Contrôleur | Logique upload/validation |
| `Candidature.php` (modifié) | Modèle | Support cv_path en BD |
| `dashboard/index.twig` (modifié) | Template | Intégration modal |
| `layout.twig` (modifié) | Template | Flash messages |
| `Session.php` (modifié) | Core | Méthode getFlash |
| `DashboardController.php` (modifié) | Contrôleur | CSRF token + flash |

---

## 🔧 Maintenance

### Nettoyer les CVs Orphelins
```sql
-- Trouver les CVs sans candidature
SELECT cv_path FROM candidatures WHERE cv_path IS NOT NULL
UNION
SELECT cv_path FROM candidatures WHERE deleted_at IS NOT NULL;

-- Supprimer les fichiers associés manuellement
```

### Vérifier les Permissions
```bash
ls -la public/uploads/cvs/
chmod 644 public/uploads/cvs/*.pdf
```

---

## 🚀 Prochaines Étapes Optionnelles

1. **Virus Scan**: Intégrer ClamAV pour scanner les PDFs
2. **Génération Miniatures**: Créer des previews des CVs
3. **Téléchargement Admin**: Admin peut télécharger les CVs
4. **Email Notifications**: Notifier l'entreprise d'une candidature
5. **Historique Versions**: Permettre plusieurs versions d'un CV
6. **Archivage**: Archiver les CVs après 2 ans

---

## ⚠️ Notes Importantes

- Les fichiers sont stockés en `/public/uploads/cvs/`
- Les noms sont au format: `candidature_USER_ID_ANNONCE_ID_TIMESTAMP.pdf`
- Les candidatures dupliquées sont empêchées au niveau BD
- La suppression du CV lors d'une erreur est sécurisée
- Les messages flash sont affichés une seule fois (comportement standard)

---

**Développé par**: [Assistant IA]  
**Date**: 2024  
**Version**: 1.0
