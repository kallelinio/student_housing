<?php
session_start();
include "database.php";

if(!isset($_SESSION['id_user'])){
header("Location: login.php");
exit();
}

$id_user = $_SESSION['id_user'];
$id_annonce = $_GET['id'];

$sql = "INSERT INTO favoris (id_user,id_annonce) VALUES (?,?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user,$id_annonce]);

header("Location: etudiants_recherche.php");
?>
