<?php
include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'];
    $destinataire = $_POST['destinataire'];
    $expediteur = $_SESSION['id_user'];

    $stmt = $pdo->prepare("INSERT INTO MESSAGE (contenu, date, expediteur, destinataire) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$contenu, $expediteur, $destinataire]);

    echo "Message envoyé.";
}
?>
