<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $type_compte = $_POST['type_compte'];

    // Vérifier si email existe déjà
    $check = $pdo->prepare("SELECT id_user FROM UTILISATEUR WHERE email=?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo "<script>
                alert('Erreur: cet email est déjà utilisé. Veuillez en choisir un autre.');
                window.location.href='../../frontend/pages/registre.php';
              </script>";
        exit;
    }

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO UTILISATEUR (nom, email, mot_de_passe, type_compte) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nom, $email, $mot_de_passe, $type_compte])) {
        echo "<script>
                alert('Inscription réussie ! Cliquez sur OK pour vous connecter.');
                window.location.href='../../frontend/pages/login.php';
              </script>";
    } else {
        echo "<script>
                alert('Erreur lors de l\'inscription. Veuillez réessayer.');
                window.location.href='../../frontend/pages/registre.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Créer un compte</h2>
    <form method="POST" action="backend/auth/register_process.php">
        <input type="text" name="nom" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <select name="type_compte" required>
            <option value="Etudiant">Etudiant</option>
            <option value="Propriétaire">Propriétaire</option>
            <option value="Admin">Admin</option>
        </select>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
</div>
</body>
</html>

