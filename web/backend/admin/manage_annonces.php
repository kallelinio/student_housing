<?php
include '../config/database.php';
session_start();

if ($_SESSION['type_compte'] !== 'Admin') {
    die("Accès refusé.");
}

$stmt = $pdo->query("SELECT * FROM ANNONCE");
echo "<h2>Gestion des annonces</h2>";
foreach ($stmt as $annonce) {
    echo "Annonce #".$annonce['id_annonce']." - Statut: ".$annonce['statut']."<br>";
}
?>
