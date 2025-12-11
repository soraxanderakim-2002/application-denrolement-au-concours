<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnostic Navbar</h1>";
echo "<pre>";

// Test 1: Check if config exists
$config_path = __DIR__ . '/admin/config.php';
echo "1. Config Path: $config_path\n";
echo "   Exists: " . (file_exists($config_path) ? "✅ YES" : "❌ NO") . "\n\n";

// Test 2: Try to load config
echo "2. Loading config.php...\n";
try {
    require_once $config_path;
    echo "   ✅ Config loaded successfully\n\n";
    
    // Test 3: Check classes
    echo "3. Checking classes:\n";
    echo "   AdminAuth exists: " . (class_exists('AdminAuth') ? "✅ YES" : "❌ NO") . "\n";
    echo "   CandidateAuth exists: " . (class_exists('CandidateAuth') ? "✅ YES" : "❌ NO") . "\n";
    echo "   DB exists: " . (class_exists('DB') ? "✅ YES" : "❌ NO") . "\n\n";
    
    // Test 4: Check methods
    if (class_exists('AdminAuth')) {
        echo "4. AdminAuth methods:\n";
        echo "   isLoggedIn: " . (method_exists('AdminAuth', 'isLoggedIn') ? "✅ YES" : "❌ NO") . "\n";
        echo "   getAdminName: " . (method_exists('AdminAuth', 'getAdminName') ? "✅ YES" : "❌ NO") . "\n";
    }
    
    if (class_exists('CandidateAuth')) {
        echo "5. CandidateAuth methods:\n";
        echo "   isLoggedIn: " . (method_exists('CandidateAuth', 'isLoggedIn') ? "✅ YES" : "❌ NO") . "\n";
        echo "   getCandidateName: " . (method_exists('CandidateAuth', 'getCandidateName') ? "✅ YES" : "❌ NO") . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error loading config: " . $e->getMessage() . "\n\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Test 5: Check navbar file
echo "\n6. Navbar file:\n";
$navbar_path = __DIR__ . '/includes/navbar.php';
echo "   Path: $navbar_path\n";
echo "   Exists: " . (file_exists($navbar_path) ? "✅ YES" : "❌ NO") . "\n";

echo "</pre>";
?>
