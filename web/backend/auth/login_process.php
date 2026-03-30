<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['type_compte'] = $user['type_compte'];

        // Redirection selon role
        if ($user['type_compte'] === 'Admin') {
            header("Location: ../admin/manage_users.php");
        } elseif ($user['type_compte'] === 'Propriétaire') {
            header("Location: ../../frontend/pages/publish_annonce.php");
        } elseif ($user['type_compte'] === 'Etudiant') {
            header("Location: ../../frontend/pages/search.php");
        } else {
            header("Location: ../../index.php");
        }
        exit;
    } else {
        echo "<script>
                alert('Email ou mot de passe incorrect.');
                window.location.href='../../frontend/pages/login.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Connexion</h2>
    <form method="POST" action="backend/auth/login_process.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas de compte ? <a href="registre.php">Créer un compte</a></p>
</div>
</body>
</html>



