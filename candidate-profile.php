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

// Données du candidat (simulées)
$candidate = array(
    'id' => $_SESSION['candidate_id'] ?? 0,
    'nom' => 'Jean Dupont',
    'prenom' => 'Jean',
    'email' => 'jean.dupont@gmail.com',
    'telephone' => '+237 6XX XXX XXX',
    'region' => 'Centre',
    'filiere' => 'Ingénierie',
    'date_naissance' => '1998-05-15',
    'lieu_naissance' => 'Yaoundé'
);

$message = '';
$error = '';

// Traiter les mises à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_info') {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $telephone = htmlspecialchars($_POST['telephone'] ?? '');
            
            if (!empty($nom) && !empty($prenom) && !empty($email)) {
                $candidate['nom'] = $nom;
                $candidate['prenom'] = $prenom;
                $candidate['email'] = $email;
                $candidate['telephone'] = $telephone;
                $_SESSION['candidate_name'] = "$prenom $nom";
                $message = 'Informations mises à jour avec succès !';
            } else {
                $error = 'Veuillez remplir tous les champs obligatoires.';
            }
        }
        elseif ($_POST['action'] === 'upload_avatar') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $message = 'Avatar mis à jour avec succès !';
            } else {
                $error = 'Erreur lors du téléchargement du fichier.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Candidat</title>
    
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    
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
            gap: 15px;
        }

        .candidate-navbar a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 6px;
            transition: var(--transition);
        }

        .candidate-navbar a:hover {
            background: rgba(255,255,255,0.1);
        }

        .container-custom {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 40px 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .profile-card {
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
            padding: 30px;
        }

        .form-row-custom {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group-custom {
            display: flex;
            flex-direction: column;
        }

        .form-group-custom label {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group-custom input,
        .form-group-custom select,
        .form-group-custom textarea {
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-group-custom input:focus,
        .form-group-custom select:focus,
        .form-group-custom textarea:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        .alert-custom {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
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

        .btn-submit {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
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

        .btn-secondary {
            background: #e9ecef;
            color: var(--primary);
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #ddd;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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

        input[type="file"] {
            display: none;
        }

        .read-only {
            background: #f8f9fa;
            border: 1px solid #e9ecef !important;
            cursor: not-allowed;
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

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <div class="container-custom">
        <!-- Alerts -->
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
            <div class="profile-avatar-large">
                <i class="bi bi-person-fill"></i>
            </div>
            <h2><?php echo htmlspecialchars($candidate['prenom'] . ' ' . $candidate['nom']); ?></h2>
            <p style="margin: 10px 0 0; opacity: 0.9;">
                <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($candidate['email']); ?>
            </p>
        </div>

        <!-- Informations Personnelles -->
        <div class="profile-card">
            <div class="card-header-custom">
                <i class="bi bi-person-lines-fill"></i>
                <h3>Mes Informations</h3>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="update_info">
                    
                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($candidate['prenom']); ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($candidate['nom']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($candidate['email']); ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($candidate['telephone']); ?>">
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="date_naissance">Date de Naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $candidate['date_naissance']; ?>" class="read-only" disabled>
                        </div>
                        <div class="form-group-custom">
                            <label for="lieu_naissance">Lieu de Naissance</label>
                            <input type="text" id="lieu_naissance" name="lieu_naissance" value="<?php echo htmlspecialchars($candidate['lieu_naissance']); ?>" class="read-only" disabled>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="region">Région</label>
                            <select id="region" name="region" class="read-only" disabled>
                                <option value="Centre" <?php echo $candidate['region'] === 'Centre' ? 'selected' : ''; ?>>Centre</option>
                                <option value="Littoral">Littoral</option>
                                <option value="Adamaoua">Adamaoua</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label for="filiere">Filière</label>
                            <select id="filiere" name="filiere" class="read-only" disabled>
                                <option value="Ingénierie" <?php echo $candidate['filiere'] === 'Ingénierie' ? 'selected' : ''; ?>>Ingénierie</option>
                                <option value="Médecine">Médecine</option>
                                <option value="Droit">Droit</option>
                            </select>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                        <a href="candidate-dashboard.php" class="btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Photo de Profil -->
        <div class="profile-card">
            <div class="card-header-custom">
                <i class="bi bi-image"></i>
                <h3>Photo de Profil</h3>
            </div>
            <div class="card-body-custom">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_avatar">
                    
                    <div class="form-group-custom" style="margin-bottom: 20px;">
                        <input type="file" id="avatar" name="avatar" accept="image/*">
                        <label for="avatar" class="file-upload-label">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <div>Cliquez ou glissez une image ici</div>
                            <small>JPG, PNG ou GIF (Max 5MB)</small>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-upload"></i> Télécharger
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
