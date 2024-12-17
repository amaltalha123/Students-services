<?php
// Vérification de la présence de l'ID dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die("ID manquant dans l'URL.");
}

// Connexion à la base de données avec PDO
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "gestiondemandes";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>");
}

// Requête SQL pour récupérer les données de l'étudiant
$requete = $pdo->prepare("SELECT * FROM demandes WHERE id = ?");
$requete->execute([$id]);
$etudiant = $requete->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die("Étudiant introuvable.");
}

// Données extraites de la base
$nom_prenom = strtoupper($etudiant['nom'] . " " . $etudiant['prenom']);
$numero_apogee = $etudiant['numero_apogee'];
$email = $etudiant['email'];
$type_document = strtoupper($etudiant['type_document']);

// Inclusion de la bibliothèque FPDF et création du PDF
require('fpdf.php');
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// En-tête
$pdf->Image('en-tete.png', 10, 5, 130, 20);
$pdf->Ln(18);
$pdf->Cell(0, 10, "ATTESTATION D'INSCRIPTION", 'TB', 1, 'C');
$pdf->Ln(10);

// Contenu
$pdf->SetFont('Arial', '', 12);
$pdf->Write(7, "Je soussigné, Responsable du Service Administratif, certifie que : \n\n");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Write(7, "Nom & Prénom : $nom_prenom \n");
$pdf->SetFont('Arial', '', 12);
$pdf->Write(7, "Numéro Apogée : $numero_apogee \n");
$pdf->Write(7, "Email : $email \n");
$pdf->Write(7, "Type de document demandé : $type_document \n\n");
$pdf->Write(7, "Cette attestation est délivrée pour servir et valoir ce que de droit.\n");

// Signature et date
$pdf->Cell(0, 10, "Fait à [Votre Ville], le " . date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(0, 10, 'Signature', 0, 1, 'R');

// Génération du fichier PDF
$pdf->Output();
?>
