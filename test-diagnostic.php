<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test Authentification</title></head><body>";
echo "<h1>Test du Système d'Authentification</h1>";

try {
    echo "<h2>1. Inclusion de config.php</h2>";
    require_once 'admin/config.php';
    echo "<p style='color:green'>✓ Config loaded</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>✗ Erreur: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>2. Vérification DB</h2>";
echo "<p>DB Connected: " . (DB::isConnected() ? "YES" : "NO (Mode simulation)") . "</p>";

echo "<h2>3. Vérification classes</h2>";
echo "<p>AdminAuth exists: " . (class_exists('AdminAuth') ? "YES" : "NO") . "</p>";
echo "<p>CandidateAuth exists: " . (class_exists('CandidateAuth') ? "YES" : "NO") . "</p>";

echo "<h2>4. Test simple</h2>";
echo "<p><a href='candidate-register.php'>→ Test candidate-register.php</a></p>";
echo "<p><a href='candidate-login.php'>→ Test candidate-login.php</a></p>";

echo "</body></html>";
?>
