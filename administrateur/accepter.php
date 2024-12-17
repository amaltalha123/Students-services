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
            e.id_etudiant as id_etudiant, 
            e.numero_apogee AS demande_numero_apogee,
            e.email AS demande_email,
            d.lieu_stage AS lieu,
            d.encadrant_ecole as encadrant_ecole,
            d.encadrant_entreprise as encadrant_entreprise,
            d.entreprise as entreprise,
            d.dure_stage as dure_stage,
            d.sujet_stage as sujet_stage,
            d.niveau_demande as niveau,
            d.type_document AS demande_type_document,
            e.CIN AS etudiant_cin,
            e.dernier_annee_valide AS  etudiant_annee_valide,
            e.niveau_validé AS  etudiant_niveau_valide,
            e.date_naissance AS etudiant_date_naissance,
            e.lieu_naissance AS etudiant_lieu_naissance, 
            e.filiere AS etudiant_filiere, 
            
            e.annee_scolaire AS etudiant_annee_scolaire FROM etudiants e   join  demandes d on d.id_etudiant = e.id_etudiant  WHERE d.id_demande = ?");
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
$cin = $etudiant['etudiant_cin'];
$date_naissance = $etudiant['etudiant_date_naissance'];
$lieu_naissance = $etudiant['etudiant_lieu_naissance'];
$filiere = $etudiant['etudiant_filiere'];
$annee_scolaire = $etudiant['etudiant_annee_scolaire'];
$niveau_valide = $etudiant['etudiant_niveau_valide'];
$annee_valide = $etudiant['etudiant_annee_valide'];

$entreprise = $etudiant['entreprise'];
$lieu = $etudiant['lieu'];
$sujet_stage = $etudiant['sujet_stage'];
$dure_stage = $etudiant['dure_stage'];
$encadrant_ecole = $etudiant['encadrant_ecole'];
$encadrant_entreprise= $etudiant['encadrant_entreprise'];

$id_etudiant=$etudiant['id_etudiant'];
$niveau=$etudiant['niveau'];




// Inclusion de la bibliothèque FPDF et création du PDF
require('fpdf/fpdf.php');
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// En-tête
$pdf->Image('en-tete.png', 0, 0, 210);
$pdf->Ln(40);





//attestation de scolarite
$type_document = trim(strtolower($type_document));
if ($type_document == 'attestation de scolarité') {
$pdf->Cell(0, 10, "ATTESTATION DE SCOLARITE", 0, 1, 'C');
$pdf->Line(63, $pdf->GetY(), 147, $pdf->GetY()); // Dessiner une ligne horizontale sous le titre

$pdf->Ln(20);

// Contenu
$pdf->SetFont('Arial', '', 12);
$pdf->Write(7, "Le Direecteur de l'Ecole Nationale des Sciences Appliquees de Tetouan atteste que : \n\n");

$h = 7;
$retrait = "      ";
$pdf->Write($h, $retrait . "l'etudiant(e)  : ");
$pdf->SetFont('', 'BIU');
$pdf->Write($h, $nom_prenom . "\n");
$pdf->SetFont('Arial', '', 12);



$pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Numéro de la carte nationale :  $cin\n"));
$pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Numéro Apogée : $numero_apogee \n"));
$pdf->Write(7, "Email : $email \n");
$pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "née le  $date_naissance a $lieu_naissance \n"));
$pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Poursuit ses études a l'Ecole Nationale des Sciences Appliquées Tetouan pour l'annee universitaire  $annee_scolaire \n\n"));
$pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Filiére : $filiere\n"));
$pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Année : $annee_scolaire\n\n\n\n\n"));


// Signature et date
$pdf->Cell(0, 10, "Fait a TETOUAN, le " . date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Le directeur', 0, 1, 'C');
$pdfFile = 'dossiertemp/attestation.pdf';
$pdf->Output('F', $pdfFile); 
}elseif ($type_document == 'attestation de réussite') {
    

    $pdf->Cell(0, 10, "ATTESTATION DE REUSSITE", 0, 1, 'C');
    $pdf->Line(63, $pdf->GetY(), 147, $pdf->GetY()); // Dessiner une ligne horizontale sous le titre
    
    $pdf->Ln(20);
    
    // Contenu
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Le Directeur de l'Ecole Nationale des Sciences Appliquées de Tétouan atteste que : \n\n"));
    
    $h = 7;
    $retrait = "      ";
    $pdf->Write($h, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',$retrait . "l'étudiant(e)  : "));
    $pdf->SetFont('', 'BIU');
    $pdf->Write($h, $nom_prenom . "\n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "née le  $date_naissance a $lieu_naissance \n"));
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "a été declaré admise au niveau  \n"));
    $pdf->Write(7, "$niveau_valide\n");
    $annee=$annee_valide-1;
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"au titre de l'année universitaire $annee - $annee_valide  \n"));



    $pdf->Cell(0, 10, "Fait a TETOUAN, le " . date('d/m/Y'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Le directeur', 0, 1, 'C');
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "N étudiant: $numero_apogee"), 0, 1, 'C');


    $pdfFile = 'dossiertemp/attestation.pdf';
    $pdf->Output('F', $pdfFile); 
  }elseif ($type_document == 'convention de stage') {//arrete ici

    $pdf->Cell(0, 10, "CONVENTION DE STAGE ", 0, 1, 'C');
    $pdf->Line(63, $pdf->GetY(), 147, $pdf->GetY()); // Dessiner une ligne horizontale sous le titre
    
    $pdf->Ln(20);
    $h = 7;
    // Contenu
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, "Entre : \n");
    $h = 7;
    $retrait = "       ";
    $pdf->Write($h, $retrait . "d'une part : \n");
    $h = 7;
    $retrait = "          ";
    $pdf->Write($h, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',$retrait . "L'Ecole National des Sciences Appliquees Tetouan \n"));
    $pdf->Write(7, "Adresse : M'Hannech 2 B.P 2222 Tetouan \n");
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Téléphone : 0539968802\n"));
    $pdf->Write(7, "Fax: 0539994624\n");
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Representé par : $encadrant_ecole\n\n"));
   
    $h = 7;
    $retrait = "       ";
    $pdf->Write($h, $retrait . "d'autre part : \n");
    $h = 7;
    $retrait = "        ";
    $pdf->Write($h, $retrait . "Nom : $entreprise\n");
    $pdf->Write(7, "Adresse : $lieu \n");
  
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Representé par :$encadrant_entreprise\n"));
    $pdf->Write(7, "Elle concerne  :\n");
    $h = 7;
    $pdf->Write($h, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"L'étudiant(e)  : "));
    $pdf->Write($h, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "l'étudiant(e)  : "));
    $pdf->SetFont('', 'BIU');
    $pdf->Write($h, $nom_prenom . "\n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, "CIN : $cin\n");
    $pdf->Write(7, "Email : $email \n");
 
    $pdf->SetFont('Arial', '', 12);


    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir

    $pdf->Write(7, "Article 1 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "La presente convention a pour objet de definir les modalites du stage effectue par l'etudiant au sein de l'entreprise ou organisation d'accueil. \n"));
    

    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    $pdf->Write(7, "Article 2 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Le programme du stage est élaboré par le personnel chargé de l'encadrement du stagiaire, en tenant compte du programme et de spécialité des études du stagiaire, ainsi que des moyens humain et matériel de l'enteprise. Cette dernière se réserve le droit de réorienter l'apprentissage en fonction des qualifications du stagiaire et du rythme de ses activités professionnelles.\n"));

    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    
    $pdf->Write(7, "Article 3 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Pendant le stage, le stagiaire est soumis aux usages et règlements de l'entreprise, notamment en matière de discipline et des horaires. En cas de manquement à ces règles, l'entreprise se réserve le droit de mettre fin au stage, après avoir prévenullétablissement de formation.\n"));
    
    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
  
    $pdf->Write(7, "Article 4 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Au terme de son stage, le stagiaire remettra un rapport de stage à l'entreprise si réclamé par celle-ci.\n"));

    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    $pdf->Write(7, "Article 5 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Le stagiaire s'engage à garder confidentielle toute information recueillie dans l'entreprise, et à n'utiliser en aucun cas ces informations pour faire l'objet d'une publication, communication à des tiers,conférences, sans l'accord préalable de l'entreprise.\n"));
   
    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    $pdf->Write(7, "Article 6 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Le stagiaire est tenu de souscrire une assurance pour la garantir contre les risques d'accident ou d'incident auxquels le stagiaire pourrait être exposé durant la période de son stage.\n"));
    
    
    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    $pdf->Write(7, "Article 7 :  \n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7,iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "En cas de non-respect de l'une des clauses de cette convention aussi bien par le stagiaire, l'entreprise se réserve le droit de mettre fin à ce stage.\n"));
    
    $pdf->SetFont('Arial', 'B', 12); // Gras (B) et taille 12
    $pdf->SetTextColor(0, 0, 0); //noir
    $pdf->Write(7, "Article 8 :  \n");

    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT',"Le stage se déroulera du ... au ... \n"));
   
    
    
    $pdf->Cell(0, 10, "Fait a TETOUAN, le " . date('d/m/Y'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Le directeur', 0, 1, 'C');
    $pdf->Cell(0, 10, "N etudiant: $numero_apogee", 0, 1, 'D');

    $pdfFile = 'dossiertemp/attestation.pdf';
    $pdf->Output('F', $pdfFile); 
    }elseif ($type_document == 'relevé de notes') {


    $total_notes = 0;
    $nombre_notes = 0;
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "RELEVE DE NOTES", 0, 1, 'C');
    $pdf->Line(63, $pdf->GetY(), 147, $pdf->GetY()); // Ligne horizontale sous le titre
    $pdf->Ln(10);

    // Informations sur l'étudiant
    $pdf->SetFont('Arial', '', 12);
    $h = 7;
    $pdf->Write($h, "L'etudiant(e) : ");
    $pdf->SetFont('', 'BIU');
    $pdf->Write($h, $nom_prenom . "\n");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write($h, "Numero de la carte nationale : $cin\n");
    $pdf->Write($h, "Numero Apogee : $numero_apogee\n");
    $pdf->Write($h, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Niveau du relevé de note : ") . $niveau . "\n");
    $pdf->Ln(5);


    
    // En-tête du tableau
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(120, 10, 'Module', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Note', 1, 1, 'C');
    if($niveau == '2AP1'){
        $requete = $pdo->prepare("SELECT * FROM 2AP1 WHERE id_etudiant = ?");
        $requete->execute([$id_etudiant]);   

        $pdf->SetFont('Arial', '', 12);

        // Récupérer les métadonnées de la première ligne pour obtenir les noms des colonnes
        $columns = $requete->fetch(PDO::FETCH_ASSOC);

        // Parcourir toutes les colonnes et afficher leurs noms et les résultats
        $colonne_vide = false;
        foreach ($columns as $colonne => $valeur) {
            if (empty($valeur) && $valeur !== 0) {
                $colonne_vide = true;
                break;  // Sortir de la boucle dès qu'une colonne est vide
            }
            if ($colonne != 'id_etudiant') {  // Ignore l'ID de l'étudiant s'il existe dans les colonnes
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
                
                // Accumuler les notes si la colonne contient des valeurs numériques
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
        }

        // Calcul de la moyenne si des notes ont été accumulées
        if ($nombre_notes > 0) {
            $moyenne = $total_notes / $nombre_notes;
            $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
            $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
        }else {
            $moyenne = 0; // Aucun module trouvé
        }
    }else if($niveau == 'CI1'){
        if($filiere=='Genie informatique'){
        $requete = $pdo->prepare("SELECT * FROM genie_informatique WHERE id_etudiant = ?");
        $requete->execute([$id_etudiant]);   

        $pdf->SetFont('Arial', '', 12);

        // Récupérer les métadonnées de la première ligne pour obtenir les noms des colonnes
        $columns = $requete->fetch(PDO::FETCH_ASSOC);

        // Parcourir toutes les colonnes et afficher leurs noms et les résultats
        $colonne_vide = false;
        foreach ($columns as $colonne => $valeur) {
            if ((empty($valeur) && $valeur !== 0) || $colonne=='Modelisation_Programmation_Objet') {
                $colonne_vide = true;
                break;  // Sortir de la boucle dès qu'une colonne est vide
            }
            if ($colonne != 'id_etudiant') {  // Ignore l'ID de l'étudiant s'il existe dans les colonnes
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
                
                // Accumuler les notes si la colonne contient des valeurs numériques
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
        }

        // Calcul de la moyenne si des notes ont été accumulées
        if ($nombre_notes > 0) {
            $moyenne = $total_notes / $nombre_notes;
            $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
            $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
        }else {
            $moyenne = 0; // Aucun module trouvé
        }
      }else if($filiere=='GSCM'){
        $requete = $pdo->prepare("SELECT * FROM gscm WHERE id_etudiant = ?");
        $requete->execute([$id_etudiant]);   

        $pdf->SetFont('Arial', '', 12);

        // Récupérer les métadonnées de la première ligne pour obtenir les noms des colonnes
        $columns = $requete->fetch(PDO::FETCH_ASSOC);

        // Parcourir toutes les colonnes et afficher leurs noms et les résultats
        $colonne_vide = false;
        foreach ($columns as $colonne => $valeur) {
            if ((empty($valeur) && $valeur !== 0) || $colonne=='Modelisation_Programmation_Objet') {
                $colonne_vide = true;
                break;  // Sortir de la boucle dès qu'une colonne est vide
            }
            if ($colonne != 'id_etudiant') {  // Ignore l'ID de l'étudiant s'il existe dans les colonnes
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
                
                // Accumuler les notes si la colonne contient des valeurs numériques
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
        }

        // Calcul de la moyenne si des notes ont été accumulées
        if ($nombre_notes > 0) {
            $moyenne = $total_notes / $nombre_notes;
            $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
            $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
        }else {
            $moyenne = 0; // Aucun module trouvé
        }
    }
    }else if($niveau=='2PA2'){
        $requete = $pdo->prepare("SELECT * FROM 2AP2 WHERE id_etudiant = ?");
        $requete->execute([$id_etudiant]);   

        $pdf->SetFont('Arial', '', 12);

        // Récupérer les métadonnées de la première ligne pour obtenir les noms des colonnes
        $columns = $requete->fetch(PDO::FETCH_ASSOC);

        // Parcourir toutes les colonnes et afficher leurs noms et les résultats
        $colonne_vide = false;
        foreach ($columns as $colonne => $valeur) {
            if (empty($valeur) && $valeur !== 0) {
                $colonne_vide = true;
                break;  // Sortir de la boucle dès qu'une colonne est vide
            }
            if ($colonne != 'id_etudiant') {  // Ignore l'ID de l'étudiant s'il existe dans les colonnes
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
                
                // Accumuler les notes si la colonne contient des valeurs numériques
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
        }

        // Calcul de la moyenne si des notes ont été accumulées
        if ($nombre_notes > 0) {
            $moyenne = $total_notes / $nombre_notes;
            $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
            $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
        }else {
            $moyenne = 0; // Aucun module trouvé
        }

    }else if($niveau=='CI2'){

        if($filiere=='Genie informatique'){
            // Récupération des colonnes de la table gscm
            $requeteColonnes = $pdo->prepare("DESCRIBE genie_informatique");
            $requeteColonnes->execute();
            $colonnes = $requeteColonnes->fetchAll(PDO::FETCH_COLUMN);
    
            // Définir les limites pour les colonnes
            $debut_colonne = 'Modelisation_Programmation_Objet';
            $fin_colonne = 'BD_Relationnelle_Objet_Repartie';
            $selectionner = false;
            $colonnes_selectionnees = [];
    
            // Filtrer les colonnes entre les limites spécifiées
            foreach ($colonnes as $colonne) {
                $colonne = trim($colonne); // Nettoie les espaces
                if (strcasecmp($colonne, $debut_colonne) == 0) {
                    $selectionner = true; // Début de la sélection
                }
                if ($selectionner) {
                    $colonnes_selectionnees[] = $colonne;
                }
                if (strcasecmp($colonne, $fin_colonne) == 0) {
                    break; // Arrêter après la dernière colonne
                }
            }
    
            // Construire et exécuter la requête SQL avec les colonnes sélectionnées
            $colonnes_str = implode(", ", $colonnes_selectionnees);
            $requete = $pdo->prepare("SELECT $colonnes_str FROM genie_informatique WHERE id_etudiant = ?");
            $requete->execute([$id_etudiant]);
    
            // Afficher les résultats dans le PDF
            $pdf->SetFont('Arial', '', 12);
            $donnees = $requete->fetch(PDO::FETCH_ASSOC);
    
            $total_notes = 0;
            $nombre_notes = 0;
    
            foreach ($donnees as $colonne => $valeur) {
                if (empty($valeur) && $valeur !== 0) {
                    $colonne_vide = true;
                    break;  // Sortir de la boucle dès qu'une colonne est vide
                }
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
    
                // Calcul des notes
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
    
            // Calcul de la moyenne
            if ($nombre_notes > 0) {
                $moyenne = $total_notes / $nombre_notes;
                $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
                $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
            } else {
                $pdf->Cell(160, 10, 'Aucune note trouvee', 1, 1, 'C');
            }
        }
        else if ($filiere == 'GSCM') {
            // Récupération des colonnes de la table gscm
            $requeteColonnes = $pdo->prepare("DESCRIBE gscm");
            $requeteColonnes->execute();
            $colonnes = $requeteColonnes->fetchAll(PDO::FETCH_COLUMN);
    
            // Définir les limites pour les colonnes
            $debut_colonne = 'Modelisation_Programmation_Objet';
            $fin_colonne = 'Theorie_Decision_GSI';
            $selectionner = false;
            $colonnes_selectionnees = [];
    
            // Filtrer les colonnes entre les limites spécifiées
            foreach ($colonnes as $colonne) {
                $colonne = trim($colonne); // Nettoie les espaces
                if (strcasecmp($colonne, $debut_colonne) == 0) {
                    $selectionner = true; // Début de la sélection
                }
                if ($selectionner) {
                    $colonnes_selectionnees[] = $colonne;
                }
                if (strcasecmp($colonne, $fin_colonne) == 0) {
                    break; // Arrêter après la dernière colonne
                }
            }
    
            // Construire et exécuter la requête SQL avec les colonnes sélectionnées
            $colonnes_str = implode(", ", $colonnes_selectionnees);
            $requete = $pdo->prepare("SELECT $colonnes_str FROM gscm WHERE id_etudiant = ?");
            $requete->execute([$id_etudiant]);
    
            // Afficher les résultats dans le PDF
            $pdf->SetFont('Arial', '', 12);
            $donnees = $requete->fetch(PDO::FETCH_ASSOC);
    
            $total_notes = 0;
            $nombre_notes = 0;
    
            foreach ($donnees as $colonne => $valeur) {
                if (empty($valeur) && $valeur !== 0) {
                    $colonne_vide = true;
                    break;  // Sortir de la boucle dès qu'une colonne est vide
                }
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
    
                // Calcul des notes
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
    
            // Calcul de la moyenne
            if ($nombre_notes > 0) {
                $moyenne = $total_notes / $nombre_notes;
                $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
                $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
            } else {
                $pdf->Cell(160, 10, 'Aucune note trouvee', 1, 1, 'C');
            }
        }
        
    }else if($niveau=='CI3'){
        if ($filiere == 'Genie informatique') {
            // Récupération des colonnes de la table genie_informatique
            $requeteColonnes = $pdo->prepare("DESCRIBE genie_informatique");
            $requeteColonnes->execute();
            $colonnes = $requeteColonnes->fetchAll(PDO::FETCH_COLUMN);
        
            // Définir les limites pour les colonnes
            $debut_colonne = 'Systemes_Integration_Progiciel';
            $fin_colonne = 'Management_III';
            $selectionner = false;
            $colonnes_selectionnees = [];
        
            // Filtrer les colonnes entre les limites spécifiées
            foreach ($colonnes as $colonne) {
                $colonne = trim($colonne); // Nettoie les espaces
                if (strcasecmp($colonne, $debut_colonne) == 0) {
                    $selectionner = true;
                }
                if ($selectionner) {
                    $colonnes_selectionnees[] = "`$colonne`"; // Ajout de backticks
                }
                if (strcasecmp($colonne, $fin_colonne) == 0) {
                    break;
                }
            }
        
            // Vérification des colonnes sélectionnées
            if (empty($colonnes_selectionnees)) {
                die("Aucune colonne trouvée entre $debut_colonne et $fin_colonne.");
            }
        
            $colonnes_str = implode(", ", $colonnes_selectionnees);
        
            try {
                // Exécution de la requête SQL
                $requete = $pdo->prepare("SELECT $colonnes_str FROM genie_informatique WHERE id_etudiant = ?");
                $requete->execute([$id_etudiant]);
                $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        
                if (!$donnees) {
                    die("Aucune donnée trouvée pour l'étudiant $id_etudiant.");
                }
        
                $pdf->SetFont('Arial', '', 12);
                $total_notes = 0;
                $nombre_notes = 0;
        
                foreach ($donnees as $colonne => $valeur) {
                    if (empty($valeur) && $valeur !== 0) {
                        $colonne_vide = true;
                        break;  // Sortir de la boucle dès qu'une colonne est vide
                    }
                    $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                    $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
        
                    // Calcul des notes
                    if (is_numeric($valeur)) {
                        $total_notes += $valeur;
                        $nombre_notes++;
                    }
                }
        
                // Calcul de la moyenne
                if ($nombre_notes > 0) {
                    $moyenne = $total_notes / $nombre_notes;
                    $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
                    $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
                } else {
                    $pdf->Cell(160, 10, 'Aucune note trouvée', 1, 1, 'C');
                }
            } catch (PDOException $e) {
                echo "Erreur SQL : " . $e->getMessage();
            }
        } else if($filiere=='GSCM'){
            // Récupération des colonnes de la table gscm
            $requeteColonnes = $pdo->prepare("DESCRIBE gscm");
            $requeteColonnes->execute();
            $colonnes = $requeteColonnes->fetchAll(PDO::FETCH_COLUMN);
    
            // Définir les limites pour les colonnes
            $debut_colonne = 'Logistique_Distribution';
            $fin_colonne = 'Langues_TEC_III';
            $selectionner = false;
            $colonnes_selectionnees = [];
    
            // Filtrer les colonnes entre les limites spécifiées
            foreach ($colonnes as $colonne) {
                $colonne = trim($colonne); // Nettoie les espaces
                if (strcasecmp($colonne, $debut_colonne) == 0) {
                    $selectionner = true; // Début de la sélection
                }
                if ($selectionner) {
                    $colonnes_selectionnees[] = $colonne;
                }
                if (strcasecmp($colonne, $fin_colonne) == 0) {
                    break; // Arrêter après la dernière colonne
                }
            }
    
            // Construire et exécuter la requête SQL avec les colonnes sélectionnées
            $colonnes_str = implode(", ", $colonnes_selectionnees);
            $requete = $pdo->prepare("SELECT $colonnes_str FROM gscm WHERE id_etudiant = ?");
            $requete->execute([$id_etudiant]);
    
            // Afficher les résultats dans le PDF
            $pdf->SetFont('Arial', '', 12);
            $donnees = $requete->fetch(PDO::FETCH_ASSOC);
    
            $total_notes = 0;
            $nombre_notes = 0;
    
            foreach ($donnees as $colonne => $valeur) {
                if (empty($valeur) && $valeur !== 0) {
                    $colonne_vide = true;
                    break;  // Sortir de la boucle dès qu'une colonne est vide
                }
                $pdf->Cell(120, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ucfirst(str_replace('_', ' ', $colonne))), 1, 0, 'C');
                $pdf->Cell(40, 10, $valeur, 1, 1, 'C');
    
                // Calcul des notes
                if (is_numeric($valeur)) {
                    $total_notes += $valeur;
                    $nombre_notes++;
                }
            }
    
            // Calcul de la moyenne
            if ($nombre_notes > 0) {
                $moyenne = $total_notes / $nombre_notes;
                $pdf->Cell(120, 10, 'Moyenne', 1, 0, 'C');
                $pdf->Cell(40, 10, number_format($moyenne, 2), 1, 1, 'C');
            } else {
                $pdf->Cell(160, 10, 'Aucune note trouvee', 1, 1, 'C');
            }
        }
       


    }
    

    }

    // Sortie du PDF
    $pdfFile = 'dossiertemp/attestation.pdf';
    $pdf->Output('F', $pdfFile); 


// attestation de reussite




// Mise à jour de l'état du document dans la base de données

$updateQuery = $pdo->prepare("UPDATE demandes SET etat_demande = 'Validée'  WHERE id_demande = ?");
 $updateQuery->execute([$id]);




// Enregistrez temporairement le PDF
// Enregistre le fichier PDF dans le dossier temporaire

if (file_exists($pdfFile)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($pdfFile) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    readfile($pdfFile);
} else {
    echo "Erreur: Le fichier PDF n'a pas été trouvé.";
}




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
    $mail->setFrom('ENSATe@uae.ac.ma', 'ENSATe');
   
    $mail->addAddress($etudiant['demande_email'], $etudiant['demande_nom'] . ' ' . $etudiant['demande_prenom']);     //Add a recipient
    $mail->addAttachment('dossiertemp/attestation.pdf');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = ' votre demande';
    $mail->Body = '
    Bonjour ,<br><br>
    Voici votre document demandée en pièce jointe.<br><br>
    Cordialement,<br>
    Service Administratif';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    header('Content-Type: application/pdf');
    
   // readfile($pdfFile); // Affiche le PDF enregistré
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
