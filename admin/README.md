# 📊 Tableau de Bord Administrateur - Enroll Concours

## Description

Système complet de gestion d'enregistrement aux concours nationaux au Cameroun avec un tableau de bord administrateur moderne et réactif.

## ✨ Fonctionnalités Principales

### 1. **Tableau de Bord (Dashboard)**

- Statistiques en temps réel
  - Nombre total d'inscrits
  - Taux de validation
  - Taux d'abandon
  - Taux de paiement
- Graphiques interactifs
  - Évolution des inscrits sur 30 jours
  - Distribution par région
  - Distribution par filière
  - Inscrits par concours
- Situation financière
  - Montant total requis
  - Montant reçu
  - Taux de recouvrement

### 2. **Statistiques Détaillées**

- Inscrits par région avec taux de validation
- Inscrits par filière d'études
- Inscrits par concours
- Taux de paiement par concours
- Export en Excel pour tous les tableaux

### 3. **Gestion des Paiements**

- Liste complète des paiements
- Filtrage par statut (validé, en attente, rejeté)
- Approuver ou rejeter les paiements
- Affichage détaillé des paiements
- Export des données de paiement

### 4. **Gestion des Candidats**

- Liste complète des candidats
- Informations détaillées (email, téléphone, région, filière)
- Affichage des statuts (validé, en attente, abandonné, rejeté)
- Filtrage et recherche
- Actions (afficher, modifier, supprimer)

### 5. **Gestion des Centres**

- Vue carte des centres de composition
- Affichage de la capacité et de l'occupation
- Tableau détaillé des centres
- Ajouter/modifier/supprimer des centres
- Taux d'occupation par centre

### 6. **Rapports Personnalisés**

- Génération de rapports (à compléter)
- Export PDF/Excel
- Rapports par région
- Rapports par filière
- Rapports par concours

### 7. **Paramètres Système**

- Configuration générale du site
- Paramètres de paiement
- Configuration des concours
- Paramètres email
- Sauvegardes et maintenance
- Gestion de la sécurité

## 🏗️ Structure des Fichiers

```
enroll-concours/
├── admin/
│   ├── config.php                 # Configuration et fonctions
│   ├── login.php                  # Page de connexion
│   ├── logout.php                 # Déconnexion
│   ├── dashboard.php              # Tableau de bord principal
│   ├── statistiques.php           # Statistiques détaillées
│   ├── rapports.php               # Génération de rapports
│   ├── paiements.php              # Gestion des paiements
│   ├── candidats.php              # Gestion des candidats
│   ├── centres.php                # Gestion des centres
│   └── parametres.php             # Paramètres système
├── assets/
│   ├── css/
│   │   └── admin-dashboard.css    # Styles du dashboard
│   └── js/
│       └── admin-dashboard.js     # Scripts du dashboard
├── bootstrap/                     # Framework Bootstrap local
└── bootstrap-icons/               # Icônes Bootstrap local
```

## 🚀 Installation et Configuration

### Prérequis

- PHP 7.4+
- MySQL/MariaDB
- Bootstrap local (fourni dans le projet)
- Bootstrap Icons local (fourni dans le projet)

### Étapes d'Installation

1. **Créer la base de données**

   ```sql
   CREATE DATABASE enroll_concours_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Importer les tables** (structure à créer)

   ```sql
   CREATE TABLE candidats (
       id INT PRIMARY KEY AUTO_INCREMENT,
       nom VARCHAR(255),
       email VARCHAR(255),
       telephone VARCHAR(20),
       region VARCHAR(100),
       filiere VARCHAR(100),
       concours_id INT,
       statut VARCHAR(50),
       date_inscription DATETIME,
       KEY(concours_id)
   );

   CREATE TABLE paiements (
       id INT PRIMARY KEY AUTO_INCREMENT,
       candidat_id INT,
       montant DECIMAL(10,2),
       methode VARCHAR(100),
       statut VARCHAR(50),
       date_paiement DATETIME,
       reference VARCHAR(255),
       KEY(candidat_id)
   );

   CREATE TABLE concours (
       id INT PRIMARY KEY AUTO_INCREMENT,
       nom VARCHAR(255),
       sigle VARCHAR(50),
       date_concours DATE,
       statut VARCHAR(50)
   );

   CREATE TABLE centres (
       id INT PRIMARY KEY AUTO_INCREMENT,
       nom VARCHAR(255),
       ville VARCHAR(100),
       region VARCHAR(100),
       places INT,
       occupees INT
   );

   CREATE TABLE admins (
       id INT PRIMARY KEY AUTO_INCREMENT,
       username VARCHAR(100) UNIQUE,
       password VARCHAR(255),
       email VARCHAR(255)
   );

   CREATE TABLE logs (
       id INT PRIMARY KEY AUTO_INCREMENT,
       admin_id INT,
       action VARCHAR(255),
       details TEXT,
       date_action DATETIME,
       KEY(admin_id)
   );
   ```

3. **Configurer les paramètres de connexion**

   - Éditer `admin/config.php`
   - Modifier les constantes DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

4. **Accéder au dashboard**
   - URL: `http://localhost/php-enroll-project/enroll-concours/admin/login.php`
   - Utilisateur par défaut: `admin`
   - Mot de passe par défaut: `admin123`

## 📊 Utilisation du Dashboard

### Connexion

1. Accéder à la page de login
2. Entrer les identifiants
3. Cliquer sur "Se Connecter"

### Navigation

- **Barre latérale**: Menu principal avec liens vers toutes les pages
- **Barre supérieure**: Affichage du titre et informations utilisateur
- **Contenu principal**: Zone d'affichage des données

### Exporter les Données

1. Sur n'importe quel tableau, chercher le bouton "Exporter Excel"
2. Cliquer pour télécharger le fichier Excel
3. Pour PDF, utiliser la fonction d'impression du navigateur

## 🎨 Personnalisation

### Couleurs

Les couleurs peuvent être modifiées dans `admin-dashboard.css`:

```css
:root {
  --primary: #2c3e50; /* Couleur principale */
  --accent: #27ae60; /* Couleur d'accent */
  --warning: #e67e22; /* Couleur d'avertissement */
  --danger: #e74c3c; /* Couleur de danger */
  --info: #3498db; /* Couleur informatif */
}
```

### Thème

Le dashboard utilise un gradient moderne par défaut. Pour le personnaliser:

1. Éditer les classes de gradient dans CSS
2. Ajuster le padding et les espacements
3. Modifier les tailles de police selon vos besoins

## 🔒 Sécurité

### Points de Sécurité

- ✅ Vérification de session à chaque page
- ✅ Sanitisation des entrées utilisateur
- ✅ Protection contre les attaques XSS
- ⚠️ À implémenter: Protection CSRF
- ⚠️ À implémenter: Authentification en base de données
- ⚠️ À implémenter: Chiffrement des mots de passe (bcrypt)

### Recommandations

1. Utiliser une authentification avec base de données
2. Implémenter bcrypt pour les mots de passe
3. Activer les sessions sécurisées (HTTPS)
4. Mettre en place un système de droits d'accès
5. Logger toutes les actions administrateur

## 📱 Responsivité

Le dashboard est entièrement responsive:

- ✅ Desktop (1200px+)
- ✅ Tablette (768px - 1199px)
- ✅ Mobile (480px - 767px)
- ✅ Petit mobile (moins de 480px)

## 📈 Graphiques et Visualisations

Les graphiques utilisent **Chart.js**:

- Graphiques en ligne (évolution)
- Graphiques en barres (comparaison)
- Graphiques circulaires (distribution)
- Graphiques mixtes

## 🔧 Fonctions Utiles (config.php)

### Statistiques

```php
getStatistiques()              // Statistiques globales
getDetailsCandidats($limit)    // Liste des candidats
getDetailsPaiements($limit)    // Liste des paiements
getTauxPaiementParConcours()   // Taux paiement par concours
getCentresComposition()        // Liste des centres
```

### Utilitaires

```php
formatFCFA($amount)            // Formater en FCFA
getStatutBadge($statut)        // Obtenir badge couleur
logAction($admin_id, $action)  // Logger une action
```

## 🐛 Dépannage

### Problème: Erreur de connexion BD

- Vérifier les paramètres dans `config.php`
- S'assurer que MySQL/MariaDB est en cours d'exécution
- Vérifier les permissions de l'utilisateur

### Problème: Styles non appliqués

- Vérifier le chemin relatif des fichiers CSS
- Rafraîchir le cache du navigateur (Ctrl+F5)
- Vérifier la console pour les erreurs

### Problème: Export Excel ne fonctionne pas

- S'assurer que la bibliothèque XLSX.js est chargée
- Vérifier que JavaScript est activé
- Essayer un autre navigateur

## 📞 Support et Maintenance

### À Compléter

- [ ] Intégration avec vraie base de données
- [ ] Authentification LDAP/Active Directory
- [ ] Système de permissions granulaires
- [ ] Notifications email
- [ ] Sauvegarde automatique
- [ ] Interface de gestion des utilisateurs

## 📄 Licence

Ce projet est développé pour le système d'enregistrement aux concours nationaux du Cameroun.

## 👨‍💻 Développement

### Technologies Utilisées

- PHP 7.4+
- Bootstrap 5
- Bootstrap Icons
- Chart.js
- XLSX.js (Excel export)
- HTML5/CSS3
- JavaScript ES6+

### Versions

- Version: 1.0.0
- Dernière mise à jour: Décembre 2025

---

**Note**: Ce dashboard est un prototype. Pour la production, veuillez implémenter une vraie base de données, un système d'authentification sécurisé et les protocoles de sécurité recommandés.
