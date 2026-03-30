<?php
session_start();
?>

<nav>

<a href="index.php">Accueil</a>

    <a href="frontend/pages/search.php">Logements</a>
    <a href="frontend/pages/publish_annonce.php">Publier</a>
    <a href="frontend/pages/message.php">Messages</a>

<?php if(isset($_SESSION['id_user'])){ ?>

<a href="backend/auth/logout.php">Logout</a>

<?php } else { ?>

<a href="frontend/pages/login.php">Login</a>

<a href="frontend/pages/register.php">Register</a>

<?php } ?>
<?php if(isset($_SESSION['id_user'])){ ?>

<span>Bonjour <?php echo $_SESSION['id_user']; ?></span>

<a href="backend/auth/logout.php">Logout</a>

<?php } ?>

</nav>