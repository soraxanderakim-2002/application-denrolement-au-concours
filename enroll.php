<?php
session_start();
require_once 'admin/config.php';

// Récupérer l'ID du concours
$concours_id = isset($_GET['concours_id']) ? intval($_GET['concours_id']) : 0;

// Données des concours (même que index.php)
$concours_list = [
    [
        'id' => 1,
        'nom' => 'Concours d\'Entrée à l\'Université Publique',
        'sigle' => 'CEUP',
        'description' => 'Accédez à l\'enseignement supérieur public du Cameroun.',
        'prix' => 50000,
        'filieres' => ['Ingénierie', 'Médecine', 'Droit', 'Économie', 'Sciences'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Ouest'],
    ],
    [
        'id' => 2,
        'nom' => 'Concours de Formation Professionnelle',
        'sigle' => 'CFP',
        'description' => 'Formation professionnelle qualifiante.',
        'prix' => 30000,
        'filieres' => ['Électronique', 'Mécanique', 'Informatique', 'Commerce', 'Hôtellerie'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Est'],
    ],
    [
        'id' => 3,
        'nom' => 'Concours Militaire',
        'sigle' => 'CM',
        'description' => 'Rejoignez les forces de défense du Cameroun.',
        'prix' => 45000,
        'filieres' => ['Infanterie', 'Marine', 'Aéronautique', 'Service'],
        'regions' => ['Nord', 'Centre', 'Littoral', 'Ouest'],
    ],
    [
        'id' => 4,
        'nom' => 'Concours Compétences et Orientation Professionnelle',
        'sigle' => 'CONCOP',
        'description' => 'Développez vos compétences professionnelles.',
        'prix' => 25000,
        'filieres' => ['Gestion', 'Marketing', 'Ressources Humaines', 'Finance', 'Entrepreneuriat'],
        'regions' => ['Nord', 'Centre', 'Sud', 'Littoral', 'Ouest', 'Est'],
    ]
];

// Trouver le concours sélectionné
$concours_selected = null;
foreach ($concours_list as $c) {
    if ($c['id'] == $concours_id) {
        $concours_selected = $c;
        break;
    }
}

// Traitement du formulaire
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll') {
    // Validation des champs
    $errors = [];
    
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $region = $_POST['region'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $concours_id = intval($_POST['concours_id'] ?? 0);
    $methode_paiement = $_POST['methode_paiement'] ?? '';

    // Validation
    if (empty($prenom)) $errors[] = 'Le prénom est obligatoire';
    if (empty($nom)) $errors[] = 'Le nom est obligatoire';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Un email valide est obligatoire';
    if (empty($telephone)) $errors[] = 'Le téléphone est obligatoire';
    if (empty($date_naissance)) $errors[] = 'La date de naissance est obligatoire';
    if (empty($lieu_naissance)) $errors[] = 'Le lieu de naissance est obligatoire';
    if (empty($region)) $errors[] = 'La région est obligatoire';
    if (empty($filiere)) $errors[] = 'La filière est obligatoire';
    if (empty($methode_paiement)) $errors[] = 'Veuillez sélectionner une méthode de paiement';

    // Validation des fichiers
    $documents_requis = ['bac' => 'BAC/Diplôme', 'cin' => 'CIN/Passeport', 'certificat' => 'Certificat'];
    $fichiers_uploades = [];

    if (empty($errors)) {
        foreach ($documents_requis as $key => $label) {
            if (isset($_FILES['document_' . $key]) && $_FILES['document_' . $key]['size'] > 0) {
                $file = $_FILES['document_' . $key];
                
                // Vérifier le type de fichier
                $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = "Format de fichier non accepté pour $label (PDF, JPEG, PNG uniquement)";
                    continue;
                }

                // Vérifier la taille (max 5MB)
                if ($file['size'] > 5 * 1024 * 1024) {
                    $errors[] = "Le fichier $label dépasse 5MB";
                    continue;
                }

                $fichiers_uploades[$key] = [
                    'name' => $file['name'],
                    'size' => $file['size'],
                    'type' => $file['type']
                ];
            }
        }
    }

    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    } else {
        // Créer le dossier pour les documents si nécessaire
        $upload_dir = 'uploads/candidatures/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Traiter les uploads
        $files_saved = [];
        foreach ($fichiers_uploades as $key => $file_info) {
            $file = $_FILES['document_' . $key];
            $filename = time() . '_' . sanitizeFilename($file['name']);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $files_saved[$key] = $filename;
            }
        }

        // Créer le numéro de référence unique
        $reference = 'ENROLL-' . date('YmdHis') . '-' . rand(1000, 9999);

        // Préparer les données pour insertion
        $candidature_data = [
            'reference' => $reference,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'date_naissance' => $date_naissance,
            'lieu_naissance' => $lieu_naissance,
            'region' => $region,
            'filiere' => $filiere,
            'concours_id' => $concours_id,
            'documents' => json_encode($files_saved),
            'statut' => 'en attente',
            'date_inscription' => date('Y-m-d H:i:s')
        ];

        // Insérer en base de données (si disponible) ou en session
        if (DB::isConnected()) {
            // Insérer dans la table candidats
            $query = "INSERT INTO candidats (prenom, nom, email, telephone, date_naissance, lieu_naissance, region, filiere, concours_id, date_inscription, statut) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'en attente')";
            
            $stmt = DB::prepare($query);
            if ($stmt) {
                $stmt->bind_param('ssssssssi', $prenom, $nom, $email, $telephone, $date_naissance, $lieu_naissance, $region, $filiere, $concours_id);
                if ($stmt->execute()) {
                    $candidat_id = $stmt->insert_id;

                    // Insérer le paiement associé
                    $prix = 0;
                    foreach ($concours_list as $c) {
                        if ($c['id'] == $concours_id) {
                            $prix = $c['prix'];
                            break;
                        }
                    }

                    $paiement_query = "INSERT INTO paiements (candidat_id, reference, montant, methode_paiement, statut, date_creation) 
                                      VALUES (?, ?, ?, ?, 'en attente', NOW())";
                    $paiement_stmt = DB::prepare($paiement_query);
                    if ($paiement_stmt) {
                        $paiement_stmt->bind_param('isds', $candidat_id, $reference, $prix, $methode_paiement);
                        $paiement_stmt->execute();
                    }

                    $message = 'Inscription réussie! Votre numéro de référence est: <strong>' . $reference . '</strong>';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de l\'enregistrement';
                    $message_type = 'error';
                }
            }
        } else {
            // Mode simulation - stocker en session
            $_SESSION['enrollments'][] = $candidature_data;
            $message = 'Inscription réussie en mode simulation! Votre numéro de référence est: <strong>' . $reference . '</strong>';
            $message_type = 'success';
        }
    }
}

function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Enrôlement - Enroll Concours</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #27ae60;
            --warning: #e67e22;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 800px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .form-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            opacity: 0.9;
            margin: 0;
        }

        .form-body {
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid var(--accent);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            padding: 0.75rem;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .invalid-feedback {
            display: block;
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .required-mark {
            color: var(--danger);
            font-weight: 700;
        }

        /* Document Upload */
        .document-upload {
            border: 2px dashed #bdc3c7;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .document-upload:hover {
            border-color: var(--accent);
            background-color: rgba(39, 174, 96, 0.05);
        }

        .document-upload.drag-over {
            border-color: var(--accent);
            background-color: rgba(39, 174, 96, 0.1);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.15);
        }

        .document-upload-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .document-upload-text {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .document-upload-hint {
            color: #7f8c8d;
            font-size: 0.85rem;
        }

        .file-input {
            display: none;
        }

        .file-name {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .payment-option {
            border: 2px solid #bdc3c7;
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            background-color: white;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option input[type="radio"]:checked + .payment-label {
            border-color: var(--accent);
        }

        .payment-option:has(input[type="radio"]:checked) {
            border-color: var(--accent);
            background-color: rgba(39, 174, 96, 0.05);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        .payment-label {
            cursor: pointer;
            margin: 0;
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .payment-name {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
        }

        /* Alert Messages */
        .alert {
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .alert-success {
            background-color: #d4edda;
            border: none;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: none;
            color: #721c24;
        }

        /* Buttons */
        .btn-submit {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: #229954;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-back {
            background-color: #bdc3c7;
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .button-group .btn-submit {
            flex: 1;
            min-width: 200px;
        }

        .checklist {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .checklist-item:last-child {
            margin-bottom: 0;
        }

        .checklist-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .concours-info {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .concours-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .concours-info-label {
            opacity: 0.9;
        }

        .concours-info-value {
            font-weight: 700;
        }

        @media (max-width: 576px) {
            .form-body {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.4rem;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .button-group .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h1><i class="bi bi-pencil-square"></i> Formulaire d'Enrôlement</h1>
            <p>Inscrivez-vous à votre concours préféré</p>
        </div>

        <div class="form-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="bi bi-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                    <?php if ($message_type === 'success'): ?>
                        <hr style="margin: 1rem 0;">
                        <p style="margin-bottom: 1rem;">
                            Veuillez garder votre numéro de référence en sécurité. Vous pouvez procéder au paiement ou vous connecter ultérieurement.
                        </p>
                        <a href="index.php" class="btn btn-back">Retourner à l'accueil</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$concours_selected): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Veuillez sélectionner un concours valide depuis la page d'accueil.
                </div>
                <a href="index.php" class="btn btn-back">Retourner à l'accueil</a>
            <?php elseif (empty($message) || $message_type !== 'success'): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="enroll">
                    <input type="hidden" name="concours_id" value="<?php echo $concours_selected['id']; ?>">

                    <!-- Concours Info -->
                    <div class="concours-info">
                        <div class="concours-info-item">
                            <span class="concours-info-label">Concours:</span>
                            <span class="concours-info-value"><?php echo $concours_selected['nom']; ?></span>
                        </div>
                        <div class="concours-info-item">
                            <span class="concours-info-label">Frais d'inscription:</span>
                            <span class="concours-info-value"><?php echo number_format($concours_selected['prix'], 0, ',', ' '); ?> FCFA</span>
                        </div>
                    </div>

                    <!-- Section: Informations Personnelles -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="bi bi-person-fill"></i> Informations Personnelles
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="prenom">Prénom <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="form-group">
                                <label for="nom">Nom <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email <span class="required-mark">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telephone">Téléphone <span class="required-mark">*</span></label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="+237..." required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="date_naissance">Date de Naissance <span class="required-mark">*</span></label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                            </div>
                            <div class="form-group">
                                <label for="lieu_naissance">Lieu de Naissance <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Informations Académiques -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="bi bi-book-fill"></i> Informations Académiques
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="region">Région <span class="required-mark">*</span></label>
                                <select class="form-select" id="region" name="region" required>
                                    <option value="">-- Sélectionner une région --</option>
                                    <?php foreach ($concours_selected['regions'] as $region): ?>
                                        <option value="<?php echo htmlspecialchars($region); ?>"><?php echo htmlspecialchars($region); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filiere">Filière <span class="required-mark">*</span></label>
                                <select class="form-select" id="filiere" name="filiere" required>
                                    <option value="">-- Sélectionner une filière --</option>
                                    <?php foreach ($concours_selected['filieres'] as $filiere): ?>
                                        <option value="<?php echo htmlspecialchars($filiere); ?>"><?php echo htmlspecialchars($filiere); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Documents -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="bi bi-file-earmark-pdf"></i> Documents Requis
                        </h3>

                        <div class="checklist">
                            <div class="checklist-item">
                                <div class="checklist-icon">1</div>
                                <span>BAC ou Diplôme équivalent</span>
                            </div>
                            <div class="checklist-item">
                                <div class="checklist-icon">2</div>
                                <span>CIN ou Passeport</span>
                            </div>
                            <div class="checklist-item">
                                <div class="checklist-icon">3</div>
                                <span>Certificat de naissance ou Extrait d'acte</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>BAC/Diplôme <span class="required-mark">*</span></label>
                            <div class="document-upload" onclick="document.getElementById('document_bac').click();">
                                <div class="document-upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                <div class="document-upload-text">Glissez-déposez ou cliquez ici</div>
                                <div class="document-upload-hint">PDF, JPEG ou PNG (Max 5MB)</div>
                            </div>
                            <input type="file" class="file-input" id="document_bac" name="document_bac" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="file-name" id="file-bac"></div>
                        </div>

                        <div class="form-group">
                            <label>CIN/Passeport <span class="required-mark">*</span></label>
                            <div class="document-upload" onclick="document.getElementById('document_cin').click();">
                                <div class="document-upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                <div class="document-upload-text">Glissez-déposez ou cliquez ici</div>
                                <div class="document-upload-hint">PDF, JPEG ou PNG (Max 5MB)</div>
                            </div>
                            <input type="file" class="file-input" id="document_cin" name="document_cin" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="file-name" id="file-cin"></div>
                        </div>

                        <div class="form-group">
                            <label>Certificat/Extrait d'acte <span class="required-mark">*</span></label>
                            <div class="document-upload" onclick="document.getElementById('document_certificat').click();">
                                <div class="document-upload-icon"><i class="bi bi-cloud-upload"></i></div>
                                <div class="document-upload-text">Glissez-déposez ou cliquez ici</div>
                                <div class="document-upload-hint">PDF, JPEG ou PNG (Max 5MB)</div>
                            </div>
                            <input type="file" class="file-input" id="document_certificat" name="document_certificat" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="file-name" id="file-certificat"></div>
                        </div>
                    </div>

                    <!-- Section: Paiement -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="bi bi-credit-card"></i> Méthode de Paiement
                        </h3>

                        <p style="color: #7f8c8d; margin-bottom: 1.5rem;">
                            Veuillez sélectionner votre méthode de paiement préférée. Le montant à payer est: <strong><?php echo number_format($concours_selected['prix'], 0, ',', ' '); ?> FCFA</strong>
                        </p>

                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="methode_paiement" value="mobile_money" required>
                                <div class="payment-label">
                                    <div class="payment-icon">📱</div>
                                    <div class="payment-name">Mobile Money</div>
                                    <small style="color: #7f8c8d;">MTN, Orange, Camtel</small>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="methode_paiement" value="virement_bancaire" required>
                                <div class="payment-label">
                                    <div class="payment-icon">🏦</div>
                                    <div class="payment-name">Virement Bancaire</div>
                                    <small style="color: #7f8c8d;">Vers nos comptes</small>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="methode_paiement" value="carte_bancaire" required>
                                <div class="payment-label">
                                    <div class="payment-icon">💳</div>
                                    <div class="payment-name">Carte Bancaire</div>
                                    <small style="color: #7f8c8d;">Visa, Mastercard</small>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="methode_paiement" value="portefeuille_electronique" required>
                                <div class="payment-label">
                                    <div class="payment-icon">💼</div>
                                    <div class="payment-name">Portefeuille Électronique</div>
                                    <small style="color: #7f8c8d;">PayPal, Stripe</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="button-group">
                        <a href="index.php" class="btn btn-back">Retour</a>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-check-circle"></i> Soumettre l'Inscription
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion des uploads de fichiers
        document.querySelectorAll('.file-input').forEach(input => {
            const docType = input.id.split('_')[1];
            const parent = input.parentElement;

            // Afficher le nom du fichier
            input.addEventListener('change', function() {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    document.getElementById('file-' + docType).textContent = '✓ ' + fileName + ' (sélectionné)';
                }
            });

            // Drag and drop
            parent.addEventListener('dragover', (e) => {
                e.preventDefault();
                parent.classList.add('drag-over');
            });

            parent.addEventListener('dragleave', () => {
                parent.classList.remove('drag-over');
            });

            parent.addEventListener('drop', (e) => {
                e.preventDefault();
                parent.classList.remove('drag-over');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    const fileName = e.dataTransfer.files[0].name;
                    document.getElementById('file-' + docType).textContent = '✓ ' + fileName + ' (sélectionné)';
                }
            });
        });

        // Validation du formulaire avant soumission
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const requiredFiles = ['document_bac', 'document_cin', 'document_certificat'];
            let allFilesSelected = true;

            requiredFiles.forEach(fileId => {
                const input = document.getElementById(fileId);
                if (!input || !input.files || input.files.length === 0) {
                    allFilesSelected = false;
                    const parent = input?.parentElement;
                    if (parent) {
                        parent.style.borderColor = '#e74c3c';
                    }
                }
            });

            if (!allFilesSelected) {
                e.preventDefault();
                alert('Veuillez sélectionner tous les documents requis');
            }
        });
    </script>
</body>
</html>
