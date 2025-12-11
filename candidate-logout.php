<?php
session_start();

// Effacer toutes les variables de session du candidat
unset($_SESSION['candidate_logged_in']);
unset($_SESSION['candidate_id']);
unset($_SESSION['candidate_email']);
unset($_SESSION['candidate_name']);
unset($_SESSION['login_time']);

// Détruire la session
session_destroy();

// Supprimer le cookie "Se souvenir de moi"
if (isset($_COOKIE['candidate_email'])) {
    setcookie('candidate_email', '', time() - 3600, '/');
}

// Rediriger vers la page d'accueil
header('Location: index.php?logout=success');
exit;
?>
