<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}
?>

Bienvenue, utilisateur ! <a href="logout.php">Déconnexion</a>
