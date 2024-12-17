<?php
$host = 'localhost';
$dbname = 'gestion_demandes';
$username = 'root'; // Par défaut pour WAMP
$password = ''; // Par défaut pour WAMP

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<?php
include 'db.php';

$query = "SELECT * FROM demandes";
$stmt = $conn->prepare($query);
$stmt->execute();
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>