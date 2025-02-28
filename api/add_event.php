<?php
require '../config/config.php';
session_start();

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Vérifier si les données sont envoyées
if (!isset($_POST['title']) || !isset($_POST['start']) || !isset($_POST['end'])) {
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$title = trim($_POST['title']);
$start = $_POST['start'];
$end = $_POST['end'];
$user_id = $_SESSION['user_id'];

// Vérifier que le créneau est valide (pas entre 12h et 14h)
$start_hour = (int) date('H', strtotime($start));
if ($start_hour >= 12 && $start_hour < 14) {
    echo json_encode(['error' => 'Impossible de réserver entre 12h et 14h']);
    exit;
}

// Insérer l'événement en base de données
$stmt = $pdo->prepare("INSERT INTO rdv (title, start, end, user_id) VALUES (:title, :start, :end, :user_id)");
$stmt->bindParam(':title', $title, PDO::PARAM_STR);
$stmt->bindParam(':start', $start, PDO::PARAM_STR);
$stmt->bindParam(':end', $end, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Erreur lors de l\'ajout du rendez-vous']);
}
?>
