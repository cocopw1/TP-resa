<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require '../config/config.php';

$user_id = $_SESSION['user_id'];

// Récupérer les rendez-vous de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT id, title, start, end FROM rdv WHERE user_id = :user_id ORDER BY start ASC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-vous</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Mes Rendez-vous</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Retour au calendrier</a>
        <a href="logout.php" class="btn btn-danger mb-3">Déconnexion</a>

        <?php if (count($rdvs) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date et heure de début</th>
                        <th>Date et heure de fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs as $rdv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rdv['title']); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($rdv['start'])); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($rdv['end'])); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-event" data-id="<?php echo $rdv['id']; ?>">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun rendez-vous trouvé.</p>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        $(".delete-event").click(function() {
            let eventId = $(this).data("id");
            if (confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ?")) {
                $.ajax({
                    url: "/api/delete_event.php",
                    type: "POST",
                    data: { id: eventId },
                    success: function(response) {
                        let result = JSON.parse(response);
                        if (result.success) {
                            alert("Rendez-vous supprimé !");
                            location.reload();
                        } else {
                            alert("Erreur : " + result.error);
                        }
                    },
                    error: function() {
                        alert("Une erreur s'est produite.");
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
