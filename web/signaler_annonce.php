<?php
session_start();
include "database.php";

if(!isset($_SESSION['id_user'])){
header("Location: login.php");
exit();
}

$id_user = $_SESSION['id_user'];
$id_annonce = $_GET['id'];

$raison = "Annonce suspecte";

$sql="INSERT INTO signalements (id_user,id_annonce,raison)
VALUES (?,?,?)";

$stmt=$pdo->prepare($sql);
$stmt->execute([$id_user,$id_annonce,$raison]);

header("Location: annonce_details.php?id=".$id_annonce);
?>