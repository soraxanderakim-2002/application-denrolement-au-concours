<?php
session_start();
require_once 'admin/config.php';

// Vérifier que l'enrôlement a réussi
if (!isset($_SESSION['enrollment_success'])) {
    header('Location: enroll-form.php');
    exit;
}

$enrollment_email = $_SESSION['enrollment_email'] ?? '';
$enrollment_data = $_SESSION['enrollment_data'] ?? [];
$candidature_id = $_GET['id'] ?? 'SIM-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

// Nettoyer la session
unset($_SESSION['enrollment_success']);
unset($_SESSION['enrollment_email']);
unset($_SESSION['enrollment_data']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrôlement Réussi - Enroll Concours</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --accent-dark: #219653;
            --light: #f8f9fa;
            --border: #e2e8f0;
            --radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Container */
        .confirmation-container {
            max-width: 600px;
            width: 100%;
        }

        /* Success Card */
        .success-card {
            background: white;
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header with Success Icon */
        .success-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .success-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            margin-bottom: 10px;
        }

        .success-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 15px;
        }

        /* Body */
        .success-body {
            padding: 40px 30px;
        }

        /* Reference Box */
        .reference-box {
            background: #f0f8ff;
            border: 2px solid var(--accent);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .reference-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .reference-number {
            font-size: 24px;
            font-weight: 800;
            color: var(--accent);
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 30px;
        }

        .info-section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item-label {
            color: #6c757d;
            font-weight: 500;
        }

        .info-item-value {
            color: var(--primary);
            font-weight: 600;
        }

        /* Check List */
        .checklist {
            background: #e8f5e9;
            border-left: 4px solid var(--accent);
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .checklist-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
            font-size: 14px;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: var(--primary);
        }

        .checklist-item:last-child {
            margin-bottom: 0;
        }

        .checklist-icon {
            color: var(--accent);
            font-weight: 700;
            font-size: 16px;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-primary, .btn-secondary {
            flex: 1;
            padding: 14px 20px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            text-decoration: none;
            color: white;
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(39, 174, 96, 0.05);
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .success-body {
                padding: 25px;
            }

            .success-header {
                padding: 30px 25px;
            }

            .success-header h1 {
                font-size: 24px;
            }

            .reference-number {
                font-size: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-card">
            <!-- Header with Success Icon -->
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Enrôlement Réussi!</h1>
                <p>Votre demande d'enrôlement a été reçue avec succès</p>
            </div>

            <!-- Body -->
            <div class="success-body">
                <!-- Reference Number -->
                <div class="reference-box">
                    <div class="reference-label">Numéro de Référence</div>
                    <div class="reference-number">#<?php echo htmlspecialchars($candidature_id); ?></div>
                </div>

                <!-- Information Summary -->
                <div class="info-section">
                    <div class="info-section-title">Informations de Contact</div>
                    <div class="info-item">
                        <span class="info-item-label">E-mail:</span>
                        <span class="info-item-value"><?php echo htmlspecialchars($enrollment_email); ?></span>
                    </div>
                    <?php if (!empty($enrollment_data)): ?>
                        <div class="info-item">
                            <span class="info-item-label">Candidat:</span>
                            <span class="info-item-value">
                                <?php echo htmlspecialchars($enrollment_data['prenom'] . ' ' . $enrollment_data['nom']); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-item-label">Concours:</span>
                            <span class="info-item-value">
                                <?php echo htmlspecialchars($enrollment_data['concours']); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-item-label">Filière:</span>
                            <span class="info-item-value">
                                <?php echo htmlspecialchars($enrollment_data['filiere']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- What's Next -->
                <div class="checklist">
                    <div class="checklist-title">Prochaines Étapes:</div>
                    <div class="checklist-item">
                        <span class="checklist-icon">✓</span>
                        <span>Vous recevrez un email de confirmation dans quelques minutes</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-icon">✓</span>
                        <span>Procédez au paiement des frais d'inscription</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-icon">✓</span>
                        <span>Attendez l'approbation de l'administration</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-icon">✓</span>
                        <span>Consultez votre tableau de bord pour le statut</span>
                    </div>
                </div>

                <!-- Important Note -->
                <div style="background: #fff3cd; border-left: 4px solid #e67e22; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 13px; color: #856404;">
                        <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong> 
                        Conservez votre numéro de référence pour suivre votre candidature.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <a href="index.php" class="btn-secondary">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a href="candidate-login.php" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Se Connecter
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
