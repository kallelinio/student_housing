<?php
session_start();
include "database.php";

// جلب آخر 4 إعلانات مقبولة فقط لعرضها في الصفحة الرئيسية
$stmt = $pdo->query("SELECT * FROM annonce WHERE statut='Publiee' ORDER BY date_publication DESC LIMIT 4");
$recent_annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sكن - منصتك لإيجاد سكن جامعي بكل سهولة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #0052FF;
            --dark: #0F172A;
            --light: #F8FAFC;
            --text: #1E293B;
        }

        body { margin: 0; font-family: 'Inter', sans-serif; color: var(--text); background: white; scroll-behavior: smooth; }

        /* Navbar */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 8%; background: white; position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .logo { font-size: 24px; font-weight: 800; color: var(--primary); text-decoration: none; }
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--dark); font-weight: 600; font-size: 15px; }
        .btn-login { background: var(--primary); color: white !important; padding: 10px 25px; border-radius: 12px; transition: 0.3s; }
        .btn-login:hover { opacity: 0.9; transform: translateY(-2px); }

        /* Hero Section */
        .hero {
            height: 80vh; background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), 
                        url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover; background-position: center;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            text-align: center; color: white; padding: 0 20px;
        }
        .hero h1 { font-size: 50px; font-weight: 800; margin-bottom: 20px; max-width: 800px; }
        .hero p { font-size: 18px; opacity: 0.9; margin-bottom: 40px; }

        /* Search Box */
        .search-container {
            background: white; padding: 10px; border-radius: 20px;
            display: flex; gap: 10px; width: 100%; max-width: 800px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .search-container input {
            flex: 1; border: none; padding: 15px 20px; outline: none; font-size: 16px; border-radius: 15px;
        }
        .btn-search {
            background: var(--primary); color: white; border: none; padding: 0 35px;
            border-radius: 15px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }

        /* Section Title */
        .section-padding { padding: 80px 8%; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-size: 32px; font-weight: 800; }

        /* Cards Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card {
            background: white; border-radius: 20px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s;
        }
        .card:hover { transform: translateY(-10px); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 20px; }
        .card-price { color: var(--primary); font-weight: 800; font-size: 20px; }
        .card-title { font-weight: 700; margin: 10px 0; font-size: 18px; }
        .card-location { color: #64748B; font-size: 14px; display: flex; align-items: center; gap: 5px; }

        /* Features */
        .features { background: var(--light); display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; }
        .feature-item { text-align: center; }
        .feature-item i { font-size: 40px; color: var(--primary); margin-bottom: 20px; }

        /* Footer */
        footer { background: var(--dark); color: white; padding: 50px 8%; text-align: center; }
    </style>
</head>
<body>

<nav>
    <a href="#" class="logo">student_housing</a>
    <div class="nav-links">
        <a href="#offres">Offres</a>
        <a href="#a-propos">À propos</a>
        <?php if(isset($_SESSION['id_user'])): ?>
            <a href="login.php" class="btn-login">Mon Profil</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="signup.php" class="btn-login">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>

<section class="hero">
    <h1>Trouvez votre futur foyer étudiant en un clic.</h1>
    <p>La plateforme n°1 en Tunisie pour la location de chambres وستوديوات قريبة من كليتك.</p>
    
    <form action="search_results.php" method="GET" class="search-container">
        <input type="text" name="query" placeholder="Où voulez-vous habiter ? (Tunis, Sousse, Sfax...)">
        <button type="submit" class="btn-search">Rechercher</button>
    </form>
</section>

<section class="section-padding" id="offres">
    <div class="section-title">
        <h2>Dernières Offres</h2>
        <p>Découvrez les annonces les plus récentes</p>
    </div>

    <div class="grid">
        <?php foreach($recent_annonces as $annonce): ?>
        <div class="card">
            <img src="uploads/<?php echo $annonce['image']; ?>" alt="Logement">
            <div class="card-content">
                <div class="card-price"><?php echo number_format($annonce['prix'], 0); ?> DT <small>/ mois</small></div>
                <div class="card-title"><?php echo htmlspecialchars($annonce['titre']); ?></div>
                <div class="card-location">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($annonce['localisation']); ?>
                </div>
                <br>
                <a href="annonce_details.php?id=<?php echo $annonce['id_annonce']; ?>" style="text-decoration:none; color:var(--primary); font-weight:700;">Voir détails →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-padding features">
    <div class="feature-item">
        <i class="fas fa-shield-check"></i>
        <h3>Logements Vérifiés</h3>
        <p>Toutes nos annonces sont vérifiées par l'administration.</p>
    </div>
    <div class="feature-item">
        <i class="fas fa-bolt"></i>
        <h3>Rapide & Simple</h3>
        <p>Contactez le propriétaire directement sans intermédiaire.</p>
    </div>
    <div class="feature-item">
        <i class="fas fa-university"></i>
        <h3>Proche des Facultés</h3>
        <p>Filtrez vos recherches selon votre université.</p>
    </div>
</section>

<footer>
    <p>&copy; 2026 Sكن. Tous droits réservés.</p>
    <div style="margin-top:20px; opacity:0.6; font-size:14px;">
        Développé pour faciliter la vie des étudiants tunisiens.
    </div>
</footer>

</body>
</html>