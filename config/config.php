<?php
$host = "127.0.0.1";
$dbname = "php_is_deep_shit";
$username = "root"; // Remplace par ton utilisateur MariaDB
$password = "toor"; // Remplace par ton mot de passe si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>