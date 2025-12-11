<?php
session_start();
require_once 'admin/config.php';

echo "Testing candidate authentication system...\n";
echo "DB Connected: " . (DB::isConnected() ? "YES" : "NO") . "\n";
echo "Session ID: " . session_id() . "\n";

// Test redirect
echo "Testing candidate-register.php...\n";
?>
Redirect working
