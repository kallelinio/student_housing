<?php
session_start();
include "database.php";

$id_user = $_SESSION['id_user'];
$id_annonce = $_GET['id'];

// نشوفو موجود ولا لا
$sql = "SELECT * FROM favoris WHERE id_user=? AND id_annonce=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user,$id_annonce]);

if($stmt->rowCount() > 0){

    // نحيو favoris
    $delete = "DELETE FROM favoris WHERE id_user=? AND id_annonce=?";
    $pdo->prepare($delete)->execute([$id_user,$id_annonce]);

}else{

    // نضيفو favoris
    $insert = "INSERT INTO favoris(id_user,id_annonce) VALUES(?,?)";
    $pdo->prepare($insert)->execute([$id_user,$id_annonce]);

}

header("Location: etudiants_recherche.php");
exit();

?>