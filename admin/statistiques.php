<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$inscritsParRegion = getInscritsParRegion();
$inscritsParFiliere = getInscritsParFiliere();
$inscritsParConcours = getInscritsParConcours();
$stats = getStatistiques();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques | Enregistrement Concours Cameroun</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    
    <style>
        :root {
          --primary: #2c3e50;
          --secondary: #34495e;
          --accent: #27ae60;
          --accent-dark: #219653;
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

        .main-content {
            padding: 0 30px 30px;
        }

        .dashboard-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
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

        .card-body {
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            padding: 20px;
            border-radius: var(--radius);
            text-align: center;
        }

        .stat-box h5 {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
        }

        .stat-box .value {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
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
        }

        .table tbody td {
            padding: 12px 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: var(--accent);
            color: #fff;
        }

        .badge-warning {
            background: var(--warning);
            color: #fff;
        }

        .badge-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-export {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-export:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .filter-section .form-group {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 0 15px 30px;
            }

            .topbar {
                padding: 15px 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0 min-vh-100">
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-2 sidebar">
                <div class="logo">
                    <i class="bi bi-graph-up"></i>
                    <span>Enroll Admin</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Tableau de Bord</a></li>
                    <li><a href="statistiques.php" class="active"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
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
                    <h1 class="title"><i class="bi bi-bar-chart"></i> Statistiques Détaillées</h1>
                    <div class="user-menu">
                        <span>Bienvenue, <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- Key Statistics -->
                    <div class="stats-grid">
                        <div class="stat-box">
                            <h5>Total Inscrits</h5>
                            <p class="value"><?php echo number_format($stats['total_inscrits'], 0, '.', ' '); ?></p>
                        </div>
                        <div class="stat-box">
                            <h5>Taux de Validation</h5>
                            <p class="value"><?php echo $stats['taux_validation']; ?>%</p>
                        </div>
                        <div class="stat-box">
                            <h5>Paiements Reçus</h5>
                            <p class="value"><?php echo number_format($stats['paiements_recus'], 0, '.', ' '); ?></p>
                        </div>
                        <div class="stat-box">
                            <h5>Montant Reçu (FCFA)</h5>
                            <p class="value"><?php echo number_format($stats['montant_recu'] / 1000000, 1, '.', ' '); ?>M</p>
                        </div>
                    </div>

                    <!-- Statistiques par Région -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-map"></i> Statistiques par Région</h3>
                            <button class="btn-export" onclick="exportTableToExcel('regionTable', 'statistiques_region.xlsx')">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" id="regionTable">
                                <thead>
                                    <tr>
                                        <th>Région</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Validés</th>
                                        <th style="text-align: center;">En Attente</th>
                                        <th style="text-align: center;">% Validation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inscritsParRegion as $region): 
                                        $pourcentage = round(($region['valides'] / $region['total']) * 100, 1);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $region['region']; ?></strong></td>
                                        <td style="text-align: center;"><?php echo number_format($region['total'], 0, '.', ' '); ?></td>
                                        <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($region['valides'], 0, '.', ' '); ?></span></td>
                                        <td style="text-align: center;"><span class="badge badge-warning"><?php echo $region['en_attente']; ?></span></td>
                                        <td style="text-align: center;"><strong><?php echo $pourcentage; ?>%</strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Statistiques par Filière -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-book"></i> Statistiques par Filière</h3>
                            <button class="btn-export" onclick="exportTableToExcel('filiereTable', 'statistiques_filiere.xlsx')">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" id="filiereTable">
                                <thead>
                                    <tr>
                                        <th>Filière</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Validés</th>
                                        <th style="text-align: center;">Abandonnés</th>
                                        <th style="text-align: center;">% Validation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inscritsParFiliere as $filiere): 
                                        $pourcentage = round(($filiere['valides'] / $filiere['total']) * 100, 1);
                                        $abandonne = round(($filiere['abandonnes'] / $filiere['total']) * 100, 1);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $filiere['filiere']; ?></strong></td>
                                        <td style="text-align: center;"><?php echo number_format($filiere['total'], 0, '.', ' '); ?></td>
                                        <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($filiere['valides'], 0, '.', ' '); ?></span></td>
                                        <td style="text-align: center;"><span class="badge badge-danger"><?php echo $filiere['abandonnes']; ?></span></td>
                                        <td style="text-align: center;"><strong><?php echo $pourcentage; ?>%</strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Statistiques par Concours -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-trophy"></i> Statistiques par Concours</h3>
                            <button class="btn-export" onclick="exportTableToExcel('concoursTable', 'statistiques_concours.xlsx')">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" id="concoursTable">
                                <thead>
                                    <tr>
                                        <th>Concours</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Validés</th>
                                        <th style="text-align: center;">Paiements</th>
                                        <th style="text-align: center;">% Paiement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inscritsParConcours as $concours): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $concours['sigle']; ?></strong><br>
                                            <small class="text-muted"><?php echo substr($concours['concours'], 0, 40); ?>...</small>
                                        </td>
                                        <td style="text-align: center;"><?php echo number_format($concours['total'], 0, '.', ' '); ?></td>
                                        <td style="text-align: center;"><span class="badge badge-success"><?php echo number_format($concours['valides'], 0, '.', ' '); ?></span></td>
                                        <td style="text-align: center;"><span class="badge badge-warning"><?php echo number_format($concours['paiements_recus'], 0, '.', ' '); ?></span></td>
                                        <td style="text-align: center;"><strong><?php echo $concours['taux_paiement']; ?>%</strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <script>
        function exportTableToExcel(tableId, fileName) {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, fileName);
        }
    </script>
</body>
</html>
