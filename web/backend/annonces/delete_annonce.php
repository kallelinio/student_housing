<?php
include '../config/database.php';
session_start();

// Vérifier si l'utilisateur est propriétaire ou admin
if (!isset($_SESSION['id_user'])) {
    die("Accès refusé.");
}

if (isset($_GET['id'])) {
    $id_annonce = $_GET['id'];

    // Vérifier si l'annonce appartient au propriétaire connecté
    $stmt = $pdo->prepare("SELECT * FROM ANNONCE WHERE id_annonce=? AND id_proprietaire=?");
    $stmt->execute([$id_annonce, $_SESSION['id_user']]);
    $annonce = $stmt->fetch();

    if ($annonce || $_SESSION['type_compte'] === 'Admin') {
        $delete = $pdo->prepare("DELETE FROM ANNONCE WHERE id_annonce=?");
        $delete->execute([$id_annonce]);
        echo "Annonce supprimée avec succès.";
    } else {
        echo "Vous n'avez pas le droit de supprimer cette annonce.";
    }
}
?>
