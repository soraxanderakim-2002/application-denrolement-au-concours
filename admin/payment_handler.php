<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Vérifier l'authentification
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentification requise']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'update_payment') {
    $payment_id = $_POST['payment_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $admin_id = $_SESSION['admin_id'];

    if (empty($payment_id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    }

    // Statuts valides
    $valid_statuses = ['en attente', 'validé', 'rejeté'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Statut invalide']);
        exit;
    }

    try {
        // Vérifier si la BDD est connectée
        if (DB::isConnected()) {
            // Mettre à jour le paiement
            $query = "UPDATE paiements SET statut = ?, date_maj = NOW() WHERE id = ?";
            $stmt = DB::prepare($query);
            $stmt->execute([$status, $payment_id]);

            // Enregistrer l'action dans les logs
            logAction($admin_id, 'UPDATE_PAYMENT', "Paiement #$payment_id - Statut: $status - Notes: $notes");

            // Si approuvé, mettre à jour le candidat
            if ($status === 'validé') {
                $query_candidate = "UPDATE candidats SET statut = 'inscription validée' 
                                  WHERE id = (SELECT id_candidat FROM paiements WHERE id = ?)";
                $stmt_candidate = DB::prepare($query_candidate);
                $stmt_candidate->execute([$payment_id]);
            }
        } else {
            // Mode simulation - afficher un message de succès
            // En production, vous devriez implémenter une vraie BDD
        }

        echo json_encode([
            'success' => true,
            'message' => 'Paiement mis à jour avec succès',
            'status' => $status
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
} 
elseif ($action === 'get_payment_details') {
    $payment_id = $_GET['id'] ?? '';

    if (empty($payment_id)) {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        exit;
    }

    try {
        if (DB::isConnected()) {
            $query = "SELECT p.*, c.prenom, c.nom, c.email, c.telephone, 
                      CONCAT(c.prenom, ' ', c.nom) as candidat_complet
                      FROM paiements p
                      JOIN candidats c ON p.id_candidat = c.id
                      WHERE p.id = ?";
            $stmt = DB::prepare($query);
            $stmt->execute([$payment_id]);
            $payment = $stmt->fetch();
        } else {
            // Mode simulation
            $payment = null;
        }

        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Paiement non trouvé']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $payment['id'],
                'reference' => $payment['reference'],
                'candidat' => $payment['candidat_complet'],
                'email' => $payment['email'],
                'montant' => $payment['montant'],
                'methode' => $payment['methode'],
                'statut' => $payment['statut'],
                'date_paiement' => $payment['date_paiement'],
                'telephone' => $payment['telephone']
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Action inconnue']);
}
