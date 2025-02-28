<?php
require '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $date_naissance = trim($_POST['date_naissance']);
    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email) {
        echo "<div class='alert alert-danger'>Email invalide.</div>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email UNION SELECT id FROM utilisateurs_temp WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
        exit;
    }

    $mot_de_passe_hash = password_hash($password, PASSWORD_DEFAULT);
    $code_validation = random_int(100000, 999999);

    $stmt = $pdo->prepare("INSERT INTO utilisateurs_temp (nom, prenom, date_naissance, adresse, telephone, email, mot_de_passe, code_validation) 
                           VALUES (:nom, :prenom, :date_naissance, :adresse, :telephone, :email, :mot_de_passe, :code_validation)");
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $stmt->bindParam(':date_naissance', $date_naissance, PDO::PARAM_STR);
    $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash, PDO::PARAM_STR);
    $stmt->bindParam(':code_validation', $code_validation, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '5eea6958f50ffa';
            $mail->Password = '7fce1160f6a26b';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('test@cocopw.fr', 'cocopw.fr');
            $mail->addAddress($email);
            $mail->Subject = 'Confirmez votre inscription';
            $mail->Body = "Bonjour $prenom,\n\nVotre code de validation est : $code_validation\nOu cliquez sur ce lien : http://localhost/verification.php?email=$email&code=$code_validation";
            $mail->send();
            header("Location: verification.php?email=$email");
            exit;
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'inscription.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h2 class="text-center mb-4">Inscription</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse postale</label>
                <input type="text" name="adresse" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Numéro de téléphone</label>
                <input type="tel" name="telephone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>
        <div class="text-center mt-3">
            <a href="connexion.php">Déjà un compte ? Se connecter</a>
        </div>
    </div>
</body>
</html>
