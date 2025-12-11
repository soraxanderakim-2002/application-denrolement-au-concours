<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (AdminAuth::login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants invalides. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur | Enregistrement Concours Cameroun</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-icons/bootstrap-icons.css">
    
    <style>
        :root {
          --primary: #2c3e50;
          --secondary: #34495e;
          --accent: #27ae60;
          --accent-dark: #219653;
          --info: #3498db;
          --radius: 12px;
          --shadow: 0 4px 12px rgba(0,0,0,0.08);
          --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
        }

        html, body {
            height: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 12px;
            color: #fff;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .login-header h1 {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .login-header p {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .demo-credentials {
            background: #f0f7ff;
            border: 1px solid #bde4ff;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
            font-size: 12px;
            color: #004085;
        }

        .demo-credentials strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- NAVBAR PERSISTANTE -->
    <?php require_once __DIR__ . '/../includes/navbar-simple.php'; ?>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1>Espace Administrateur</h1>
                <p>Enregistrement aux Concours Nationaux</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="bi bi-person"></i> Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password"><i class="bi bi-lock"></i> Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-login">Connexion</button>
            </form>

            <div class="demo-credentials">
                <strong>Identifiants de démonstration :</strong>
                Utilisateur: <code>admin</code><br>
                Mot de passe: <code>admin123</code>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
