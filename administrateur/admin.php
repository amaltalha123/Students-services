<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des demandes</title>
    <link rel="stylesheet" href="demande.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background-color: #fff; /* Couleur principale pour l'arrière-plan */
        color: #333; /* Couleur du texte principal */
    }

    h1 {
        text-align: center;
        font-weight: 600;
        color:  #C69F9A;
        padding: 15px 0;
        margin: 0;
    }

    table {
        margin: 50px auto;
        border-collapse: collapse;
        width: 80%;
        max-width: 1000px;
        border: 1px solid #D5BDAF; /* Bordure avec une teinte douce */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #FFFFFF; /* Fond du tableau */
    }

    th {
        background-color: #C17C74; /* Couleur des en-têtes */
        color: #FFF;
        font-weight: bold;
        padding: 15px;
        text-align: center;
        border-bottom: 2px solid #EBE8D0; /* Lien avec le fond */
    }

    td {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #EBD8D0;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #F5EBE0; /* Lignes alternées */
    }

    tr:hover {
        background-color: #D5BDAF; /* Survol */
        transform: scale(1.01);
        transition: all 0.2s ease-in-out;
    }

    a {
        text-decoration: none;
        color: #C17C74; /* Couleur des liens */
        font-weight: bold;
        margin: 0 5px;
    }

    a:hover {
        color: #C69F9A; /* Lien survolé */
        text-decoration: underline;
    }

    @media only screen and (max-width: 768px) {
        table {
            width: 95%;
        }

        th, td {
            padding: 10px;
            font-size: 14px;
        }

        h1 {
            font-size: 20px;
        }
        
    }
</style>

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
    <h1>Liste des demandes</h1>
    <br>
    <table border="1">
        <thead>
            <tr>
                
                <th>Nom</th>
                <th>Prénom</th>
                <th>Numéro Apogée</th>
                <th>Filière</th>
                <th>dernier niveau Validée</th>
                <th>Email</th>
                <th>Type Document</th>
                <th>Actions</th>
            </tr>
        </thead>
       
        <tbody>
            <?php
            $servername = "localhost";
            $username_db = "root";
            $password_db = "";
            $dbname = "services";

            // Connexion à la base de données
            $conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);

            if ($conn->connect_error) {
                die("<p>Erreur de connexion à la base de données : " . $conn->connect_error . "</p>");
            }

            $sql = "SELECT 
            e.nom AS demande_nom, 
            e.prenom AS demande_prenom,
            e.filiere AS filiere,
            e.niveau_validé AS niveau_valide, 
            d.niveau_demande as niveau,
            e.numero_apogee AS demande_numero_apogee,
            e.email AS demande_email, 
            d.type_document AS demande_type_document,
            d.id_demande as id_demande, 
            d.id_etudiant AS id_etudiant
        FROM etudiants e
        JOIN demandes d ON d.id_etudiant = e.id_etudiant
        WHERE d.etat_demande = 'En cours'";

// Exécuter la requête
            $resultat = $conn->query($sql);
           

            // Vérification des résultats
            if ($resultat && $resultat->num_rows > 0) {
    while ($row = $resultat->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row["demande_nom"]) . "</td>
            <td>" . htmlspecialchars($row["demande_prenom"]) . "</td>
            <td>" . htmlspecialchars($row["demande_numero_apogee"]) . "</td>
            <td>" . htmlspecialchars($row["filiere"]) . "</td>
            <td>" . htmlspecialchars($row["niveau_valide"]) . "</td>
            <td>" . htmlspecialchars($row["demande_email"]) . "</td>
            <td>" . htmlspecialchars($row["demande_type_document"]) . " " . htmlspecialchars($row["niveau"]) . "</td> <!-- Concatenation du type de document et du semestre -->
            <td>
                <a class='btn btn-outline-success btn-sm' href='accepter.php?id_demande=" . $row["id_demande"] . "'>Accepter</a>
                <a class='btn btn-outline-danger btn-sm' href='refuser.php?id_demande=" . $row["id_demande"] . "'>Refuser</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Aucune demande trouvée</td></tr>";
}

            // Fermeture de la connexion
            $conn->close();
            ?>
        </tbody>
    </table>
    <footer>
        <p>&copy; 2024 Administration des Demandes</p>
    </footer>

    <script>
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

        if (window.location.pathname === '/chemin/vers/accueill.php') {
            history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                alert("Retour arrière bloqué !");
                history.pushState(null, null, window.location.href);
            };
        }
    </script>


    <script src="demande.js"></script>
</body>
</html>


