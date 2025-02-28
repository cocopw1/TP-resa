<?php
session_start();
require '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
        exit;
    }

    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID du rendez-vous manquant']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $event_id = intval($_POST['id']);

    // Vérifier si l'événement appartient bien à l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT id FROM rdv WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $event_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => "Rendez-vous introuvable ou accès refusé"]);
        exit;
    }

    // Supprimer l'événement
    $stmt = $pdo->prepare("DELETE FROM rdv WHERE id = :id");
    $stmt->bindParam(':id', $event_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Rendez-vous supprimé"]);
    } else {
        echo json_encode(['success' => false, 'error' => "Erreur lors de la suppression"]);
    }
} else {
    echo json_encode(['success' => false, 'error' => "Requête invalide"]);
}
?>
