<?php
session_start();
require_once 'config.php';

AdminAuth::checkLogin();

// Vérifier que c'est un administrateur
if ($_SESSION['admin_role'] !== 'administrateur') {
    header('Location: ../index.php');
    exit;
}

// Traiter la mise à jour du profil
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_id'];
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_info') {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $telephone = htmlspecialchars($_POST['telephone'] ?? '');
            
            if (!empty($nom) && !empty($email)) {
                // Simuler la mise à jour
                $_SESSION['admin_name'] = $nom;
                $_SESSION['admin_email'] = $email;
                $message = 'Informations mises à jour avec succès !';
            } else {
                $error = 'Veuillez remplir tous les champs obligatoires.';
            }
        }
        elseif ($_POST['action'] === 'upload_avatar') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/avatars/';
                
                // Créer le répertoire s'il n'existe pas
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($file_ext), $allowed_ext)) {
                    $new_filename = 'avatar_' . $admin_id . '_' . time() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                        $_SESSION['admin_avatar'] = $new_filename;
                        $message = 'Avatar mis à jour avec succès !';
                    } else {
                        $error = 'Erreur lors du téléchargement du fichier.';
                    }
                } else {
                    $error = 'Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.';
                }
            } else {
                $error = 'Veuillez sélectionner un fichier valide.';
            }
        }
        elseif ($_POST['action'] === 'change_password') {
            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                $error = 'Veuillez remplir tous les champs de mot de passe.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Les nouveaux mots de passe ne correspondent pas.';
            } elseif (strlen($new_password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } else {
                // Vérifier le ancien mot de passe (simulé)
                if ($old_password === 'admin123') {
                    $message = 'Mot de passe changé avec succès !';
                } else {
                    $error = 'Ancien mot de passe incorrect.';
                }
            }
        }
    }
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';
$admin_avatar = $_SESSION['admin_avatar'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Admin Dashboard</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    
    <style>
        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 40px 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar.default {
            background: var(--accent);
            font-size: 48px;
        }

        .profile-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .profile-card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-card-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .profile-card-body {
            padding: 30px;
        }

        .form-group-custom {
            margin-bottom: 20px;
        }

        .form-group-custom label {
            display: block;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group-custom input,
        .form-group-custom textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-group-custom input:focus,
        .form-group-custom textarea:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .alert-custom {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            border-color: var(--accent);
            color: var(--accent);
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-wrapper input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: block;
            padding: 20px;
            border: 2px dashed var(--accent);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: rgba(39, 174, 96, 0.05);
        }

        .file-upload-label:hover {
            background: rgba(39, 174, 96, 0.1);
            border-color: var(--accent-dark);
        }

        .file-upload-label i {
            font-size: 32px;
            color: var(--accent);
            display: block;
            margin-bottom: 10px;
        }

        .row-custom {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 30px 15px;
            }

            .profile-card-body {
                padding: 20px;
            }

            .row-custom {
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
                    <li><a href="paiements.php"><i class="bi bi-credit-card"></i> Paiements</a></li>
                    <li><a href="centres.php"><i class="bi bi-building"></i> Centres</a></li>
                    <li><a href="statistiques.php"><i class="bi bi-graph-up"></i> Statistiques</a></li>
                    <li><a href="rapports.php"><i class="bi bi-file-text"></i> Rapports</a></li>
                    <li><a href="parametres.php"><i class="bi bi-gear"></i> Paramètres</a></li>
                    <li><hr style="border-color: rgba(255,255,255,0.2);"></li>
                    <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i> Mon Profil</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <div class="dashboard-content">
                <div class="topbar">
                    <h1><i class="bi bi-person-circle"></i> Mon Profil</h1>
                    <div class="topbar-right">
                        <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
                    </div>
                </div>

                <main class="content">
                    <!-- Messages -->
                    <?php if (!empty($message)): ?>
                    <div class="alert-custom alert-success">
                        <i class="bi bi-check-circle"></i> <?php echo $message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                    <div class="alert-custom alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-avatar <?php echo empty($admin_avatar) ? 'default' : ''; ?>">
                            <?php if (!empty($admin_avatar)): ?>
                                <img src="../assets/avatars/<?php echo htmlspecialchars($admin_avatar); ?>" alt="Avatar">
                            <?php else: ?>
                                <i class="bi bi-person-fill"></i>
                            <?php endif; ?>
                        </div>
                        <h2><?php echo htmlspecialchars($admin_name); ?></h2>
                        <p style="margin: 10px 0 0; opacity: 0.9;">
                            <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($admin_email); ?>
                        </p>
                    </div>

                    <!-- Profile Cards -->
                    <div class="row-custom">
                        <!-- Informations Personnelles -->
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <i class="bi bi-person-lines-fill"></i>
                                <h4>Informations Personnelles</h4>
                            </div>
                            <div class="profile-card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_info">
                                    
                                    <div class="form-group-custom">
                                        <label for="nom">Nom Complet *</label>
                                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($admin_name); ?>" required>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin_email); ?>" required>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="telephone">Téléphone</label>
                                        <input type="tel" id="telephone" name="telephone" placeholder="+237 6XX XXX XXX">
                                    </div>

                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle"></i> Mettre à jour
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <i class="bi bi-image"></i>
                                <h4>Photo de Profil</h4>
                            </div>
                            <div class="profile-card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="upload_avatar">
                                    
                                    <div class="form-group-custom">
                                        <label>Télécharger un avatar</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" id="avatar" name="avatar" accept="image/*">
                                            <label for="avatar" class="file-upload-label">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                                <div>Cliquez ou glissez une image ici</div>
                                                <small>JPG, PNG ou GIF (Max 5MB)</small>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-upload"></i> Télécharger
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Changer le mot de passe -->
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <i class="bi bi-lock"></i>
                                <h4>Sécurité</h4>
                            </div>
                            <div class="profile-card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="form-group-custom">
                                        <label for="old_password">Ancien mot de passe *</label>
                                        <input type="password" id="old_password" name="old_password" required>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="new_password">Nouveau mot de passe *</label>
                                        <input type="password" id="new_password" name="new_password" required>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="confirm_password">Confirmer le mot de passe *</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                    </div>

                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-shield-check"></i> Changer le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </main>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
