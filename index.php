<?php
session_start();

// Données des concours
$concours = [
    [
        'id' => 1,
        'nom' => 'Concours d\'Entrée à l\'Université Publique',
        'sigle' => 'CEUP',
        'description' => 'Accédez à l\'enseignement supérieur public du Cameroun. Plusieurs filières scientifiques et littéraires disponibles.',
        'date_ouverture' => '2025-02-01',
        'date_fermeture' => '2025-03-31',
        'date_epreuve' => '2025-05-15',
        'prix' => 50000,
        'places' => 5000,
        'filieres' => ['Ingénierie', 'Médecine', 'Droit', 'Économie', 'Sciences'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Ouest'],
        'icon' => '🎓'
    ],
    [
        'id' => 2,
        'nom' => 'Concours de Formation Professionnelle',
        'sigle' => 'CFP',
        'description' => 'Formation professionnelle qualifiante dans les domaines de la technique et du commerce.',
        'date_ouverture' => '2025-01-15',
        'date_fermeture' => '2025-02-28',
        'date_epreuve' => '2025-04-10',
        'prix' => 30000,
        'places' => 3000,
        'filieres' => ['Électronique', 'Mécanique', 'Informatique', 'Commerce', 'Hôtellerie'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Est'],
        'icon' => '⚙️'
    ],
    [
        'id' => 3,
        'nom' => 'Concours Militaire',
        'sigle' => 'CM',
        'description' => 'Rejoignez les forces de défense du Cameroun. Formation militaire de qualité internationale.',
        'date_ouverture' => '2025-03-01',
        'date_fermeture' => '2025-04-30',
        'date_epreuve' => '2025-06-20',
        'prix' => 45000,
        'places' => 500,
        'filieres' => ['Infanterie', 'Marine', 'Aéronautique', 'Service'],
        'regions' => ['Nord', 'Centre', 'Littoral', 'Ouest'],
        'icon' => '🪖'
    ],
    [
        'id' => 4,
        'nom' => 'Concours Compétences et Orientation Professionnelle',
        'sigle' => 'CONCOP',
        'description' => 'Évaluez et développez vos compétences professionnelles avec les meilleurs experts.',
        'date_ouverture' => '2025-01-20',
        'date_fermeture' => '2025-03-20',
        'date_epreuve' => '2025-05-01',
        'prix' => 25000,
        'places' => 2000,
        'filieres' => ['Gestion', 'Marketing', 'Ressources Humaines', 'Finance', 'Entrepreneuriat'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Ouest', 'Est'],
        'icon' => '💼'
    ]
];

// Fonction pour formater la devise
function formatFCFA($amount) {
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

// Fonction pour formater la date
function formatDate($date) {
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    return strftime('%d %B %Y', strtotime($date));
}

// Vérifier si un concours est ouvert
function isConcourOuvert($dateOpen, $dateClose) {
    $now = new DateTime();
    $open = new DateTime($dateOpen);
    $close = new DateTime($dateClose);
    return $now >= $open && $now <= $close;
}

// Statut concours
function getStatutConcours($dateOpen, $dateClose) {
    $now = new DateTime();
    $open = new DateTime($dateOpen);
    $close = new DateTime($dateClose);
    
    if ($now > $close) {
        return ['statut' => 'Fermé', 'classe' => 'danger'];
    } elseif ($now < $open) {
        return ['statut' => 'Bientôt', 'classe' => 'warning'];
    } else {
        return ['statut' => 'Ouvert', 'classe' => 'success'];
    }
}

$isLoggedInAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
$isLoggedInCandidate = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Concours - Inscriptions aux Concours Publics du Cameroun</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #27ae60;
            --warning: #e67e22;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* NAVBAR */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            margin-right: 2rem;
        }

        .navbar-brand i {
            margin-right: 0.5rem;
            color: var(--accent);
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: white !important;
        }

        .btn-login {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            margin: 0 0.25rem;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #229954;
            transform: translateY(-2px);
            color: white;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, #1abc9c 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><path d="M0,300 Q300,200 600,300 T1200,300 L1200,600 L0,600 Z" fill="rgba(255,255,255,0.1)"/></svg>') no-repeat bottom;
            background-size: cover;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn-primary-hero {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-hero:hover {
            background-color: #229954;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-secondary-hero {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-secondary-hero:hover {
            background-color: white;
            color: var(--primary);
        }

        /* STATS */
        .stats-section {
            padding: 3rem 0;
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
        }

        .stat-item {
            text-align: center;
            padding: 2rem 0;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        /* CONCOURS CARDS */
        .concours-section {
            padding: 4rem 0;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), #1abc9c);
            border-radius: 2px;
        }

        .concours-card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .concours-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .concours-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            position: relative;
        }

        .concours-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .concours-sigle {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 0.25rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .concours-body {
            padding: 2rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .concours-nom {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.8rem;
        }

        .concours-description {
            color: #7f8c8d;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            flex-grow: 1;
        }

        .concours-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-icon {
            color: var(--accent);
            margin-right: 0.8rem;
            font-weight: 600;
            min-width: 20px;
        }

        .detail-label {
            color: #7f8c8d;
            margin-right: 0.5rem;
        }

        .detail-value {
            color: var(--primary);
            font-weight: 600;
        }

        .statut-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .statut-success {
            background-color: #d4edda;
            color: #155724;
        }

        .statut-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .statut-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .concours-footer {
            border-top: 1px solid #e0e0e0;
            padding-top: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .btn-enroll {
            flex: 1;
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        .btn-enroll:hover {
            background-color: #229954;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .btn-enroll:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-info-concours {
            flex: 1;
            background-color: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        .btn-info-concours:hover {
            background-color: var(--primary);
            color: white;
            text-decoration: none;
        }

        /* INFOS SECTION */
        .infos-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .info-box {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid var(--accent);
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .info-box h5 {
            color: var(--accent);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* FOOTER */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 3rem 0 1rem;
            border-top: 1px solid #34495e;
        }

        .footer-section h6 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--accent);
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-link:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            padding-top: 2rem;
            margin-top: 2rem;
            text-align: center;
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-primary-hero, .btn-secondary-hero {
                width: 100%;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .concours-icon {
                font-size: 2.5rem;
            }

            .concours-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Bienvenue sur Enroll Concours</h1>
                <p>Plateforme d'inscription aux concours publics du Cameroun</p>
                <div class="hero-buttons">
                    <a href="enroll-form.php" class="btn btn-primary-hero" style="background: linear-gradient(135deg, #27ae60, #219653); font-weight: 700;">
                        <i class="bi bi-pencil-fill"></i> Enroll Now
                    </a>
                    <a href="#concours" class="btn btn-secondary-hero">Voir les Concours</a>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Concours Disponibles</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number">12,500+</div>
                        <div class="stat-label">Candidats Inscrits</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Sécurité Garantie</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support Client</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONCOURS -->
    <section id="concours" class="concours-section">
        <div class="container">
            <h2 class="section-title">Concours Disponibles</h2>
            <div class="row">
                <?php foreach ($concours as $c): ?>
                    <?php $statut = getStatutConcours($c['date_ouverture'], $c['date_fermeture']); ?>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="concours-card">
                            <div class="concours-header">
                                <div class="concours-icon"><?php echo $c['icon']; ?></div>
                                <h3 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($c['nom']); ?></h3>
                                <span class="concours-sigle"><?php echo $c['sigle']; ?></span>
                            </div>
                            <div class="concours-body">
                                <div class="statut-badge statut-<?php echo $statut['classe']; ?>">
                                    <i class="bi bi-circle-fill" style="font-size: 0.6rem; margin-right: 0.4rem;"></i>
                                    <?php echo $statut['statut']; ?>
                                </div>
                                <p class="concours-description"><?php echo htmlspecialchars($c['description']); ?></p>
                                <div class="concours-details">
                                    <div class="detail-item">
                                        <span class="detail-icon">📅</span>
                                        <span class="detail-label">Inscriptions:</span>
                                        <span class="detail-value"><?php echo formatDate($c['date_ouverture']); ?> - <?php echo formatDate($c['date_fermeture']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-icon">✏️</span>
                                        <span class="detail-label">Épreuve:</span>
                                        <span class="detail-value"><?php echo formatDate($c['date_epreuve']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-icon">💰</span>
                                        <span class="detail-label">Prix:</span>
                                        <span class="detail-value"><?php echo formatFCFA($c['prix']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-icon">👥</span>
                                        <span class="detail-label">Places:</span>
                                        <span class="detail-value"><?php echo number_format($c['places'], 0, ',', ' '); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="concours-footer">
                                <?php if ($statut['statut'] === 'Ouvert'): ?>
                                    <a href="enroll-form.php?concours_id=<?php echo $c['id']; ?>" class="btn btn-enroll">
                                        <i class="bi bi-pencil-fill"></i> S'inscrire
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-enroll" disabled>
                                        <i class="bi bi-lock-fill"></i> <?php echo $statut['statut'] === 'Fermé' ? 'Fermé' : 'Bientôt'; ?>
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-info-concours" onclick="toggleDetails(<?php echo $c['id']; ?>)">
                                    <i class="bi bi-info-circle"></i> Détails
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- INFOS -->
    <section id="infos" class="infos-section">
        <div class="container">
            <h2 class="section-title" style="color: white;">Comment ça marche?</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="bi bi-1-circle"></i> Créer un Compte</h5>
                        <p>Remplissez le formulaire d'inscription avec vos informations personnelles. C'est gratuit et sécurisé.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="bi bi-2-circle"></i> Choisir un Concours</h5>
                        <p>Sélectionnez le concours qui vous intéresse parmi les 4 concours disponibles.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="bi bi-3-circle"></i> Remplir le Formulaire</h5>
                        <p>Complétez votre dossier avec tous les documents requis et vos informations académiques.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="bi bi-4-circle"></i> Effectuer le Paiement</h5>
                        <p>Payez les frais d'inscription via les méthodes acceptées et recevez votre confirmation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="py-5" style="background-color: white; border-top: 1px solid #e0e0e0;">
        <div class="container">
            <h2 class="section-title">Besoin d'Aide?</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div style="background-color: #f8f9fa; padding: 2rem; border-radius: 12px; text-align: center;">
                        <p style="color: #7f8c8d; margin-bottom: 1.5rem;">
                            Contactez notre équipe de support pour toute question ou assistance.
                        </p>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-telephone" style="font-size: 2rem; color: var(--accent);"></i>
                                <p style="margin-top: 1rem; color: var(--primary);">+237 123 456 789</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-envelope" style="font-size: 2rem; color: var(--accent);"></i>
                                <p style="margin-top: 1rem; color: var(--primary);">support@enrollconcours.cm</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-geo-alt" style="font-size: 2rem; color: var(--accent);"></i>
                                <p style="margin-top: 1rem; color: var(--primary);">Yaoundé, Cameroun</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6><i class="bi bi-mortarboard-fill"></i> Enroll Concours</h6>
                        <p style="font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                            Votre plateforme pour réussir vos concours au Cameroun.
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Navigation</h6>
                        <a href="#concours" class="footer-link">Concours</a>
                        <a href="#infos" class="footer-link">Informations</a>
                        <a href="#contact" class="footer-link">Contact</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Légal</h6>
                        <a href="#" class="footer-link">Conditions d'utilisation</a>
                        <a href="#" class="footer-link">Politique de confidentialité</a>
                        <a href="#" class="footer-link">Mentions légales</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Réseaux Sociaux</h6>
                        <a href="#" class="footer-link"><i class="bi bi-facebook"></i> Facebook</a>
                        <a href="#" class="footer-link"><i class="bi bi-twitter"></i> Twitter</a>
                        <a href="#" class="footer-link"><i class="bi bi-linkedin"></i> LinkedIn</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Enroll Concours. Tous les droits réservés. | Plateforme d'Inscription aux Concours Publics du Cameroun</p>
            </div>
        </div>
    </footer>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDetails(concourId) {
            alert('Détails du concours ' + concourId);
            // À implémenter: modal avec détails complets
        }
    </script>
</body>
</html>
