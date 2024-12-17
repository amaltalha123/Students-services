<?php
session_start();

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "services";

$conn = new mysqli($servername, $username_db, $password_db, $dbname, 3306);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
// Vérifiez si les données ont été envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
//$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';


$name = isset($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : '';
$prenom = isset($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : '';
$email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
$numero_apogee = isset($_POST['numero_apogee']) ? htmlspecialchars($_POST['numero_apogee']) : '';
$reclamation = isset($_POST['reclamation']) ? htmlspecialchars($_POST['reclamation']) : '';


// Validation simple des données
if (empty($name) || empty($email) || empty($prenom) || empty($numero_apogee) || empty($reclamation)) {
    echo "Tous les champs sont requis.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "L'adresse e-mail n'est pas valide.";
} else {

    $stmt = $conn->prepare("SELECT id_etudiant FROM etudiants 
                        WHERE LOWER(numero_apogee) = LOWER(?) 
                          AND LOWER(nom) = LOWER(?) 
                          AND LOWER(prenom) = LOWER(?)");
$stmt->bind_param("sss", $numero_apogee, $name, $prenom);   
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    // Récupération de l'ID de l'étudiant
    $row = $result->fetch_assoc();
    $id_etudiant = $row['id_etudiant'];

    // Insérer la réclamation dans la table des réclamations
    $etat_reclamation = "en cours";
    $stmt_insert = $conn->prepare("INSERT INTO reclamations (id_etudiant, contenu_reclamation, etat_reclamation) 
                                   VALUES (?, ?, ?)");
    $stmt_insert->bind_param("iss", $id_etudiant, $reclamation, $etat_reclamation);

    if ($stmt_insert->execute()) {
        echo "Formulaire soumis avec succès.";
    $stmt_insert->close();
    exit; 
    } else {
        echo "Erreur lors de l'enregistrement de la réclamation : " . $conn->error;
        exit;
    }

} 
else{
    echo "les informations sont invalides";}
    
}
exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre une demande</title>
    <link rel="stylesheet" href="styles.css">
</head>


<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
        <a href="../acceuill.php"><button class="sidebar-btn"><h3>Acceuille</h3></button></a>
        <a href="Studentdemande.php"><button class="sidebar-btn"><h3>Ajouter une demande</h3></button></a>
        <a href="Student.php"><button class="sidebar-btn"><h3>Faire une réclamation</h3></button></a>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div id="Demande" class="section ">
            <div class="form-container">
            <h2>Soumettre une Demande</h2>
            <form action="" method="POST" id="monFormulaire">
            <!-- Identité de l'étudiant -->
             <div class="group-container">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nomd" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenomd" name="prenom" required>
            </div>
            </div>
            <div class="group-container">
            <div class="form-group">
                <label for="email">E-mail :</label>
                <input type="email" id="emaild" name="email" required>
            </div>
            <div class="form-group">
                <label for="numero_apogee">Numéro Apogée :</label>
                <input type="text" id="numero_apogeed" name="numero_apogee" required>
            </div>
            </div>
        
            <!-- Type de document demandé -->
            <div class="group-container">
            
            <div class="form-group">
                <label for="CIN">Numéro CIN :</label>
                <input type="text" id="CIN" name="CIN" required>
            </div>
            <div class="form-group">
                <label for="type_document">Type de Document :</label>
                <select id="type_document" name="type_document" required>
                    <option value="">-- Sélectionner un type --</option>
                    <option value="Attestation de scolarité">Attestation de scolarité</option>
                    <option value="Relevé de notes">Relevé de notes</option>
                    <option value="Convention de stage">Convention de stage</option>
                    <option value="Attestation de réussite">Attestation de réussite</option>
                </select>
            </div>
            </div>
            <div id="dynamic-fields" class="group-container1">
            
            </div>
            
            

            <!-- Bouton pour soumettre -->
            <div class="form-group">
                <div class="submit">
                <button type="submit">Soumettre la Demande</button>
                </div>
            </div>
            </form>
        </div>

         </div>
    
         <div id="Reclamation" class="section active">
        <div class="form-container">
            <h2>Ajouter une réclamation</h2>
            <form action="Student.php" method="POST">
                <!-- Identité de l'étudiant -->
                <div class="group-container">
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                </div>
                <div class="group-container">
                    <div class="form-group">
                        <label for="email">E-mail :</label>
                        <input type="email" id="email" name="email" required>
                        <span id="email_error" class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="numero_apogee">Numéro Apogée :</label>
                        <input type="text" id="numero_apogee" name="numero_apogee" required>
                        <span id="numero_error" class="error"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reclamation">Réclamation :</label>
                    <textarea id="reclamation" name="reclamation" required></textarea>
                </div>
                <!-- Bouton pour soumettre -->
                <div class="form-group">
                    <div class="submit">
                        <button  type="submit" id="submitButton" disabled >Soumettre la réclamation</button>
                    </div>
                </div>
            </form>
            <div class="reponse" id="response"></div>
        </div>
    </div>
    </div>
    </div>

    <script src="script1.js">
</script>
</body>

</html>

