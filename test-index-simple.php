<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Index</title>
    <link rel='stylesheet' href='bootstrap/css/bootstrap.min.css'>
    <style>
        body { background: #f0f0f0; padding: 20px; }
        .test-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class='test-box'>
        <h1>Test de Diagnostic</h1>";

// Test 1: Check paths
echo "<p><strong>1. Vérification des chemins:</strong></p>";
$paths = [
    'index.php' => __DIR__ . '/index.php',
    'admin/config.php' => __DIR__ . '/admin/config.php',
    'includes/navbar.php' => __DIR__ . '/includes/navbar.php',
    'assets/css/navbar.css' => __DIR__ . '/assets/css/navbar.css'
];

foreach ($paths as $name => $path) {
    $exists = file_exists($path);
    $class = $exists ? 'success' : 'error';
    $status = $exists ? '✅ Existe' : '❌ Manquant';
    echo "<p class='$class'>$name: $status</p>";
}

// Test 2: Try to load navbar
echo "<p><strong>2. Chargement de la navbar:</strong></p>";
try {
    ob_start();
    require_once __DIR__ . '/includes/navbar.php';
    $navbar_output = ob_get_clean();
    echo "<p class='success'>✅ Navbar chargée avec succès (" . strlen($navbar_output) . " caractères)</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 3: Session
echo "<p><strong>3. Session:</strong></p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session status: " . (session_status() === PHP_SESSION_NONE ? 'None' : 'Active') . "</p>";

echo "    </div>
</body>
</html>";
?>
