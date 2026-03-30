<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<h2>Publier une annonce</h2>
<form method="POST" action="../../backend/annonces/add_annonce.php" enctype="multipart/form-data">
    <input type="text" name="titre" placeholder="Titre">
    <input type="number" name="prix" placeholder="Prix">
    <input type="text" name="localisation" placeholder="Localisation">
    <textarea name="description" placeholder="Description"></textarea>
    <input type="file" name="photo">
    <button type="submit">Publier</button>
</form>

<?php include '../../includes/footer.php'; ?>
