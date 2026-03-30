<?php
include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $prix = $_POST['prix'];
    $localisation = $_POST['localisation'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], "../../uploads/logements/".$photo);

    $stmt = $pdo->prepare("INSERT INTO LOGEMENT (titre, prix, localisation, description, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $prix, $localisation, $description, $photo]);

    echo "Annonce publiée avec succès.";
}
?>
