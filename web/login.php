<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - StudentHousing</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- تأكد من المسار -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .container input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .container button {
            width: 100%;
            padding: 12px;
            background: #0454ea;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .container button:hover {
            background: #023c9d;
        }
        .container p {
            text-align: center;
            margin-top: 15px;
        }
        .container a {
            color: #0454ea;
            text-decoration: none;
        }
        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - StudentHousing</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Connexion</h2>
    <form method="POST" action="login_process.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <!-- لازم يكون submit باش يبعث الفورم للـ PHP -->
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas de compte ? <a href="signup.php">Créer un compte</a></p>
</div>
</body>
</html>

</body>
</html>