# 🎓 Pages Publiques - Documentation

Ce document détaille les 3 nouvelles pages publiques créées pour le système Enroll Concours.

---

## 📄 1. Page d'Accueil (index.php)

### Objectif

Page principale visible par tous - candidats, administrateurs et visiteurs anonymes.

### Sections

#### 🔝 Navbar

- Logo "Enroll Concours" avec icône
- Liens de navigation: Concours, Informations, Contact
- Boutons de connexion dynamiques:
  - Visiteur anonyme → "Admin" et "Candidat"
  - Admin connecté → "Dashboard Admin" et "Déconnexion"
  - Candidat connecté → "Mon Dashboard" et "Déconnexion"

#### 🎯 Hero Section

- Titre principal: "Bienvenue sur Enroll Concours"
- Sous-titre: "Plateforme d'inscription aux concours publics du Cameroun"
- 2 CTAs: "Voir les Concours" et "En savoir plus"
- Fond dégradé avec SVG wave

#### 📊 Statistics Section

- 4 KPIs: 4 Concours, 12500+ Candidats, 100% Sécurité, 24/7 Support

#### 🎓 Section Concours

**Affichage en cartes avec:**

- En-tête coloré avec icône et sigle
- Statut: "Ouvert" 🟢 / "Bientôt" 🟡 / "Fermé" 🔴
- Description courte
- Détails: Dates ouverture/fermeture, Date épreuve, Prix, Places
- Boutons:
  - S'inscrire (si ouvert)
  - Détails (modal)

**4 Concours Disponibles:**

1. CEUP - Concours d'Entrée à l'Université Publique (50,000 FCFA)
2. CFP - Concours de Formation Professionnelle (30,000 FCFA)
3. CM - Concours Militaire (45,000 FCFA)
4. CONCOP - Compétences et Orientation Professionnelle (25,000 FCFA)

#### ℹ️ Comment ça marche?

4 étapes visuelles:

1. Créer un compte
2. Choisir un concours
3. Remplir le formulaire
4. Effectuer le paiement

#### 📞 Section Contact

- Numéro de téléphone
- Email de support
- Localisation

#### 🔗 Footer

- Navigation
- Légal (CGU, Confidentialité, Mentions)
- Réseaux sociaux
- Copyright

### Features Techniques

- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Dégradés CSS modernes
- ✅ Icônes Bootstrap Icons
- ✅ Animation au survol des cartes
- ✅ Vérification d'état de connexion
- ✅ Statuts dynamiques basés sur dates

---

## 📝 2. Formulaire d'Enrôlement (enroll.php)

### Objectif

Formulaire complet d'inscription aux concours avec upload de documents et sélection de paiement.

### Accès

- Depuis index.php via bouton "S'inscrire" sur une carte de concours
- URL: `enroll.php?concours_id=1`

### Sections du Formulaire

#### 🏆 En-tête

- Titre: "Formulaire d'Enrôlement"
- Affichage du concours sélectionné et du prix

#### 👤 Informations Personnelles

- Prénom \*
- Nom \*
- Email \*
- Téléphone \* (avec placeholder: +237...)
- Date de Naissance \*
- Lieu de Naissance \*

#### 📚 Informations Académiques

- Région \* (dropdown - régions spécifiques au concours)
- Filière \* (dropdown - filières spécifiques au concours)

#### 📄 Documents Requis

Checklist avec 3 documents obligatoires:

1. BAC/Diplôme
2. CIN/Passeport
3. Certificat de naissance

**Chaque document avec:**

- Zone drag-and-drop interactive
- Support: PDF, JPEG, PNG
- Max 5MB par fichier
- Affichage du nom du fichier sélectionné

#### 💳 Méthode de Paiement

4 options à sélectionner:

1. 📱 Mobile Money (MTN, Orange, Camtel)
2. 🏦 Virement Bancaire
3. 💳 Carte Bancaire (Visa, Mastercard)
4. 💼 Portefeuille Électronique (PayPal, Stripe)

**Affichage du montant à payer en FCFA**

### Traitement du Formulaire

**Validation côté serveur:**

- Tous les champs requis
- Email valide
- Fichiers: type et taille
- Messages d'erreur clairs

**Après validation réussie:**

- Génération d'un numéro de référence unique: `ENROLL-YYYYMMDDHHmmss-XXXX`
- Upload des fichiers dans `/uploads/candidatures/`
- Insertion en base de données (ou simulation si pas de BDD)
- Affichage du numéro de référence
- Lien de retour à l'accueil

### Features Techniques

- ✅ Validation formulaire HTML5 + PHP
- ✅ Upload de fichiers sécurisé
- ✅ Drag-and-drop pour documents
- ✅ Gestion erreurs gracieuse
- ✅ Affichage dynamique du concours sélectionné
- ✅ Génération numéro de référence unique

---

## 🔐 3. Page de Connexion Candidat (candidate-login.php)

### Objectif

Page de connexion sécurisée pour les candidats.

### Features

#### 🎨 Design

- Centré sur page avec fond dégradé
- Carte blanche avec ombre
- Header coloré avec icône utilisateur
- Layout responsive

#### 🔑 Formulaire

**Champs:**

- Email (avec icône envelope)
- Mot de passe (avec toggle show/hide)
- Case "Se souvenir de moi"

**Boutons:**

- "Se connecter" (primaire)
- "Mot de passe oublié?" (lien)
- "S'inscrire maintenant" (footer)

#### 📝 Identifiants de Test

Affichés dans une zone info (couleur info):

- Email: `candidat@example.com`
- Mot de passe: `password123`

#### 🔓 Authentification

**Processus:**

1. Validation des champs (email + password)
2. Vérification des identifiants
3. Création de session:
   - `user_logged_in` = true
   - `user_id` = 1
   - `user_role` = 'candidat'
   - `user_email` = email
   - `user_name` = nom
4. Redirection vers `candidate-dashboard.php`

**Redirection automatique:**

- Si déjà connecté → Dashboard candidat

#### 🎯 Sections

**Haut de page:**

- Lien "Retour à l'accueil"

**En-tête:**

- Icône utilisateur
- Titre: "Candidat"
- Sous-titre: "Connectez-vous à votre compte"

**Contenu:**

- Zone info avec identifiants de test
- Formulaire avec 2 champs
- Case "Se souvenir de moi"
- Lien mot de passe oublié
- Bouton de connexion
- Divider "ou"
- Boutons sociaux (Facebook, Google) - en développement
- Lien inscription

### Features Techniques

- ✅ Session PHP sécurisée
- ✅ Toggle password visibility
- ✅ Validation HTML5
- ✅ Gestion des messages d'erreur
- ✅ Redirection automatique si connecté
- ✅ Responsive design
- ✅ Identifiants de test pré-remplis

---

## 🔗 Flux de Navigation

```
index.php (Accueil)
├── Bouton "S'inscrire" → enroll.php?concours_id=X
├── Bouton "Admin" → admin/login.php
├── Bouton "Candidat" → candidate-login.php
└── Navbar → Sections internes (#concours, #infos, #contact)

candidate-login.php (Connexion Candidat)
├── Connexion réussie → candidate-dashboard.php
├── S'inscrire → enroll.php?concours_id=1
└── Retour → index.php

enroll.php (Formulaire d'Enrôlement)
├── Inscription réussie → Affiche numéro de référence
├── Retour → index.php
└── Erreur → Affiche messages d'erreur
```

---

## 📱 Responsive Design

### Breakpoints

- **Desktop:** 1024px+ (grille 2 colonnes concours)
- **Tablette:** 768px+ (grille adaptée)
- **Mobile:** <768px (single colonne, full width buttons)

### Optimisations

- Navigation mobile avec toggle burger
- Cards concours empilées sur mobile
- Formulaire 100% width
- Boutons full width sur mobile

---

## 🎨 Palette Couleur

```
Primaire:  #2c3e50 (Bleu foncé)
Secondaire: #34495e (Bleu foncé +)
Accent:    #27ae60 (Vert)
Warning:   #e67e22 (Orange)
Danger:    #e74c3c (Rouge)
Light:     #ecf0f1 (Gris clair)
```

---

## 📊 Données des Concours

Chaque concours inclut:

```json
{
    "id": 1,
    "nom": "Concours d'Entrée à l'Université Publique",
    "sigle": "CEUP",
    "description": "...",
    "date_ouverture": "2025-02-01",
    "date_fermeture": "2025-03-31",
    "date_epreuve": "2025-05-15",
    "prix": 50000,
    "places": 5000,
    "filieres": ["Ingénierie", "Médecine", ...],
    "regions": ["Nord", "Centre", ...],
    "icon": "🎓"
}
```

---

## ✅ Checklist Implémentation

- ✅ index.php - Page d'accueil complète
- ✅ enroll.php - Formulaire avec upload documents
- ✅ candidate-login.php - Connexion candidat
- ✅ Validation formulaires
- ✅ Upload fichiers sécurisé
- ✅ Authentification session
- ✅ Design responsive
- ✅ Messages d'erreur/succès
- ✅ Numéro de référence unique
- ✅ Statuts concours dynamiques

---

## 🚀 Next Steps

- [ ] Créer page d'administration pour gestion des concours
- [ ] Implémenter gateway de paiement (MTN Money, etc)
- [ ] Email de confirmation d'inscription
- [ ] Récupération mot de passe
- [ ] Page détails concours (modal ou page complète)
- [ ] Tableau de bord candidat complet
- [ ] Système de notifications

---

**Date:** Décembre 2025  
**Status:** Prêt pour test local
