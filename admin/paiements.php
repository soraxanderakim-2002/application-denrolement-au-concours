<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$paiements = getDetailsPaiements(20);
$stats = getStatistiques();
$montantTotal = array_sum(array_column($paiements, 'montant'));
$montantPayes = array_sum(array_map(function($p) { return ($p['statut'] === 'validé') ? $p['montant'] : 0; }, $paiements));
$tauxPaiementParConcours = getTauxPaiementParConcours();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Paiements | Enregistrement Concours Cameroun</title>
    
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
            border-left: 4px solid var(--accent);
        }

        .kpi-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: var(--muted);
            font-weight: 600;
        }

        .kpi-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
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

        .btn-approve {
            background: var(--accent);
            color: #fff;
        }

        .btn-approve:hover {
            background: var(--accent-dark);
        }

        .btn-reject {
            background: var(--danger);
            color: #fff;
        }

        .btn-reject:hover {
            background: #c0392b;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 0 15px 30px;
            }

            .topbar {
                padding: 15px 20px;
            }

            .kpi-container {
                grid-template-columns: 1fr;
                gap: 15px;
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
                    <li><a href="paiements.php" class="active"><i class="bi bi-credit-card"></i> Paiements</a></li>
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
                    <h1 class="title"><i class="bi bi-credit-card"></i> Suivi des Paiements</h1>
                    <div class="user-menu">
                        <span>Bienvenue, <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <!-- KPI Cards -->
                    <div class="kpi-container">
                        <div class="kpi-card">
                            <h3>Montant Total Requis</h3>
                            <p class="value"><?php echo number_format($stats['montant_total'] / 1000000, 1, '.', ' '); ?>M</p>
                        </div>
                        <div class="kpi-card">
                            <h3>Montant Reçu</h3>
                            <p class="value"><?php echo number_format($stats['montant_recu'] / 1000000, 1, '.', ' '); ?>M</p>
                        </div>
                        <div class="kpi-card">
                            <h3>Paiements Reçus</h3>
                            <p class="value"><?php echo number_format($stats['paiements_recus'], 0, '.', ' '); ?></p>
                        </div>
                        <div class="kpi-card">
                            <h3>En Attente de Paiement</h3>
                            <p class="value"><?php echo number_format($stats['paiements_en_attente'], 0, '.', ' '); ?></p>
                        </div>
                    </div>

                    <!-- Filtre -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Statut</label>
                                <select id="statusFilter" class="form-control">
                                    <option value="">Tous</option>
                                    <option value="paid">Payé</option>
                                    <option value="pending">En Attente</option>
                                    <option value="failed">Échoué</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Concours</label>
                                <select id="concoursFilter" class="form-control">
                                    <option value="">Tous les Concours</option>
                                    <option value="ceup">CEUP</option>
                                    <option value="cfp">CFP</option>
                                    <option value="cm">CM</option>
                                    <option value="concop">CONCOP</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Méthode</label>
                                <select id="methodFilter" class="form-control">
                                    <option value="">Toutes les Méthodes</option>
                                    <option value="mtn">MTN Mobile Money</option>
                                    <option value="orange">Orange Money</option>
                                    <option value="bank">Virement Bancaire</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-export w-100">
                                    <i class="bi bi-download"></i> Exporter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- KPI Paiements -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--accent);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Montant Total</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--primary); margin: 0;">
                                <?php echo formatFCFA($montantTotal); ?>
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--accent);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Montant Reçu</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--accent); margin: 0;">
                                <?php echo formatFCFA($montantPayes); ?>
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--info);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">Taux Recouvrement</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--info); margin: 0;">
                                <?php echo ($montantTotal > 0) ? round(($montantPayes / $montantTotal) * 100, 1) : 0; ?>%
                            </p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--warning);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">En Attente</h4>
                            <p style="font-size: 24px; font-weight: 700; color: var(--warning); margin: 0;">
                                <?php echo formatFCFA($montantTotal - $montantPayes); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Tableau Paiements -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-list-check"></i> Liste des Paiements</h3>
                            <button class="btn-export" onclick="exportTableToExcel('paiementsTable', 'paiements.xlsx')">
                                <i class="bi bi-download"></i> Exporter Excel
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="paiementsTable">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            <th>Candidat</th>
                                            <th style="text-align: center;">Montant</th>
                                            <th>Méthode</th>
                                            <th style="text-align: center;">Statut</th>
                                            <th>Date</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paiements as $paiement): 
                                            $badgeInfo = getStatutBadge($paiement['statut']);
                                            $badgeClass = $badgeInfo['color'] === 'success' ? 'badge-success' : ($badgeInfo['color'] === 'warning' ? 'badge-warning' : 'badge-danger');
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $paiement['id']; ?></strong><br>
                                                <small class="text-muted"><?php echo $paiement['reference']; ?></small>
                                            </td>
                                            <td><strong><?php echo $paiement['candidat']; ?></strong></td>
                                            <td style="text-align: center;"><strong><?php echo formatFCFA($paiement['montant']); ?></strong></td>
                                            <td>
                                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; background: var(--light); border-radius: 6px; font-size: 13px;">
                                                    <i class="bi bi-currency-naira"></i>
                                                    <?php echo $paiement['methode']; ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <i class="bi bi-<?php echo $badgeInfo['icon']; ?>"></i>
                                                    <?php echo ucfirst($paiement['statut']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $paiement['date_paiement']; ?></td>
                                            <td style="text-align: center;">
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" onclick="viewPayment(this)" title="Afficher">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <?php if (strtolower($paiement['statut']) === 'en attente'): ?>
                                                    <button class="btn-action btn-approve" onclick="approvePayment(this)" title="Approuver">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button class="btn-action btn-reject" onclick="rejectPayment(this)" title="Rejeter">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Taux de Paiement par Concours -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-pie-chart"></i> Taux de Paiement par Concours</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
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
                                        <?php foreach ($tauxPaiementParConcours as $paiement): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $paiement['sigle']; ?></strong><br>
                                                <small class="text-muted"><?php echo substr($paiement['concours'], 0, 35); ?>...</small>
                                            </td>
                                            <td style="text-align: center;"><?php echo number_format($paiement['inscrits'], 0, '.', ' '); ?></td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-success"><?php echo number_format($paiement['payes'], 0, '.', ' '); ?></span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-warning"><?php echo number_format($paiement['en_attente'], 0, '.', ' '); ?></span>
                                            </td>
                                            <td style="text-align: center;"><strong><?php echo formatFCFA($paiement['montant_total']); ?></strong></td>
                                            <td>
                                                <div class="progress-bar-custom">
                                                    <div class="bar" style="width: <?php echo $paiement['taux']; ?>%"></div>
                                                </div>
                                                <small><?php echo $paiement['taux']; ?>%</small>
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

    <!-- Modal Voir Paiement -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; border: none;">
                    <h5 class="modal-title"><i class="bi bi-receipt"></i> Détails du Paiement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Paiement:</strong> <span id="paymentId"></span></p>
                            <p><strong>Référence:</strong> <span id="paymentRef"></span></p>
                            <p><strong>Candidat:</strong> <span id="candidatName"></span></p>
                            <p><strong>Email:</strong> <span id="candidatEmail"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Montant:</strong> <span id="paymentAmount" style="color: var(--accent); font-weight: 700;"></span></p>
                            <p><strong>Méthode:</strong> <span id="paymentMethod"></span></p>
                            <p><strong>Statut:</strong> <span id="paymentStatus"></span></p>
                            <p><strong>Date:</strong> <span id="paymentDate"></span></p>
                        </div>
                    </div>
                    <hr>
                    <div id="approvalForm" style="display: none;">
                        <h6>Approbation/Rejet du Paiement</h6>
                        <div class="mb-3">
                            <label class="form-label">Notes d'approbation</label>
                            <textarea class="form-control" id="approvalNotes" rows="3" placeholder="Ajouter des notes..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" onclick="confirmApproval()">
                                <i class="bi bi-check-circle"></i> Approuver
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="confirmRejection()">
                                <i class="bi bi-x-circle"></i> Rejeter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <script>
        let currentPaymentId = null;
        let currentPaymentBtn = null;

        function viewPayment(btn) {
            const row = btn.closest('tr');
            const cells = row.querySelectorAll('td');
            
            const refCell = cells[0].textContent;
            const refMatch = refCell.match(/\d+/);
            const id = refMatch ? refMatch[0] : '';
            const reference = refCell.split('\n')[1] || '';
            
            const candidat = cells[1].textContent;
            const montant = cells[2].textContent;
            const methode = cells[3].textContent;
            const statut = cells[4].textContent;
            const date = cells[5].textContent;

            currentPaymentId = id;
            currentPaymentBtn = btn;

            document.getElementById('paymentId').textContent = id;
            document.getElementById('paymentRef').textContent = reference;
            document.getElementById('candidatName').textContent = candidat;
            document.getElementById('paymentAmount').textContent = montant;
            document.getElementById('paymentMethod').textContent = methode.trim();
            document.getElementById('paymentStatus').innerHTML = cells[4].innerHTML;
            document.getElementById('paymentDate').textContent = date;

            const approvalForm = document.getElementById('approvalForm');
            if (statut.toLowerCase().includes('attente')) {
                approvalForm.style.display = 'block';
            } else {
                approvalForm.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function approvePayment(btn) {
            const row = btn.closest('tr');
            const refCell = row.querySelectorAll('td')[0];
            const idMatch = refCell.textContent.match(/\d+/);
            const id = idMatch ? idMatch[0] : '';

            if (confirm('Êtes-vous sûr d\'approuver ce paiement ?')) {
                updatePaymentStatus(id, 'validé', btn, 'Paiement approuvé avec succès');
            }
        }

        function rejectPayment(btn) {
            const row = btn.closest('tr');
            const refCell = row.querySelectorAll('td')[0];
            const idMatch = refCell.textContent.match(/\d+/);
            const id = idMatch ? idMatch[0] : '';

            if (confirm('Êtes-vous sûr de rejeter ce paiement ?')) {
                updatePaymentStatus(id, 'rejeté', btn, 'Paiement rejeté');
            }
        }

        function confirmApproval() {
            if (!currentPaymentId) return;
            
            const notes = document.getElementById('approvalNotes').value;
            updatePaymentStatus(currentPaymentId, 'validé', currentPaymentBtn, 'Paiement approuvé', notes);
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
        }

        function confirmRejection() {
            if (!currentPaymentId) return;
            
            const notes = document.getElementById('approvalNotes').value;
            updatePaymentStatus(currentPaymentId, 'rejeté', currentPaymentBtn, 'Paiement rejeté', notes);
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
        }

        function updatePaymentStatus(paymentId, status, btn, message, notes = '') {
            const formData = new FormData();
            formData.append('action', 'update_payment');
            formData.append('payment_id', paymentId);
            formData.append('status', status);
            formData.append('notes', notes);

            fetch('payment_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showErrorMessage(data.message || 'Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                showErrorMessage('Erreur: ' + error.message);
            });
        }

        function showSuccessMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                <i class="bi bi-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 4000);
        }

        function showErrorMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                <i class="bi bi-exclamation-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 4000);
        }

        function exportTableToExcel(tableId, fileName) {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, fileName);
        }
    </script>
</body>
</html>
