<?php
/**
 * Configuration pour le système d'enregistrement aux concours nationaux
 * Cameroun - Enregistrement Nationaux
 */

// Configuration de la base de données (à implémenter avec une vraie BDD)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'enroll_concours');

// Classe pour la gestion de la base de données
class DB {
    private static $connection = null;
    private static $connected = false;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                // Créer la connexion
                self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if (self::$connection->connect_error) {
                    // Connexion échouée - mode simulation
                    self::$connected = false;
                    self::$connection = null;
                    return null;
                }
                
                self::$connection->set_charset("utf8");
                self::$connected = true;
            } catch (mysqli_sql_exception $e) {
                // Erreur MySQLi - mode simulation
                self::$connected = false;
                self::$connection = null;
                return null;
            } catch (Exception $e) {
                // Erreur générale - mode simulation
                self::$connected = false;
                self::$connection = null;
                return null;
            }
        }
        return self::$connection;
    }
    
    public static function isConnected() {
        return self::$connected;
    }
    
    public static function prepare($sql) {
        $connection = self::getConnection();
        if ($connection === null) {
            return null;
        }
        return $connection->prepare($sql);
    }
    
    public static function query($sql) {
        $connection = self::getConnection();
        if ($connection === null) {
            return null;
        }
        return $connection->query($sql);
    }
}

// Configuration système
define('SITE_URL', 'http://localhost/enroll-concours/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('ASSETS_URL', SITE_URL . 'assets/');

// Régions du Cameroun
$regions = array(
    'Adamaoua', 'Centre', 'Est', 'Extrême-Nord', 'Littoral', 'Nord', 
    'Nord-Ouest', 'Ouest', 'Sud', 'Sud-Ouest'
);

// Filières d'études
$filieres = array(
    'Ingénierie', 'Médecine', 'Droit', 'Économie', 'Lettres', 'Sciences', 'Informatique'
);

// Concours disponibles
$concours = array(
    array('id' => 1, 'nom' => 'Concours d\'Entrée à l\'Université Publique', 'sigle' => 'CEUP', 'date' => '2025-06-15'),
    array('id' => 2, 'nom' => 'Concours de la Fonction Publique', 'sigle' => 'CFP', 'date' => '2025-07-20'),
    array('id' => 3, 'nom' => 'Concours Militaire', 'sigle' => 'CM', 'date' => '2025-08-10'),
    array('id' => 4, 'nom' => 'Concours de Police', 'sigle' => 'CONCOP', 'date' => '2025-09-05')
);

// Définir le fuseau horaire
date_default_timezone_set('Africa/Douala');

// Classe pour la gestion des sessions utilisateur
class AdminAuth {
    public static function checkLogin() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . ADMIN_URL . 'login.php');
            exit;
        }
    }
    
    public static function login($username, $password, $role = 'administrateur') {
        // Authentification simulée avec rôle
        // En production, utiliser la BDD pour vérifier les credentials
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_user'] = $username;
            $_SESSION['admin_name'] = 'Administrateur';
            $_SESSION['admin_email'] = 'admin@enroll-concours.cm';
            $_SESSION['admin_role'] = 'administrateur';
            return true;
        }
        // Utilisateur standard (candidat)
        elseif ($username && $password) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = 2;
            $_SESSION['user_username'] = $username;
            $_SESSION['user_name'] = 'Candidat';
            $_SESSION['user_role'] = 'candidat';
            return true;
        }
        return false;
    }
    
    public static function checkAdminLogin() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . ADMIN_URL . 'login.php');
            exit;
        }
        
        // Vérifier que c'est un administrateur
        if ($_SESSION['admin_role'] !== 'administrateur') {
            header('Location: ../index.php');
            exit;
        }
    }
    
    public static function checkCandidateLogin() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ../login-candidate.php');
            exit;
        }
    }
    
    public static function logout() {
        session_destroy();
        header('Location: ' . ADMIN_URL . 'login.php');
        exit;
    }
}

// Classe pour la gestion des sessions candidat
class CandidateAuth {
    
    public static function checkLogin() {
        if (!isset($_SESSION['candidate_logged_in']) || $_SESSION['candidate_logged_in'] !== true) {
            // Rediriger vers la page de connexion avec URL de retour
            header('Location: ' . SITE_URL . 'candidate-login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }
    
    public static function register($prenom, $nom, $email, $telephone, $password) {
        // Vérifier que l'email n'existe pas déjà
        if (DB::isConnected()) {
            $stmt = DB::prepare("SELECT id FROM candidats WHERE email = ?");
            $result = $stmt->execute([$email]);
            if ($result && $result->num_rows > 0) {
                return array('success' => false, 'error' => 'Email déjà utilisé');
            }
            
            // Hasher le mot de passe
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
            
            // Insérer le candidat
            $stmt = DB::prepare("
                INSERT INTO candidats (prenom, nom, email, telephone, password, date_inscription)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$prenom, $nom, $email, $telephone, $password_hashed])) {
                return array('success' => true, 'message' => 'Compte créé avec succès');
            } else {
                return array('success' => false, 'error' => 'Erreur lors de la création du compte');
            }
        } else {
            // Mode simulation
            return array('success' => true, 'message' => 'Compte créé (mode simulation)');
        }
    }
    
    public static function login($email, $password) {
        if (DB::isConnected()) {
            // Vérifier dans la base de données
            $stmt = DB::prepare("SELECT id, prenom, nom, password FROM candidats WHERE email = ?");
            $result = $stmt->execute([$email]);
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Vérifier le mot de passe
                if (password_verify($password, $row['password'])) {
                    // Configurer les variables de session
                    $_SESSION['candidate_logged_in'] = true;
                    $_SESSION['candidate_id'] = $row['id'];
                    $_SESSION['candidate_email'] = $email;
                    $_SESSION['candidate_name'] = $row['prenom'] . ' ' . $row['nom'];
                    $_SESSION['login_time'] = time();
                    return array('success' => true, 'candidate_id' => $row['id']);
                } else {
                    return array('success' => false, 'error' => 'Email ou mot de passe incorrect');
                }
            } else {
                return array('success' => false, 'error' => 'Email ou mot de passe incorrect');
            }
        } else {
            // Mode simulation
            if (isset($_SESSION['candidate_email']) && $_SESSION['candidate_email'] === $email && 
                isset($_SESSION['candidate_password']) && $_SESSION['candidate_password'] === $password) {
                $_SESSION['candidate_logged_in'] = true;
                $_SESSION['candidate_id'] = 0;
                $_SESSION['login_time'] = time();
                return array('success' => true, 'candidate_id' => 0);
            } else {
                return array('success' => false, 'error' => 'Email ou mot de passe incorrect');
            }
        }
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['candidate_logged_in']) && $_SESSION['candidate_logged_in'] === true;
    }
    
    public static function logout() {
        unset($_SESSION['candidate_logged_in']);
        unset($_SESSION['candidate_id']);
        unset($_SESSION['candidate_email']);
        unset($_SESSION['candidate_name']);
        unset($_SESSION['login_time']);
        
        // Supprimer le cookie "Se souvenir de moi"
        if (isset($_COOKIE['candidate_email'])) {
            setcookie('candidate_email', '', time() - 3600, '/');
        }
    }
    
    public static function getCandidateId() {
        return $_SESSION['candidate_id'] ?? null;
    }
    
    public static function getCandidateName() {
        return $_SESSION['candidate_name'] ?? null;
    }
    
    public static function getCandidateEmail() {
        return $_SESSION['candidate_email'] ?? null;
    }
}

// Fonction pour obtenir les statistiques simulées
function getStatistiques() {
    return array(
        'total_inscrits' => 45382,
        'inscrits_aujourd_hui' => 234,
        'taux_validation' => 87.5,
        'taux_abandon' => 12.5,
        'paiements_recus' => 38920,
        'paiements_en_attente' => 6462,
        'montant_total' => 1950000000,  // en FCFA
        'montant_recu' => 1752420000
    );
}

// Fonction pour obtenir les inscrits par région
function getInscritsParRegion() {
    global $regions;
    $data = array();
    foreach ($regions as $region) {
        $data[] = array(
            'region' => $region,
            'total' => rand(2000, 8000),
            'valides' => rand(1500, 7000),
            'en_attente' => rand(100, 1000)
        );
    }
    return $data;
}

// Fonction pour obtenir les inscrits par filière
function getInscritsParFiliere() {
    global $filieres;
    $data = array();
    foreach ($filieres as $filiere) {
        $data[] = array(
            'filiere' => $filiere,
            'total' => rand(3000, 12000),
            'valides' => rand(2500, 10500),
            'abandonnes' => rand(200, 2000)
        );
    }
    return $data;
}

// Fonction pour obtenir les inscrits par concours
function getInscritsParConcours() {
    global $concours;
    $data = array();
    foreach ($concours as $c) {
        $data[] = array(
            'concours' => $c['nom'],
            'sigle' => $c['sigle'],
            'total' => rand(5000, 20000),
            'valides' => rand(4000, 17500),
            'paiements_recus' => rand(3000, 15000),
            'taux_paiement' => rand(60, 95)
        );
    }
    return $data;
}

// Fonction pour obtenir les données de suivi temporel
function getEvolutionInscrits() {
    $data = array();
    $date = strtotime('2025-01-01');
    for ($i = 0; $i < 30; $i++) {
        $data[] = array(
            'date' => date('d/m', $date),
            'inscrits' => 1000 + rand(-200, 800),
            'validates' => 850 + rand(-150, 700),
            'paiements' => 700 + rand(-100, 600)
        );
        $date = strtotime('+1 day', $date);
    }
    return $data;
}

// Fonction pour obtenir les candidats avec détails
function getDetailsCandidats($limit = 10) {
    $data = array();
    $noms = array('Ngo Eric', 'Kamga Pierre', 'Atata Marie', 'Bele Jean', 'Molai Sophie', 'Diop Alain', 'Tata Evelyne', 'Kato Serge', 'Monde Paul', 'Seke Cynthia');
    $filieres = array('Ingénierie', 'Médecine', 'Droit', 'Économie', 'Informatique');
    $regions = array('Adamaoua', 'Centre', 'Est', 'Extrême-Nord', 'Littoral', 'Nord', 'Nord-Ouest', 'Ouest', 'Sud', 'Sud-Ouest');
    $statuts = array('validé', 'en attente', 'abandonné', 'rejeté');
    
    for ($i = 1; $i <= $limit; $i++) {
        $data[] = array(
            'id' => $i,
            'nom' => $noms[rand(0, count($noms)-1)] . ' ' . rand(1000, 9999),
            'email' => 'candidat' . $i . '@email.cm',
            'telephone' => '+237 6' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
            'region' => $regions[rand(0, count($regions)-1)],
            'filiere' => $filieres[rand(0, count($filieres)-1)],
            'concours' => 'CEUP ' . (2024 + rand(0, 1)),
            'statut' => $statuts[rand(0, count($statuts)-1)],
            'date_inscription' => date('d/m/Y', strtotime('-' . rand(1, 90) . ' days')),
            'paiement' => (rand(1, 10) > 3) ? 'Validé' : 'En attente'
        );
    }
    return $data;
}

// Fonction pour obtenir les paiements
function getDetailsPaiements($limit = 10) {
    $data = array();
    $statuts = array('validé', 'en attente', 'rejeté', 'remboursé');
    $methodes = array('MTN Mobile Money', 'Orange Money', 'Virement Bancaire', 'Chèque');
    
    for ($i = 1; $i <= $limit; $i++) {
        $data[] = array(
            'id' => 'PAY-' . date('Y') . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'candidat' => 'Candidat ' . $i,
            'montant' => 25000 + rand(0, 50000),
            'methode' => $methodes[rand(0, count($methodes)-1)],
            'statut' => $statuts[rand(0, count($statuts)-1)],
            'date_paiement' => date('d/m/Y H:i', strtotime('-' . rand(1, 60) . ' days')),
            'reference' => 'REF-' . strtoupper(substr(md5(rand()), 0, 8))
        );
    }
    return $data;
}

// Fonction pour obtenir le taux de paiement par concours
function getTauxPaiementParConcours() {
    global $concours;
    $data = array();
    foreach ($concours as $c) {
        $inscrits = rand(5000, 20000);
        $payes = rand(3000, 18000);
        $taux = round(($payes / $inscrits) * 100, 1);
        $data[] = array(
            'concours' => $c['nom'],
            'sigle' => $c['sigle'],
            'inscrits' => $inscrits,
            'payes' => $payes,
            'en_attente' => $inscrits - $payes,
            'taux' => $taux,
            'montant_total' => $payes * 25000
        );
    }
    return $data;
}

// Fonction pour obtenir les centres de composition
function getCentresComposition() {
    $centres = array(
        array('id' => 1, 'nom' => 'Lycée de Yaoundé A', 'ville' => 'Yaoundé', 'region' => 'Centre', 'places' => 500, 'occupees' => 487),
        array('id' => 2, 'nom' => 'Lycée de Douala', 'ville' => 'Douala', 'region' => 'Littoral', 'places' => 600, 'occupees' => 598),
        array('id' => 3, 'nom' => 'Lycée de Kumba', 'ville' => 'Kumba', 'region' => 'Sud-Ouest', 'places' => 300, 'occupees' => 285),
        array('id' => 4, 'nom' => 'Lycée de Garoua', 'ville' => 'Garoua', 'region' => 'Nord', 'places' => 400, 'occupees' => 350),
        array('id' => 5, 'nom' => 'Lycée de Buea', 'ville' => 'Buea', 'region' => 'Sud-Ouest', 'places' => 350, 'occupees' => 340),
        array('id' => 6, 'nom' => 'Lycée de Bamenda', 'ville' => 'Bamenda', 'region' => 'Nord-Ouest', 'places' => 450, 'occupees' => 420),
    );
    return $centres;
}

// Fonction pour formater la devise en FCFA
function formatFCFA($amount) {
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

// Fonction pour obtenir le statut avec couleur
function getStatutBadge($statut) {
    $colors = array(
        'validé' => array('color' => 'success', 'icon' => 'check-circle'),
        'en attente' => array('color' => 'warning', 'icon' => 'clock'),
        'abandonné' => array('color' => 'danger', 'icon' => 'x-circle'),
        'rejeté' => array('color' => 'danger', 'icon' => 'exclamation-circle'),
        'en cours' => array('color' => 'info', 'icon' => 'arrow-repeat'),
        'remboursé' => array('color' => 'secondary', 'icon' => 'arrow-left'),
    );
    return $colors[$statut] ?? array('color' => 'secondary', 'icon' => 'question-circle');
}
?>
