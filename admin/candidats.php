<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$candidats = getDetailsCandidats(20);
$stats = getStatistiques();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Candidats | Enregistrement Concours Cameroun</title>
    
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

        .badge-info {
            background: var(--info);
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
        }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            box-shadow: var(--shadow);
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-view {
            background: var(--info);
            color: #fff;
        }

        .btn-view:hover {
            background: #2980b9;
        }

        .btn-edit {
            background: var(--warning);
            color: #fff;
        }

        .btn-edit:hover {
            background: #d68910;
        }

        .btn-delete {
            background: var(--danger);
            color: #fff;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .search-box {
            flex: 1;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-box input {
            flex: 1;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 0 15px 30px;
            }

            .topbar {
                padding: 15px 20px;
            }

            .search-box {
                flex-wrap: wrap;
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
                    <li><a href="rapports.php"><i class="bi bi-file-earmark-text"></i> Rapports</a></li>
                    <li><a href="paiements.php"><i class="bi bi-credit-card"></i> Paiements</a></li>
                    <li><a href="candidats.php" class="active"><i class="bi bi-people"></i> Candidats</a></li>
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
                    <h1 class="title"><i class="bi bi-people"></i> Gestion des Candidats</h1>
                    <div class="user-menu">
                        <span>Bienvenue, <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- Filtre et Recherche -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="search-box">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un candidat...">
                                    <button class="btn btn-export">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <select id="statusFilter" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="valid">Validé</option>
                                    <option value="pending">En Attente</option>
                                    <option value="rejected">Rejeté</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <select id="regionFilter" class="form-control">
                                    <option value="">Toutes les régions</option>
                                    <option value="centre">Centre</option>
                                    <option value="littoral">Littoral</option>
                                    <option value="ouest">Ouest</option>
                                    <option value="nord">Nord</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button class="btn btn-export w-100">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau Candidats -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-list-check"></i> Liste des Candidats (<?php echo count($candidats); ?>)</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="candidatsTable">
                                    <thead>
                                        <tr>
                                            <th>Candidat</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Région</th>
                                            <th>Filière</th>
                                            <th>Concours</th>
                                            <th style="text-align: center;">Statut</th>
                                            <th style="text-align: center;">Paiement</th>
                                            <th>Date</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($candidats as $candidat): 
                                            $badgeInfo = getStatutBadge($candidat['statut']);
                                            $badgeClass = $badgeInfo['color'] === 'success' ? 'badge-success' : ($badgeInfo['color'] === 'warning' ? 'badge-warning' : 'badge-danger');
                                            $paiementClass = $candidat['paiement'] === 'Validé' ? 'badge-success' : 'badge-warning';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $candidat['nom']; ?></strong><br>
                                                <small class="text-muted">ID: #<?php echo $candidat['id']; ?></small>
                                            </td>
                                            <td><small><?php echo $candidat['email']; ?></small></td>
                                            <td><small><?php echo $candidat['telephone']; ?></small></td>
                                            <td><?php echo $candidat['region']; ?></td>
                                            <td><?php echo $candidat['filiere']; ?></td>
                                            <td><?php echo $candidat['concours']; ?></td>
                                            <td style="text-align: center;">
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <i class="bi bi-<?php echo $badgeInfo['icon']; ?>"></i>
                                                    <?php echo ucfirst($candidat['statut']); ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge <?php echo $paiementClass; ?>"><?php echo $candidat['paiement']; ?></span>
                                            </td>
                                            <td><?php echo $candidat['date_inscription']; ?></td>
                                            <td style="text-align: center;">
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" title="Afficher" onclick="viewCandidate(<?php echo $candidat['id']; ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn-action btn-edit" title="Modifier" onclick="editCandidate(<?php echo $candidat['id']; ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn-action btn-delete" title="Supprimer" onclick="deleteCandidate(<?php echo $candidat['id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination (simple) -->
                    <nav aria-label="Page navigation" style="text-align: center;">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <script>
        document.querySelectorAll('.btn-export').forEach(btn => {
            btn.addEventListener('click', function() {
                const table = document.getElementById('candidatsTable');
                const workbook = XLSX.utils.table_to_book(table);
                XLSX.writeFile(workbook, 'candidats.xlsx');
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#candidatsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce candidat ?')) {
                    alert('Candidat supprimé');
                    // À implémenter avec une vraie logique de suppression
                }
            });
        });
    </script>
</body>
</html>
