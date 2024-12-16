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
$CIN = isset($_POST['CIN']) ? trim(htmlspecialchars($_POST['CIN'])) : '';
$type_document = isset($_POST['type_document']) ? htmlspecialchars($_POST['type_document']) : '';
$entreprise = isset($_POST['entreprise']) ? htmlspecialchars($_POST['entreprise']) : '';
$dure_stage = isset($_POST['duree_stage']) ? htmlspecialchars($_POST['duree_stage']) : '';
$sujet_stage = isset($_POST['sujet_stage']) ? htmlspecialchars($_POST['sujet_stage']) : '';
$localisation = isset($_POST['localisation']) ? htmlspecialchars($_POST['localisation']) : '';
$encadrant_entreprise = isset($_POST['encadrant_entreprise']) ? htmlspecialchars($_POST['encadrant_entreprise']) : '';
$encadrant_ecole = isset($_POST['encadrant_ecole']) ? htmlspecialchars($_POST['encadrant_ecole']) : '';
$niveau_demande = isset($_POST['niveau_demande']) ? htmlspecialchars($_POST['niveau_demande']) : '';

// Validation simple des données
if (empty($name) || empty($email) || empty($prenom) || empty($numero_apogee) || empty($type_document)) {
    echo "Tous les champs sont requis.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "L'adresse e-mail n'est pas valide.";
} else {
    // Rechercher l'étudiant
    $stmt = $conn->prepare("SELECT id_etudiant FROM etudiants 
                            WHERE LOWER(numero_apogee) = LOWER(?) 
                              AND LOWER(nom) = LOWER(?) 
                              AND LOWER(prenom) = LOWER(?) AND LOWER(CIN) = LOWER(?) LIMIT 1");
    $stmt->bind_param("ssss", $numero_apogee, $name, $prenom,$CIN);
    $stmt->execute();
    $result = $stmt->get_result();
    

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_etudiant = $row['id_etudiant'];
        $etat_demande = "en cours";

        // Vérifier le type de document
        if ($type_document === "Convention de stage") {
            $stmt_insert = $conn->prepare("INSERT INTO demandes 
                (id_etudiant, etat_demande, type_document, lieu_stage, encadrant_ecole, encadrant_entreprise, dure_stage, sujet_stage, entreprise) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                

            $stmt_insert->bind_param("isssssiss", $id_etudiant, $etat_demande, $type_document, $localisation, $encadrant_ecole, $encadrant_entreprise, $dure_stage, $sujet_stage, $entreprise);
        }elseif (in_array($type_document, ["Attestation de réussite", "Attestation de scolarité"])) {
                $stmt_insert = $conn->prepare("INSERT INTO demandes 
                (id_etudiant, etat_demande, type_document) 
                VALUES (?, ?, ?)");
                $stmt_insert->bind_param("iss", $id_etudiant, $etat_demande, $type_document);

        }elseif (in_array($type_document, ["Relevé de notes"])) {
                $stmt_insert = $conn->prepare("INSERT INTO demandes 
                (id_etudiant, etat_demande, type_document,niveau_demande) 
                VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param("isss", $id_etudiant, $etat_demande, $type_document,$niveau_demande); 
        }else {
                echo "Type de document invalide.";
                exit;
        }

        if ($stmt_insert->execute()) {
                echo "Formulaire soumis avec succès.";
                exit;
        } else {
                echo "Erreur : " . $conn->error;
        }
        $stmt_insert->close();
    } else {
        echo "Étudiant introuvable.";
        exit;
    }
    $stmt->close();
}
$conn->close();
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
        <a href="../acceuill.php"><button class="sidebar-btn">Acceuille</button></a>
        <a href="Studentdemande.php"><button class="sidebar-btn">Demander un Document</button></a>
        <a href="Student.php"><button class="sidebar-btn">Faire une réclamation</button></a>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div id="Demande">
            <div class="form-container">
            <h2>Soumettre une Demande</h2>
            <form action="" method="POST" id="monFormulaire">
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
                <button type="submit" id="submitButton">Soumettre la Demande</button>
                </div>
            </div>
            </form>
            <div class="reponse" id="response"></div>
        </div>

         </div>    
    </div>
    </div>

    <script src="scriptdemande.js">
</script>
</body>

</html>

