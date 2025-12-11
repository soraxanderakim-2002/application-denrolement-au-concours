<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$centres = getCentresComposition();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Centres | Enroll Concours</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #27ae60;
            --accent-dark: #219653;
            --info: #3498db;
            --warning: #e67e22;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --muted: #6c757d;
            --radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
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
            transition: all 0.3s ease;
        }

        .sidebar .nav-menu a:hover,
        .sidebar .nav-menu a.active {
            background: var(--accent);
            color: #fff;
        }

        .topbar {
            background: #fff;
            padding: 20px 30px;
            border-radius: var(--radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            transition: all 0.3s ease;
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

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 12px;
            cursor: pointer;
            margin: 0 3px;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: var(--info);
            color: #fff;
        }

        .btn-edit:hover {
            background: #2980b9;
        }

        .btn-delete {
            background: var(--danger);
            color: #fff;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .centre-card {
            background: var(--light);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--accent);
            transition: all 0.3s ease;
        }

        .centre-card:hover {
            box-shadow: var(--shadow);
            transform: translateX(5px);
        }

        .centre-card h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .centre-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: var(--accent);
            font-size: 18px;
        }

        .info-item span {
            font-size: 14px;
        }

        .capacity-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 8px;
        }

        .capacity-bar .filled {
            height: 100%;
            background: var(--accent);
            transition: width 0.3s ease;
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

        .table tbody tr:hover {
            background: var(--light);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .btn-add {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-2 sidebar">
                <div class="logo">
                    <i class="bi bi-graph-up"></i>
                    <span>Enroll Admin</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Tableau de Bord</a></li>
                    <li><a href="statistiques.php"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
                    <li><a href="rapports.php"><i class="bi bi-file-earmark-text"></i> Rapports</a></li>
                    <li><a href="paiements.php"><i class="bi bi-credit-card"></i> Paiements</a></li>
                    <li><a href="candidats.php"><i class="bi bi-people"></i> Candidats</a></li>
                    <li><a href="centres.php" class="active"><i class="bi bi-building"></i> Centres</a></li>
                    <li><hr class="text-white-50 my-3"></li>
                    <li><a href="parametres.php"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <!-- Topbar -->
                <div class="topbar">
                    <h1 class="title"><i class="bi bi-building"></i> Gestion des Centres de Composition</h1>
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCentreModal">
                        <i class="bi bi-plus-circle"></i> Ajouter Centre
                    </button>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- Vue Carte -->
                    <div style="margin-bottom: 30px;">
                        <h4 style="color: var(--primary); font-weight: 700; margin-bottom: 20px;">
                            <i class="bi bi-map"></i> Vue Centres
                        </h4>
                        <?php foreach ($centres as $centre): 
                            $taux = round(($centre['occupees'] / $centre['places']) * 100, 1);
                        ?>
                        <div class="centre-card">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h5><?php echo $centre['nom']; ?></h5>
                                    <div class="centre-info">
                                        <div class="info-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?php echo $centre['ville']; ?> - <?php echo $centre['region']; ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-chair"></i>
                                            <span><?php echo $centre['occupees']; ?> / <?php echo $centre['places']; ?> places</span>
                                        </div>
                                    </div>
                                    <div class="capacity-bar">
                                        <div class="filled" style="width: <?php echo $taux; ?>%"></div>
                                    </div>
                                    <small class="text-muted">Taux d'occupation : <?php echo $taux; ?>%</small>
                                </div>
                                <div>
                                    <button class="btn-action btn-edit" onclick="editCentre(<?php echo $centre['id']; ?>)">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteCentre(<?php echo $centre['id']; ?>)">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tableau des Centres -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-table"></i> Liste Complète des Centres</h3>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Centre</th>
                                            <th>Ville/Région</th>
                                            <th style="text-align: center;">Places Total</th>
                                            <th style="text-align: center;">Occupées</th>
                                            <th style="text-align: center;">Disponibles</th>
                                            <th>Taux d'Occupation</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($centres as $centre):
                                            $taux = round(($centre['occupees'] / $centre['places']) * 100, 1);
                                            $disponibles = $centre['places'] - $centre['occupees'];
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $centre['nom']; ?></strong></td>
                                            <td><?php echo $centre['ville']; ?> - <?php echo $centre['region']; ?></td>
                                            <td style="text-align: center;"><?php echo $centre['places']; ?></td>
                                            <td style="text-align: center;"><span class="badge badge-success"><?php echo $centre['occupees']; ?></span></td>
                                            <td style="text-align: center;"><span class="badge badge-warning"><?php echo $disponibles; ?></span></td>
                                            <td>
                                                <div class="capacity-bar">
                                                    <div class="filled" style="width: <?php echo $taux; ?>%"></div>
                                                </div>
                                                <small><?php echo $taux; ?>%</small>
                                            </td>
                                            <td style="text-align: center;">
                                                <button class="btn-action btn-edit">Modifier</button>
                                                <button class="btn-action btn-delete">Supprimer</button>
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
    </div>

    <!-- Modal Ajouter Centre -->
    <div class="modal fade" id="addCentreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: #fff; border: none;">
                    <h5 class="modal-title"><i class="bi bi-building"></i> Ajouter Nouveau Centre</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nom du Centre</label>
                            <input type="text" class="form-control" placeholder="Ex: Lycée de Yaoundé">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" class="form-control" placeholder="Ex: Yaoundé">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Région</label>
                            <select class="form-select">
                                <option>Sélectionner une région...</option>
                                <option>Centre</option>
                                <option>Littoral</option>
                                <option>Nord</option>
                                <option>Sud-Ouest</option>
                                <option>Nord-Ouest</option>
                                <option>Ouest</option>
                                <option>Est</option>
                                <option>Adamaoua</option>
                                <option>Extrême-Nord</option>
                                <option>Sud</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre de Places</label>
                            <input type="number" class="form-control" placeholder="Ex: 500" min="1">
                        </div>
                        <button type="submit" class="btn" style="background: var(--accent); color: #fff; width: 100%;">
                            <i class="bi bi-check-circle"></i> Ajouter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCentre(id) {
            alert('Modification du centre ' + id);
        }

        function deleteCentre(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce centre?')) {
                alert('Centre ' + id + ' supprimé');
            }
        }
    </script>
</body>
</html>
