<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
require_once 'admin/config.php';

// Rediriger si pas connecté
if (!isset($_SESSION['candidate_logged_in']) || $_SESSION['candidate_logged_in'] !== true) {
    header('Location: candidate-login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['candidate_id'] ?? 0;
$user_name = $_SESSION['candidate_name'] ?? 'Candidat';

// Données simulées pour les inscriptions du candidat
$inscriptions = array(
    array(
        'id' => 1,
        'concours' => 'Concours d\'Entrée à l\'Université Publique',
        'sigle' => 'CEUP',
        'filiere' => 'Ingénierie',
        'region' => 'Centre',
        'date_inscription' => '2025-01-15',
        'statut' => 'complète',
        'paiement' => 'validé',
        'documents' => 'OK'
    ),
    array(
        'id' => 2,
        'concours' => 'Concours de la Fonction Publique',
        'sigle' => 'CFP',
        'filiere' => 'Droit',
        'region' => 'Centre',
        'date_inscription' => '2025-01-20',
        'statut' => 'en attente',
        'paiement' => 'en attente',
        'documents' => 'Incomplet'
    ),
    array(
        'id' => 3,
        'concours' => 'Concours Militaire',
        'sigle' => 'CM',
        'filiere' => 'Médecine',
        'region' => 'Littoral',
        'date_inscription' => '2025-01-25',
        'statut' => 'rejetée',
        'paiement' => 'rejeté',
        'documents' => 'Manquants'
    )
);

// Compter les statuts
$completed = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'complète'));
$pending = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'en attente'));
$rejected = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'rejetée'));
$total = count($inscriptions);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Inscriptions | Enroll Concours</title>
    
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
    
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

        .candidate-navbar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-lg);
        }

        .candidate-navbar .brand {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 20px;
            font-weight: 700;
        }

        .candidate-navbar .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .candidate-navbar .user-menu a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .candidate-navbar .user-menu a:hover {
            background: rgba(255,255,255,0.1);
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: #fff;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
        }

        .kpi-card.warning {
            border-left-color: var(--warning);
        }

        .kpi-card.danger {
            border-left-color: var(--danger);
        }

        .kpi-card.info {
            border-left-color: var(--info);
        }

        .kpi-card h4 {
            color: var(--muted);
            font-size: 14px;
            margin: 0 0 10px;
            text-transform: uppercase;
        }

        .kpi-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .kpi-card.warning .value {
            color: var(--warning);
        }

        .kpi-card.danger .value {
            color: var(--danger);
        }

        .kpi-card.info .value {
            color: var(--info);
        }

        .main-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-custom h3 {
            margin: 0;
            font-weight: 700;
        }

        .card-body-custom {
            padding: 20px;
        }

        .inscription-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .inscription-item:hover {
            box-shadow: var(--shadow);
        }

        .inscription-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .inscription-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .inscription-sigle {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .inscription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--primary);
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-completed {
            background: rgba(39, 174, 96, 0.2);
            color: var(--accent);
        }

        .status-pending {
            background: rgba(230, 126, 34, 0.2);
            color: var(--warning);
        }

        .status-rejected {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-custom {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .btn-view {
            background: var(--info);
            color: #fff;
        }

        .btn-view:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-edit {
            background: var(--accent);
            color: #fff;
        }

        .btn-edit:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 64px;
            color: #d0d0d0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .candidate-navbar {
                flex-direction: column;
                gap: 15px;
            }

            .candidate-navbar .user-menu {
                width: 100%;
                justify-content: space-around;
            }

            .inscription-header {
                flex-direction: column;
                gap: 10px;
            }

            .inscription-details {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <div class="container-custom">
        <!-- Page Title -->
        <div class="page-title">
            <i class="bi bi-file-earmark-check"></i>
            Mes Inscriptions aux Concours
        </div>

        <!-- KPI Cards -->
        <div class="kpi-row">
            <div class="kpi-card">
                <h4>Total Inscriptions</h4>
                <p class="value"><?php echo $total; ?></p>
            </div>
            <div class="kpi-card accent">
                <h4>Complétées</h4>
                <p class="value"><?php echo $completed; ?></p>
            </div>
            <div class="kpi-card warning">
                <h4>En Attente</h4>
                <p class="value"><?php echo $pending; ?></p>
            </div>
            <div class="kpi-card danger">
                <h4>Rejetées</h4>
                <p class="value"><?php echo $rejected; ?></p>
            </div>
        </div>

        <!-- Inscriptions List -->
        <div class="main-card">
            <div class="card-header-custom">
                <i class="bi bi-list-check"></i>
                <h3>Liste de mes Inscriptions</h3>
            </div>
            <div class="card-body-custom">
                <?php if (empty($inscriptions)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>Aucune inscription</h3>
                        <p>Vous n'avez pas encore d'inscription aux concours.</p>
                        <a href="enroll.php" style="color: var(--accent); text-decoration: none; margin-top: 15px; display: inline-block; font-weight: 600;">
                            <i class="bi bi-plus-circle"></i> Créer une inscription
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($inscriptions as $inscription): ?>
                    <div class="inscription-item">
                        <div class="inscription-header">
                            <div>
                                <h4 class="inscription-title"><?php echo $inscription['concours']; ?></h4>
                                <span class="inscription-sigle"><?php echo $inscription['sigle']; ?></span>
                            </div>
                            <span class="status-badge status-<?php echo str_replace('é', 'e', $inscription['statut']) === 'completee' ? 'completed' : ($inscription['statut'] === 'en attente' ? 'pending' : 'rejected'); ?>">
                                <i class="bi bi-<?php 
                                    if ($inscription['statut'] === 'complète') echo 'check-circle';
                                    elseif ($inscription['statut'] === 'en attente') echo 'clock';
                                    else echo 'x-circle';
                                ?>"></i>
                                <?php echo ucfirst($inscription['statut']); ?>
                            </span>
                        </div>

                        <div class="inscription-details">
                            <div class="detail-item">
                                <span class="detail-label">Filière</span>
                                <span class="detail-value"><?php echo $inscription['filiere']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Région</span>
                                <span class="detail-value"><?php echo $inscription['region']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date d'Inscription</span>
                                <span class="detail-value"><?php echo date('d/m/Y', strtotime($inscription['date_inscription'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Paiement</span>
                                <span class="detail-value">
                                    <span class="status-badge status-<?php 
                                        echo $inscription['paiement'] === 'validé' ? 'completed' : ($inscription['paiement'] === 'en attente' ? 'pending' : 'rejected'); 
                                    ?>">
                                        <?php echo ucfirst($inscription['paiement']); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Documents</span>
                                <span class="detail-value">
                                    <span class="status-badge status-<?php 
                                        echo $inscription['documents'] === 'OK' ? 'completed' : 'pending'; 
                                    ?>">
                                        <?php echo $inscription['documents']; ?>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn-custom btn-view" onclick="viewInscription(<?php echo $inscription['id']; ?>)">
                                <i class="bi bi-eye"></i> Détails
                            </button>
                            <?php if ($inscription['statut'] === 'en attente' || $inscription['statut'] === 'rejetée'): ?>
                            <button class="btn-custom btn-edit" onclick="editInscription(<?php echo $inscription['id']; ?>)">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="main-card" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; border-radius: var(--radius);">
            <div style="padding: 40px 20px; text-align: center;">
                <h3 style="margin-bottom: 15px;"><i class="bi bi-pencil-square"></i> Vous souhaitez vous inscrire à un autre concours ?</h3>
                <p style="margin-bottom: 20px; opacity: 0.9;">Découvrez tous les concours disponibles et inscrivez-vous maintenant.</p>
                <a href="enroll.php" style="display: inline-block; background: var(--accent); color: #fff; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: var(--transition);" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-plus-circle"></i> Découvrir les concours
                </a>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewInscription(id) {
            alert('Voir les détails de l\'inscription #' + id);
            // Rediriger vers la page de détails
        }

        function editInscription(id) {
            alert('Modifier l\'inscription #' + id);
            // Rediriger vers la page d'édition
        }
    </script>
</body>
</html>
