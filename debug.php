<?php
// Fichier de débogage pour tracer les redirections
session_start();

$log = __DIR__ . '/debug.log';

$debug_info = date('Y-m-d H:i:s') . " | " . 
    $_SERVER['REQUEST_URI'] . " | " . 
    "Session ID: " . session_id() . " | " .
    "candidate_logged_in: " . (isset($_SESSION['candidate_logged_in']) ? 'YES' : 'NO') . "\n";

file_put_contents($log, $debug_info, FILE_APPEND);

// Afficher les infos
echo "<pre>Debug Log:\n";
echo file_get_contents($log);
echo "</pre>";
?>
