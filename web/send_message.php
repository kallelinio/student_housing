<?php
session_start();
include "database.php";

$id_sender = $_SESSION['id_user'];
$id_receiver = $_POST['id_receiver'];
$id_annonce = $_POST['id_annonce'];
$message = $_POST['message'];

// تحقق destinataire موجود
$check = $pdo->prepare("SELECT id_user FROM utilisateur WHERE id_user=?");
$check->execute([$id_receiver]);

if($check->rowCount() == 0){
    die("Erreur: utilisateur destinataire غير موجود");
}

if(!empty($message)){
    $sql = "INSERT INTO message (contenu, date, expediteur, destinataire, id_annonce)
            VALUES (?, NOW(), ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$message, $id_sender, $id_receiver, $id_annonce]);
}

header("Location: message.php?id_receiver=".$id_receiver."&id_annonce=".$id_annonce);
exit();
?>