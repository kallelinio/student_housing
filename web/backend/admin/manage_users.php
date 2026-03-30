<?php
include '../config/database.php';
session_start();

if ($_SESSION['type_compte'] !== 'Admin') {
    die("Accès refusé.");
}

$stmt = $pdo->query("SELECT * FROM UTILISATEUR");
echo "<h2>Gestion des utilisateurs</h2>";
foreach ($stmt as $user) {
    echo $user['nom']." - ".$user['email']." - ".$user['type_compte']."<br>";
}
?>
