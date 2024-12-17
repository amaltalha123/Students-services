<?php

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "services";

$conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Admin - Gestion des Réclamations</title>
    <link rel="stylesheet" href="Reclamation.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>


<body>
    <nav class="navbar">
        <div class="menu-container">
            <div class="menu-icon" onclick="toggleMenu()">
                <i class="fa fa-bars menu-i" id="icone"></i>
            </div>
            <div class="menu" onmouseleave="closeMenu(event)">
                <ul>
                    <li><a href="admindashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="admin.php"><i class="fa fa-exclamation-circle"></i> Liste des Demandes</a></li>
                    <li><a href="historique.php"><i class="fa fa-history"></i> Historique</a></li>
                    <li><a href="Reclamation.php"><i class="fa fa-exclamation-circle"></i> Reclamations</a></li>
                    
                </ul>
            </div>
        </div>
        <div class="navbar-items">
            <div class="profile">
                <?php
                session_start();
                if (isset($_SESSION['admin_name'])) {
                    echo "
                        <div class='profile-container'>
                            <i class='fas fa-user-circle profile-icon'></i>
                            <span class='admin-name'>" . htmlspecialchars($_SESSION['admin_name']) . "</span>
                        </div>";
                }
                ?>
            </div>

            <button id="modeToggle">
                <i id="modeIcon" class="fa"></i>
            </button>
            <i class="fa fa-sign-out-alt logout-icon" title="Déconnexion"></i>
            <div id="logoutCard" style="display: none;">
                <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                <div class="btn">
                    <button id="confirmLogout">Oui</button>
                    <button id="cancelLogout">Non</button>
                </div>
            </div>
        </div>
    </nav>


    <main>
        <div class="main">
            <div class="main-container">

                <h1>Liste des Réclamations</h1>
                <table id="reclamationTable">
                    <thead>
                        <tr>
                            <th>Nom Étudiant</th>
                            <th>Prénom Étudiant</th>
                            <th>Apogée</th>
                            <th>Contenu</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="reclamationBody">
                        <?php

                        $conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);

                        // Récupération des réclamations
                        $sql = "SELECT 
                                r.id_reclamation, 
                                e.nom, 
                                e.prenom,
                                e.email, 
                                e.numero_apogee, 
                                r.contenu_reclamation, 
                                r.date_reclamation 
                            FROM reclamations r
                            JOIN etudiants e ON r.id_etudiant = e.id_etudiant
                            WHERE r.etat_reclamation = 'En cours'";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr id='row_{$row['id_reclamation']}'>
                                <td>" . htmlspecialchars($row['nom']) . "</td>
                                <td>" . htmlspecialchars($row['prenom']) . "</td>
                                <td>" . htmlspecialchars($row['numero_apogee']) . "</td>
                                <td>" . htmlspecialchars($row['contenu_reclamation']) . "</td>
                                <td>" . date('d/m/Y H:i', strtotime($row['date_reclamation'])) . "</td>
                                <td>
                                    <form method='POST' class='traiterForm'>
                                        <input type='hidden' name='id_reclamation' value='" . intval($row['id_reclamation']) . "'>
                                        <button  class='btn btn-outline-success btn-sm' type='submit'>Traiter</button>
                                    </form>
                                </td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Aucune réclamation en cours</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <script>
            // Traitement de la réclamation sans redirection
            const forms = document.querySelectorAll('.traiterForm');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const idReclamation = this.querySelector('input[name="id_reclamation"]').value;

                    fetch('traiter_reclamation.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'id_reclamation=' + idReclamation,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Supprime la ligne correspondante de la table sans recharger la page
                                const row = document.getElementById('row_' + idReclamation);
                                if (row) {
                                    row.remove();
                                }

                                // Si toutes les réclamations sont traitées, afficher le message "Aucune réclamation en cours"
                                const tableBody = document.getElementById('reclamationBody');
                                if (tableBody.rows.length === 0) {
                                    tableBody.innerHTML = "<tr><td colspan='6'>Aucune réclamation en cours</td></tr>";
                                }
                            } else {
                                alert("Erreur : " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Erreur :", error);
                            alert("Une erreur s'est produite. Veuillez réessayer.");
                        });
                });
            });
        </script>
    </main>

    <footer>
        <p>&copy; 2024 Administration des Réclamations</p>
    </footer>

    <script>
        // Gestion de la déconnexion avec confirmation
        const logoutIcon = document.querySelector('.logout-icon');
        const logoutCard = document.getElementById('logoutCard');
        const confirmLogoutBtn = document.getElementById('confirmLogout');
        const cancelLogoutBtn = document.getElementById('cancelLogout');

        logoutIcon.addEventListener('click', function() {
            logoutCard.style.display = 'block';
        });

        cancelLogoutBtn.addEventListener('click', function() {
            logoutCard.style.display = 'none';
        });

        confirmLogoutBtn.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        // Bloquer le retour en arrière après déconnexion
        if (window.location.pathname === 'Students-sevices/accueill.php') {
            history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                alert("Retour arrière bloqué !");
                history.pushState(null, null, window.location.href);
            };
        }
    </script>


    <script src="Reclamation.js"></script>
</body>

</html>