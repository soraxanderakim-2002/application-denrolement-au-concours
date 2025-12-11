# 🚀 ENROLL CONCOURS - Guide Complet du Système

## 📋 Vue d'ensemble

**Enroll Concours** est une plateforme complète d'inscription aux concours publics du Cameroun avec:

- 👥 **3 rôles utilisateurs:** Admin, Candidat, Visiteur
- 🎓 **4 concours:** CEUP, CFP, CM, CONCOP
- 📊 **Dashboard personnalisé** par rôle
- 💳 **Système de paiement** intégré
- 📄 **Gestion de documents** sécurisée

---

## 🌐 Architecture du Site

### Pages Publiques (Visiteurs)

```
index.php .......................... Accueil avec tous les concours
candidate-login.php ................ Connexion candidat
enroll.php ......................... Formulaire d'inscription
admin/login.php .................... Connexion admin
```

### Pages Admin (Administrateurs)

```
admin/dashboard.php ................ Dashboard statistiques
admin/profile.php .................. Profil + avatar admin
admin/approbations.php ............. Approbation des candidatures
admin/candidats.php ................ Gestion des candidats
admin/paiements.php ................ Gestion des paiements
admin/rapports.php ................. Rapports et analytics
admin/parametres.php ............... Paramètres système
```

### Pages Candidat (Candidats Connectés)

```
candidate-dashboard.php ............ Dashboard candidat
candidate-profile.php .............. Profil candidat + avatar
```

---

## 🔐 Authentification

### Rôles et Permissions

#### 👨‍💼 **ADMINISTRATEUR**

- Accès: `/admin/dashboard.php`
- Requiert: `admin/login.php`
- Identifiants: `admin` / `admin123` _(à configurer)_
- Permissions:
  - ✅ Approbation/rejet des candidatures
  - ✅ Gestion des paiements
  - ✅ Génération de rapports
  - ✅ Modification du profil + avatar
  - ✅ Vue sur tous les candidats

#### 👤 **CANDIDAT**

- Accès: `/candidate-dashboard.php`
- Requiert: `candidate-login.php`
- Identifiants de test: `candidat@example.com` / `password123`
- Permissions:
  - ✅ Voir ses inscriptions
  - ✅ Modifier son profil
  - ✅ Uploader son avatar
  - ✅ Suivre le statut de ses candidatures

#### 👁️ **VISITEUR (Anonyme)**

- Accès illimité aux pages publiques
- Peut voir tous les concours
- Peut s'inscrire à un concours
- Peut accéder aux pages de connexion

### Flux d'Authentification

```
Visiteur
├── Visite index.php
├── Clique "S'inscrire" → enroll.php
│   └── Remplit formulaire → Création numéro référence
├── Clique "Admin" → admin/login.php
│   └── Connexion réussie → admin/dashboard.php
└── Clique "Candidat" → candidate-login.php
    └── Connexion réussie → candidate-dashboard.php
```

---

## 📄 Pages Détaillées

### 1️⃣ INDEX.PHP - Page d'Accueil

**Contenu:**

- Navbar avec navigation + boutons login
- Hero section avec CTA
- Section statistiques (4 KPIs)
- Section concours (4 cartes)
- Section "Comment ça marche?" (4 étapes)
- Section contact
- Footer

**Features:**

- ✅ Responsive design
- ✅ Vérification état connexion
- ✅ Statuts dynamiques concours
- ✅ CTA vers enroll.php avec concours_id

**Données statiques:**

- 4 concours avec descriptions, prix, dates
- Filieres et régions par concours

---

### 2️⃣ ENROLL.PHP - Formulaire d'Enrôlement

**Sections:**

1. **Informations Personnelles**

   - Prénom, Nom, Email, Téléphone
   - Date de naissance, Lieu de naissance

2. **Informations Académiques**

   - Région (dropdown dynamique)
   - Filière (dropdown dynamique)

3. **Documents Requis**

   - BAC/Diplôme
   - CIN/Passeport
   - Certificat/Extrait d'acte
   - Support: PDF, JPEG, PNG (max 5MB)
   - Drag-and-drop enabled

4. **Méthode de Paiement**
   - Mobile Money
   - Virement Bancaire
   - Carte Bancaire
   - Portefeuille Électronique

**Processing:**

- Validation PHP stricte
- Upload sécurisé (sanitize filename)
- Création numéro référence: `ENROLL-YYYYMMDDhhmmss-XXXX`
- Insertion DB ou simulation en session
- Confirmation avec numéro de référence

**Stockage:**

- Documents: `/uploads/candidatures/`
- Données: Table `candidats` + `paiements`

---

### 3️⃣ CANDIDATE-LOGIN.PHP - Connexion Candidat

**Features:**

- Simple et intuitif
- Identifiants de test pré-remplis
- Toggle password visibility
- Lien "Mot de passe oublié?"
- Lien "S'inscrire"
- Boutons sociaux (placeholder)

**Authentification:**

```php
$_SESSION['user_logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'candidat';
$_SESSION['user_email'] = $email;
$_SESSION['user_name'] = 'Prénom Nom';
```

**Redirection:**

- Succès → `candidate-dashboard.php`
- Erreur → Affiche message + relance formulaire

---

### 4️⃣ CANDIDATE-DASHBOARD.PHP - Dashboard Candidat

**Affichage:**

- 4 KPI cards: Total, Complètes, En attente, Rejetées
- Liste des inscriptions avec statuts
- Boutons "Voir détails", "Modifier"
- CTA pour nouvelles inscriptions

**Données:**

- Simulated data de 3 inscriptions
- Statuts: Complète, En attente, Rejetée
- Paiements: Validé, En attente, Refusé
- Documents: OK, Incomplet, Manquant

---

### 5️⃣ CANDIDATE-PROFILE.PHP - Profil Candidat

**Sections:**

1. **Mes Informations**

   - Éditable: Prénom, Nom, Email, Téléphone
   - Lecture seule: Date naissance, Lieu, Région, Filière
   - Formulaire avec validation

2. **Photo de Profil**
   - Upload avatar
   - Stockage: `/assets/avatars/`
   - Support: JPG, PNG, GIF
   - Affichage dans navbar

---

### 6️⃣ ADMIN/PROFILE.PHP - Profil Admin

**Sections:**

1. **Informations Personnelles**

   - Éditable: Nom, Email, Téléphone
   - Formulaire avec validation

2. **Photo de Profil**

   - Upload avatar
   - Stockage: `/assets/avatars/`
   - Support: JPG, PNG, GIF

3. **Changement Mot de Passe**
   - Saisie ancien mot de passe
   - Saisie nouveau mot de passe + confirmation
   - Validation

---

### 7️⃣ ADMIN/APPROBATIONS.PHP - Approbations Candidatures

**Vue:**

- 4 KPI cards: Total, En attente, Approuvée, Rejetée
- Grille de cartes candidats (3 colonnes desktop, 1 mobile)
- Chaque carte: Info candidat, Docs checklist, Statut

**Actions:**

- Bouton "Approuver" → Modal avec détails
- Bouton "Rejeter" → Modal avec champ "Raison"
- Soumission → Mise à jour statut

**Données simulées:** 4 candidatures avec statuts variés

---

### 8️⃣ ADMIN/PAIEMENTS.PHP - Gestion Paiements

**Vue:**

- 4 KPI cards: Montant total, Montant reçu, Taux, Montant en attente
- Table paiements avec: Référence, Candidat, Montant, Méthode, Statut, Date
- Tableau taux par concours

**Actions:**

- Approuver paiement (si en attente)
- Rejeter paiement (si en attente)
- Modal avec notes approval
- Export Excel

**Données:** Simulated payment data

---

### 9️⃣ ADMIN/RAPPORTS.PHP - Rapports Analytics

**Sections:**

1. **Résumé KPI**

   - Montant attendu, Montant récupéré, Taux, Montant en attente

2. **Paiements par Région**

   - Tableau avec: Région, Places, Attendu, Reçu, Taux, Progress bar

3. **Paiements par Méthode**

   - Tableau avec: Méthode, Nombre, Montant, Taux

4. **Taux par Concours**
   - Tableau avec: Concours, Places, Montant total, Montant reçu, Taux

**Filtres:**

- Date start et Date end
- Validation des dates

**Export:**

- Boutons Excel pour chaque section
- Utilise XLSX.js

---

## 📊 Modèle de Données

### Table: `candidats`

```sql
CREATE TABLE candidats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    telephone VARCHAR(20),
    date_naissance DATE,
    lieu_naissance VARCHAR(100),
    region VARCHAR(100),
    filiere VARCHAR(100),
    concours_id INT,
    date_inscription TIMESTAMP,
    statut ENUM('en attente', 'approuve', 'rejete') DEFAULT 'en attente',
    documents JSON
);
```

### Table: `paiements`

```sql
CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidat_id INT,
    reference VARCHAR(50) UNIQUE,
    montant DECIMAL(10,2),
    methode_paiement VARCHAR(50),
    statut ENUM('en attente', 'validé', 'refusé') DEFAULT 'en attente',
    date_creation TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (candidat_id) REFERENCES candidats(id)
);
```

---

## 🎨 Design & Styling

### Framework

- Bootstrap 5 (local)
- Bootstrap Icons
- CSS personnalisé inline

### Couleurs

```css
--primary: #2c3e50     /* Bleu foncé */
--secondary: #34495e   /* Bleu foncé + */
--accent: #27ae60      /* Vert */
--warning: #e67e22     /* Orange */
--danger: #e74c3c      /* Rouge */
```

### Responsive

- Desktop: 1024px+ (full layout)
- Tablet: 768px+ (adapted grid)
- Mobile: <768px (stacked, single col)

---

## 🔧 Configuration & Setup

### Fichier Principal: `admin/config.php`

**Contient:**

- Classe `DB` - Connexion MySQL
- Classe `AdminAuth` - Authentification
- Fonctions utilitaires

```php
class DB {
    public static function prepare($query) { ... }
    public static function query($query) { ... }
    public static function isConnected() { ... }
}

class AdminAuth {
    public static function checkAdminLogin() { ... }
    public static function checkCandidateLogin() { ... }
    public static function login($email, $password, $role) { ... }
}
```

### Variables de Session

**Admin:**

```php
$_SESSION['admin_logged_in'] = true
$_SESSION['admin_id'] = 1
$_SESSION['admin_role'] = 'administrateur'
$_SESSION['admin_email'] = 'admin@example.com'
```

**Candidat:**

```php
$_SESSION['user_logged_in'] = true
$_SESSION['user_id'] = 1
$_SESSION['user_role'] = 'candidat'
$_SESSION['user_email'] = 'candidat@example.com'
$_SESSION['user_name'] = 'Prénom Nom'
```

---

## 📱 Fonctionnalités Clés

### ✅ Implémentées

| Feature                  | Priorité | Status       |
| ------------------------ | -------- | ------------ |
| Page d'accueil           | P1       | ✅ Complète  |
| Formulaire d'enrôlement  | P1       | ✅ Complète  |
| Upload documents         | P1       | ✅ Complète  |
| Connexion candidat       | P1       | ✅ Complète  |
| Dashboard candidat       | P1       | ✅ Complète  |
| Profil candidat          | P2       | ✅ Complète  |
| Connexion admin          | P1       | ✅ Existante |
| Dashboard admin          | P1       | ✅ Existant  |
| Approbation candidatures | P2       | ✅ Complète  |
| Gestion paiements        | P2       | ✅ Existante |
| Rapports                 | P2       | ✅ Existants |
| Profil admin             | P2       | ✅ Complète  |

### 🔲 À Faire

- [ ] Intégration gateway paiement réelle
- [ ] Email notifications
- [ ] Récupération mot de passe
- [ ] Page détails concours (modal complète)
- [ ] Système de chat support
- [ ] Export PDF dossier candidat
- [ ] Historique paiements candidat
- [ ] Certificats téléchargeables

---

## 🧪 Test & Utilisation

### Identifiants de Test

**Admin:**

- Email: `admin` ou `admin@example.com` _(à configurer)_
- Mot de passe: `admin123` _(à configurer)_

**Candidat:**

- Email: `candidat@example.com`
- Mot de passe: `password123`

### Concours de Test

```
1. CEUP (50,000 FCFA)   - 5000 places
2. CFP (30,000 FCFA)    - 3000 places
3. CM (45,000 FCFA)     - 500 places
4. CONCOP (25,000 FCFA) - 2000 places
```

### Parcours Utilisateur Complet

1. Visite `index.php`
2. Clique "S'inscrire" sur un concours
3. Remplit `enroll.php`
4. Reçoit numéro de référence
5. Peut se connecter via `candidate-login.php`
6. Voit son inscription dans `candidate-dashboard.php`
7. Peut éditer profil dans `candidate-profile.php`

---

## 📚 Documentation Supplémentaire

- `SYSTEM_OVERVIEW.md` - Vue d'ensemble du système
- `PUBLIC_PAGES.md` - Documentation pages publiques
- `INSTALLATION.md` - Installation et setup
- `DATABASE.sql` - Schema MySQL

---

## 🚀 Déploiement

### Prérequis

- PHP 7.4+
- MySQL 5.7+
- Serveur web (Apache, Nginx)

### Steps

1. Cloner le repository
2. Importer `database.sql` en MySQL
3. Configurer `admin/config.php` avec les identifiants DB
4. Créer dossiers: `/uploads/candidatures/`, `/assets/avatars/`
5. Donner permissions: `chmod 755 uploads/ assets/`
6. Tester localement puis déployer

### Sécurité

- Utiliser HTTPS en production
- Valider et sanitizer toutes entrées
- Hash les mots de passe (bcrypt)
- CSRF tokens sur tous les formulaires
- Rate limiting sur login
- WAF/DDoS protection

---

## 📞 Support

**Contact:**

- Email: support@enrollconcours.cm
- Tél: +237 123 456 789
- Localisation: Yaoundé, Cameroun

---

**Version:** 1.0  
**Dernière mise à jour:** Décembre 2025  
**Statut:** Prêt pour démonstration locale
