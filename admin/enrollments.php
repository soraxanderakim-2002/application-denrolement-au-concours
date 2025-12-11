<?php
session_start();
require_once 'config.php';

// Vérifier que l'utilisateur est admin
AdminAuth::checkAdminLogin();

// Traiter les actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_enrollment') {
        $candidature_id = $_POST['candidature_id'] ?? 0;
        $notes = $_POST['notes'] ?? '';
        
        if ($candidature_id && DB::isConnected()) {
            try {
                $stmt = DB::prepare("
                    UPDATE candidatures 
                    SET statut = 'complète', date_approbation = NOW(), notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$notes, $candidature_id]);
                
                $_SESSION['message'] = 'Enrôlement approuvé avec succès';
                $_SESSION['message_type'] = 'success';
            } catch (Exception $e) {
                $_SESSION['message'] = 'Erreur lors de l\'approbation';
                $_SESSION['message_type'] = 'danger';
            }
        }
    } elseif ($action === 'reject_enrollment') {
        $candidature_id = $_POST['candidature_id'] ?? 0;
        $raison = $_POST['raison'] ?? '';
        
        if ($candidature_id && $raison && DB::isConnected()) {
            try {
                $stmt = DB::prepare("
                    UPDATE candidatures 
                    SET statut = 'rejetée', date_rejet = NOW(), raison_rejet = ?
                    WHERE id = ?
                ");
                $stmt->execute([$raison, $candidature_id]);
                
                $_SESSION['message'] = 'Enrôlement rejeté';
                $_SESSION['message_type'] = 'info';
            } catch (Exception $e) {
                $_SESSION['message'] = 'Erreur lors du rejet';
                $_SESSION['message_type'] = 'danger';
            }
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer les enrôlements
$enrolements = [];
if (DB::isConnected()) {
    $result = DB::query("
        SELECT c.id, c.nom, c.prenom, c.email, c.telephone, 
               ca.id as candidature_id, ca.concours, ca.filiere, ca.region, 
               ca.statut, ca.date_inscription
        FROM candidats c
        LEFT JOIN candidatures ca ON c.id = ca.candidat_id
        WHERE ca.statut = 'en attente'
        ORDER BY ca.date_inscription DESC
        LIMIT 50
    ");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $enrolements[] = $row;
        }
    }
} else {
    // Données simulées
    $enrolements = [
        [
            'id' => 1,
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@gmail.com',
            'telephone' => '+237612345678',
            'candidature_id' => 1,
            'concours' => 'CEUP',
            'filiere' => 'Ingénierie',
            'region' => 'Centre',
            'statut' => 'en attente',
            'date_inscription' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'id' => 2,
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@email.com',
            'telephone' => '+237698765432',
            'candidature_id' => 2,
            'concours' => 'CFP',
            'filiere' => 'Commerce',
            'region' => 'Nord',
            'statut' => 'en attente',
            'date_inscription' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 3,
            'nom' => 'Durand',
            'prenom' => 'Pierre',
            'email' => 'pierre.durand@email.com',
            'telephone' => '+237687654321',
            'candidature_id' => 3,
            'concours' => 'CM',
            'filiere' => 'Sciences',
            'region' => 'Sud',
            'statut' => 'en attente',
            'date_inscription' => date('Y-m-d H:i:s')
        ]
    ];
}

$total_enrollments = count($enrolements);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enrôlements - Admin</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --accent-dark: #219653;
            --danger: #e74c3c;
            --warning: #e67e22;
            --light: #f8f9fa;
            --border: #e2e8f0;
            --radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }

        .page-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kpi-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: white;
            border: none;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .kpi-icon {
            font-size: 32px;
            margin-bottom: 12px;
            color: var(--accent);
        }

        .kpi-title {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .kpi-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .enrollment-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .enrollment-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow);
        }

        .enrollment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .enrollment-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .enrollment-date {
            font-size: 12px;
            color: #6c757d;
            margin: 0;
        }

        .enrollment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
        }

        .enrollment-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-approve, .btn-reject {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-approve {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.2);
        }

        .btn-approve:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-reject {
            background: white;
            border: 2px solid var(--danger);
            color: var(--danger);
        }

        .btn-reject:hover {
            background: var(--danger);
            color: white;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            outline: none;
        }

        .alert-message {
            padding: 15px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            border: 2px solid var(--accent);
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border: 2px solid var(--danger);
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #cbd5e0;
        }

        @media (max-width: 768px) {
            .kpi-cards {
                grid-template-columns: 1fr;
            }

            .enrollment-details {
                grid-template-columns: 1fr;
            }

            .enrollment-actions {
                flex-direction: column;
            }

            .btn-approve, .btn-reject {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: white; box-shadow: var(--shadow); padding: 15px 0;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php" style="color: var(--primary); font-weight: 800;">
                <i class="fas fa-graduation-cap" style="color: var(--accent); margin-right: 10px;"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-file-alt"></i> Enrôlements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approbations.php"><i class="fas fa-check-circle"></i> Approbations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <i class="fas fa-file-alt"></i> Gestion des Enrôlements
            </h1>
            <p style="margin: 8px 0 0 0; opacity: 0.9;">Examinez et approuvez les nouveaux enrôlements</p>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="container-fluid">
            <div class="alert-message alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $_SESSION['message']; ?></span>
            </div>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container-fluid" style="padding: 0 20px; margin-bottom: 40px;">
        <!-- KPI Cards -->
        <div class="kpi-cards">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                <div class="kpi-title">Total Enrôlements</div>
                <p class="kpi-value"><?php echo $total_enrollments; ?></p>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-title">En Attente</div>
                <p class="kpi-value"><?php echo $total_enrollments; ?></p>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-title">Approuvés</div>
                <p class="kpi-value">0</p>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-title">Rejetés</div>
                <p class="kpi-value">0</p>
            </div>
        </div>

        <!-- Enrollment List -->
        <?php if (!empty($enrolements)): ?>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--primary); margin-bottom: 20px; margin-top: 40px;">
                <i class="fas fa-list"></i> Enrôlements en Attente
            </h2>

            <?php foreach ($enrolements as $enrollment): ?>
                <div class="enrollment-card">
                    <div class="enrollment-header">
                        <div>
                            <p class="enrollment-name">
                                <?php echo htmlspecialchars($enrollment['prenom'] . ' ' . $enrollment['nom']); ?>
                            </p>
                            <p class="enrollment-date">
                                <i class="fas fa-calendar-alt"></i>
                                Enrôlé le <?php echo date('d M Y à H:i', strtotime($enrollment['date_inscription'])); ?>
                            </p>
                        </div>
                        <span style="background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            <i class="fas fa-clock"></i> En Attente
                        </span>
                    </div>

                    <div class="enrollment-details">
                        <div class="detail-item">
                            <span class="detail-label">E-mail</span>
                            <span class="detail-value"><?php echo htmlspecialchars($enrollment['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Téléphone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($enrollment['telephone']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Concours</span>
                            <span class="detail-value"><?php echo htmlspecialchars($enrollment['concours']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Filière</span>
                            <span class="detail-value"><?php echo htmlspecialchars($enrollment['filiere']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Région</span>
                            <span class="detail-value"><?php echo htmlspecialchars($enrollment['region']); ?></span>
                        </div>
                    </div>

                    <div class="enrollment-actions">
                        <button class="btn-approve" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $enrollment['candidature_id']; ?>">
                            <i class="fas fa-check"></i> Approuver
                        </button>
                        <button class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $enrollment['candidature_id']; ?>">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal<?php echo $enrollment['candidature_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approuver l'Enrôlement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <p>Êtes-vous sûr d'approuver l'enrôlement de <strong><?php echo htmlspecialchars($enrollment['prenom'] . ' ' . $enrollment['nom']); ?></strong> ?</p>
                                    <div class="mb-3">
                                        <label for="notes<?php echo $enrollment['candidature_id']; ?>" class="form-label">Notes (optionnel)</label>
                                        <textarea class="form-control" id="notes<?php echo $enrollment['candidature_id']; ?>" name="notes" rows="3" placeholder="Ajouter des notes..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <input type="hidden" name="action" value="approve_enrollment">
                                    <input type="hidden" name="candidature_id" value="<?php echo $enrollment['candidature_id']; ?>">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal<?php echo $enrollment['candidature_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Rejeter l'Enrôlement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <p>Veuillez indiquer la raison du rejet:</p>
                                    <div class="mb-3">
                                        <label for="raison<?php echo $enrollment['candidature_id']; ?>" class="form-label">Raison du Rejet <span style="color: var(--danger);">*</span></label>
                                        <textarea class="form-control" id="raison<?php echo $enrollment['candidature_id']; ?>" name="raison" rows="3" required placeholder="Expliquez pourquoi cet enrôlement est rejeté..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <input type="hidden" name="action" value="reject_enrollment">
                                    <input type="hidden" name="candidature_id" value="<?php echo $enrollment['candidature_id']; ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 style="color: var(--primary); margin: 15px 0;">Aucun Enrôlement en Attente</h3>
                <p>Tous les enrôlements ont été traités ou aucun enrôlement n'a été reçu.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
