<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<h2>Recherche de logements</h2>

<form method="GET" action="search.php">
    <input type="text" name="localisation" placeholder="Ville">
    <input type="number" name="budget" placeholder="Budget max">
    <button type="submit">Rechercher</button>
</form>

<?php
include '../../backend/config/database.php';
if (isset($_GET['localisation'])) {
    $stmt = $pdo->prepare("SELECT * FROM LOGEMENT WHERE localisation LIKE ?");
    $stmt->execute(['%'.$_GET['localisation'].'%']);
    foreach ($stmt as $logement) {
        echo "<div><a href='logement_details.php?id=".$logement['id_logement']."'>".$logement['titre']."</a></div>";
    }
}
?>

<?php include '../../includes/footer.php'; ?>
