<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar - Visualisation Interactive</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --danger: #e74c3c;
            --info: #3498db;
            --warning: #f39c12;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 20px;
        }

        .container-main {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: var(--primary);
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2rem;
            color: #666;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 5px solid var(--accent);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .stat-card h3 {
            color: var(--accent);
            font-size: 2.5rem;
            margin: 0;
            font-weight: 700;
        }

        .stat-card p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 0.95rem;
        }

        .features-container {
            margin: 50px 0;
        }

        .feature-group {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .feature-group h2 {
            color: var(--primary);
            margin-bottom: 20px;
            border-bottom: 3px solid var(--accent);
            padding-bottom: 10px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list i {
            color: var(--accent);
            font-size: 1.3rem;
            width: 30px;
            text-align: center;
        }

        .navbar-preview {
            background: #212529;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            display: flex;
            align-items: center;
            gap: 30px;
            overflow-x: auto;
        }

        .navbar-item {
            color: #e0e0e0;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .navbar-item:hover {
            color: var(--accent);
            background: #404040;
        }

        .navbar-item.active {
            color: var(--accent);
            font-weight: bold;
            border-bottom: 2px solid var(--accent);
        }

        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .comparison-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .comparison-box h3 {
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comparison-box.before h3 {
            color: var(--danger);
        }

        .comparison-box.before h3 i {
            color: var(--danger);
        }

        .comparison-box.after h3 {
            color: var(--accent);
        }

        .comparison-box.after h3 i {
            color: var(--accent);
        }

        .point {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .point:last-child {
            border-bottom: none;
        }

        .point i {
            margin-top: 3px;
            flex-shrink: 0;
        }

        .before .point i {
            color: var(--danger);
        }

        .after .point i {
            color: var(--accent);
        }

        .timeline {
            position: relative;
            padding: 30px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent);
        }

        .timeline-item {
            margin-bottom: 30px;
            margin-left: 80px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -50px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--accent);
            border: 3px solid white;
        }

        .timeline-item h4 {
            color: var(--primary);
            margin: 0 0 5px 0;
            font-weight: 700;
        }

        .timeline-item p {
            color: #666;
            margin: 0;
            font-size: 0.95rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--accent) 0%, #1e8449 100%);
            color: white;
            padding: 50px;
            border-radius: 12px;
            text-align: center;
            margin: 50px 0;
        }

        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .btn-cta {
            background: white;
            color: var(--accent);
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            color: var(--accent);
        }

        @media (max-width: 768px) {
            .comparison {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .navbar-preview {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid rgba(0,0,0,0.1);
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Actual Navbar -->
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-main">
        <!-- Header -->
        <div class="header">
            <h1>🎯 Barre de Navigation Persistante</h1>
            <p>Solution complète pour une navigation intuitive et cohérente</p>
        </div>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>8</h3>
                <p>Pages Intégrées</p>
            </div>
            <div class="stat-card">
                <h3>3</h3>
                <p>Rôles Utilisateur</p>
            </div>
            <div class="stat-card">
                <h3>100%</h3>
                <p>Responsive Design</p>
            </div>
            <div class="stat-card">
                <h3>5+</h3>
                <p>Animations</p>
            </div>
        </div>

        <!-- Features -->
        <div class="features-container">
            <div class="feature-group">
                <h2><i class="bi bi-star"></i> Fonctionnalités Principales</h2>
                <ul class="feature-list">
                    <li><i class="bi bi-check-circle"></i> Navigation adaptative selon le rôle utilisateur</li>
                    <li><i class="bi bi-check-circle"></i> Détection automatique de la page active</li>
                    <li><i class="bi bi-check-circle"></i> Design responsive (mobile, tablette, desktop)</li>
                    <li><i class="bi bi-check-circle"></i> Animations fluides et élégantes</li>
                    <li><i class="bi bi-check-circle"></i> Menus déroulants intuitifs</li>
                    <li><i class="bi bi-check-circle"></i> Accessibilité WCAG AA/AAA</li>
                    <li><i class="bi bi-check-circle"></i> Performance optimisée</li>
                    <li><i class="bi bi-check-circle"></i> Code réutilisable et maintenable</li>
                </ul>
            </div>

            <!-- Navbar Preview -->
            <div class="feature-group">
                <h2><i class="bi bi-palette"></i> Aperçu de la Navbar</h2>
                <div class="navbar-preview">
                    <span style="font-weight: bold;">🎓 CONCOURS</span>
                    <span>|</span>
                    <a class="navbar-item active" href="#">Accueil</a>
                    <a class="navbar-item" href="#">S'inscrire</a>
                    <a class="navbar-item" href="#">Connexion</a>
                    <a class="navbar-item" href="#">Admin</a>
                    <span style="margin-left: auto;">☰ (Mobile)</span>
                </div>
            </div>

            <!-- Three User Profiles -->
            <div class="feature-group">
                <h2><i class="bi bi-people"></i> Trois Profils Utilisateur</h2>
                <div class="comparison">
                    <div style="padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid #3498db;">
                        <h4>👤 Visiteur</h4>
                        <small>Non authentifié</small>
                        <ul style="list-style: none; padding: 10px 0 0 0; margin: 0;">
                            <li>✓ Accueil</li>
                            <li>✓ S'inscrire</li>
                            <li>✓ Connexion Candidat</li>
                            <li>✓ Admin</li>
                        </ul>
                    </div>
                    <div style="padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid var(--accent);">
                        <h4>🎓 Candidat</h4>
                        <small>Authentifié</small>
                        <ul style="list-style: none; padding: 10px 0 0 0; margin: 0;">
                            <li>✓ Accueil</li>
                            <li>✓ Mon Espace</li>
                            <li>✓ Tableau de Bord</li>
                            <li>✓ Profil & Déconnexion</li>
                        </ul>
                    </div>
                    <div style="padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid var(--danger);">
                        <h4>⚙️ Admin</h4>
                        <small>Authentifié</small>
                        <ul style="list-style: none; padding: 10px 0 0 0; margin: 0;">
                            <li>✓ Accueil</li>
                            <li>✓ Administration</li>
                            <li>✓ Gestion Complète</li>
                            <li>✓ Profil & Déconnexion</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Comparison Avant/Après -->
            <div class="feature-group">
                <h2><i class="bi bi-arrow-left-right"></i> Avant vs Après</h2>
                <div class="comparison">
                    <div class="comparison-box before">
                        <h3><i class="bi bi-x-circle"></i> Avant</h3>
                        <div class="point">
                            <i class="bi bi-x"></i>
                            <span>Navbar différente sur chaque page</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-x"></i>
                            <span>Code dupliqué partout</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-x"></i>
                            <span>Modifications difficiles</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-x"></i>
                            <span>Manque de cohérence</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-x"></i>
                            <span>Problèmes d'accessibilité</span>
                        </div>
                    </div>
                    <div class="comparison-box after">
                        <h3><i class="bi bi-check-circle"></i> Après</h3>
                        <div class="point">
                            <i class="bi bi-check"></i>
                            <span>Navbar unique et réutilisable</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-check"></i>
                            <span>Code centralisé</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-check"></i>
                            <span>Modifications simples</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-check"></i>
                            <span>Cohérence garantie</span>
                        </div>
                        <div class="point">
                            <i class="bi bi-check"></i>
                            <span>Accessible et responsive</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="feature-group">
                <h2><i class="bi bi-calendar"></i> Timeline du Projet</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <h4>Création du Composant</h4>
                        <p>includes/navbar.php - 336 lignes</p>
                    </div>
                    <div class="timeline-item">
                        <h4>Design et Style</h4>
                        <p>assets/css/navbar.css - 342 lignes</p>
                    </div>
                    <div class="timeline-item">
                        <h4>Intégration Complète</h4>
                        <p>8 pages mises à jour</p>
                    </div>
                    <div class="timeline-item">
                        <h4>Documentation</h4>
                        <p>5 guides complets créés</p>
                    </div>
                    <div class="timeline-item">
                        <h4>Tests et Vérification</h4>
                        <p>Outil de test interactif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="cta-section">
            <h2>Prêt à Commencer?</h2>
            <p>La navbar est complètement intégrée et prête pour la production</p>
            <a href="index.php" class="btn-cta"><i class="bi bi-house"></i> Accueil</a>
            <a href="test-navbar.php" class="btn-cta"><i class="bi bi-bug"></i> Tests</a>
            <a href="NAVBAR_QUICK_START.md" class="btn-cta"><i class="bi bi-book"></i> Documentation</a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>✅ Barre de Navigation Persistante | v1.0 | Prête pour la Production</p>
            <p style="font-size: 0.9rem; color: #999;">Créée le 9 Décembre 2025</p>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
