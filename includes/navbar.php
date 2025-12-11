<?php
/**
 * Navigation Bar - Persistent Navigation for All User Roles
 * Displays different menu items based on user authentication status and role
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current user role and status
$is_admin = false;
$is_candidate = false;
$user_name = '';
$user_role = '';

// Try to load auth classes and determine role
try {
    // Check if config is not already loaded
    if (!defined('APP_NAME')) {
        $config_path = __DIR__ . '/../admin/config.php';
        if (file_exists($config_path)) {
            require_once $config_path;
        }
    }
    
    // Check admin
    if (class_exists('AdminAuth') && method_exists('AdminAuth', 'isLoggedIn')) {
        $is_admin = AdminAuth::isLoggedIn();
        if ($is_admin && method_exists('AdminAuth', 'getAdminName')) {
            $user_name = AdminAuth::getAdminName();
            $user_role = 'Admin';
        }
    }
    
    // Check candidate
    if (!$is_admin && class_exists('CandidateAuth') && method_exists('CandidateAuth', 'isLoggedIn')) {
        $is_candidate = CandidateAuth::isLoggedIn();
        if ($is_candidate && method_exists('CandidateAuth', 'getCandidateName')) {
            $user_name = CandidateAuth::getCandidateName();
            $user_role = 'Candidat';
        }
    }
} catch (Exception $e) {
    // Silently fail - navbar will still work
}

// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="/enroll-concours/index.php">
            <i class="bi bi-mortarboard"></i> CONCOURS
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Public Pages (Always Visible) -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" 
                       href="/enroll-concours/index.php">
                        <i class="bi bi-house"></i> Accueil
                    </a>
                </li>

                <!-- Admin Menu -->
                <?php if ($is_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> Administration
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/dashboard.php">
                                    <i class="bi bi-graph-up"></i> Tableau de bord
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'candidats.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/candidats.php">
                                    <i class="bi bi-people"></i> Gestion des candidats
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'enrollments.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/enrollments.php">
                                    <i class="bi bi-file-text"></i> Inscriptions
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'centres.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/centres.php">
                                    <i class="bi bi-building"></i> Centres d'examen
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'paiements.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/paiements.php">
                                    <i class="bi bi-credit-card"></i> Paiements
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'approbations.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/approbations.php">
                                    <i class="bi bi-check-circle"></i> Approbations
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'statistiques.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/statistiques.php">
                                    <i class="bi bi-bar-chart"></i> Statistiques
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'rapports.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/rapports.php">
                                    <i class="bi bi-file-pdf"></i> Rapports
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'parametres.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/admin/parametres.php">
                                    <i class="bi bi-sliders"></i> Paramètres
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Candidate Menu -->
                <?php if ($is_candidate): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="candidateDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> Mon Espace
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="candidateDropdown">
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'candidate-dashboard.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/candidate-dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Tableau de bord
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'candidate-profile.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/candidate-profile.php">
                                    <i class="bi bi-person-lines-fill"></i> Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page === 'enroll-form.php' ? 'active' : ''; ?>" 
                                   href="/enroll-concours/enroll-form.php">
                                    <i class="bi bi-clipboard-check"></i> Nouvelle inscription
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Enrollment Page (For Non-logged Users) -->
                <?php if (!$is_admin && !$is_candidate): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'enroll-form.php' ? 'active' : ''; ?>" 
                           href="/enroll-concours/enroll-form.php">
                            <i class="bi bi-clipboard-check"></i> S'inscrire
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Separator -->
                <li class="nav-item">
                    <hr class="d-lg-none">
                </li>

                <!-- User Profile & Logout -->
                <?php if ($is_admin || $is_candidate): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> 
                            <span class="d-lg-inline d-none"><?php echo htmlspecialchars($user_name); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($user_role); ?>
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($is_candidate): ?>
                                <li>
                                    <a class="dropdown-item" href="/enroll-concours/candidate-logout.php">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($is_admin): ?>
                                <li>
                                    <a class="dropdown-item" href="/enroll-concours/admin/logout.php">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Login Button (For Non-logged Users) -->
                <?php if (!$is_admin && !$is_candidate): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/enroll-concours/candidate-login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion Candidat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/enroll-concours/admin/login.php">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        z-index: 1000;
    }

    .navbar-brand {
        color: #fff !important;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .navbar-brand:hover {
        color: #4CAF50 !important;
        transform: scale(1.05);
    }

    .nav-link {
        color: #e0e0e0 !important;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        color: #4CAF50 !important;
    }

    .nav-link.active {
        color: #4CAF50 !important;
        font-weight: 500;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #4CAF50;
    }

    .dropdown-menu-dark {
        background-color: #2d2d2d;
        border: 1px solid #404040;
    }

    .dropdown-menu-dark .dropdown-item {
        color: #e0e0e0;
        transition: all 0.2s ease;
    }

    .dropdown-menu-dark .dropdown-item:hover {
        background-color: #404040;
        color: #4CAF50;
        padding-left: 1.75rem;
    }

    .dropdown-menu-dark .dropdown-item.active {
        background-color: #4CAF50;
        color: #fff;
    }

    .navbar-toggler {
        border-color: #4CAF50;
    }

    .navbar-toggler:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%234CAF50' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Icon styling */
    .nav-link i,
    .dropdown-item i {
        margin-right: 0.5rem;
        width: 1.2rem;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            margin-top: 1rem;
            border-top: 1px solid #404040;
            padding-top: 1rem;
        }

        .nav-link {
            padding: 0.5rem 0 !important;
            font-size: 1rem;
        }

        .dropdown-menu {
            border: none !important;
            padding: 0 !important;
            background-color: transparent !important;
        }

        .dropdown-menu .dropdown-item {
            padding: 0.75rem 1rem !important;
            border-left: 2px solid transparent;
            margin-left: -1rem;
        }

        .dropdown-menu .dropdown-item:hover {
            border-left-color: #4CAF50;
        }
    }

    /* Dark mode compatible */
    :root {
        --navbar-bg: #212529;
        --navbar-text: #e0e0e0;
        --navbar-active: #4CAF50;
    }
</style>
