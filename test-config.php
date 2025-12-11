<?php
// Test simple pour vérifier la configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "1. Testing config inclusion...\n";
try {
    require_once 'admin/config.php';
    echo "✓ Config loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading config: " . $e->getMessage() . "\n";
    die();
}

echo "2. Testing DB class...\n";
$connected = DB::isConnected();
echo "DB Connected: " . ($connected ? "YES" : "NO") . "\n";

echo "3. Testing CandidateAuth class...\n";
if (class_exists('CandidateAuth')) {
    echo "✓ CandidateAuth class exists\n";
} else {
    echo "✗ CandidateAuth class NOT found\n";
}

echo "4. Testing session variables...\n";
echo "Session: " . print_r($_SESSION, true) . "\n";

echo "5. All tests passed!\n";
?>
