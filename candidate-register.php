<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
require_once 'admin/config.php';

// Si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['candidate_logged_in']) && $_SESSION['candidate_logged_in']) {
    header('Location: candidate-dashboard.php');
    exit;
}

// Traiter l'enregistrement
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $telephone = trim($_POST['telephone'] ?? '');
        
        // Validations
        if (empty($prenom)) $errors['prenom'] = 'Le prénom est obligatoire';
        if (empty($nom)) $errors['nom'] = 'Le nom est obligatoire';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        if ($password !== $password_confirm) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
        }
        
        if (empty($telephone)) {
            $errors['telephone'] = 'Le téléphone est obligatoire';
        }
        
        // Vérifier si l'email existe déjà
        if (!$errors && DB::isConnected()) {
            $stmt = DB::prepare("SELECT id FROM candidats WHERE email = ?");
            
            if ($stmt) {
                $result = $stmt->execute([$email]);
                if ($result && $result->num_rows > 0) {
                    $errors['email'] = 'Cet email est déjà utilisé';
                }
            }
        }
        
        // Si pas d'erreurs, enregistrer le candidat
        if (!$errors) {
            if (DB::isConnected()) {
                try {
                    // Hacher le mot de passe
                    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
                    
                    // Créer le candidat
                    $stmt = DB::prepare("
                        INSERT INTO candidats (prenom, nom, email, telephone, password, date_inscription)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    
                    if ($stmt && $stmt->execute([$prenom, $nom, $email, $telephone, $password_hashed])) {
                        $success = true;
                        $_SESSION['message_success'] = 'Compte créé avec succès! Vous pouvez maintenant vous connecter.';
                        
                        // Redirection après 3 secondes
                        header('Refresh: 2; URL=candidate-login.php');
                    }
                } catch (Exception $e) {
                    $errors['database'] = 'Erreur lors de la création du compte: ' . $e->getMessage();
                }
            } else {
                // Mode simulation
                $_SESSION['candidate_email'] = $email;
                $_SESSION['candidate_password'] = $password;
                $_SESSION['candidate_name'] = $prenom . ' ' . $nom;
                $success = true;
                $_SESSION['message_success'] = 'Compte créé avec succès (mode simulation)! Vous pouvez maintenant vous connecter.';
                header('Refresh: 2; URL=candidate-login.php');
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
    <title>Enregistrement - Enroll Concours</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --accent-dark: #219653;
            --danger: #e74c3c;
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
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background: white;
            box-shadow: var(--shadow);
            padding: 15px 0;
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Main Container */
        .register-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Card */
        .register-card {
            background: white;
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
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

        /* Header */
        .register-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .register-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            margin-bottom: 10px;
        }

        .register-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 14px;
        }

        /* Body */
        .register-body {
            padding: 40px 30px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 14px;
        }

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

        /* Required mark */
        .required {
            color: var(--danger);
            margin-left: 4px;
        }

        /* Errors */
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

        /* Success message */
        .alert-success {
            background: #d4edda;
            border: 2px solid var(--accent);
            color: #155724;
            border-radius: var(--radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success i {
            font-size: 18px;
        }

        /* Buttons */
        .btn-register {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
            width: 100%;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, var(--accent-dark), #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Link */
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 14px;
        }

        .login-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--accent-dark);
            text-decoration: underline;
        }

        /* Password strength */
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background: var(--danger);
            transition: var(--transition);
        }

        .strength-bar.weak {
            width: 33%;
            background: var(--danger);
        }

        .strength-bar.medium {
            width: 66%;
            background: var(--warning);
        }

        .strength-bar.strong {
            width: 100%;
            background: var(--accent);
        }

        .strength-text {
            font-size: 12px;
            margin-top: 4px;
            color: #6c757d;
        }

        /* Info box */
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid var(--accent);
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--primary);
        }

        .info-box i {
            margin-right: 8px;
            color: var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-body {
                padding: 25px;
            }

            .register-header {
                padding: 30px 25px;
            }

            .register-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <!-- Main Content -->
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <h1><i class="fas fa-user-plus"></i> S'Enregistrer</h1>
                <p>Créez un compte pour commencer</p>
            </div>

            <!-- Body -->
            <div class="register-body">
                <?php if ($success): ?>
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Succès!</strong><br>
                            Votre compte a été créé avec succès. Redirection vers la connexion...
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors) && !$success): ?>
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

                <?php if (!$success): ?>
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <strong>Champs obligatoires:</strong> Tous les champs marqués d'un astérisque (<span style="color: var(--danger);">*</span>) sont obligatoires.
                    </div>

                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="action" value="register">

                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : ''; ?>" 
                                   id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" 
                                   placeholder="Votre prénom" required>
                            <?php if (isset($errors['prenom'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['prenom']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" 
                                   id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" 
                                   placeholder="Votre nom" required>
                            <?php if (isset($errors['nom'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                   placeholder="exemple@email.com" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone <span class="required">*</span></label>
                            <input type="tel" class="form-control <?php echo isset($errors['telephone']) ? 'is-invalid' : ''; ?>" 
                                   id="telephone" name="telephone" value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" 
                                   placeholder="+237 6XX XXX XXX" required>
                            <?php if (isset($errors['telephone'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['telephone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe <span class="required">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password" placeholder="Minimum 6 caractères" required
                                   onchange="checkPasswordStrength()">
                            <div class="password-strength">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmer mot de passe <span class="required">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" 
                                   id="password_confirm" name="password_confirm" placeholder="Répétez le mot de passe" required>
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password_confirm']; ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn-register">
                            <i class="fas fa-check"></i> Créer le Compte
                        </button>
                    </form>

                    <div class="login-link">
                        Vous avez déjà un compte? <a href="candidate-login.php">Se Connecter</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            if (password.length === 0) {
                strengthBar.className = 'strength-bar';
                strengthText.textContent = '';
                return;
            }

            let strength = 'weak';
            let score = 0;

            // Critères de force
            if (password.length >= 6) score++;
            if (password.length >= 10) score++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password)) score++;

            if (score <= 2) {
                strength = 'weak';
                strengthBar.className = 'strength-bar weak';
                strengthText.textContent = '🔴 Faible';
            } else if (score <= 3) {
                strength = 'medium';
                strengthBar.className = 'strength-bar medium';
                strengthText.textContent = '🟡 Moyen';
            } else {
                strength = 'strong';
                strengthBar.className = 'strength-bar strong';
                strengthText.textContent = '🟢 Fort';
            }
        }

        // Vérifier la force du mot de passe au changement
        document.getElementById('password').addEventListener('input', checkPasswordStrength);
    </script>
</body>
</html>
