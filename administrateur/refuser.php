<?php
// Vérification de la présence de l'ID dans l'URL
if (isset($_GET['id_demande'])) {
    $id = $_GET['id_demande'];
} else {
    die("ID manquant dans l'URL.");
}

// Connexion à la base de données avec PDO
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "services";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>");
}

// Requête SQL pour récupérer les données de l'étudiant


$requete = $pdo->prepare("SELECT e.nom AS demande_nom, 
            e.prenom AS demande_prenom, 
            e.numero_apogee AS demande_numero_apogee,
            e.email AS demande_email, 
            d.type_document AS demande_type_document
           FROM etudiants e   join  demandes d on d.id_etudiant = e.id_etudiant  WHERE d.id_demande = ?");
$requete->execute([$id]);

$etudiant = $requete->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die("Étudiant introuvable.");
}

// Données extraites de la base
$nom_prenom = strtoupper($etudiant['demande_nom'] . " " . $etudiant['demande_prenom']);
$numero_apogee = $etudiant['demande_numero_apogee'];
$email = $etudiant['demande_email'];
$type_document = strtoupper($etudiant['demande_type_document']);
 // Email de l'étudiant à qui envoyer le message

 $updateQuery = $pdo->prepare("UPDATE demandes SET etat_demande = 'Refusée' WHERE id_demande = ?");
 $updateQuery->execute([$id]);
//ajouter variable message 
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
                        //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'rr9444037@gmail.com';                     //SMTP username
    $mail->Password   = 'ectl omzr ogjz moil';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', 'ENSATe');
   
    $mail->addAddress($etudiant['demande_email'], $etudiant['demande_nom'] . ' ' . $etudiant['demande_prenom']);     //Add a recipient
    

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "Refus de votre demande";
    $mail->Body = "
            Bonjour,<br><br>
            Nous vous informons que votre demande  a été refusée <br><br>
            Veuillez corriger votre demande ou nous contacter pour plus d'informations.<br><br>
            Cordialement,<br>
            Service Administratif
        ";

        $mail->send();
        echo "Un message de refus a été envoyé à l'étudiant.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
    }
    header("Refresh: 0; url=admin.php");  // Redirige vers admin.php après 3 secondes
    exit; 
?>
