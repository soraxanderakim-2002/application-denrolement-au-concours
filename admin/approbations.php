<?php
session_start();
require_once 'config.php';

AdminAuth::checkAdminLogin();

// Récupérer les candidatures en attente
$candidatures = array(
    array(
        'id' => 1,
        'candidat' => 'Jean Dupont',
        'email' => 'jean.dupont@gmail.com',
        'concours' => 'CEUP',
        'filiere' => 'Ingénierie',
        'region' => 'Centre',
        'statut' => 'en attente',
        'date_inscription' => '2025-01-15',
        'documents' => array('Bac' => 'OK', 'CIN' => 'OK', 'Certificat' => 'Manquant'),
        'paiement' => 'validé',
        'raison_rejet' => ''
    ),
    array(
        'id' => 2,
        'candidat' => 'Marie Martin',
        'email' => 'marie.martin@gmail.com',
        'concours' => 'CFP',
        'filiere' => 'Droit',
        'region' => 'Littoral',
        'statut' => 'en attente',
        'date_inscription' => '2025-01-20',
        'documents' => array('Bac' => 'OK', 'CIN' => 'Manquant', 'Certificat' => 'OK'),
        'paiement' => 'en attente',
        'raison_rejet' => ''
    ),
    array(
        'id' => 3,
        'candidat' => 'Paul Kamdem',
        'email' => 'paul.kamdem@gmail.com',
        'concours' => 'CM',
        'filiere' => 'Médecine',
        'region' => 'Adamaoua',
        'statut' => 'approuvée',
        'date_inscription' => '2025-01-10',
        'documents' => array('Bac' => 'OK', 'CIN' => 'OK', 'Certificat' => 'OK'),
        'paiement' => 'validé',
        'raison_rejet' => ''
    ),
    array(
        'id' => 4,
        'candidat' => 'Aline Nkomo',
        'email' => 'aline.nkomo@gmail.com',
        'concours' => 'CONCOP',
        'filiere' => 'Sciences',
        'region' => 'Sud-Ouest',
        'statut' => 'rejetée',
        'date_inscription' => '2025-01-25',
        'documents' => array('Bac' => 'OK', 'CIN' => 'OK', 'Certificat' => 'OK'),
        'paiement' => 'rejeté',
        'raison_rejet' => 'Paiement non effectué'
    )
);

// Traiter les actions d'approbation/rejet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $candidature_id = $_POST['candidature_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $raison = $_POST['raison'] ?? '';
    
    if ($action === 'approve') {
        // Approuver la candidature
        $message = "Candidature #{$candidature_id} approuvée avec succès !";
    } elseif ($action === 'reject') {
        // Rejeter la candidature
        $message = "Candidature #{$candidature_id} rejetée.";
    }
}

// Compter les statuts
$pending = count(array_filter($candidatures, fn($c) => $c['statut'] === 'en attente'));
$approved = count(array_filter($candidatures, fn($c) => $c['statut'] === 'approuvée'));
$rejected = count(array_filter($candidatures, fn($c) => $c['statut'] === 'rejetée'));
$total = count($candidatures);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approbation des Inscriptions | Admin Dashboard</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    
    <style>
        .approval-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .candidature-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            border-top: 4px solid #e9ecef;
        }

        .candidature-card.pending {
            border-top-color: var(--warning);
        }

        .candidature-card.approved {
            border-top-color: var(--accent);
        }

        .candidature-card.rejected {
            border-top-color: var(--danger);
        }

        .candidature-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .card-header-approval {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: start;
            border-bottom: 1px solid #e9ecef;
        }

        .card-header-approval h5 {
            margin: 0;
            color: var(--primary);
            font-weight: 700;
        }

        .status-label {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-label.pending {
            background: rgba(230, 126, 34, 0.2);
            color: var(--warning);
        }

        .status-label.approved {
            background: rgba(39, 174, 96, 0.2);
            color: var(--accent);
        }

        .status-label.rejected {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        .card-body-approval {
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            color: var(--muted);
            font-weight: 600;
        }

        .info-value {
            color: var(--primary);
            font-weight: 600;
        }

        .documents-check {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .doc-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
        }

        .doc-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .doc-status.ok {
            background: rgba(39, 174, 96, 0.2);
            color: var(--accent);
        }

        .doc-status.missing {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        .action-buttons-approval {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-approve, .btn-reject {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-approve {
            background: rgba(39, 174, 96, 0.2);
            color: var(--accent);
        }

        .btn-approve:hover {
            background: var(--accent);
            color: #fff;
        }

        .btn-reject {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        .btn-reject:hover {
            background: var(--danger);
            color: #fff;
        }

        .modal-approval {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-approval.active {
            display: flex;
        }

        .modal-content-approval {
            background: #fff;
            border-radius: var(--radius);
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: var(--shadow-lg);
        }

        .modal-content-approval h4 {
            color: var(--primary);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .form-group-approval {
            margin-bottom: 20px;
        }

        .form-group-approval label {
            display: block;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group-approval textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .form-group-approval textarea:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-buttons .btn-confirm {
            background: var(--accent);
            color: #fff;
        }

        .modal-buttons .btn-confirm:hover {
            background: var(--accent-dark);
        }

        .modal-buttons .btn-cancel {
            background: #e9ecef;
            color: var(--primary);
        }

        .modal-buttons .btn-cancel:hover {
            background: #ddd;
        }

        @media (max-width: 768px) {
            .approval-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-row">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="logo">
                    <i class="bi bi-shield-check"></i>
                    <span>Admin Panel</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="dashboard.php"><i class="bi bi-house-door"></i> Tableau de Bord</a></li>
                    <li><a href="candidats.php"><i class="bi bi-people"></i> Candidats</a></li>
                    <li><a href="approbations.php" class="active"><i class="bi bi-check-circle"></i> Approbations</a></li>
                    <li><a href="paiements.php"><i class="bi bi-credit-card"></i> Paiements</a></li>
                    <li><a href="centres.php"><i class="bi bi-building"></i> Centres</a></li>
                    <li><a href="statistiques.php"><i class="bi bi-graph-up"></i> Statistiques</a></li>
                    <li><a href="rapports.php"><i class="bi bi-file-text"></i> Rapports</a></li>
                    <li><a href="parametres.php"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><hr style="border-color: rgba(255,255,255,0.2);"></li>
                    <li><a href="profile.php"><i class="bi bi-person-circle"></i> Mon Profil</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <div class="dashboard-content">
                <div class="topbar">
                    <h1><i class="bi bi-check-circle"></i> Approbation des Inscriptions</h1>
                    <div class="topbar-right">
                        <span class="user-name"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
                    </div>
                </div>

                <main class="content">
                    <!-- KPI Cards -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--muted);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">TOTAL</h4>
                            <p style="font-size: 28px; font-weight: 700; color: var(--primary); margin: 0;"><?php echo $total; ?></p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--warning);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">EN ATTENTE</h4>
                            <p style="font-size: 28px; font-weight: 700; color: var(--warning); margin: 0;"><?php echo $pending; ?></p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--accent);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">APPROUVÉES</h4>
                            <p style="font-size: 28px; font-weight: 700; color: var(--accent); margin: 0;"><?php echo $approved; ?></p>
                        </div>
                        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid var(--danger);">
                            <h4 style="color: var(--muted); margin: 0 0 10px; font-size: 14px;">REJETÉES</h4>
                            <p style="font-size: 28px; font-weight: 700; color: var(--danger); margin: 0;"><?php echo $rejected; ?></p>
                        </div>
                    </div>

                    <!-- Candidatures Cards -->
                    <div class="approval-container">
                        <?php foreach ($candidatures as $candidature): ?>
                        <div class="candidature-card <?php echo $candidature['statut']; ?>">
                            <div class="card-header-approval">
                                <div>
                                    <h5><?php echo htmlspecialchars($candidature['candidat']); ?></h5>
                                    <small style="color: var(--muted);"><?php echo htmlspecialchars($candidature['email']); ?></small>
                                </div>
                                <span class="status-label <?php echo $candidature['statut']; ?>">
                                    <?php echo ucfirst($candidature['statut']); ?>
                                </span>
                            </div>

                            <div class="card-body-approval">
                                <div class="info-row">
                                    <span class="info-label">Concours</span>
                                    <span class="info-value"><?php echo $candidature['concours']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Filière</span>
                                    <span class="info-value"><?php echo $candidature['filiere']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Région</span>
                                    <span class="info-value"><?php echo $candidature['region']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Paiement</span>
                                    <span class="info-value">
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: <?php echo $candidature['paiement'] === 'validé' ? 'rgba(39, 174, 96, 0.2)' : 'rgba(230, 126, 34, 0.2)'; ?>; color: <?php echo $candidature['paiement'] === 'validé' ? 'var(--accent)' : 'var(--warning)'; ?>;">
                                            <?php echo ucfirst($candidature['paiement']); ?>
                                        </span>
                                    </span>
                                </div>

                                <!-- Documents Check -->
                                <div class="documents-check">
                                    <strong style="font-size: 13px; color: var(--primary);">Documents:</strong>
                                    <?php foreach ($candidature['documents'] as $doc => $status): ?>
                                    <div class="doc-item">
                                        <span><?php echo $doc; ?></span>
                                        <span class="doc-status <?php echo $status === 'OK' ? 'ok' : 'missing'; ?>">
                                            <i class="bi bi-<?php echo $status === 'OK' ? 'check-circle' : 'x-circle'; ?>"></i>
                                            <?php echo $status; ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Action Buttons -->
                                <?php if ($candidature['statut'] === 'en attente'): ?>
                                <div class="action-buttons-approval">
                                    <button class="btn-approve" onclick="openApprovalModal(<?php echo $candidature['id']; ?>, 'approve')">
                                        <i class="bi bi-check-circle"></i> Approuver
                                    </button>
                                    <button class="btn-reject" onclick="openApprovalModal(<?php echo $candidature['id']; ?>, 'reject')">
                                        <i class="bi bi-x-circle"></i> Rejeter
                                    </button>
                                </div>
                                <?php elseif ($candidature['statut'] === 'rejetée'): ?>
                                <p style="color: var(--danger); font-size: 13px; margin-top: 15px;">
                                    <strong>Raison:</strong> <?php echo htmlspecialchars($candidature['raison_rejet']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </main>
            </div>
        </div>
    </div>

    <!-- Modal Approbation/Rejet -->
    <div class="modal-approval" id="approvalModal">
        <div class="modal-content-approval">
            <h4 id="modalTitle">Approuver la candidature</h4>
            <form method="POST">
                <input type="hidden" name="candidature_id" id="candidatureId">
                <input type="hidden" name="action" id="actionType">

                <div class="form-group-approval" id="reasonGroup" style="display: none;">
                    <label for="raison">Raison du rejet *</label>
                    <textarea id="raison" name="raison" placeholder="Expliquez pourquoi vous rejetez cette candidature..."></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="submit" class="btn-confirm" id="confirmBtn">Approuver</button>
                    <button type="button" class="btn-cancel" onclick="closeApprovalModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function openApprovalModal(candidatureId, action) {
            const modal = document.getElementById('approvalModal');
            const reasonGroup = document.getElementById('reasonGroup');
            const modalTitle = document.getElementById('modalTitle');
            const confirmBtn = document.getElementById('confirmBtn');
            const actionType = document.getElementById('actionType');

            document.getElementById('candidatureId').value = candidatureId;
            actionType.value = action;

            if (action === 'reject') {
                reasonGroup.style.display = 'block';
                modalTitle.textContent = 'Rejeter la candidature';
                confirmBtn.textContent = 'Rejeter';
                confirmBtn.style.background = 'var(--danger)';
            } else {
                reasonGroup.style.display = 'none';
                modalTitle.textContent = 'Approuver la candidature';
                confirmBtn.textContent = 'Approuver';
                confirmBtn.style.background = 'var(--accent)';
            }

            modal.classList.add('active');
        }

        function closeApprovalModal() {
            document.getElementById('approvalModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('approvalModal');
            if (event.target === modal) {
                closeApprovalModal();
            }
        }
    </script>
</body>
</html>
