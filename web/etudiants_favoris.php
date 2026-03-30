<?php
session_start();
include "database.php";

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// جلب الإعلانات المفضلة مع التأكد من جلب المسافة والخصائص
$sql = "SELECT a.* FROM annonce a
        JOIN favoris f ON a.id_annonce = f.id_annonce
        WHERE f.id_user = ?
        ORDER BY f.id_favori DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #0052FF;
            --accent: #FF385C;
            --text-main: #1A202C;
            --text-light: #718096;
            --bg: #F8FAFC;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding-bottom: 100px;
            color: var(--text-main);
        }

        /* Header أنيق */
        .fav-header {
            padding: 40px 25px 20px;
            background: white;
            border-bottom: 1px solid #EDF2F7;
        }

        .fav-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .fav-header p {
            font-size: 14px;
            color: var(--text-light);
            margin: 5px 0 0;
            font-weight: 500;
        }

        /* قائمة المفضلة */
        .annonces-list {
            padding: 20px;
            display: grid;
            gap: 20px;
        }

        .modern-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            display: flex; /* تغيير الـ Layout ليكون أفقي في المفضلة */
            height: 160px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
            border: 1px solid #F1F5F9;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }

        .card-image {
            width: 140px;
            min-width: 140px;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .type-tag {
            position: absolute; top: 10px; left: 10px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary);
            padding: 4px 8px; border-radius: 8px;
            font-size: 10px; font-weight: 800;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .remove-fav {
            position: absolute; bottom: 10px; right: 10px;
            background: var(--white); border: none; width: 32px; height: 32px;
            border-radius: 50%; color: var(--accent); display: flex;
            align-items: center; justify-content: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        /* المحتوى */
        .card-content {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-header h3 {
            font-size: 16px;
            margin: 0;
            font-weight: 700;
            color: #2D3748;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .rating { font-size: 12px; font-weight: 700; color: #F6AD55; }

        .location { 
            color: var(--text-light); 
            font-size: 12px; 
            margin: 4px 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .amenities { 
            display: flex; 
            gap: 12px; 
            margin: 8px 0; 
            color: #A0AEC0; 
            font-size: 12px;
        }

        .price { 
            font-size: 17px; 
            font-weight: 800; 
            color: var(--primary); 
            margin: 0;
        }
        .price span { font-size: 11px; font-weight: 400; color: var(--text-light); }

        /* Bottom Nav (نفس الستايل الموحد) */
        .bottom-nav {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 400px;
            background: rgba(26, 32, 44, 0.95); backdrop-filter: blur(10px);
            display: flex; justify-content: space-around; padding: 12px;
            border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .nav-item { text-decoration: none; color: #A0AEC0; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .nav-item i { font-size: 20px; }
        .nav-item span { font-size: 10px; font-weight: 600; }
        .nav-item.active { color: white; }

        /* حالة الصفحة فارغة */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
            color: var(--text-light);
        }
        .empty-state i { font-size: 50px; margin-bottom: 20px; opacity: 0.3; }
    </style>
</head>
<body>

<header class="fav-header">
    <h1>Mes Favoris</h1>
    <p><?php echo count($favoris); ?> logement<?php echo count($favoris) > 1 ? 's' : ''; ?> enregistré<?php echo count($favoris) > 1 ? 's' : ''; ?></p>
</header>

<div class="annonces-list">
    <?php if(count($favoris) > 0): ?>
        <?php foreach($favoris as $row): ?>
        <a href="annonce_details.php?id=<?php echo $row['id_annonce']; ?>" class="modern-card">
            <div class="card-image">
                <img src="uploads/<?php echo $row['image'] ? $row['image'] : 'default.jpg'; ?>">
                <span class="type-tag"><?php echo htmlspecialchars($row['type_logement']); ?></span>
                <form action="toggle_favori.php" method="GET" style="margin:0;" onclick="event.stopPropagation(); event.preventDefault();">
                    <input type="hidden" name="id" value="<?php echo $row['id_annonce']; ?>">
                    <button type="submit" class="remove-fav">
                        <i class="fas fa-heart"></i>
                    </button>
                </form>
            </div>
            
            <div class="card-content">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                    <span class="rating"><i class="fas fa-star"></i> 4.8</span>
                </div>
                
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i> 
                    <?php echo htmlspecialchars($row['localisation']); ?>
                </p>
                
                <div class="amenities">
                    <span title="WiFi"><i class="fas fa-wifi"></i></span>
                    <span title="Meublé"><i class="fas fa-couch"></i></span>
                    <span title="Distance"><i class="fas fa-walking"></i> <?php echo $row['distance_fac']; ?></span>
                </div>
                
                <p class="price"><?php echo number_format($row['prix'], 0); ?> DT<span>/mois</span></p>
            </div>
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="far fa-heart"></i>
            <h3>Aucun favori pour le moment</h3>
            <p>Explorez les annonces et cliquez sur le coeur pour les retrouver ici.</p>
        </div>
    <?php endif; ?>
</div>

<nav class="bottom-nav">
    <a href="etudiants_recherche.php" class="nav-item"><i class="fas fa-search"></i><span>Explorer</span></a>
    <a href="etudiants_favoris.php" class="nav-item active"><i class="fas fa-heart"></i><span>Favoris</span></a>
    <a href="etudiants_message.php" class="nav-item"><i class="far fa-comment-dots"></i><span>Messages</span></a>
    <a href="etudiants_myprofil.php" class="nav-item"><i class="far fa-user"></i><span>Profil</span></a>
</nav>

</body>
</html>