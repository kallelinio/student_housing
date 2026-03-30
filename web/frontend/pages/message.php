<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<h2>Messages</h2>
<form method="POST" action="../../backend/messages/send_message.php">
    <textarea name="contenu" placeholder="Votre message"></textarea>
    <input type="hidden" name="destinataire" value="ID_DU_DESTINATAIRE">
    <button type="submit">Envoyer</button>
</form>

<?php include '../../includes/footer.php'; ?>
