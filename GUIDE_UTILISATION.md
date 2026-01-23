# 🎓 JobDating - Système de Candidature avec Upload CV

## 📖 Guide d'Utilisation

### 👤 Pour les Apprenants

#### **Comment Postuler à une Offre d'Emploi**

1. **Accédez au Dashboard**
   - Connectez-vous avec vos identifiants
   - Vous arrivez directement sur votre tableau de bord

2. **Découvrez les Offres**
   - Scrollez jusqu'à la section "Annonces disponibles"
   - Chaque offre affiche:
     - Titre du poste
     - Nom de l'entreprise
     - Type de contrat
     - Compétences requises
     - Date de publication

3. **Postulez à une Offre**
   - Cliquez sur le bouton **"Postuler"** (bleu)
   - Un modal s'ouvre avec le formulaire de candidature

4. **Remplissez le Formulaire**
   - **Message de motivation**: Exprimez votre intérêt
     - Minimum: 50 caractères
     - Maximum: 2000 caractères
     - Conseil: Soyez authentique et précis
   
   - **CV en PDF**: Téléchargez votre curriculum vitae
     - Format: PDF uniquement
     - Taille max: 5 MB
     - Assurez-vous que votre CV est à jour

5. **Vérifiez les Informations**
   - Cochez la case "Je confirme que les informations fournies sont exactes"
   - Vérifiez que tout est correct

6. **Envoyez la Candidature**
   - Cliquez sur **"Envoyer la candidature"**
   - Une notification apparaît pour confirmer le succès
   - Le bouton devient "Postulé" pour cette offre

#### **Consultez vos Candidatures**
- Allez dans **"Mes candidatures"** (menu latéral)
- Vous verrez l'historique de toutes vos candidatures
- Le statut peut être:
  - 🟡 **En attente**: Entreprise en évaluation
  - 🟢 **Validée**: Entretien programmé
  - 🔴 **Refusée**: Dossier non retenu

#### **Mettez à Jour votre Profil**
- Cliquez sur **"Mon profil"** (menu latéral)
- Mettez à jour:
  - Vos informations personnelles
  - Votre mot de passe
  - Votre CV principal

---

### 🏢 Pour les Administrateurs

#### **Accès au Tableau de Bord Admin**
- Adresse: `/admin/dashboard`
- Vous avez accès à:
  - Gestion des candidatures
  - Validation/Refus de dossiers
  - Téléchargement des CVs

#### **Gestion des Candidatures**
1. **Consulter les CVs**
   - Allez dans la section candidatures
   - Cliquez sur une candidature
   - Téléchargez le CV du candidat

2. **Mettre à Jour les Statuts**
   - Changez le statut: En attente → Validée/Refusée
   - Les apprenants reçoivent une notification

---

## 🔒 Sécurité et Confidentialité

### ✅ Protections Implémentées
- **Chiffrage**: Les CVs ne sont jamais exécutables
- **Validation MIME**: Vérification du type de fichier réel
- **Limite de Taille**: Maximum 5 MB par CV
- **CSRF Protection**: Chaque formulaire est sécurisé
- **Permissions Fichiers**: Lecture seule, pas de modification

### 📝 Vos Données
- Vos CVs sont stockés de manière sécurisée
- Accès limité aux administrateurs
- Suppression automatique après 2 ans d'inactivité

---

## ⚠️ Dépannage

### **Erreur: "Seuls les fichiers PDF sont acceptés"**
- Vérifiez que votre fichier est vraiment en format PDF
- Ouvrez-le avec Adobe Reader ou un lecteur PDF
- Réexportez-le si nécessaire

### **Erreur: "Le fichier dépasse la taille maximale de 5 MB"**
- Votre CV fait plus de 5 MB
- Compressez les images dans votre PDF
- Utilisez un service de compression en ligne

### **Erreur: "Vous avez déjà postulé à cette offre"**
- Vous ne pouvez postuler qu'une fois par offre
- Contactez l'admin si vous voulez mettre à jour

### **Le modal ne s'ouvre pas**
- Vérifiez que JavaScript est activé
- Rechargez la page (Ctrl+F5)
- Essayez un autre navigateur

### **La candidature n'est pas enregistrée**
- Attendez quelques secondes
- Vérifiez que votre connexion est stable
- Essayez à nouveau

---

## 📋 Spécifications Techniques

### **Formats Acceptés**
- ✅ PDF standard (PDF 1.4+)
- ❌ PDF Scannés de mauvaise qualité
- ❌ PDF avec mot de passe
- ❌ PDF protégés en lecture seule

### **Recommandations pour le CV**
- Nommez-le: `Prenom_Nom_CV.pdf`
- Taille recommandée: 1-3 MB
- Pages: 1-2 pages (idéal)
- Couleurs: Noir et blanc ou couleurs claires
- Police: Arial ou Calibri (lisible)

### **Caractéristiques du Message de Motivation**
- Minimum 50 caractères
- Maximum 2000 caractères (~400 mots)
- Peut inclure des sauts de ligne
- Pas de copier-coller recommandé

---

## 🎯 Conseils pour Réussir vos Candidatures

### **1. Lisez l'Offre Complètement**
- Comprenez les requis et souhaits
- Notez les points clés à mention

### **2. Personnalisez Votre Message**
- Ne pas utiliser le même message pour toutes les offres
- Montrez que vous connaissez l'entreprise
- Expliquez pourquoi VOUS convenz spécifiquement

### **3. Préparez Votre CV**
- À jour avec vos expériences récentes
- Sans fautes d'orthographe
- Format professionnel et lisible
- Photo d'identité de qualité (si requise)

### **4. Timing**
- Postulez rapidement après parution de l'offre
- Évitez les soumissions en fin de journée
- Vérifiez votre email régulièrement

### **5. Suivi**
- Consultez régulièrement vos candidatures
- Notez les réponses des entreprises
- Suivez up après 1-2 semaines si pas de réponse

---

## 📞 Support

### **Besoin d'aide?**
- **Email**: support@jobdating.youcode.ma
- **Chat**: Disponible sur le site
- **Téléphone**: +212 XXX XXX XXX
- **Horaires**: Lun-Ven, 9h-18h

### **Signaler un Problème**
- Cliquez sur l'icône ?  en haut à droite
- Ou envoyez un email avec:
  - Description du problème
  - Capture d'écran si possible
  - Heure et date de l'incident
  - Votre ID d'utilisateur

---

## 📚 Ressources Additionnelles

### **Guides Pratiques**
- [Créer un bon CV]()
- [Rédiger une lettre de motivation]()
- [Préparer un entretien d'embauche]()

### **Tutoriels Vidéo**
- [Comment postuler sur JobDating]()
- [Maximiser vos chances de succès]()
- [Gestion de votre profil]()

---

## ✨ Fonctionnalités Futures

Nous travaillons actuellement sur:
- ✨ Chat direct avec les entreprises
- ✨ Notifications par email automatiques
- ✨ Présentation de vidéo CV
- ✨ Filtrage avancé des offres
- ✨ Système de recommandations

---

**Dernière mise à jour**: Décembre 2024  
**Version**: 1.0  
**Plateforme**: JobDating YouCode
