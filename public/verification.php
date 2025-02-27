<?php
require '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $code_saisi = trim($_POST['code']);

    if (!$email || !$code_saisi) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    // Vérifier si l'email et le code existent dans la table temporaire
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs_temp WHERE email = :email AND code_validation = :code");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':code', $code_saisi, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Insérer l'utilisateur validé dans la table principale
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
        $stmt->bindParam(':nom', $user['nom'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $user['email'], PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $user['mot_de_passe'], PDO::PARAM_STR);
        $stmt->execute();

        // Supprimer l'entrée de la table temporaire
        $stmt = $pdo->prepare("DELETE FROM utilisateurs_temp WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        echo "Inscription confirmée ! Vous pouvez maintenant vous connecter.";
    } else {
        echo "Code incorrect ou email non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification</title>
</head>
<body>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Code de validation: <input type="text" name="code" required><br>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
