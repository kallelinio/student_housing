<?php
include "database.php";

try {

$sql = "SELECT * FROM annonce 
        WHERE statut = 'Publiee' 
        AND disponibilite = 1 
        ORDER BY date_publication DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
die("Erreur : " . $e->getMessage());
}

 foreach ($annonces as $row): 


$is_fav = false;

if(isset($_SESSION['id_user'])){
    
    $fav_sql = "SELECT 1 FROM favoris WHERE id_user=? AND id_annonce=?";
    $fav_stmt = $pdo->prepare($fav_sql);
    $fav_stmt->execute([$_SESSION['id_user'],$row['id_annonce']]);
    
    $is_fav = $fav_stmt->fetch();
}

?>

<a href="toggle_favori.php?id=<?php echo $row['id_annonce']; ?>" 
class="fav-btn <?php if($is_fav) echo 'active'; ?>">

<i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart"></i>

</a>

<?php endforeach; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche logement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        :root {
            --primary: #0052FF;
            --accent: #FF385C;
            --text-main: #1A202C;
            --text-light: #718096;
            --bg: #F7FAFC;
            --white: #ffffff;
        }

        body {
            margin: 0; font-family: 'Inter', sans-serif;
            background: var(--bg); color: var(--text-main);
            padding-bottom: 90px;
        }

        /* HEADER المطور */
        .main-header {
            background: var(--white); padding: 15px 5%;
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            display: flex; justify-content: space-between; align-items: center;
        }

        .logo-area h1 { margin: 0; font-size: 22px; color: var(--primary); font-weight: 800; }
        .logo-area p { margin: 0; font-size: 12px; color: var(--text-light); }

        /* SEARCH BAR العصرية */
        .search-container {
            padding: 30px 5%; background: var(--white);
            border-bottom: 1px solid #EDF2F7;
        }

        .search-box {
            display: grid; grid-template-columns: 1fr 1fr 1fr auto;
            gap: 10px; background: #F1F5F9; padding: 8px;
            border-radius: 50px; max-width: 900px; margin: 0 auto;
        }

        .search-item {
            display: flex; align-items: center; gap: 10px;
            background: var(--white); padding: 10px 20px;
            border-radius: 40px; border: 1px solid transparent;
            transition: 0.3s;
        }

        .search-item:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,82,255,0.1); }
        .search-item i { color: var(--primary); font-size: 14px; }
        .search-item input, .search-item select {
            border: none; outline: none; width: 100%; font-size: 14px; font-weight: 500;
        }

        .search-btn {
            background: var(--primary); color: white; border: none;
            padding: 0 25px; border-radius: 40px; cursor: pointer;
            font-weight: 600; transition: 0.3s;
        }
        .search-btn:hover { background: #0041CC; transform: scale(1.05); }

        /* GRID الإعلانات */
        .annonces-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px; padding: 40px 5%;
        }

        /* CARD التصميم الجديد */
        .annonce-card {
            background: var(--white); border-radius: 20px; overflow: hidden;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            position: relative; cursor: pointer;
        }

        .annonce-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        .card-image { height: 220px; position: relative; overflow: hidden; }
        .card-image img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .annonce-card:hover .card-image img { transform: scale(1.1); }

        .type-tag {
            position: absolute; top: 15px; left: 15px;
            background: rgba(0, 82, 255, 0.9); color: white;
            padding: 6px 14px; font-size: 11px; font-weight: 700;
            border-radius: 30px; backdrop-filter: blur(5px);
        }

        .fav-btn {
            position: absolute; top: 15px; right: 15px;
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--white); border: none;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.3s; z-index: 10;
        }
        .fav-btn i { color: #CBD5E0; font-size: 18px; transition: 0.3s; }
        .fav-btn.active i { color: var(--accent); }
        .fav-btn:hover { transform: scale(1.2); }

        .card-content { padding: 20px; }
        .card-content h3 { margin: 0; font-size: 17px; font-weight: 700; color: var(--text-main); }
        .location { font-size: 13px; color: var(--text-light); margin: 8px 0; display: flex; align-items: center; gap: 5px; }
        
        .price-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 15px; padding-top: 15px; border-top: 1px solid #F1F5F9;
        }
        .price { font-size: 18px; font-weight: 800; color: var(--primary); }
        .price span { font-size: 12px; font-weight: 400; color: var(--text-light); }

        /* BOTTOM NAV المطور */
        .bottom-nav {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 400px;
            background: rgba(26, 32, 44, 0.95); backdrop-filter: blur(10px);
            display: flex; justify-content: space-around; padding: 12px;
            border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .nav-item { text-decoration: none; color: #A0AEC0; display: flex; flex-direction: column; align-items: center; gap: 4px; transition: 0.3s; }
        .nav-item i { font-size: 20px; }
        .nav-item span { font-size: 10px; font-weight: 600; }
        .nav-item.active { color: var(--white); }
        .nav-item.active i { transform: translateY(-2px); }

        /* Responsive */
        @media (max-width: 768px) {
            .search-box { grid-template-columns: 1fr; border-radius: 20px; }
            .search-btn { padding: 12px; }
        }
    </style>
</head>

<body>

<header class="main-header">
    <div class="logo-area">
        <h1>Settat Logement</h1>
        <p>Trouvez votre prochain foyer</p>
    </div>

</header>

<div class="search-container">
    <div class="search-box">
        <div class="search-item">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" placeholder="Où cherchez-vous ?">
        </div>
        <div class="search-item">
            <i class="fas fa-home"></i>
            <select>
                <option>Tous types</option>
                <option>Studio</option>
                <option>Appartement</option>
            </select>
        </div>
        <div class="search-item">
            <i class="fas fa-tag"></i>
            <input type="number" placeholder="Budget max">
        </div>
        <button class="search-btn">Rechercher</button>
    </div>
</div>

<div class="annonces-grid">
    <?php foreach ($annonces as $row): ?>
        <?php
        $is_fav = false;
        if(isset($_SESSION['id_user'])){
            $fav_stmt = $pdo->prepare("SELECT 1 FROM favoris WHERE id_user=? AND id_annonce=?");
            $fav_stmt->execute([$_SESSION['id_user'], $row['id_annonce']]);
            $is_fav = $fav_stmt->fetch();
        }
        ?>
        
        <div class="annonce-card" onclick="openAnnonce(<?php echo $row['id_annonce']; ?>)">
            <div class="card-image">
                <img src="uploads/<?php echo $row['image'] ? $row['image'] : 'default.jpg'; ?>">
                <span class="type-tag"><?php echo htmlspecialchars($row['type_logement']); ?></span>
                
                <form action="toggle_favori.php" method="GET" style="margin:0;" onclick="event.stopPropagation();">
                    <input type="hidden" name="id" value="<?php echo $row['id_annonce']; ?>">
                    <button type="submit" class="fav-btn <?php echo $is_fav ? 'active' : ''; ?>">
                        <i class="<?php echo $is_fav ? 'fas' : 'heart-icon far'; ?> fa-heart"></i>
                    </button>
                </form>
            </div>

            <div class="card-content">
                <div style="display:flex; justify-content:space-between;">
                    <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                    <span style="font-size:12px; font-weight:bold;"><i class="fas fa-star" style="color:#F6AD55;"></i> 4.9</span>
                </div>
                
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i> 
                    <?php echo htmlspecialchars($row['localisation']); ?>
                </p>

                <div class="price-row">
                    <div class="price"><?php echo number_format($row['prix'], 0, '.', ' '); ?> <span>DT / mois</span></div>
                    <i class="fas fa-arrow-right" style="color: var(--primary); opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<nav class="bottom-nav">
    <a href="etudiants_recherche.php" class="nav-item active"><i class="fas fa-search"></i><span>Explorer</span></a>
    <a href="etudiants_favoris.php" class="nav-item"><i class="far fa-heart"></i><span>Favoris</span></a>
    <a href="etudiants_message.php" class="nav-item"><i class="far fa-comment-dots"></i><span>Messages</span></a>
    <a href="etudiants_myprofil.php" class="nav-item"><i class="far fa-user"></i><span>Profil</span></a>
</nav>

<script>
    function openAnnonce(id) {
        window.location.href = "annonce_details.php?id=" + id;
    }
</script>

</body>
</html>