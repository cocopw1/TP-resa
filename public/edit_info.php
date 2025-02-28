<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_info'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $date_naissance = $_POST['date_naissance'];
        $adresse = trim($_POST['adresse']);
        $telephone = trim($_POST['telephone']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            $error = "Email invalide.";
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, date_naissance = ?, adresse = ?, telephone = ?, email = ? WHERE id = ?");
            if ($stmt->execute([$nom, $prenom, $date_naissance, $adresse, $telephone, $email, $user_id])) {
                $success = "Informations mises à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }
    } elseif (isset($_POST['delete_account'])) {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            session_destroy();
            header("Location: inscription.php");
            exit;
        } else {
            $error = "Erreur lors de la suppression du compte.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mes informations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h2 class="text-center mb-4">Modifier mes informations</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($user['date_naissance']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($user['adresse']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($user['telephone']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" name="update_info" class="btn btn-primary w-100">Mettre à jour</button>
        </form>

        <hr>

        <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
            <button type="submit" name="delete_account" class="btn btn-danger w-100">Supprimer mon compte</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php">Retour au calendrier</a>
        </div>
    </div>
</body>
</html>
