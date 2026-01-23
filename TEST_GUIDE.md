# 🧪 Guide de Test du Système de Candidature

## 🎯 Objectif
Tester complètement le système de candidature avec upload CV

---

## 📋 Prérequis

- ✅ Serveur Laragon/Xampp démarré
- ✅ Base de données `job_dating_youcode` existante
- ✅ Apprenant enregistré et connecté
- ✅ Annonces visibles dans le dashboard

---

## 🧪 Scénarios de Test

### **Test 1: Ouverture du Modal**

**Étapes:**
1. Connectez-vous comme apprenant
2. Allez au `/dashboard`
3. Trouvez une annonce
4. Cliquez le bouton **"Postuler"** (bleu)

**Résultat Attendu:**
- ✅ Modal s'ouvre
- ✅ Titre du modal: "Soumettre une candidature"
- ✅ Titre de l'annonce: Affiché dans le header
- ✅ Formulaire visible avec 2 champs
- ✅ Boutons "Annuler" et "Envoyer"

**Erreurs Possibles:**
- ❌ Modal ne s'ouvre pas → JavaScript désactivé?
- ❌ Titre vide → Issue de Twig
- ❌ Formulaire vide → CSS cassé

---

### **Test 2: Validation JavaScript du Message**

**Étapes:**
1. Modal ouvert
2. Tapez moins de 50 caractères
3. Tentez d'envoyer

**Résultat Attendu:**
- ✅ Validation HTML5
- ✅ Message d'erreur si < 50 car
- ✅ Scroll vers le champ

**Erreurs Possibles:**
- ❌ Pas de validation → HTML cassé
- ❌ Message en faux positif → JavaScript issue

---

### **Test 3: Validation JavaScript du CV**

**Étapes:**
1. Modal ouvert
2. Cliquez "Sélectionner fichier"
3. Essayez un fichier NON-PDF (Word, Excel, etc.)

**Résultat Attendu:**
- ✅ Dialog d'alerte: "Veuillez sélectionner un fichier PDF"
- ✅ Fichier NON sélectionné
- ✅ Affichage du nom vide

**Erreurs Possibles:**
- ❌ Alerte ne s'affiche pas → JavaScript cassé
- ❌ Fichier accepté → Validation absente

---

### **Test 4: Test de Taille Fichier**

**Étapes:**
1. Modal ouvert
2. Sélectionnez un PDF de plus de 5 MB
3. Vérifiez le message

**Résultat Attendu:**
- ✅ Alerte: "Le fichier dépasse la taille maximale de 5 MB"
- ✅ Fichier NON sélectionné

**Erreurs Possibles:**
- ❌ Pas de vérification → JavaScript incomplete
- ❌ Alerte personnalisée → Vérifier l'implémentation

---

### **Test 5: Sélection PDF Valide**

**Étapes:**
1. Modal ouvert
2. Sélectionnez un fichier PDF valide (< 5 MB)
3. Vérifiez l'affichage

**Résultat Attendu:**
- ✅ Affichage: "✓ nom_fichier.pdf (X.XX MB)"
- ✅ Texte vert/succès
- ✅ Nom du fichier visible
- ✅ Taille affichée correctement

**Erreurs Possibles:**
- ❌ Nom non affiché → updateFileName() cassé
- ❌ Taille incorrecte → Calcul Math issue

---

### **Test 6: Message de Motivation Valide**

**Étapes:**
1. Modal ouvert
2. Écrivez un message (50+ caractères)
3. Vérifiez le message

**Résultat Attendu:**
- ✅ Texte s'affiche correctement
- ✅ Sauts de ligne préservés
- ✅ Pas de limite visible dans textarea

**Erreurs Possibles:**
- ❌ Texte tronqué → CSS issue
- ❌ Sauts de ligne perdus → Twig issue

---

### **Test 7: Soumission Complète (Succès)**

**Étapes:**
1. Modal ouvert
2. Remplissez:
   - Message: "Je suis passionné par ce poste..."
   - CV: Sélectionnez un PDF valide
3. Cochez la confirmation
4. Cliquez "Envoyer la candidature"

**Résultat Attendu:**
- ✅ Modal se ferme
- ✅ Notification vert: "Candidature envoyée avec succès!"
- ✅ Dashboard rechargé
- ✅ Bouton "Postuler" → Badge "Postulé"
- ✅ Candidature dans sidebar

**Erreurs Possibles:**
- ❌ Erreur PHP 500 → Vérifier logs serveur
- ❌ BD error → Vérifier connexion
- ❌ Fichier non sauvé → Permissions uploads
- ❌ CV non trouvé → Path incorrect

---

### **Test 8: Prévention Doublons**

**Étapes:**
1. Candidature créée (Test 7)
2. Rafraîchissez le dashboard
3. Trouvez la même annonce
4. Cliquez "Postuler" (devrait être "Postulé")

**Résultat Attendu:**
- ✅ Bouton disabled: "Postulé"
- ✅ Couleur grise
- ✅ Pas d'action possible

**Erreurs Possibles:**
- ❌ Bouton Postuler actif → Badge non mis à jour
- ❌ Modal ouvre → Badge pas checké

---

### **Test 9: Tentative Doublon (Directe)**

**Étapes:**
1. Candidature créée (Test 7)
2. Changez l'URL en `/candidature`
3. Créez un form POST manuel
4. Soumettez même offre

**Résultat Attendu:**
- ✅ Redirection: /dashboard
- ✅ Message erreur: "Vous avez déjà postulé à cette offre"
- ✅ Notification rouge

**Erreurs Possibles:**
- ❌ Doublon créé → hasApplied() ne fonctionne pas
- ❌ Pas de message → Flash cassé

---

### **Test 10: Vérification Fichier Enregistré**

**Étapes:**
1. Candidature créée (Test 7)
2. Allez dans `/public/uploads/cvs/`
3. Cherchez le fichier

**Résultat Attendu:**
- ✅ Fichier existe: `candidature_1_5_1704067200.pdf`
- ✅ Format: `candidature_{user_id}_{annonce_id}_{timestamp}.pdf`
- ✅ Taille: Corresponde au fichier original

**Erreurs Possibles:**
- ❌ Répertoire vide → Upload failed
- ❌ Nom aléatoire → Naming scheme incorrect
- ❌ Taille incorrecte → Corruption fichier

---

### **Test 11: Vérification Base de Données**

**Étapes:**
1. Candidature créée (Test 7)
2. Ouvrez phpMyAdmin
3. Allez dans `job_dating_youcode` → `candidatures`

**Résultat Attendu:**
- ✅ Nouvelle ligne exists
- ✅ `user_id`: ID de l'apprenant
- ✅ `annonce_id`: ID de l'annonce
- ✅ `message`: Message enregistré
- ✅ `cv_path`: Chemin du PDF
- ✅ `statut`: 'en_attente'
- ✅ `date_candidature`: Timestamp correct

**Erreurs Possibles:**
- ❌ cv_path NULL → Pas d'upload
- ❌ cv_path incorrect → Path issue
- ❌ Pas d'enregistrement → BD error

---

### **Test 12: Message de Candidatures**

**Étapes:**
1. Candidature créée (Test 7)
2. Cliquez "Mes candidatures" (sidebar)
3. Vérifiez la liste

**Résultat Attendu:**
- ✅ Candidature visible dans la liste
- ✅ Titre de l'annonce affiché
- ✅ Entreprise affichée
- ✅ Statut: Badge jaune "En attente"
- ✅ Date correcte

**Erreurs Possibles:**
- ❌ Candidature manquante → Query issue
- ❌ Données incorrectes → JOIN broken
- ❌ Statut mal affiché → Badge CSS issue

---

### **Test 13: Erreur Validation Serveur**

**Étapes:**
1. Modifiez le formulaire HTML (DevTools)
2. Enlevez le champ `_token`
3. Soumettez

**Résultat Attendu:**
- ✅ Redirection: /dashboard
- ✅ Message erreur: "Token de sécurité invalide"
- ❌ Candidature NOT créée

**Erreurs Possibles:**
- ❌ Candidature créée → CSRF pas vérifiée
- ❌ Pas de message erreur → Validation skipped

---

### **Test 14: Message Vide**

**Étapes:**
1. Modal ouvert
2. Sélectionnez un PDF
3. Laissez le message vide
4. Cliquez "Envoyer"

**Résultat Attendu:**
- ✅ Redirection: /dashboard
- ✅ Message erreur: "Le message doit contenir..."
- ❌ Candidature NOT créée

**Erreurs Possibles:**
- ❌ Candidature créée → Validation serveur failed
- ❌ Pas de message → Flash issue

---

### **Test 15: Erreur Fermeture Modal**

**Étapes:**
1. Modal ouvert
2. Remplissez partiellement
3. Cliquez "Annuler"

**Résultat Attendu:**
- ✅ Modal se ferme
- ✅ Formulaire vidé
- ✅ Aucune candidature créée

**Erreurs Possibles:**
- ❌ Modal ne se ferme pas → JavaScript issue
- ❌ Formulaire non vidé → Reset failed

---

## 🔍 Tests de Sécurité

### **Test Security 1: File Type Spoofing**

**Étapes:**
1. Renommez un .exe en .pdf
2. Soumettez via formulaire

**Résultat Attendu:**
- ✅ Erreur serveur: "Seuls les fichiers PDF sont acceptés"
- ❌ Fichier NOT sauvegardé
- ❌ Candidature NOT créée

**Pourquoi:** Vérification MIME type, pas seulement extension

---

### **Test Security 2: Path Traversal**

**Étapes:**
1. Modifiez le formulaire (DevTools)
2. Changez `annonce_id` en malveillant
3. Soumettez

**Résultat Attendu:**
- ✅ Validation stricte
- ✅ ID invalide rejeté
- ❌ Candidature NOT créée

**Pourquoi:** Cast en (int)

---

### **Test Security 3: Large File Upload**

**Étapes:**
1. Créez un PDF de 10 MB
2. Soumettez

**Résultat Attendu:**
- ✅ Erreur: "Le fichier dépasse la taille maximale"
- ❌ Fichier NOT sauvegardé

**Pourquoi:** Limite 5 MB

---

## 📊 Checklist de Test

```
[ ] Test 1: Modal ouverture
[ ] Test 2: Validation message JS
[ ] Test 3: Validation CV JS
[ ] Test 4: Taille fichier
[ ] Test 5: PDF valide
[ ] Test 6: Message valid
[ ] Test 7: Soumission OK
[ ] Test 8: Prévention doublon
[ ] Test 9: Tentative doublon
[ ] Test 10: Fichier enregistré
[ ] Test 11: BD vérifiée
[ ] Test 12: Candidatures visibles
[ ] Test 13: CSRF check
[ ] Test 14: Message vide
[ ] Test 15: Modal fermeture
[ ] Security 1: File type
[ ] Security 2: Path traversal
[ ] Security 3: Large file
```

---

## 🐛 Dépannage

### **Problem: Modal ne s'ouvre pas**
- Vérifier: Console JavaScript (F12)
- Solution: Rechargez la page
- Fallback: Essayez un autre navigateur

### **Problem: Fichier non accepté**
- Vérifier: Type MIME réel (pas juste extension)
- Solution: Convertissez en PDF avec Adobe
- Fallback: Utilisez un autre PDF

### **Problem: Erreur 500**
- Vérifier: Logs PHP (`error_log`)
- Solution: Permissions dossier uploads
- Fallback: Vérifiez la DB connexion

### **Problem: Candidature pas enregistrée**
- Vérifier: Network tab (F12)
- Solution: Rechargez la page
- Fallback: Essayez à nouveau

---

## ✅ Test Passé

Quand tous les tests passent avec succès:

```
✅ Système de Candidature VALIDÉ
✅ Upload CV FONCTIONNEL
✅ Sécurité IMPLÉMENTÉE
✅ Prêt pour PRODUCTION
```

---

**Last Updated:** Décembre 2024  
**Version:** 1.0
