<?php
require '../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage des entrées utilisateur
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email) {
        echo "Email invalide.";
        exit;
    }

    // Récupération de l'utilisateur avec une requête préparée
    $stmt = $pdo->prepare("SELECT id, mot_de_passe FROM utilisateurs WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        echo "Connexion réussie !";
    } else {
        echo "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="POST">
    Email: <input type="email" name="email" required><br>
    Mot de passe: <input type="password" name="password" required><br>
    <button type="submit">Se connecter</button>
</form>

    
<a href="inscription.php">inscription</a>
</body>
</html>