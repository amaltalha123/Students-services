<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "services";
$port = 3306;

$conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Admin - Gestion des Réclamations</title>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="historique.css">
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
                    <li><a href="Reclamation.php"><i class="fa fa-cog"></i> Reclamations</a></li>
                    <li><a href="historique.php"><i class="fa fa-history"></i> Historique</a></li>
                    
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
    <h1>Historique des Demandes</h1>

<form action="" method="GET">
    <div class="container">
  
        <!-- Section des Filtres -->
        <div class="filter-section">
            <div class="card shadow mt-3">
                <div class="card-body">
                    
                    
                    <!-- Filtres : Type de document -->
                    <h6>Type de document</h6>
                    <hr>
                    <?php
                        $default_documents = [
                            "attestation de scolarité",
                            "relevé de notes",
                            "convention de stage",
                            "attestation de réussite"
                        ];
                        
                        foreach ($default_documents as $doc) {
                            $checked_doc = isset($_GET['document_types']) && in_array($doc, $_GET['document_types']) ? "checked" : "";
                            echo "
                                <div>
                                    <input type='checkbox' name='document_types[]' value='$doc' id='doc_$doc' $checked_doc />
                                    <label for='doc_$doc'>$doc</label>
                                </div>
                            ";
                        }
                    ?>

                    <!-- Filtres : État de la demande -->
                    <h6 class="mt-4">État de la demande</h6>
                    <hr>
                    <?php
                        $default_states = ["validée", "refusée"];
                        
                        foreach ($default_states as $state) {
                            $checked_state = isset($_GET['states']) && in_array($state, $_GET['states']) ? "checked" : "";
                            echo "
                                <div>
                                    <input type='checkbox' name='states[]' value='$state' id='state_$state' $checked_state />
                                    <label for='state_$state'>$state</label>
                                </div>
                            ";
                        }
                    ?>
                    <div class="card-header">
                        <button type="submit" class="btn btn-primary btn-sm float-end">Filtrer</button>
                      

                    </div>
                    <div class="card-header">
                      
                        <a href="historique.php" class="reset-button">Rénitialiser</a>

                    </div>
                  

                </div>
            </div>
        </div>

        <!-- Section Historique des Demandes -->
        <div class="history-section">
            <?php
            // Construire la requête SQL avec les filtres
            $where_clauses = [];

            if (isset($_GET['document_types']) && !empty($_GET['document_types'])) {
                $doc_types = $_GET['document_types'];
                $doc_filter = "d.type_document IN ('" . implode("', '", $doc_types) . "')";
                $where_clauses[] = $doc_filter;
            }

            if (isset($_GET['states']) && !empty($_GET['states'])) {
                $states = $_GET['states'];
                $state_filter = "d.etat_demande IN ('" . implode("', '", $states) . "')";
                $where_clauses[] = $state_filter;
            }
         

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
            $sql = "SELECT d.id_demande, e.nom, d.type_document, d.etat_demande, d.date_demande
                    FROM demandes d
                    INNER JOIN etudiants e ON d.id_etudiant = e.id_etudiant
                    $where_sql";

            $results = $conn->query($sql);

            if ($results && $results->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Nom</th>
                        <th>Type de Document</th>
                        <th>Date de Demande</th>
                        <th>État de la Demande</th>
                      </tr>";
                while ($result = $results->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $result["nom"] . "</td>";
                    echo "<td>" . $result["type_document"] . "</td>";
                    echo "<td>" . $result["date_demande"] . "</td>";
                    echo "<td>" . $result["etat_demande"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Aucune demande trouvée avec les critères sélectionnés.";
            }
            ?>
        </div>
    </div>
</form>

</main>



    <footer>
        <p>&copy; 2024 Administration de l'historique</p>
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
    </script>

    <script>
        // Fonction pour afficher/masquer le menu déroulant
function toggleDropdown(menuId) {
    var menu = document.getElementById(menuId);
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

// Cacher les menus après avoir appliqué les filtres
document.querySelector('.apply-filter-btn').addEventListener('click', function() {
    var dropdowns = document.querySelectorAll('.dropdown-content');
    dropdowns.forEach(function(dropdown) {
        dropdown.style.display = 'none';
    });
});

    </script>

    <script src="historique.js"></script>
</body>

</html>

