<?php
include '../config/database.php';

// Récupérer toutes les annonces publiées
$stmt = $pdo->query("SELECT A.id_annonce, A.date_publication, A.statut, L.titre, L.prix, L.localisation 
                     FROM ANNONCE A 
                     JOIN LOGEMENT L ON A.id_logement = L.id_logement 
                     WHERE A.statut='Publiée'");

echo "<h2>Liste des annonces</h2>";
foreach ($stmt as $annonce) {
    echo "<div>";
    echo "<h3>".$annonce['titre']."</h3>";
    echo "<p>Prix: ".$annonce['prix']." €</p>";
    echo "<p>Localisation: ".$annonce['localisation']."</p>";
    echo "<p>Date: ".$annonce['date_publication']."</p>";
    echo "<a href='../../frontend/pages/logement_details.php?id=".$annonce['id_annonce']."'>Voir détails</a>";
    echo "</div><hr>";
}
?>
