<?php
session_start();
// Connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "services";
$conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Récupération des statistiques
$sql_total_etudiants = "SELECT COUNT(*) AS total_etudiants FROM etudiants";
$result_total_etudiants = $conn->query($sql_total_etudiants);
$total_etudiants = $result_total_etudiants->fetch_assoc()['total_etudiants'];

$sql_total_info = "SELECT COUNT(*) AS total_info FROM etudiants WHERE filiere='Genie informatique'";
$result_total_info = $conn->query($sql_total_info);
$total_etudiants_info = $result_total_info->fetch_assoc()['total_info'];

$sql_total_gscm = "SELECT COUNT(*) AS total_gscm FROM etudiants WHERE filiere='GSCM'";
$result_total_gscm = $conn->query($sql_total_gscm);
$total_etudiants_gscm = $result_total_gscm->fetch_assoc()['total_gscm'];

$sql_total_2ap = "SELECT COUNT(*) AS total_2ap FROM etudiants WHERE filiere='2AP1' OR filiere='2AP2'";
$result_total_2ap = $conn->query($sql_total_2ap);
$total_etudiants_2ap = $result_total_2ap->fetch_assoc()['total_2ap'];



$sql_total_demandes = "SELECT COUNT(*) AS total_demandes FROM demandes";
$result_total_demandes = $conn->query($sql_total_demandes);
$total_demandes = $result_total_demandes->fetch_assoc()['total_demandes'];

$sql_demandes_traitees = "SELECT COUNT(*) AS demandes_traitees FROM demandes WHERE etat_demande = 'Validée'";
$result_demandes_traitees = $conn->query($sql_demandes_traitees);
$demandes_traitees = $result_demandes_traitees->fetch_assoc()['demandes_traitees'];

$sql_demandes_en_cours = "SELECT COUNT(*) AS demandes_en_cours FROM demandes WHERE etat_demande = 'En cours'";
$result_demandes_en_cours = $conn->query($sql_demandes_en_cours);
$demandes_en_cours = $result_demandes_en_cours->fetch_assoc()['demandes_en_cours'];

$sql_total_reclamations = "SELECT COUNT(*) AS total_reclamations FROM reclamations";
$result_total_reclamations = $conn->query($sql_total_reclamations);
$total_reclamations = $result_total_reclamations->fetch_assoc()['total_reclamations'];

$sql_reclamations_traitees = "SELECT COUNT(*) AS reclamations_traitees FROM reclamations WHERE etat_reclamation = 'Traitée'";
$result_reclamations_traitees = $conn->query($sql_reclamations_traitees);
$reclamations_traitees = $result_reclamations_traitees->fetch_assoc()['reclamations_traitees'];

// Calcul des pourcentages
$pourcentage_demandes_traitees = $total_demandes > 0 ? round(($demandes_traitees / $total_demandes) * 100, 2) : 0;
$pourcentage_demandes_en_cours = $total_demandes > 0 ? round(($demandes_en_cours / $total_demandes) * 100, 2) : 0;
$pourcentage_reclamations_traitees = $total_reclamations > 0 ? round(($reclamations_traitees / $total_reclamations) * 100, 2) : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admindashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
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
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
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
            </header>
<main>
    <div class="container">
        <div class="content">
            <div id="dashboard" class="section active">
                <div class="dashboard">
                    <h1>Tableau de Bord</h1>
                    <div class="stats">
                        <div class="stat">
                            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: #FF69B4;"></i>
                            <h2><?= $total_etudiants ?></h2>
                            <p>Total Étudiants</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: #1e9aff;"></i>
                            <h2><?= $total_etudiants_info ?></h2>
                            <p>Total des étudiant en Génie informatique</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: #FF69B4;"></i>
                            <h2><?= $total_etudiants_gscm ?></h2>
                            <p>Total des étudiants en GSCM</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-file-alt" style="font-size: 2.5rem; color: #1e9aff;"></i>
                            <h2><?= $total_demandes ?></h2>
                            <p>Total Demandes</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: #1e9aff;"></i>
                            <h2><?= $total_etudiants_2ap ?></h2>
                            <p>Total des étudiant en années préparatoire</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #28a745;"></i>
                            <h2><?= $demandes_traitees ?></h2>
                            <p>Demandes Traitées</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-clock" style="font-size: 2.5rem; color: #ffc107;"></i>
                            <h2><?= $demandes_en_cours ?></h2>
                             <p>Demandes en Cours</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-exclamation-circle" style="font-size: 2.5rem; color: #dc3545;"></i>
                            <h2><?= $total_reclamations ?></h2>
                            <p>Total Réclamations</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-percentage" style="font-size: 2.5rem; color: #FF69B4;"></i>
                            <h2><?= $pourcentage_demandes_traitees ?>%</h2>
                            <p>Pourcentage Demandes Traitées</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-tasks" style="font-size: 2.5rem; color: #17a2b8;"></i>
                            <h2><?= $pourcentage_demandes_en_cours ?>%</h2>
                            <p>Pourcentage Demandes en Cours</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-thumbs-up" style="font-size: 2.5rem; color: #28a745;"></i>
                            <h2><?= $pourcentage_reclamations_traitees ?>%</h2>
                            <p>Pourcentage Réclamations Traitées</p>
                        </div>               
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
   
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
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }
    </script>
    <script src="dashboard.js"></script> 
</body>
<footer>
        <p>&copy; 2024 Administration du tableau de bord</p>
    </footer>
</html>
