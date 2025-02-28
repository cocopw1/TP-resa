<?php
require '../config/config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, title, start FROM rdv");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Erreur lors du chargement des événements', 'details' => $e->getMessage()]);
}
?>
