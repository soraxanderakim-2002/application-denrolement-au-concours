<?php
session_start();
require_once 'admin/config.php';

// Vérifier que le candidat est connecté
// Les candidats déjà enregistrés doivent se connecter
// Les nouveaux candidats peuvent créer un compte lors de l'inscription
if (!isset($_SESSION['candidate_logged_in']) || $_SESSION['candidate_logged_in'] !== true) {
    // Option 1: Rediriger vers la connexion
    // header('Location: candidate-login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    // exit;
    
    // Option 2: Afficher un message mais permettre de continuer (pour simplifier)
    // Les candidats peuvent s'inscrire sans compte préalable
}

// Traiter l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'submit_enrollment') {
        $errors = [];
        
        // Validation des champs obligatoires
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $date_naissance = $_POST['date_naissance'] ?? '';
        $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $filiere = trim($_POST['filiere'] ?? '');
        $concours = trim($_POST['concours'] ?? '');
        
        // Validations
        if (empty($nom)) $errors['nom'] = 'Le nom est obligatoire';
        if (empty($prenom)) $errors['prenom'] = 'Le prénom est obligatoire';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }
        if (empty($telephone)) $errors['telephone'] = 'Le téléphone est obligatoire';
        if (empty($date_naissance)) $errors['date_naissance'] = 'La date de naissance est obligatoire';
        if (empty($lieu_naissance)) $errors['lieu_naissance'] = 'Le lieu de naissance est obligatoire';
        if (empty($region)) $errors['region'] = 'La région est obligatoire';
        if (empty($filiere)) $errors['filiere'] = 'La filière est obligatoire';
        if (empty($concours)) $errors['concours'] = 'Le concours est obligatoire';
        
        // Vérifier si l'email existe déjà
        if (!$errors) {
            if (DB::isConnected()) {
                $stmt = DB::prepare("SELECT id FROM candidats WHERE email = ?");
                $result = $stmt->execute([$email]);
                if ($result && $result->num_rows > 0) {
                    $errors['email'] = 'Cet email est déjà utilisé';
                }
            }
        }
        
        // Si pas d'erreurs, créer le candidat et l'inscription
        if (!$errors) {
            if (DB::isConnected()) {
                try {
                    // Créer le candidat
                    $stmt = DB::prepare("
                        INSERT INTO candidats (nom, prenom, email, telephone, date_naissance, lieu_naissance, region, filiere, date_inscription)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    if ($stmt->execute([$nom, $prenom, $email, $telephone, $date_naissance, $lieu_naissance, $region, $filiere])) {
                        $candidat_id = $stmt->insert_id;
                        
                        // Créer l'inscription au concours
                        $stmt = DB::prepare("
                            INSERT INTO candidatures (candidat_id, concours, filiere, region, statut, date_inscription)
                            VALUES (?, ?, ?, ?, 'en attente', NOW())
                        ");
                        
                        if ($stmt->execute([$candidat_id, $concours, $filiere, $region])) {
                            $candidature_id = $stmt->insert_id;
                            
                            // Créer un paiement
                            $prix_concours = 50000; // Prix par défaut, peut être modifié
                            $stmt = DB::prepare("
                                INSERT INTO paiements (candidat_id, candidature_id, montant, methode_paiement, statut, date_paiement)
                                VALUES (?, ?, ?, 'en attente', 'en attente', NOW())
                            ");
                            
                            $stmt->execute([$candidat_id, $candidature_id, $prix_concours]);
                            
                            $_SESSION['enrollment_success'] = true;
                            $_SESSION['enrollment_email'] = $email;
                            header('Location: enrollment-confirmation.php?id=' . $candidature_id);
                            exit;
                        }
                    }
                } catch (Exception $e) {
                    $errors['database'] = 'Erreur lors de l\'enregistrement: ' . $e->getMessage();
                }
            } else {
                // Mode simulation (DB non disponible)
                $_SESSION['enrollment_success'] = true;
                $_SESSION['enrollment_email'] = $email;
                $_SESSION['enrollment_data'] = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'date_naissance' => $date_naissance,
                    'lieu_naissance' => $lieu_naissance,
                    'region' => $region,
                    'filiere' => $filiere,
                    'concours' => $concours
                ];
                header('Location: enrollment-confirmation.php');
                exit;
            }
        }
    }
}

// Données des concours
$concours_list = [
    ['id' => 'ceup', 'nom' => 'Concours d\'Entrée à l\'Université', 'sigle' => 'CEUP', 'prix' => 50000, 'deadline' => '2025-03-15'],
    ['id' => 'cfp', 'nom' => 'Concours Formation Professionnelle', 'sigle' => 'CFP', 'prix' => 45000, 'deadline' => '2025-03-20'],
    ['id' => 'cm', 'nom' => 'Concours Master', 'sigle' => 'CM', 'prix' => 60000, 'deadline' => '2025-04-10'],
    ['id' => 'concop', 'nom' => 'Concours Officiel Public', 'sigle' => 'CONCOP', 'prix' => 55000, 'deadline' => '2025-03-25'],
];

// Données des régions
$regions = ['Nord', 'Sud', 'Est', 'Ouest', 'Centre', 'Littoral'];

// Données des filières
$filieres = ['Ingénierie', 'Commerce', 'Sciences', 'Technologie', 'Gestion', 'Droit'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Enrôlement - Enroll Concours</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--primary);
        }

        /* Navigation */
        .navbar {
            background: white;
            box-shadow: var(--shadow);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            color: var(--primary) !important;
            font-weight: 500;
            margin: 0 15px;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--accent) !important;
        }

        /* Main Container */
        .form-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Header */
        .form-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 40px 30px;
            border-radius: var(--radius);
            margin-bottom: 40px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .form-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .form-header p {
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
        }

        /* Form */
        .form-card {
            background: white;
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 40px;
            margin-bottom: 30px;
        }

        /* Section Heading */
        .section-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 30px 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent);
            color: var(--primary);
            font-size: 20px;
            font-weight: 700;
        }

        .section-heading i {
            color: var(--accent);
            font-size: 24px;
        }

        .section-heading:first-of-type {
            margin-top: 0;
        }

        /* Form Labels */
        label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required {
            color: var(--danger);
            margin-left: 4px;
        }

        /* Form Controls */
        .form-control {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: var(--transition);
            background: white;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #cbd5e0;
        }

        /* Form Group */
        .mb-3 {
            margin-bottom: 20px;
        }

        /* Radio Buttons */
        .form-check {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            margin-bottom: 15px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .form-check-label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 500;
        }

        /* Error Messages */
        .alert-danger {
            background: #fdeaea;
            border: 2px solid var(--danger);
            color: var(--danger);
            border-radius: var(--radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger i {
            font-size: 18px;
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        /* Info Box */
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid var(--accent);
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            color: var(--primary);
            font-size: 14px;
            line-height: 1.6;
        }

        .info-box i {
            color: var(--accent);
            margin-right: 8px;
            font-size: 16px;
        }

        /* Buttons */
        .btn-submit {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            border: none;
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
            width: 100%;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--accent-dark), #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-reset {
            background: white;
            color: var(--primary);
            border: 2px solid var(--border);
            padding: 12px 30px;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            margin-right: 10px;
        }

        .btn-reset:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(39, 174, 96, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-card {
                padding: 25px;
            }

            .form-header {
                padding: 30px 20px;
            }

            .form-header h1 {
                font-size: 24px;
            }

            .section-heading {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <!-- Main Content -->
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h1><i class="fas fa-edit"></i> Formulaire d'Enrôlement</h1>
            <p>Rejoignez-nous en remplissant le formulaire ci-dessous</p>
        </div>

        <!-- Form -->
        <div class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Erreurs détectées:</strong><br>
                        <?php foreach ($errors as $error): ?>
                            • <?php echo htmlspecialchars($error); ?><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>Veuillez remplir tous les champs marqués d'un astérisque (<span style="color: var(--danger);">*</span>)</strong>
            </div>

            <form method="POST" action="" novalidate>
                <input type="hidden" name="action" value="submit_enrollment">

                <!-- Section 1: Informations Personnelles -->
                <div class="section-heading">
                    <i class="fas fa-user"></i>
                    Informations Personnelles
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prenom">Prénom <span class="required">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : ''; ?>" 
                               id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" 
                               placeholder="Votre prénom" required>
                        <?php if (isset($errors['prenom'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['prenom']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nom">Nom <span class="required">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" 
                               id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" 
                               placeholder="Votre nom" required>
                        <?php if (isset($errors['nom'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email">Adresse E-mail <span class="required">*</span></label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="exemple@email.com" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telephone">Téléphone <span class="required">*</span></label>
                        <input type="tel" class="form-control <?php echo isset($errors['telephone']) ? 'is-invalid' : ''; ?>" 
                               id="telephone" name="telephone" value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" 
                               placeholder="+237 6XX XXX XXX" required>
                        <?php if (isset($errors['telephone'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['telephone']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Section 2: Informations de Naissance -->
                <div class="section-heading">
                    <i class="fas fa-calendar-alt"></i>
                    Informations de Naissance
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_naissance">Date de Naissance <span class="required">*</span></label>
                        <input type="date" class="form-control <?php echo isset($errors['date_naissance']) ? 'is-invalid' : ''; ?>" 
                               id="date_naissance" name="date_naissance" 
                               value="<?php echo htmlspecialchars($_POST['date_naissance'] ?? ''); ?>" required>
                        <?php if (isset($errors['date_naissance'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['date_naissance']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="lieu_naissance">Lieu de Naissance <span class="required">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['lieu_naissance']) ? 'is-invalid' : ''; ?>" 
                               id="lieu_naissance" name="lieu_naissance" 
                               value="<?php echo htmlspecialchars($_POST['lieu_naissance'] ?? ''); ?>" 
                               placeholder="Ville/Région de naissance" required>
                        <?php if (isset($errors['lieu_naissance'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['lieu_naissance']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Section 3: Localisation -->
                <div class="section-heading">
                    <i class="fas fa-map-marker-alt"></i>
                    Localisation
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="region">Région <span class="required">*</span></label>
                        <select class="form-control <?php echo isset($errors['region']) ? 'is-invalid' : ''; ?>" 
                                id="region" name="region" required>
                            <option value="">-- Sélectionner une région --</option>
                            <?php foreach ($regions as $r): ?>
                                <option value="<?php echo $r; ?>" <?php echo ($_POST['region'] ?? '') === $r ? 'selected' : ''; ?>>
                                    <?php echo $r; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['region'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['region']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="filiere">Filière <span class="required">*</span></label>
                        <select class="form-control <?php echo isset($errors['filiere']) ? 'is-invalid' : ''; ?>" 
                                id="filiere" name="filiere" required>
                            <option value="">-- Sélectionner une filière --</option>
                            <?php foreach ($filieres as $f): ?>
                                <option value="<?php echo $f; ?>" <?php echo ($_POST['filiere'] ?? '') === $f ? 'selected' : ''; ?>>
                                    <?php echo $f; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['filiere'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['filiere']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Section 4: Sélection du Concours -->
                <div class="section-heading">
                    <i class="fas fa-book"></i>
                    Sélection du Concours
                </div>

                <div class="mb-3">
                    <label for="concours">Concours <span class="required">*</span></label>
                    <select class="form-control <?php echo isset($errors['concours']) ? 'is-invalid' : ''; ?>" 
                            id="concours" name="concours" required>
                        <option value="">-- Sélectionner un concours --</option>
                        <?php foreach ($concours_list as $c): ?>
                            <option value="<?php echo $c['id']; ?>" 
                                    data-prix="<?php echo $c['prix']; ?>"
                                    data-deadline="<?php echo $c['deadline']; ?>"
                                    data-nom="<?php echo htmlspecialchars($c['nom']); ?>"
                                    <?php echo ($_POST['concours'] ?? '') === $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['sigle'] . ' - ' . $c['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['concours'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['concours']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Concours Info -->
                <div id="concoursInfo" style="display: none; margin-bottom: 20px;">
                    <div style="background: #e8f5e9; border-left: 4px solid var(--accent); padding: 15px; border-radius: 8px;">
                        <p style="margin: 0;">
                            <strong>Coût:</strong> <span id="infoPrix" style="color: var(--accent); font-weight: 700;"></span> FCFA<br>
                            <strong>Date limite:</strong> <span id="infoDeadline" style="color: var(--accent); font-weight: 700;"></span>
                        </p>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 40px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="reset" class="btn-reset">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Soumettre l'Enrôlement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Afficher les informations du concours sélectionné
        document.getElementById('concours').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const prix = selected.getAttribute('data-prix');
            const deadline = selected.getAttribute('data-deadline');
            const infoDiv = document.getElementById('concoursInfo');
            
            if (prix && deadline) {
                document.getElementById('infoPrix').textContent = parseInt(prix).toLocaleString();
                document.getElementById('infoDeadline').textContent = new Date(deadline).toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        });

        // Formater les dates pour l'affichage
        document.addEventListener('DOMContentLoaded', function() {
            const concours = document.getElementById('concours');
            if (concours.value) {
                concours.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
