# 🚀 Guide d'Installation - Enroll Concours

## Prérequis Système

- **Serveur Web**: Apache 2.4+
- **PHP**: 7.4 ou supérieur
- **Base de Données**: MySQL 5.7+ ou MariaDB 10.0+
- **Navigateur**: Chrome, Firefox, Safari ou Edge (versions récentes)

## 📋 Installation Étape par Étape

### Étape 1: Préparation de l'Environnement

```bash
# Créer le répertoire du projet (s'il n'existe pas)
mkdir -p /var/www/html/php-enroll-project/enroll-concours
cd /var/www/html/php-enroll-project/enroll-concours

# Donner les permissions
chmod -R 755 .
chmod -R 777 ./uploads  # Si le dossier existe
```

### Étape 2: Installation de la Base de Données

#### Méthode 1: Avec phpMyAdmin

1. Ouvrir phpMyAdmin (`http://localhost/phpmyadmin`)
2. Aller à l'onglet "SQL"
3. Copier le contenu du fichier `database.sql`
4. Coller et exécuter

#### Méthode 2: Avec la ligne de commande

```bash
# Se connecter à MySQL
mysql -u root -p

# Créer la base de données
CREATE DATABASE enroll_concours_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importer le fichier SQL
mysql -u root -p enroll_concours_db < /chemin/vers/database.sql
```

#### Méthode 3: Avec MySQL Workbench

1. Ouvrir MySQL Workbench
2. File → Open SQL Script → Sélectionner `database.sql`
3. Exécuter (Ctrl + Shift + Enter)

### Étape 3: Configuration de l'Application

1. **Éditer le fichier de configuration** `admin/config.php`:

```php
<?php
// Ligne 8-12: Configuration de la base de données
define('DB_HOST', 'localhost');      // Généralement 'localhost'
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASSWORD', '');           // Mot de passe MySQL
define('DB_NAME', 'enroll_concours_db');  // Nom de la base
?>
```

2. **Configuration du fuseau horaire** (ligne 35):

```php
date_default_timezone_set('Africa/Douala');
```

### Étape 4: Vérification de l'Installation

1. **Accéder à la page d'accueil**:

   ```
   http://localhost/php-enroll-project/enroll-concours/admin-dashboard.html
   ```

2. **Tester la connexion au dashboard**:

   - Cliquer sur "Se Connecter"
   - Utilisateur: `admin`
   - Mot de passe: `admin123`
   - URL directe: `http://localhost/php-enroll-project/enroll-concours/admin/login.php`

3. **Vérifier la structure des fichiers**:
   ```
   enroll-concours/
   ├── admin/
   │   ├── config.php ✓
   │   ├── login.php ✓
   │   ├── dashboard.php ✓
   │   └── ... (autres fichiers PHP)
   ├── assets/
   │   ├── css/
   │   │   └── admin-dashboard.css ✓
   │   └── js/
   │       └── admin-dashboard.js ✓
   ├── bootstrap/ ✓
   ├── bootstrap-icons/ ✓
   ├── database.sql ✓
   └── admin-dashboard.html ✓
   ```

## 🔐 Sécurité Post-Installation

### Changer les Identifiants Par Défaut

```sql
-- Générer un hash SHA-256 sécurisé du nouveau mot de passe
-- Utiliser une fonction de hachage: SHA2('nouveau_mot_de_passe', 256)

UPDATE admins
SET password = SHA2('votre_nouveau_mot_de_passe', 256),
    email = 'votre_email@example.com'
WHERE username = 'admin';
```

### Protéger les Fichiers Sensibles

```bash
# Protéger les fichiers de configuration
chmod 600 admin/config.php
chmod 600 database.sql

# Créer un dossier uploads avec permissions
mkdir uploads
chmod 777 uploads
```

### Activer HTTPS

```apache
# Ajouter à .htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

## 🧪 Test de l'Installation

### Test 1: Connexion Base de Données

```php
<?php
require_once 'admin/config.php';
echo "Connexion réussie!";
?>
```

### Test 2: Vérifier les Permissions

```bash
# Vérifier la permission en écriture
touch admin/test.tmp && rm admin/test.tmp
echo "Permission en écriture OK"
```

### Test 3: Tester les Graphiques

1. Accéder au Dashboard
2. Vérifier que les graphiques s'affichent
3. Ouvrir la console (F12) pour vérifier les erreurs

## 🚨 Dépannage

### Erreur: "Erreur de connexion à la base de données"

**Solutions:**

- Vérifier que MySQL/MariaDB est démarré
- Vérifier les identifiants dans `admin/config.php`
- S'assurer que la base de données existe
- Vérifier l'utilisateur MySQL a les bonnes permissions

```bash
mysql -u root -p -e "SHOW DATABASES;"
mysql -u root -p -e "SELECT USER();"
```

### Erreur: "Fichiers CSS/JS non trouvés"

**Solutions:**

- Vérifier les chemins relatifs (généralement `/bootstrap/` et `/assets/`)
- S'assurer que les fichiers existent physiquement
- Vérifier les permissions de lecture (chmod 644)
- Rafraîchir le cache navigateur (Ctrl + F5)

### Erreur: "Session expirée"

**Solutions:**

- Vérifier la configuration PHP `session.cookie_secure`
- S'assurer que `session_start()` est appelé en premier
- Vérifier le dossier `/tmp` pour les fichiers de session

### Erreur: "Export Excel ne fonctionne pas"

**Solutions:**

- S'assurer que Chart.js et XLSX.js sont chargés
- Vérifier que JavaScript est activé
- Ouvrir la console (F12) pour les erreurs JavaScript
- Tester avec un autre navigateur

## 📊 Données de Test

### Insérer des Candidats de Test

```sql
INSERT INTO candidats (nom, email, telephone, region_id, filiere_id, concours_id, statut)
VALUES
('Dupont Pierre', 'pierre@example.cm', '+237600000001', 1, 1, 1, 'validé'),
('Nkomo Marie', 'marie@example.cm', '+237600000002', 2, 2, 1, 'validé'),
('Kameni Jean', 'kameni@example.cm', '+237600000003', 3, 3, 2, 'en_attente');
```

### Insérer des Paiements de Test

```sql
INSERT INTO paiements (candidat_id, montant, methode_paiement, statut)
SELECT id, 25000, 'MTN Mobile Money', 'validé'
FROM candidats LIMIT 3;
```

## 🔄 Mise à Jour Régulière

### Sauvegarde de la Base de Données

```bash
# Sauvegarde manuelle
mysqldump -u root -p enroll_concours_db > backup_$(date +%Y%m%d).sql

# Restauration
mysql -u root -p enroll_concours_db < backup_20250109.sql
```

### Nettoyage des Anciens Logs

```sql
-- Supprimer les logs de plus de 30 jours
DELETE FROM logs
WHERE date_action < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## 📞 Support et Aide

### Ressources

- Documentation PHP: https://www.php.net/docs.php
- Bootstrap Documentation: https://getbootstrap.com/docs
- Chart.js Documentation: https://www.chartjs.org/docs/latest/

### Vérifier les Logs PHP

```bash
# Localiser le fichier error.log PHP
php -ini | grep error_log

# Afficher les dernières erreurs
tail -f /var/log/apache2/error.log
```

## ✅ Checklist Post-Installation

- [ ] Base de données créée et importée
- [ ] Configuration mise à jour dans `admin/config.php`
- [ ] Page de connexion accessible
- [ ] Dashboard affiche les données
- [ ] Graphiques affichés correctement
- [ ] Export Excel fonctionne
- [ ] Mots de passe administrateur changés
- [ ] Fichiers sensibles protégés (chmod 600)
- [ ] HTTPS activé (production)
- [ ] Sauvegardes programmées

## 🎉 Prochaines Étapes

1. **Personnaliser le dashboard**

   - Modifier les couleurs dans `assets/css/admin-dashboard.css`
   - Ajouter le logo de votre organisation

2. **Configurer les emails**

   - Mettre à jour les paramètres SMTP dans `admin/parametres.php`
   - Tester l'envoi d'emails

3. **Ajouter des utilisateurs**

   - Créer des comptes administrateur dans la base de données
   - Assigner les rôles appropriés

4. **Former les utilisateurs**
   - Réaliser une session de formation
   - Documenter les processus

---

**Installation complétée avec succès!** 🎊

Si vous avez des questions, consultez le fichier `admin/README.md` pour plus de détails sur les fonctionnalités.
