<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require '../config/config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des RDV</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js'></script>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        #calendar { max-width: 900px; margin: auto; }
        .alert { color: red; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Bienvenue <?php echo $_SESSION['user_name']; ?> </h2>
    <a href="logout.php">Déconnexion</a>
    
    <!-- Message de succès ou d'erreur -->
    <div id="message" class="alert" style="display:none;"></div>
    <a href="mesrendezvous.php" class="btn btn-secondary">Mes Rendez-vous</a>
    <a href="edit_info.php" class="btn btn-secondary">Mon Compte</a>
    <div id='calendar'></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'timeGridWeek',
                allDaySlot: false,
                slotMinTime: '09:00:00',
                slotMaxTime: '18:00:00',
                slotDuration: '01:00:00',
                nowIndicator: true,
                events: '/api/load_event.php',
                selectable: true,
                select: function(info) {
                    let startHour = info.start.getHours();
                    let endHour = info.end.getHours();

                    // Désactiver la sélection entre 12h et 14h
                    if (startHour >= 12 && startHour < 14) {
                        showMessage("Impossible de réserver entre 12h et 14h.");
                        return;
                    }

                    let title = prompt("Entrez un titre pour le RDV :");
                    if (title) {
                        $.ajax({
                            url: '/api/add_event.php',
                            type: 'POST',
                            data: {
                                title: title,
                                start: info.startStr,
                                end: info.endStr
                            },
                            success: function(response) {
                                if (response.success) {
                                    calendar.refetchEvents();
                                    showMessage("Rendez-vous ajouté avec succès.", "success");
                                } else {
                                    showMessage(response.error);
                                }
                            },
                            error: function() {
                                showMessage("Une erreur s'est produite lors de l'ajout du rendez-vous.");
                            }
                        });
                    }
                }
            });
            calendar.render();
        });

        function showMessage(message, type = 'error') {
            var messageDiv = $('#message');
            messageDiv.removeClass('success').removeClass('error');
            messageDiv.addClass(type).text(message).show();
        }
    </script>
</body>
</html>
