<?php
/**
 * Navigation Bar Test & Verification Script
 * Tests all navbar functionality and permissions
 */

session_start();
require_once 'admin/config.php';

// Test scenarios
$tests_passed = 0;
$tests_failed = 0;

// Helper function to log results
function log_test($test_name, $result) {
    global $tests_passed, $tests_failed;
    if ($result) {
        echo "✅ <strong style='color: green;'>PASS</strong>: $test_name<br>";
        $tests_passed++;
    } else {
        echo "❌ <strong style='color: red;'>FAIL</strong>: $test_name<br>";
        $tests_failed++;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Navbar - Enroll Concours</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .test-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border-left: 4px solid #4CAF50;
            background: #f0f8f5;
            border-radius: 4px;
        }
        .result-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            border: 1px solid #ddd;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #4CAF50;
            margin-top: 20px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
            font-weight: bold;
        }
        .stat-pass {
            background: #27ae60;
        }
        .stat-fail {
            background: #e74c3c;
        }
        .stat-total {
            background: #3498db;
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="test-container">
        <h1><i class="bi bi-bug"></i> Tests de la Barre de Navigation</h1>
        
        <p class="lead">Vérification complète du fonctionnement de la navbar sur toutes les pages.</p>

        <!-- Configuration Tests -->
        <div class="test-section">
            <h2><i class="bi bi-gear"></i> Configuration</h2>
            
            <?php
                // Test 1: Navbar file exists
                $navbar_path = __DIR__ . '/includes/navbar.php';
                log_test('Fichier navbar.php existe', file_exists($navbar_path));
                
                // Test 2: CSS file exists
                $css_path = __DIR__ . '/assets/css/navbar.css';
                log_test('Fichier navbar.css existe', file_exists($css_path));
                
                // Test 3: Config file exists
                $config_path = __DIR__ . '/admin/config.php';
                log_test('Fichier config.php existe', file_exists($config_path));
            ?>
        </div>

        <!-- Authentication Tests -->
        <div class="test-section">
            <h2><i class="bi bi-lock"></i> Authentification</h2>
            
            <?php
                // Test 4: AdminAuth class exists
                log_test('Classe AdminAuth existe', class_exists('AdminAuth'));
                
                // Test 5: CandidateAuth class exists
                log_test('Classe CandidateAuth existe', class_exists('CandidateAuth'));
                
                // Test 6: AdminAuth::isLoggedIn() is callable
                log_test('Méthode AdminAuth::isLoggedIn() existe', is_callable(['AdminAuth', 'isLoggedIn']));
                
                // Test 7: CandidateAuth::isLoggedIn() is callable
                log_test('Méthode CandidateAuth::isLoggedIn() existe', is_callable(['CandidateAuth', 'isLoggedIn']));
                
                // Test 8: Current login states
                $is_admin = AdminAuth::isLoggedIn();
                $is_candidate = CandidateAuth::isLoggedIn();
                echo "<div class='result-box'>";
                echo "État Admin: " . ($is_admin ? "✅ Connecté" : "⭕ Non connecté") . "<br>";
                echo "État Candidat: " . ($is_candidate ? "✅ Connecté" : "⭕ Non connecté") . "<br>";
                echo "</div>";
            ?>
        </div>

        <!-- Session Tests -->
        <div class="test-section">
            <h2><i class="bi bi-person-circle"></i> Sessions</h2>
            
            <?php
                // Test 9: Session started
                log_test('Session est active', isset($_SESSION));
                
                // Test 10: Check session values
                $session_info = [];
                if (isset($_SESSION['admin_logged_in'])) $session_info['Admin'] = $_SESSION['admin_logged_in'];
                if (isset($_SESSION['candidate_logged_in'])) $session_info['Candidat'] = $_SESSION['candidate_logged_in'];
                
                if (empty($session_info)) {
                    echo "<div class='result-box' style='color: #666;'>";
                    echo "<i class='bi bi-info-circle'></i> Aucune session de connexion active (normal pour visiteur)";
                    echo "</div>";
                } else {
                    echo "<div class='result-box'>";
                    foreach ($session_info as $role => $status) {
                        echo "$role: " . ($status ? "✅ Actif" : "⭕ Inactif") . "<br>";
                    }
                    echo "</div>";
                }
            ?>
        </div>

        <!-- Page Detection Tests -->
        <div class="test-section">
            <h2><i class="bi bi-file-earmark"></i> Détection de Page</h2>
            
            <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                
                // Test 11: Current page detection
                log_test('Page actuelle détectée', !empty($current_page));
                
                echo "<div class='result-box'>";
                echo "Page actuelle: <strong>$current_page</strong><br>";
                echo "URI complet: <strong>" . $_SERVER['REQUEST_URI'] . "</strong>";
                echo "</div>";
            ?>
        </div>

        <!-- Pages Verification -->
        <div class="test-section">
            <h2><i class="bi bi-diagram-3"></i> Pages avec Navbar</h2>
            
            <?php
                $pages = [
                    'index.php' => 'Accueil',
                    'candidate-login.php' => 'Connexion Candidat',
                    'candidate-register.php' => 'Enregistrement Candidat',
                    'candidate-dashboard.php' => 'Dashboard Candidat',
                    'candidate-profile.php' => 'Profil Candidat',
                    'enroll-form.php' => 'Formulaire d\'Inscription',
                    'admin/login.php' => 'Connexion Admin',
                    'admin/dashboard.php' => 'Dashboard Admin'
                ];
                
                echo "<table class='table table-sm table-bordered'>";
                echo "<thead style='background: #f0f0f0;'><tr><th>Page</th><th>Description</th><th>Statut</th></tr></thead>";
                echo "<tbody>";
                
                foreach ($pages as $file => $desc) {
                    $full_path = __DIR__ . '/' . $file;
                    $exists = file_exists($full_path);
                    $status = $exists ? '✅ Existe' : '❌ Manquant';
                    $status_color = $exists ? 'green' : 'red';
                    
                    echo "<tr>";
                    echo "<td><code>$file</code></td>";
                    echo "<td>$desc</td>";
                    echo "<td style='color: $status_color; font-weight: bold;'>$status</td>";
                    echo "</tr>";
                    
                    log_test("Page $file existe", $exists);
                }
                
                echo "</tbody>";
                echo "</table>";
            ?>
        </div>

        <!-- Features Tests -->
        <div class="test-section">
            <h2><i class="bi bi-star"></i> Fonctionnalités</h2>
            
            <?php
                // Read navbar.php to check for key features
                $navbar_content = file_get_contents(__DIR__ . '/includes/navbar.php');
                
                $features = [
                    'AdminAuth::isLoggedIn()' => 'Détection Admin',
                    'CandidateAuth::isLoggedIn()' => 'Détection Candidat',
                    'dropdown-menu' => 'Menus Déroulants',
                    'navbar-toggler' => 'Menu Mobile',
                    'sticky-top' => 'Navbar Fixe',
                    'bi bi-person-circle' => 'Icône Profil',
                    'candidate-logout.php' => 'Lien Déconnexion'
                ];
                
                foreach ($features as $code => $feature) {
                    $has_feature = strpos($navbar_content, $code) !== false;
                    log_test("Fonctionnalité: $feature", $has_feature);
                }
            ?>
        </div>

        <!-- CSS Tests -->
        <div class="test-section">
            <h2><i class="bi bi-palette"></i> Style et Design</h2>
            
            <?php
                $css_content = file_get_contents(__DIR__ . '/assets/css/navbar.css');
                
                $css_features = [
                    '--navbar-bg' => 'Variables CSS',
                    '@keyframes slideDown' => 'Animations',
                    '@media (max-width: 991.98px)' => 'Design Responsive',
                    'transition' => 'Transitions Fluides',
                    'box-shadow' => 'Ombres Visuelles'
                ];
                
                foreach ($css_features as $pattern => $feature) {
                    $has_pattern = strpos($css_content, $pattern) !== false;
                    log_test("Style: $feature", $has_pattern);
                }
            ?>
        </div>

        <!-- Summary -->
        <div class="test-section" style="border-left-color: #3498db; background: #ecf0f1;">
            <h2><i class="bi bi-graph-up"></i> Résumé</h2>
            
            <div class="stats">
                <div class="stat-card stat-pass">
                    <div style="font-size: 32px;">✅</div>
                    <div style="font-size: 24px;"><?php echo $tests_passed; ?></div>
                    <div>Tests Réussis</div>
                </div>
                <div class="stat-card stat-fail">
                    <div style="font-size: 32px;">❌</div>
                    <div style="font-size: 24px;"><?php echo $tests_failed; ?></div>
                    <div>Tests Échoués</div>
                </div>
                <div class="stat-card stat-total">
                    <div style="font-size: 32px;">📊</div>
                    <div style="font-size: 24px;"><?php echo $tests_passed + $tests_failed; ?></div>
                    <div>Total des Tests</div>
                </div>
            </div>

            <?php
                $success_rate = ($tests_passed + $tests_failed) > 0 
                    ? round(($tests_passed / ($tests_passed + $tests_failed)) * 100) 
                    : 0;
                    
                $color = $success_rate === 100 ? 'green' : ($success_rate >= 80 ? 'orange' : 'red');
                
                echo "<div class='result-box' style='text-align: center; border: 2px solid $color;'>";
                echo "<strong style='font-size: 18px; color: $color;'>Taux de Réussite: $success_rate%</strong>";
                
                if ($success_rate === 100) {
                    echo "<p style='color: green; margin-top: 10px;'>✅ Tous les tests sont passés! La navbar est prête à l'emploi.</p>";
                } elseif ($success_rate >= 80) {
                    echo "<p style='color: orange; margin-top: 10px;'>⚠️ La plupart des tests sont passés. Quelques points à vérifier.</p>";
                } else {
                    echo "<p style='color: red; margin-top: 10px;'>❌ Certains tests ont échoué. Veuillez vérifier les fichiers.</p>";
                }
                
                echo "</div>";
            ?>
        </div>

        <!-- Recommendations -->
        <div class="test-section" style="border-left-color: #3498db; background: #e3f2fd;">
            <h2><i class="bi bi-lightbulb"></i> Recommandations</h2>
            
            <ul>
                <li><strong>Testez sur mobile</strong>: Redimensionnez le navigateur pour voir le menu hamburger</li>
                <li><strong>Testez les connexions</strong>: Connectez-vous en tant que candidat et admin pour voir les menus différents</li>
                <li><strong>Testez les liens</strong>: Vérifiez que tous les liens de la navbar fonctionnent</li>
                <li><strong>Testez les animations</strong>: Les transitions doivent être fluides</li>
                <li><strong>Testez la surbrillance</strong>: La page active doit être mise en surbrillance</li>
            </ul>
        </div>

        <!-- Navigation Links -->
        <div style="margin-top: 40px; text-align: center; padding: 20px; background: #f0f8f5; border-radius: 8px;">
            <h3><i class="bi bi-arrow-left-right"></i> Tester la Navigation</h3>
            <p>Cliquez sur les liens ci-dessous pour tester la navbar sur différentes pages:</p>
            <div>
                <a href="index.php" class="btn btn-sm btn-primary me-2">
                    <i class="bi bi-house"></i> Accueil
                </a>
                <a href="candidate-login.php" class="btn btn-sm btn-info me-2">
                    <i class="bi bi-box-arrow-in-right"></i> Connexion Candidat
                </a>
                <a href="admin/login.php" class="btn btn-sm btn-warning me-2">
                    <i class="bi bi-shield-lock"></i> Admin
                </a>
                <a href="enroll-form.php" class="btn btn-sm btn-success me-2">
                    <i class="bi bi-pencil"></i> S'inscrire
                </a>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
