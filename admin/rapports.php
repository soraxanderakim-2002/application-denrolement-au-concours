<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$stats = getStatistiques();
$tauxPaiementParConcours = getTauxPaiementParConcours();

// Données simulées pour les régions
$regions = array('Adamaoua', 'Centre', 'Est', 'Extrême-Nord', 'Littoral', 'Nord', 'Nord-Ouest', 'Ouest', 'Sud', 'Sud-Ouest');
$stats_regions = array();
foreach ($regions as $region) {
    $total = rand(100, 500);
    $valides = rand(80, $total);
    $stats_regions[] = array(
        'region' => $region,
        'total_paiements' => $total,
        'paiements_valides' => $valides,
        'en_attente' => rand(5, $total - $valides),
        'rejetes' => $total - $valides - rand(5, $total - $valides),
        'montant_total' => $total * 25000,
        'montant_recouvre' => $valides * 25000
    );
}

// Données simulées pour les méthodes de paiement
$stats_methodes = array(
    array(
        'methode' => 'Mobile Money',
        'total' => rand(800, 1200),
        'valides' => rand(700, 1100),
        'montant_total' => rand(15000000, 25000000),
        'montant_recouvre' => rand(12000000, 24000000)
    ),
    array(
        'methode' => 'Virement Bancaire',
        'total' => rand(400, 600),
        'valides' => rand(350, 550),
        'montant_total' => rand(8000000, 15000000),
        'montant_recouvre' => rand(7000000, 14000000)
    ),
    array(
        'methode' => 'Carte Bancaire',
        'total' => rand(300, 500),
        'valides' => rand(250, 450),
        'montant_total' => rand(6000000, 12000000),
        'montant_recouvre' => rand(5000000, 11000000)
    ),
    array(
        'methode' => 'Espèces',
        'total' => rand(200, 400),
        'valides' => rand(150, 350),
        'montant_total' => rand(3000000, 8000000),
        'montant_recouvre' => rand(2500000, 7500000)
    )
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports | Enregistrement Concours Cameroun</title>
    
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

        .report-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
            transition: var(--transition);
            border-left: 4px solid var(--accent);
        }

        .report-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
        }

        .report-card .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .report-card .report-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .report-card .report-type {
            display: inline-block;
            background: var(--light);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .report-card .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .report-card .meta-item {
            display: flex;
            flex-direction: column;
        }

        .report-card .meta-item label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .report-card .meta-item value {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
        }

        .report-card .report-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-pdf {
            background: var(--danger);
            color: #fff;
        }

        .btn-pdf:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .btn-excel {
            background: var(--accent);
            color: #fff;
        }

        .btn-excel:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
        }

        .btn-view {
            background: var(--info);
            color: #fff;
        }

        .btn-view:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(39, 174, 96, 0.2);
            color: var(--accent);
        }

        .generate-report-btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .generate-report-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .filter-section h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-control {
            padding: 8px 12px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        @media (max-width: 768px) {
            .report-card .report-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .report-card .report-meta {
                grid-template-columns: 1fr 1fr;
            }

            .report-card .report-actions {
                flex-wrap: wrap;
                margin-top: 15px;
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
                    <li><a href="statistiques.php"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
                    <li><a href="rapports.php" class="active"><i class="bi bi-file-earmark-text"></i> Rapports</a></li>
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
                    <h1 class="title"><i class="bi bi-file-earmark-text"></i> Rapports et Exports</h1>
                    <div class="user-menu">
                        <span>Bienvenue, <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- Filtre et Génération -->
                    <div class="filter-section">
                        <h5><i class="bi bi-funnel"></i> Filtrer et Générer un Rapport</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reportType">Type de Rapport</label>
                                    <select id="reportType" class="form-control">
                                        <option value="">Sélectionnez un type</option>
                                        <option value="monthly">Rapport Mensuel</option>
                                        <option value="regional">Rapport Régional</option>
                                        <option value="filiere">Rapport par Filière</option>
                                        <option value="concours">Rapport par Concours</option>
                                        <option value="payment">Rapport de Paiement</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dateStart">Date Début</label>
                                    <input type="date" id="dateStart" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dateEnd">Date Fin</label>
                                    <input type="date" id="dateEnd" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="format">Format Export</label>
                                    <select id="format" class="form-control">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                        <option value="csv">CSV</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="generate-report-btn">
                            <i class="bi bi-file-earmark-plus"></i> Générer le Rapport
                        </button>
                    </div>

                    <!-- Rapports Disponibles -->
                    <h4 style="color: var(--primary); margin-bottom: 20px;"><i class="bi bi-folder-check"></i> Rapports de Paiements</h4>

                    <!-- KPI Summary -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--accent);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Montant Total Attendu</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--primary); margin: 0;">
                                <?php 
                                $total = array_sum(array_column($stats_regions, 'montant_total'));
                                echo formatFCFA($total);
                                ?>
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--accent);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Montant Recouvré</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--accent); margin: 0;">
                                <?php 
                                $recouvre = array_sum(array_column($stats_regions, 'montant_recouvre'));
                                echo formatFCFA($recouvre);
                                ?>
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--info);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Taux Recouvrement</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--info); margin: 0;">
                                <?php 
                                $taux = ($total > 0) ? round(($recouvre / $total) * 100, 1) : 0;
                                echo $taux . '%';
                                ?>
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--warning);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Montant En Attente</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--warning); margin: 0;">
                                <?php 
                                $en_attente = $total - $recouvre;
                                echo formatFCFA($en_attente);
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- Paiements par Région -->
                    <div class="report-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <p class="report-title"><i class="bi bi-map"></i> Paiements par Région</p>
                            <button class="btn-excel" onclick="exportTableToExcel('regionsTable', 'paiements_regions.xlsx')" style="background: var(--accent); color: #fff; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer;">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="regionsTable">
                                <thead>
                                    <tr>
                                        <th>Région</th>
                                        <th style="text-align: center;">Total Paiements</th>
                                        <th style="text-align: center;">Validés</th>
                                        <th style="text-align: center;">En Attente</th>
                                        <th style="text-align: center;">Rejetés</th>
                                        <th style="text-align: center;">Montant Total</th>
                                        <th style="text-align: center;">Montant Recouvré</th>
                                        <th>Taux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats_regions as $region): 
                                        $montant_total = $region['montant_total'] ?? 0;
                                        $montant_recouvre = $region['montant_recouvre'] ?? 0;
                                        $taux = ($montant_total > 0) ? round(($montant_recouvre / $montant_total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $region['region']; ?></strong></td>
                                        <td style="text-align: center;"><?php echo $region['total_paiements']; ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-success"><?php echo $region['paiements_valides']; ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: rgba(230, 126, 34, 0.2); color: var(--warning);"><?php echo $region['en_attente']; ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: rgba(231, 76, 60, 0.2); color: var(--danger);"><?php echo $region['rejetes']; ?></span>
                                        </td>
                                        <td style="text-align: center;"><strong><?php echo formatFCFA($montant_total); ?></strong></td>
                                        <td style="text-align: center;"><strong><?php echo formatFCFA($montant_recouvre); ?></strong></td>
                                        <td>
                                            <div style="background: #f0f0f0; height: 20px; border-radius: 6px; overflow: hidden; width: 150px; margin-bottom: 5px;">
                                                <div style="background: var(--accent); height: 100%; width: <?php echo $taux; ?>%; transition: width 0.3s ease;"></div>
                                            </div>
                                            <small><strong><?php echo $taux; ?>%</strong></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Paiements par Méthode -->
                    <div class="report-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <p class="report-title"><i class="bi bi-credit-card"></i> Paiements par Méthode</p>
                            <button class="btn-excel" onclick="exportTableToExcel('methodesTable', 'paiements_methodes.xlsx')" style="background: var(--accent); color: #fff; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer;">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="methodesTable">
                                <thead>
                                    <tr>
                                        <th>Méthode</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Validés</th>
                                        <th style="text-align: center;">Montant Total</th>
                                        <th style="text-align: center;">Montant Recouvré</th>
                                        <th>Pourcentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats_methodes as $methode): 
                                        $montant_total = $methode['montant_total'] ?? 0;
                                        $montant_recouvre = $methode['montant_recouvre'] ?? 0;
                                        $pourcentage = ($montant_total > 0) ? round(($montant_recouvre / $montant_total) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo ucfirst($methode['methode']); ?></strong>
                                        </td>
                                        <td style="text-align: center;"><?php echo $methode['total']; ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-success"><?php echo $methode['valides']; ?></span>
                                        </td>
                                        <td style="text-align: center;"><strong><?php echo formatFCFA($montant_total); ?></strong></td>
                                        <td style="text-align: center;"><strong><?php echo formatFCFA($montant_recouvre); ?></strong></td>
                                        <td>
                                            <div style="background: #f0f0f0; height: 20px; border-radius: 6px; overflow: hidden; width: 150px; margin-bottom: 5px;">
                                                <div style="background: var(--accent); height: 100%; width: <?php echo $pourcentage; ?>%; transition: width 0.3s ease;"></div>
                                            </div>
                                            <small><strong><?php echo $pourcentage; ?>%</strong></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Taux par Concours -->
                    <div class="report-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <p class="report-title"><i class="bi bi-bar-chart"></i> Taux de Paiement par Concours</p>
                            <button class="btn-excel" onclick="exportTableToExcel('concoursTable', 'paiements_concours.xlsx')" style="background: var(--accent); color: #fff; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer;">
                                <i class="bi bi-download"></i> Exporter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="concoursTable">
                                <thead>
                                    <tr>
                                        <th>Concours</th>
                                        <th style="text-align: center;">Inscrits</th>
                                        <th style="text-align: center;">Payés</th>
                                        <th style="text-align: center;">En Attente</th>
                                        <th style="text-align: center;">Montant Total</th>
                                        <th>Taux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tauxPaiementParConcours as $concours): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $concours['sigle']; ?></strong><br>
                                            <small class="text-muted"><?php echo substr($concours['concours'], 0, 35); ?>...</small>
                                        </td>
                                        <td style="text-align: center;"><?php echo $concours['inscrits']; ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-success"><?php echo $concours['payes']; ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: rgba(230, 126, 34, 0.2); color: var(--warning);"><?php echo $concours['en_attente']; ?></span>
                                        </td>
                                        <td style="text-align: center;"><strong><?php echo formatFCFA($concours['montant_total']); ?></strong></td>
                                        <td>
                                            <div style="background: #f0f0f0; height: 20px; border-radius: 6px; overflow: hidden; width: 150px; margin-bottom: 5px;">
                                                <div style="background: var(--accent); height: 100%; width: <?php echo $concours['taux']; ?>%; transition: width 0.3s ease;"></div>
                                            </div>
                                            <small><strong><?php echo $concours['taux']; ?>%</strong></small>
                                        </td>
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportTableToExcel(tableId, fileName) {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, fileName);
        }

        function exportToExcel(reportId) {
            alert('Export Excel - ' + reportId);
        }

        function exportToPDF(reportId) {
            alert('Export PDF - ' + reportId);
        }

        document.querySelector('.generate-report-btn').addEventListener('click', function() {
            const type = document.getElementById('reportType').value;
            const dateStart = document.getElementById('dateStart').value;
            const dateEnd = document.getElementById('dateEnd').value;
            const format = document.getElementById('format').value;

            if (!type || !dateStart || !dateEnd || !format) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            if (new Date(dateStart) > new Date(dateEnd)) {
                alert('La date de début doit être antérieure à la date de fin');
                return;
            }

            alert('Rapport généré: ' + type + ' du ' + dateStart + ' au ' + dateEnd + ' en format ' + format);
        });
    </script>
</body>
</html>
