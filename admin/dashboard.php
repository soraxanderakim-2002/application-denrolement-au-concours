<?php
session_start();
require_once 'config.php';

// Vérifier l'authentification
AdminAuth::checkLogin();

$stats = getStatistiques();
$inscritsParRegion = getInscritsParRegion();
$inscritsParFiliere = getInscritsParFiliere();
$inscritsParConcours = getInscritsParConcours();
$evolutionInscrits = getEvolutionInscrits();

// Calcul des KPIs
$totalRecu = $stats['paiements_recus'];
$totalAttente = $stats['paiements_en_attente'];
$montantRecu = $stats['montant_recu'];
$tauxPaiement = round(($totalRecu / ($totalRecu + $totalAttente)) * 100, 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur | Enregistrement Concours Cameroun</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <style>
        :root {
          --primary: #2c3e50;
          --secondary: #34495e;
          --accent: #27ae60;
          --accent-dark: #219653;
          --accent-light: #2ecc71;
          --warning: #e67e22;
          --danger: #e74c3c;
          --info: #3498db;
          --muted: #6c757d;
          --light: #f8f9fa;
          --dark: #343a40;
          --radius: 12px;
          --shadow: 0 4px 12px rgba(0,0,0,0.08);
          --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
          --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            min-height: 100vh;
            padding: 30px 20px;
            position: sticky;
            top: 0;
            box-shadow: var(--shadow-lg);
        }

        .sidebar .logo {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: var(--radius);
        }

        .sidebar .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar .nav-menu li {
            margin-bottom: 10px;
        }

        .sidebar .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }

        .sidebar .nav-menu a:hover,
        .sidebar .nav-menu a.active {
            background: var(--accent);
            color: #fff;
        }

        .sidebar .nav-menu i {
            font-size: 18px;
            width: 20px;
        }

        .topbar {
            background: #fff;
            padding: 20px 30px;
            border-radius: var(--radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: var(--shadow);
        }

        .topbar .title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .topbar .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topbar .user-menu a {
            color: var(--muted);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .topbar .user-menu a:hover {
            background: var(--light);
            color: var(--primary);
        }

        .main-content {
            padding: 0 30px 30px;
        }

        .kpi-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: #fff;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-left: 4px solid var(--accent);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .kpi-card.danger {
            border-left-color: var(--danger);
        }

        .kpi-card.warning {
            border-left-color: var(--warning);
        }

        .kpi-card.info {
            border-left-color: var(--info);
        }

        .kpi-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light);
            border-radius: 12px;
            font-size: 28px;
        }

        .kpi-card.danger .kpi-icon {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .kpi-card.warning .kpi-icon {
            background: rgba(230, 126, 34, 0.1);
            color: var(--warning);
        }

        .kpi-card.info .kpi-icon {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .kpi-card.danger .kpi-icon,
        .kpi-card.warning .kpi-icon,
        .kpi-card.info .kpi-icon {
            background: var(--light);
            color: var(--primary);
        }

        .kpi-content h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: var(--muted);
            font-weight: 600;
        }

        .kpi-content .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .kpi-content .percent {
            font-size: 12px;
            color: var(--accent);
            margin-top: 5px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .dashboard-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header .badge {
            background: var(--accent);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .chart-container {
            position: relative;
            height: 450px;
            margin-bottom: 20px;
        }

        .table-responsive-custom {
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: var(--light);
        }

        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: var(--primary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 12px 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--light);
        }

        .badge-success {
            background: var(--accent);
        }

        .badge-warning {
            background: var(--warning);
        }

        .badge-danger {
            background: var(--danger);
        }

        .badge-info {
            background: var(--info);
        }

        .btn-custom {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-export {
            background: var(--accent);
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-export:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .progress-bar-custom {
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-bar-custom .bar {
            height: 100%;
            background: var(--accent);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .progress-bar-custom.danger .bar {
            background: var(--danger);
        }

        .progress-bar-custom.warning .bar {
            background: var(--warning);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .full-width-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 250px;
                height: 100vh;
                transition: left 0.3s ease;
                z-index: 1000;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                padding: 0 15px 30px;
            }

            .topbar {
                padding: 15px 20px;
                margin-bottom: 15px;
            }

            .topbar .title {
                font-size: 18px;
            }

            .kpi-container {
                grid-template-columns: 1fr;
                gap: 15px;
                margin-bottom: 20px;
            }

            .cards-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/../includes/navbar-simple.php'; ?>
    
    <div class="container-fluid h-100">
        <div class="row h-100 g-0">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 col-xl-2 sidebar">
                <div class="logo">
                    <i class="bi bi-graph-up"></i>
                    <span>Enroll Admin</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Tableau de Bord</a></li>
                    <li><a href="statistiques.php"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
                    <li><a href="rapports.php"><i class="bi bi-file-earmark-text"></i> Rapports</a></li>
                    <li><a href="paiements.php"><i class="bi bi-credit-card"></i> Paiements</a></li>
                    <li><a href="candidats.php"><i class="bi bi-people"></i> Candidats</a></li>
                    <li><a href="centres.php"><i class="bi bi-building"></i> Centres</a></li>
                    <li><hr class="text-white-50 my-3"></li>
                    <li><a href="parametres.php"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <!-- Topbar -->
                <div class="topbar">
                    <h1 class="title"><i class="bi bi-speedometer2"></i> Tableau de Bord</h1>
                    <div class="user-menu">
                        <span>Bienvenue, <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                        <i class="bi bi-person-circle" style="font-size: 24px; color: var(--primary);"></i>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- KPI Cards -->
                    <div class="kpi-container">
                        <div class="kpi-card">
                            <div class="kpi-icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="kpi-content">
                                <h3>Total Inscrits</h3>
                                <p class="value"><?php echo number_format($stats['total_inscrits'], 0, '.', ' '); ?></p>
                                <p class="percent"><i class="bi bi-arrow-up"></i> +<?php echo $stats['inscrits_aujourd_hui']; ?> aujourd'hui</p>
                            </div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="kpi-content">
                                <h3>Taux de Validation</h3>
                                <p class="value"><?php echo $stats['taux_validation']; ?>%</p>
                                <p class="percent">Profils validés</p>
                            </div>
                        </div>

                        <div class="kpi-card danger">
                            <div class="kpi-icon">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div class="kpi-content">
                                <h3>Taux d'Abandon</h3>
                                <p class="value"><?php echo $stats['taux_abandon']; ?>%</p>
                                <p class="percent">Processus incomplets</p>
                            </div>
                        </div>

                        <div class="kpi-card warning">
                            <div class="kpi-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="kpi-content">
                                <h3>Taux de Paiement</h3>
                                <p class="value"><?php echo $tauxPaiement; ?>%</p>
                                <p class="percent"><?php echo number_format($totalRecu, 0, '.', ' '); ?> paiements</p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Tables -->
                    <div class="cards-container">
                        <!-- Évolution des Inscrits -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-graph-up"></i> Évolution des Inscrits</h3>
                                <span class="badge">30 jours</span>
                            </div>
                            <div class="card-body">
                                <canvas id="evolutionChart" style="max-height: 450px;"></canvas>
                            </div>
                        </div>

                        <!-- Distribution par Région -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-map"></i> Distribution par Région</h3>
                                <span class="badge"><?php echo count($inscritsParRegion); ?> régions</span>
                            </div>
                            <div class="card-body">
                                <canvas id="regionsChart" style="max-height: 450px;"></canvas>
                            </div>
                        </div>

                        <!-- Distribution par Filière -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-book"></i> Distribution par Filière</h3>
                                <span class="badge"><?php echo count($inscritsParFiliere); ?> filières</span>
                            </div>
                            <div class="card-body">
                                <canvas id="filieresChart" style="max-height: 450px;"></canvas>
                            </div>
                        </div>

                        <!-- Inscrits par Concours -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-trophy"></i> Inscrits par Concours</h3>
                                <span class="badge">4 concours</span>
                            </div>
                            <div class="card-body">
                                <canvas id="concoursChart" style="max-height: 450px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Tables -->
                    <div class="full-width-grid">
                        <!-- Tableau Inscrits par Région -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-map"></i> Détail Inscrits par Région</h3>
                                <button class="btn btn-export" onclick="exportTableToExcel('regionTable', 'inscrits_region.xlsx')">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive-custom">
                                    <table class="table" id="regionTable">
                                        <thead>
                                            <tr>
                                                <th>Région</th>
                                                <th style="text-align: center;">Total</th>
                                                <th style="text-align: center;">Validés</th>
                                                <th style="text-align: center;">En Attente</th>
                                                <th>Taux</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inscritsParRegion as $region): 
                                                $tauxValidation = round(($region['valides'] / $region['total']) * 100, 1);
                                            ?>
                                            <tr>
                                                <td><?php echo $region['region']; ?></td>
                                                <td style="text-align: center;"><strong><?php echo number_format($region['total'], 0, '.', ' '); ?></strong></td>
                                                <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($region['valides'], 0, '.', ' '); ?></span></td>
                                                <td style="text-align: center;"><span class="badge badge-warning"><?php echo $region['en_attente']; ?></span></td>
                                                <td>
                                                    <div class="progress-bar-custom">
                                                        <div class="bar" style="width: <?php echo $tauxValidation; ?>%"></div>
                                                    </div>
                                                    <small><?php echo $tauxValidation; ?>%</small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau Inscrits par Filière -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-book"></i> Détail Inscrits par Filière</h3>
                                <button class="btn btn-export" onclick="exportTableToExcel('filiereTable', 'inscrits_filiere.xlsx')">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive-custom">
                                    <table class="table" id="filiereTable">
                                        <thead>
                                            <tr>
                                                <th>Filière</th>
                                                <th style="text-align: center;">Total</th>
                                                <th style="text-align: center;">Validés</th>
                                                <th style="text-align: center;">Abandonnés</th>
                                                <th>Taux de Validation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inscritsParFiliere as $filiere): 
                                                $tauxValidation = round(($filiere['valides'] / $filiere['total']) * 100, 1);
                                            ?>
                                            <tr>
                                                <td><strong><?php echo $filiere['filiere']; ?></strong></td>
                                                <td style="text-align: center;"><?php echo number_format($filiere['total'], 0, '.', ' '); ?></td>
                                                <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($filiere['valides'], 0, '.', ' '); ?></span></td>
                                                <td style="text-align: center;"><span class="badge badge-danger"><?php echo $filiere['abandonnes']; ?></span></td>
                                                <td>
                                                    <div class="progress-bar-custom">
                                                        <div class="bar" style="width: <?php echo $tauxValidation; ?>%"></div>
                                                    </div>
                                                    <small><?php echo $tauxValidation; ?>%</small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau Inscrits par Concours -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3><i class="bi bi-trophy"></i> Détail Inscrits par Concours</h3>
                                <button class="btn btn-export" onclick="exportTableToExcel('concoursTable', 'inscrits_concours.xlsx')">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive-custom">
                                    <table class="table" id="concoursTable">
                                        <thead>
                                            <tr>
                                                <th>Concours</th>
                                                <th style="text-align: center;">Total</th>
                                                <th style="text-align: center;">Validés</th>
                                                <th style="text-align: center;">Paiements</th>
                                                <th>Taux Paiement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inscritsParConcours as $concours): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $concours['sigle']; ?></strong><br>
                                                    <small class="text-muted"><?php echo $concours['concours']; ?></small>
                                                </td>
                                                <td style="text-align: center;"><?php echo number_format($concours['total'], 0, '.', ' '); ?></td>
                                                <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($concours['valides'], 0, '.', ' '); ?></span></td>
                                                <td style="text-align: center;"><span class="badge badge-info"><?php echo number_format($concours['paiements_recus'], 0, '.', ' '); ?></span></td>
                                                <td>
                                                    <div class="progress-bar-custom">
                                                        <div class="bar" style="width: <?php echo $concours['taux_paiement']; ?>%"></div>
                                                    </div>
                                                    <small><?php echo $concours['taux_paiement']; ?>%</small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Overview -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-currency-naira"></i> Situation Financière</h3>
                            <span class="badge">FCFA</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Montant Total Requis</h5>
                                    <p class="h4" style="color: var(--primary); font-weight: 700;">
                                        <?php echo number_format($stats['montant_total'], 0, '.', ' '); ?> FCFA
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Montant Reçu</h5>
                                    <p class="h4" style="color: var(--accent); font-weight: 700;">
                                        <?php echo number_format($montantRecu, 0, '.', ' '); ?> FCFA
                                    </p>
                                    <div class="progress-bar-custom">
                                        <div class="bar" style="width: <?php echo round(($montantRecu / $stats['montant_total']) * 100, 1); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <script>
        // Données pour les graphiques
        const evolutionData = <?php echo json_encode($evolutionInscrits); ?>;
        const regionsData = <?php echo json_encode($inscritsParRegion); ?>;
        const filieresData = <?php echo json_encode($inscritsParFiliere); ?>;
        const concoursData = <?php echo json_encode($inscritsParConcours); ?>;

        // Couleurs
        const colors = {
            primary: '#2c3e50',
            accent: '#27ae60',
            warning: '#e67e22',
            danger: '#e74c3c',
            info: '#3498db',
            light: '#f8f9fa'
        };

        // Graphique Évolution des Inscrits
        const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: evolutionData.map(d => d.date),
                datasets: [
                    {
                        label: 'Inscrits',
                        data: evolutionData.map(d => d.inscrits),
                        borderColor: colors.accent,
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Validés',
                        data: evolutionData.map(d => d.validates),
                        borderColor: colors.info,
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Paiements',
                        data: evolutionData.map(d => d.paiements),
                        borderColor: colors.warning,
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        // Graphique Régions
        const regionsCtx = document.getElementById('regionsChart').getContext('2d');
        new Chart(regionsCtx, {
            type: 'bar',
            data: {
                labels: regionsData.map(r => r.region),
                datasets: [
                    {
                        label: 'Total',
                        data: regionsData.map(r => r.total),
                        backgroundColor: colors.accent,
                        borderRadius: 8
                    },
                    {
                        label: 'Validés',
                        data: regionsData.map(r => r.valides),
                        backgroundColor: colors.info,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        // Graphique Filières (Doughnut)
        const filieresCtx = document.getElementById('filieresChart').getContext('2d');
        new Chart(filieresCtx, {
            type: 'doughnut',
            data: {
                labels: filieresData.map(f => f.filiere),
                datasets: [{
                    data: filieresData.map(f => f.total),
                    backgroundColor: [
                        colors.accent, colors.info, colors.warning, colors.danger,
                        '#9b59b6', '#1abc9c', '#34495e', '#f39c12'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique Concours
        const concoursCtx = document.getElementById('concoursChart').getContext('2d');
        new Chart(concoursCtx, {
            type: 'bar',
            data: {
                labels: concoursData.map(c => c.sigle),
                datasets: [
                    {
                        label: 'Total Inscrits',
                        data: concoursData.map(c => c.total),
                        backgroundColor: colors.accent
                    },
                    {
                        label: 'Paiements',
                        data: concoursData.map(c => c.paiements_recus),
                        backgroundColor: colors.info
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Fonction d'export Excel
        function exportTableToExcel(tableId, fileName) {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, fileName);
        }

        // Export PDF (à implémenter avec une vraie bibliothèque PDF)
        function exportTableToPDF(tableId, fileName) {
            alert('Export PDF en développement. Utilisez la fonction d\'export Excel pour le moment.');
        }
    </script>
</body>
</html>
