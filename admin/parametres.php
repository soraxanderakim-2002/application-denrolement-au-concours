<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = 'Paramètres enregistrés avec succès!';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres | Enroll Concours</title>
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
            gap: 10px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .card-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            outline: none;
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            outline: none;
        }

        .btn-save {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .section-title {
            color: var(--primary);
            font-weight: 700;
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
        }

        .two-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
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
                    <li><a href="centres.php"><i class="bi bi-building"></i> Centres</a></li>
                    <li><hr class="text-white-50 my-3"></li>
                    <li><a href="parametres.php" class="active"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <!-- Topbar -->
                <div class="topbar">
                    <h1 class="title"><i class="bi bi-gear"></i> Paramètres Système</h1>
                </div>

                <!-- Main Content Area -->
                <div class="main-content">
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?php echo $message; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Paramètres Généraux -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-info-circle"></i> Paramètres Généraux</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="section-title">Configuration Système</div>
                                <div class="two-columns">
                                    <div class="form-group">
                                        <label>Nom du Site</label>
                                        <input type="text" class="form-control" value="Enroll Concours - Cameroun" placeholder="Nom du site">
                                    </div>
                                    <div class="form-group">
                                        <label>Email de Contact</label>
                                        <input type="email" class="form-control" value="contact@enroll.cm" placeholder="Email principal">
                                    </div>
                                </div>

                                <div class="two-columns">
                                    <div class="form-group">
                                        <label>Téléphone</label>
                                        <input type="tel" class="form-control" value="+237 6XX XXX XXX" placeholder="Téléphone">
                                    </div>
                                    <div class="form-group">
                                        <label>Adresse</label>
                                        <input type="text" class="form-control" value="Yaoundé, Cameroun" placeholder="Adresse">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description du Site</label>
                                    <textarea class="form-control" rows="4" placeholder="Description...">Système d'enregistrement aux concours nationaux du Cameroun</textarea>
                                </div>

                                <div class="section-title">Configuration des Paiements</div>
                                <div class="two-columns">
                                    <div class="form-group">
                                        <label>Montant Inscription (FCFA)</label>
                                        <input type="number" class="form-control" value="25000" placeholder="Montant">
                                    </div>
                                    <div class="form-group">
                                        <label>Devise</label>
                                        <input type="text" class="form-control" value="FCFA" disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Passerelles Paiement Activées</label>
                                    <div style="display: flex; gap: 20px;">
                                        <div>
                                            <input type="checkbox" id="mtn" checked>
                                            <label for="mtn">MTN Mobile Money</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="orange" checked>
                                            <label for="orange">Orange Money</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="bank" checked>
                                            <label for="bank">Virement Bancaire</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="check" checked>
                                            <label for="check">Chèque</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-title">Configuration des Concours</div>
                                <div class="form-group">
                                    <label>Délai Limite d'Inscription (jours)</label>
                                    <input type="number" class="form-control" value="90" placeholder="Nombre de jours">
                                </div>

                                <div class="form-group">
                                    <label>Délai Paiement après Inscription (jours)</label>
                                    <input type="number" class="form-control" value="30" placeholder="Nombre de jours">
                                </div>

                                <div class="form-group">
                                    <label>Tarif par Concours Supplémentaire (FCFA)</label>
                                    <input type="number" class="form-control" value="15000" placeholder="Montant">
                                </div>

                                <div class="section-title">Configuration Email</div>
                                <div class="two-columns">
                                    <div class="form-group">
                                        <label>SMTP Serveur</label>
                                        <input type="text" class="form-control" value="mail.enroll.cm" placeholder="Serveur SMTP">
                                    </div>
                                    <div class="form-group">
                                        <label>Port SMTP</label>
                                        <input type="number" class="form-control" value="587" placeholder="Port">
                                    </div>
                                </div>

                                <div class="two-columns">
                                    <div class="form-group">
                                        <label>Email SMTP</label>
                                        <input type="email" class="form-control" value="noreply@enroll.cm" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <label>Mot de passe SMTP</label>
                                        <input type="password" class="form-control" placeholder="Mot de passe">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" checked>
                                        Utiliser TLS
                                    </label>
                                </div>

                                <button type="submit" class="btn-save">
                                    <i class="bi bi-check-circle"></i> Enregistrer Paramètres
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sauvegardes et Maintenance -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-cloud-arrow-down"></i> Sauvegardes et Maintenance</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: grid; gap: 15px;">
                                <div style="padding: 15px; background: var(--light); border-radius: 8px;">
                                    <h5 style="color: var(--primary); margin-bottom: 10px;">
                                        <i class="bi bi-cloud-arrow-down"></i> Dernière Sauvegarde
                                    </h5>
                                    <p style="margin: 0; color: var(--muted);">
                                        <small>09/12/2025 à 14:30 (4.2 GB)</small>
                                    </p>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                    <button style="background: var(--accent); color: #fff; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                        <i class="bi bi-cloud-arrow-down"></i> Créer Sauvegarde
                                    </button>
                                    <button style="background: var(--info); color: #fff; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                        <i class="bi bi-arrow-repeat"></i> Restaurer Sauvegarde
                                    </button>
                                    <button style="background: var(--warning); color: #fff; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                                        <i class="bi bi-download"></i> Télécharger Sauvegarde
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="bi bi-shield-lock"></i> Sécurité</h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Ancien Mot de Passe</label>
                                    <input type="password" class="form-control" placeholder="Entrez votre mot de passe actuel">
                                </div>

                                <div class="form-group">
                                    <label>Nouveau Mot de Passe</label>
                                    <input type="password" class="form-control" placeholder="Nouveau mot de passe">
                                </div>

                                <div class="form-group">
                                    <label>Confirmer le Mot de Passe</label>
                                    <input type="password" class="form-control" placeholder="Confirmez le nouveau mot de passe">
                                </div>

                                <button type="submit" class="btn-save">
                                    <i class="bi bi-check-circle"></i> Changer Mot de Passe
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
