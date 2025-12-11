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

$error = '';
$remember = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        $error = 'Email et mot de passe sont obligatoires';
    } else {
        $login_success = false;
        $candidate_id = null;
        $candidate_name = '';

        if (DB::isConnected()) {
            // Rechercher le candidat dans la base de données
            $stmt = DB::prepare("SELECT id, prenom, nom, password FROM candidats WHERE email = ?");
            
            if ($stmt) {
                $result = $stmt->execute([$email]);
                
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    
                    // Vérifier le mot de passe
                    if (password_verify($password, $row['password'])) {
                        $login_success = true;
                        $candidate_id = $row['id'];
                        $candidate_name = $row['prenom'] . ' ' . $row['nom'];
                    }
                }
            }
        } else {
            // Mode simulation (pour test sans base de données)
            if (isset($_SESSION['candidate_email']) && $_SESSION['candidate_email'] === $email && 
                $_SESSION['candidate_password'] === $password) {
                $login_success = true;
                $candidate_id = 0;
                $candidate_name = $_SESSION['candidate_name'] ?? 'Candidat';
            }
        }

        if ($login_success) {
            // Configurer les variables de session
            $_SESSION['candidate_logged_in'] = true;
            $_SESSION['candidate_id'] = $candidate_id;
            $_SESSION['candidate_email'] = $email;
            $_SESSION['candidate_name'] = $candidate_name;
            $_SESSION['login_time'] = time();

            // Si "Se souvenir de moi" est coché
            if ($remember_me) {
                setcookie('candidate_email', $email, time() + (30 * 24 * 60 * 60), '/'); // 30 jours
            }

            // Redirection
            $redirect = $_GET['redirect'] ?? 'candidate-dashboard.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    }
}

// Vérifier si une adresse email est en cookie
$cookie_email = $_COOKIE['candidate_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Enroll Concours</title>
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
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Card */
        .login-card {
            background: white;
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-width: 420px;
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
        .login-header {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            margin-bottom: 10px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 14px;
        }

        /* Body */
        .login-body {
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
            display: block;
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

        /* Error */
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

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }

        /* Button */
        .btn-login {
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
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--accent-dark), #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Links */
        .login-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        .login-links a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-links a:hover {
            color: var(--accent-dark);
            text-decoration: underline;
        }

        /* Register link */
        .register-link {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            color: var(--accent-dark);
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
            .login-body {
                padding: 25px;
            }

            .login-header {
                padding: 30px 25px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-links {
                flex-direction: column;
                gap: 10px;
            }

            .login-links a {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/includes/navbar-simple.php'; ?>

    <!-- Main Content -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
                <p>Connectez-vous pour accéder à votre compte</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['message_success'])): ?>
                    <div class="info-box" style="background: #d4edda; border-left-color: var(--accent);">
                        <i class="fas fa-check-circle"></i>
                        <strong><?php echo htmlspecialchars($_SESSION['message_success']); ?></strong>
                    </div>
                    <?php unset($_SESSION['message_success']); ?>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($cookie_email); ?>" 
                               placeholder="votre@email.com" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="••••••••" required>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="remember_me" name="remember_me" value="1">
                        <label for="remember_me">Se souvenir de moi</label>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Se Connecter
                    </button>
                </form>

                <div class="login-links">
                    <a href="candidate-register.php"><i class="fas fa-user-plus"></i> Créer un compte</a>
                    <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
                </div>

                <div class="register-link">
                    Pas de compte? <a href="candidate-register.php">S'enregistrer maintenant</a>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
