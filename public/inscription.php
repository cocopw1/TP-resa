<?php
require '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email) {
        echo "Email invalide.";
        exit;
    }

    // Vérifier si l'email existe déjà (dans utilisateurs et utilisateurs_temp)
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email UNION SELECT id FROM utilisateurs_temp WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo "Cet email est déjà utilisé.";
        exit;
    }

    // Hachage sécurisé du mot de passe
    $mot_de_passe_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Générer un code de validation à 6 chiffres
    $code_validation = random_int(100000, 999999);

    // Insérer dans la table temporaire
    $stmt = $pdo->prepare("INSERT INTO utilisateurs_temp (nom, email, mot_de_passe, code_validation) VALUES (:nom, :email, :mot_de_passe, :code_validation)");
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash, PDO::PARAM_STR);
    $stmt->bindParam(':code_validation', $code_validation, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Envoyer l'email de confirmation
        $to = $email;
        $subject = "Confirmez votre inscription";
        $message = "Bonjour $nom,\n\nVotre code de validation est : $code_validation\nOu cliquez sur le lien suivant pour confirmer : http://localhost/verifier.php?email=$email&code=$code_validation";
        $headers = "From: noreply@mon-site.com";

        mail($to, $subject, $message, $headers);

        echo "Un email de confirmation a été envoyé. Vérifiez votre boîte mail.";
    } else {
        echo "Erreur lors de l'inscription.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <form method="POST">
        Nom: <input type="text" name="nom" required><br>
        Email: <input type="email" name="email" required><br>
        Mot de passe: <input type="password" name="password" required><br>
        <button type="submit">S'inscrire</button>
    </form>
    <a href="connexion.php">Déjà un compte ? Se connecter</a>
</body>
</html>
