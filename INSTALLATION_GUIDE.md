# 🚀 Guide d'Installation et d'Utilisation - Système d'Enrôlement

## 📋 Table des Matières

1. [Prérequis](#prérequis)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Utilisation](#utilisation)
5. [Dépannage](#dépannage)
6. [FAQ](#faq)

---

## 🔧 Prérequis

### Logiciels Requis:

- **PHP** 7.4+ avec support MySQLi
- **MySQL/MariaDB** 5.7+
- **Serveur Web** (Apache, Nginx)
- **Navigateur** moderne (Chrome, Firefox, Safari, Edge)

### Extensions PHP Requises:

```
- mysqli (extension MySQL)
- session
- filter
- date
```

### Accès Fichiers:

- Lecture/Écriture sur le dossier `/assets/avatars/`
- Lecture sur tous les fichiers PHP
- Écriture pour les logs (optionnel)

---

## 📥 Installation

### Étape 1: Télécharger les Fichiers

```bash
# Cloner ou télécharger le projet
cd /var/www/html
git clone [repository-url] enroll-concours
cd enroll-concours
```

### Étape 2: Créer la Base de Données

```sql
-- Se connecter à MySQL
mysql -u root -p

-- Créer la base
CREATE DATABASE enroll_concours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sélectionner la base
USE enroll_concours;

-- Créer les tables
CREATE TABLE candidats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(100) NOT NULL,
    region VARCHAR(50) NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

CREATE TABLE candidatures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidat_id INT NOT NULL,
    concours VARCHAR(50) NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    region VARCHAR(50) NOT NULL,
    statut ENUM('en attente', 'complète', 'rejetée') DEFAULT 'en attente',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_approbation DATETIME NULL,
    date_rejet DATETIME NULL,
    notes LONGTEXT NULL,
    raison_rejet LONGTEXT NULL,
    FOREIGN KEY (candidat_id) REFERENCES candidats(id) ON DELETE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_candidat (candidat_id)
);

CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidat_id INT NOT NULL,
    candidature_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    methode_paiement VARCHAR(50) DEFAULT 'en attente',
    statut ENUM('en attente', 'validé', 'rejeté') DEFAULT 'en attente',
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidat_id) REFERENCES candidats(id) ON DELETE CASCADE,
    FOREIGN KEY (candidature_id) REFERENCES candidatures(id) ON DELETE CASCADE,
    INDEX idx_candidat (candidat_id),
    INDEX idx_statut (statut)
);

CREATE TABLE administrateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('administrateur', 'super_admin') DEFAULT 'administrateur',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion DATETIME NULL,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
);

-- Créer un administrateur par défaut
INSERT INTO administrateurs (nom, email, password, role)
VALUES ('Admin', 'admin@enrollconcours.cm', 'admin123', 'administrator');
```

### Étape 3: Configurer la Connexion BD

Éditer `admin/config.php` :

```php
// Ligne ~15-20
define('DB_HOST', 'localhost');      // IP/Hostname du serveur BD
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASSWORD', 'password');   // Mot de passe MySQL
define('DB_NAME', 'enroll_concours'); // Nom de la base
```

### Étape 4: Créer les Répertoires

```bash
# Créer le dossier pour les avatars
mkdir -p assets/avatars
chmod 755 assets/avatars

# Vérifier les permissions
ls -la assets/
```

### Étape 5: Configurer le Serveur Web

#### Pour Apache:

```apache
# Dans un fichier .htaccess ou httpd.conf
<Directory "/var/www/html/enroll-concours">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### Pour Nginx:

```nginx
server {
    listen 80;
    server_name enrollconcours.local;
    root /var/www/html/enroll-concours;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### Étape 6: Tester l'Installation

```
Ouvrir le navigateur:
http://localhost/enroll-concours/
```

Vous devriez voir la page d'accueil avec les concours.

---

## ⚙️ Configuration

### Configuration PHP Recommandée

```php
; Dans php.ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
session.save_path = "/var/lib/php/sessions"
```

### Configuration Sessions

```php
// Dans admin/config.php
session_start();
session_set_cookie_params([
    'lifetime' => 3600,      // 1 heure
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => false,       // true en HTTPS
    'httponly' => true,      // Sécurité
    'samesite' => 'Lax'
]);
```

### Variables d'Environnement (Optionnel)

```bash
# Créer un fichier .env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=password
DB_NAME=enroll_concours
APP_ENV=production
APP_DEBUG=false
```

---

## 📖 Utilisation

### 👤 Pour un Candidat

#### 1. Accès à la Page d'Inscription

```
URL: http://localhost/enroll-concours/index.php
```

**Actions Disponibles:**

- Lire les informations des concours
- Cliquer sur "Enroll Now"
- Consulter les détails (prix, filières, régions)

#### 2. Remplissage du Formulaire

```
URL: http://localhost/enroll-concours/enroll-form.php
```

**Formulaire avec 4 sections:**

1. **Infos Personnelles**

   - Prénom (texte)
   - Nom (texte)
   - Email (email)
   - Téléphone (tel)

2. **Infos de Naissance**

   - Date de naissance (date)
   - Lieu de naissance (texte)

3. **Localisation**

   - Région (dropdown)
   - Filière (dropdown)

4. **Sélection Concours**
   - Concours (dropdown avec prix et deadline)

**Validation:**

- Tous les champs obligatoires (\*)
- Email au format valide
- Email pas déjà utilisé

#### 3. Soumission

Cliquer sur "Soumettre l'Enrôlement"

**Processus:**

- Validation serveur
- Création en BD
- Redirection confirmation

#### 4. Page de Confirmation

```
URL: http://localhost/enroll-concours/enrollment-confirmation.php
```

**Affichage:**

- ✅ Message de succès
- 📝 Numéro de référence
- 📋 Informations saisies
- 📋 Checklist prochaines étapes

**Actions:**

- Retourner à l'accueil
- Se connecter (futur)

---

### 👨‍💼 Pour un Administrateur

#### 1. Connexion

```
URL: http://localhost/enroll-concours/admin/login.php
Identifiant: admin@enrollconcours.cm
Mot de passe: admin123
```

#### 2. Accès au Dashboard

```
URL: http://localhost/enroll-concours/admin/dashboard.php
```

**Vue d'ensemble:**

- Statistiques globales
- Derniers enrôlements
- Paiements
- Rapports

#### 3. Gestion des Enrôlements

```
URL: http://localhost/enroll-concours/admin/enrollments.php
```

**Interface:**

- 📊 KPI cards (4 cartes)
- 📋 Liste des enrôlements en attente
- 👤 Détails du candidat pour chaque enrôlement

**Actions sur chaque enrôlement:**

**Approuver:**

1. Cliquer sur "Approuver"
2. Modal apparaît
3. Ajouter des notes (optionnel)
4. Cliquer "Approuver"
5. ✅ Enrôlement approuvé (changement BD)

**Rejeter:**

1. Cliquer sur "Rejeter"
2. Modal apparaît
3. Entrer la raison (obligatoire)
4. Cliquer "Rejeter"
5. ❌ Enrôlement rejeté (changement BD)

---

## 🐛 Dépannage

### Erreur 1: "Unknown Database 'enroll_concours'"

**Cause:** La base de données n'existe pas ou le nom est incorrect.

**Solution:**

```sql
-- Vérifier la base existe
SHOW DATABASES;

-- Si n'existe pas, créer
CREATE DATABASE enroll_concours;

-- Vérifier config.php a le bon nom
define('DB_NAME', 'enroll_concours');
```

### Erreur 2: "Access Denied for User"

**Cause:** Mauvais identifiant ou mot de passe MySQL.

**Solution:**

```php
// Dans admin/config.php
define('DB_USER', 'correct_user');     // Bon utilisateur
define('DB_PASSWORD', 'correct_pass'); // Bon mot de passe
```

### Erreur 3: "Call to Undefined Function"

**Cause:** Extension MySQLi non activée.

**Solution:**

```bash
# Vérifier php.ini
php -m | grep mysqli

# Si absent, installer
# Ubuntu/Debian
sudo apt-get install php-mysqli

# Ou activer dans php.ini
extension=mysqli
```

### Erreur 4: "Permission Denied" sur Avatar

**Cause:** Dossier `assets/avatars` sans permissions.

**Solution:**

```bash
chmod 755 assets/avatars
chmod 755 assets/

# Vérifier propriétaire
ls -la assets/
# Doit être www-data ou votre utilisateur
```

### Erreur 5: "Header Already Sent"

**Cause:** Whitespace avant `<?php` ou après `?>`.

**Solution:**

```php
<?php
// AUCUN ESPACE AVANT
session_start();
// ...
?>
<!-- AUCUN ESPACE APRÈS -->
```

---

## ❓ FAQ

### Q: Comment réinitialiser le mot de passe admin?

**R:** Directement en BD:

```sql
UPDATE administrateurs
SET password = 'newpassword'
WHERE email = 'admin@enrollconcours.cm';
```

### Q: Comment activer les logs?

**R:** Dans `admin/config.php`:

```php
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php-errors.log');
```

### Q: Peut-on avoir plusieurs administrateurs?

**R:** Oui! Ajouter en BD:

```sql
INSERT INTO administrateurs (nom, email, password, role)
VALUES ('Admin 2', 'admin2@enrollconcours.cm', 'password', 'administrateur');
```

### Q: Comment changer le prix des concours?

**R:** Dans `enroll-form.php` ou `index.php`:

```php
$prix_concours = [
    'ceup' => 50000,
    'cfp' => 45000,
    // ...
];
```

### Q: Les candidats peuvent-ils modifier leur enrôlement?

**R:** Actuellement non. À implémenter:

```php
// Ajouter dans candidate-dashboard.php
- Bouton "Modifier" pour enrôlements en attente
- Page de modification (edit-enrollment.php)
- Validation avant modification
```

### Q: Comment exporter les enrôlements?

**R:** Via `admin/enrollments.php`:

```php
// À ajouter: Bouton Export Excel
// Utiliser: XLSX.js ou PHPExcel
```

### Q: Le système supporte-t-il HTTPS?

**R:** Oui, ajouter dans `admin/config.php`:

```php
session_set_cookie_params([
    'secure' => true,  // Seulement HTTPS
    'httponly' => true // Pas de JS
]);
```

### Q: Quelle est la limite de taille des fichiers?

**R:** Actuellement: 10 MB (configurable en `php.ini`)

### Q: Les données sont-elles sauvegardées?

**R:** Oui, tout est en BD. Faire des backups régulièrement:

```bash
mysqldump -u root -p enroll_concours > backup_$(date +%Y%m%d).sql
```

---

## 🔐 Sécurité

### Bonnes Pratiques Implémentées:

✅ Requêtes paramétrées (prévient SQL injection)
✅ Validation des entrées
✅ Échappement HTML (prévient XSS)
✅ Authentification session
✅ Gestion des erreurs sécurisée

### Recommandations Supplémentaires:

- 🔒 Utiliser HTTPS en production
- 🔑 Changer les identifiants par défaut
- 📝 Activer les logs d'erreur
- 🔄 Faire des backups réguliers
- 🚨 Monitorer les activités suspectes
- 🔐 Implémenter 2FA (futur)

---

## 📞 Support

- **Email:** support@enrollconcours.cm
- **Téléphone:** +237 6XX XXX XXX
- **Documentation:** https://enrollconcours.cm/docs

---

**Créé:** Décembre 2025
**Version:** 2.0
**Licence:** MIT
