<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<?php
include '../../backend/config/database.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM LOGEMENT WHERE id_logement=?");
$stmt->execute([$id]);
$logement = $stmt->fetch();
?>

<h2><?php echo $logement['titre']; ?></h2>
<p><?php echo $logement['description']; ?></p>
<p>Prix: <?php echo $logement['prix']; ?> €</p>
<img src="../../uploads/logements/<?php echo $logement['photo']; ?>" width="300">

<?php include '../../includes/footer.php'; ?>
