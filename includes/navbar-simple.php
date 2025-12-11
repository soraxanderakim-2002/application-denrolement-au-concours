<?php
/**
 * Simple Navigation Bar - Test Version
 */

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine auth status
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
$is_candidate = isset($_SESSION['candidate_logged_in']) && $_SESSION['candidate_logged_in'];
$user_name = $_SESSION['candidate_name'] ?? $_SESSION['admin_user'] ?? 'Utilisateur';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    :root {
        --navbar-bg: #212529;
        --navbar-text: #e0e0e0;
        --navbar-active: #4CAF50;
    }
    
    .navbar {
        background-color: var(--navbar-bg);
        z-index: 1000;
        padding: 1rem 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .navbar-brand {
        color: white !important;
        font-weight: bold;
        font-size: 1.5rem;
    }
    
    .navbar-brand i {
        margin-right: 0.5rem;
        color: var(--navbar-active);
    }
    
    .nav-link {
        color: var(--navbar-text) !important;
        margin: 0 0.5rem;
        transition: all 0.3s ease;
    }
    
    .nav-link:hover {
        color: var(--navbar-active) !important;
    }
    
    .nav-link.active {
        color: var(--navbar-active) !important;
        border-bottom: 2px solid var(--navbar-active);
    }
    
    .dropdown-menu {
        background-color: #2d2d2d;
        border: 1px solid #404040;
    }
    
    .dropdown-menu .dropdown-item {
        color: var(--navbar-text);
    }
    
    .dropdown-menu .dropdown-item:hover {
        background-color: #404040;
        color: var(--navbar-active);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-mortarboard"></i> CONCOURS
        </a>

        <!-- Toggle Button (Mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Accueil -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house"></i> Accueil
                    </a>
                </li>

                <!-- Candidat Menu -->
                <?php if ($is_candidate): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="candidateMenu" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-fill"></i> Mon Espace
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="candidateMenu">
                        <li><a class="dropdown-item" href="candidate-dashboard.php">Tableau de bord</a></li>
                        <li><a class="dropdown-item" href="candidate-profile.php">Mon profil</a></li>
                        <li><a class="dropdown-item" href="enroll-form.php">Nouvelle inscription</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Admin Menu -->
                <?php if ($is_admin): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Administration
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminMenu">
                        <li><a class="dropdown-item" href="admin/dashboard.php">Tableau de bord</a></li>
                        <li><a class="dropdown-item" href="admin/candidats.php">Candidats</a></li>
                        <li><a class="dropdown-item" href="admin/enrollments.php">Inscriptions</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admin/parametres.php">Paramètres</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Public Links (Non logged in) -->
                <?php if (!$is_admin && !$is_candidate): ?>
                <li class="nav-item">
                    <a class="nav-link" href="enroll-form.php">
                        <i class="bi bi-pencil"></i> S'inscrire
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="candidate-login.php">
                        <i class="bi bi-box-arrow-in-right"></i> Connexion
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/login.php">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                </li>
                <?php endif; ?>

                <!-- User Menu (Logged In) -->
                <?php if ($is_admin || $is_candidate): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_name); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userMenu">
                        <?php if ($is_candidate): ?>
                        <li><a class="dropdown-item" href="candidate-logout.php">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a></li>
                        <?php endif; ?>
                        <?php if ($is_admin): ?>
                        <li><a class="dropdown-item" href="admin/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
