<?php 
session_start();
include "database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); exit();
}

$id_user = $_SESSION['id_user'];

// جلب الإعلانات مع ترتيب الأحدث أولاً
$stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_proprietaire = ? ORDER BY date_publication DESC");
$stmt->execute([$id_user]);
$annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Annonces | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #0052FF;
            --success: #00BA88;
            --warning: #F4A100;
            --danger: #FF3B30;
            --bg: #F8FAFC;
            --white: #ffffff;
            --text-main: #1A202C;
            --text-light: #718096;
            --shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg); 
            margin: 0; 
            padding-bottom: 110px; 
            color: var(--text-main);
        }

        /* 1. الهيدر الموحد */
        .blue-header {
            background: var(--primary);
            color: white;
            padding: 50px 25px 90px;
            text-align: left;
        }
        .blue-header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .blue-header p { margin: 5px 0 0; font-size: 14px; opacity: 0.85; }

        .container {
            max-width: 500px;
            margin: -60px auto 0;
            padding: 0 20px;
        }

        /* 2. الحاوية البيضاء الرئيسية */
        .white-wrapper {
            background: var(--white);
            border-radius: 24px;
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #F1F5F9;
        }
        .section-header h2 { font-size: 18px; font-weight: 800; margin: 0; }
        .count-tag { 
            font-size: 12px; 
            color: var(--primary); 
            background: #EBF2FF; 
            padding: 6px 12px; 
            border-radius: 30px; 
            font-weight: 700;
        }

        /* 3. كروت الإعلانات (Annonce Cards) */
        .annonces-grid { display: flex; flex-direction: column; gap: 20px; }

        .annonce-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #F1F5F9;
            transition: transform 0.2s;
            position: relative;
        }

        .image-container { 
            position: relative; 
            height: 180px; 
            width: 100%; 
            cursor: pointer;
            overflow: hidden;
        }
        .image-container img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .annonce-card:hover .image-container img { transform: scale(1.05); }

        /* الشارات (Badges) */
        .badge {
            position: absolute; top: 12px; left: 12px;
            padding: 5px 12px; border-radius: 30px; font-size: 10px; font-weight: 800; text-transform: uppercase;
        }
        .badge-active { background: var(--success); color: white; }
        .badge-pending { background: var(--warning); color: white; }

        /* قائمة الخيارات (Dropdown) */
        .options-menu {
            position: absolute; top: 12px; right: 12px;
        }
        .dots-btn {
            background: rgba(255, 255, 255, 0.9); border: none; width: 35px; height: 35px;
            border-radius: 50%; cursor: pointer; color: var(--text-main);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px);
        }

        .dropdown-content {
            display: none; position: absolute; top: 40px; right: 0;
            background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-width: 140px; z-index: 100; overflow: hidden;
        }
        .dropdown-content a {
            display: flex; align-items: center; gap: 10px; padding: 12px 15px; 
            text-decoration: none; color: var(--text-main); font-size: 13px; font-weight: 600;
            transition: 0.2s;
        }
        .dropdown-content a:hover { background: #F8FAFC; color: var(--primary); }
        .dropdown-content a.delete-link { color: var(--danger); }
        .dropdown-content a.delete-link:hover { background: #FFF5F5; }

        /* Card Content */
        .card-body { padding: 18px; }
        .card-body h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .location { font-size: 13px; color: var(--text-light); margin: 6px 0 12px; display: flex; align-items: center; gap: 4px; }
        .price { font-size: 18px; font-weight: 800; color: var(--primary); margin: 0; }
        .price span { font-size: 12px; font-weight: 400; color: var(--text-light); }

        .card-footer {
            margin-top: 15px; padding-top: 15px; border-top: 1px solid #F1F5F9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .stats-group { display: flex; gap: 12px; }
        .stat-item { font-size: 11px; color: var(--text-light); display: flex; align-items: center; gap: 4px; font-weight: 600; }
        .type-tag { font-size: 10px; background: #F8FAFC; padding: 4px 10px; border-radius: 30px; color: var(--text-light); font-weight: 700; border: 1px solid #E2E8F0; }

        /* Floating Button & Bottom Nav */
        .fab-add {
            position: fixed; bottom: 100px; right: 25px;
            width: 60px; height: 60px; background: var(--primary);
            color: white; border-radius: 50%; font-size: 24px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(0,82,255,0.4); text-decoration: none; z-index: 999;
        }

        /* --- Bottom Nav Styles --- */
.bottom-nav {
    position: fixed; 
    bottom: 20px; 
    left: 50%; 
    transform: translateX(-50%);
    width: 90%; 
    max-width: 400px;
    background: rgba(26, 32, 44, 0.95); /* لون داكن شفاف */
    backdrop-filter: blur(12px); /* تأثير الضباب الخلفي */
    display: flex; 
    justify-content: space-around; 
    padding: 14px;
    border-radius: 35px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    z-index: 1000;
}

.nav-item { 
    text-decoration: none; 
    color: #718096; 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 5px; 
}

.nav-item i { 
    font-size: 20px; 
}

.nav-item span { 
    font-size: 10px; 
    font-weight: 700; 
}

.nav-item.active { 
    color: white; /* اللون الأبيض للعنصر النشط */
}
    </style>
</head>
<body>

<div class="blue-header">
    <h1>Mes Annonces</h1>
    <p>Gérez vos biens immobiliers en un clic</p>
</div>

<div class="container">
    <div class="white-wrapper">
        <div class="section-header">
            <h2>Liste des biens</h2>
            <span class="count-tag"><?php echo count($annonces); ?> Total</span>
        </div>

        <div class="annonces-grid">
            <?php foreach ($annonces as $row): ?>
            <div class="annonce-card">
                <div class="image-container">
                    <img src="uploads/<?php echo $row['image'] ? $row['image'] : 'default.jpg'; ?>" 
                         onclick="location.href='annonce_details.php?id=<?php echo $row['id_annonce']; ?>'">
                    
                    <span class="badge <?php echo ($row['statut'] == 'Publiee') ? 'badge-active' : 'badge-pending'; ?>">
                        <?php echo ($row['statut'] == 'Publiee') ? 'Publiée' : 'En attente'; ?>
                    </span>

                    <div class="options-menu">
                        <button class="dots-btn" onclick="toggleMenu(event, this)">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="modifier_annonce.php?id=<?php echo $row['id_annonce']; ?>"><i class="fas fa-edit"></i> Modifier</a>
                            <a href="supprimer_annonce.php?id=<?php echo $row['id_annonce']; ?>" class="delete-link" onclick="return confirm('Voulez-vous vraiment supprimer cet annonce ?')"><i class="fas fa-trash-alt"></i> Supprimer</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['localisation']); ?></p>
                    <p class="price"><?php echo number_format($row['prix'], 0, '.', ' '); ?> DT <span>/ mois</span></p>
                    
                    <div class="card-footer">
                        <div class="stats-group">
                            <div class="stat-item"><i class="far fa-eye"></i> 0</div>
                            <div class="stat-item"><i class="far fa-heart"></i> 0</div>
                        </div>
                        <span class="type-tag"><?php echo htmlspecialchars($row['type_logement']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($annonces)): ?>
                <div style="text-align:center; padding: 40px 0;">
                    <i class="fas fa-home" style="font-size: 50px; color: #E2E8F0; margin-bottom: 15px;"></i>
                    <p style="color: var(--text-light);">Vous n'avez pas encore d'annonces.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<a href="proprietaire_publish_annonce.php" class="fab-add"><i class="fas fa-plus"></i></a>

<nav class="bottom-nav">
    <a href="proprietaire_dashboard.php" class="nav-item">
        <i class="fas fa-chart-pie"></i>
        <span>Stats</span>
    </a>
    <a href="proprietaire_myannonces.php" class="nav-item active">
        <i class="fas fa-house-user"></i>
        <span>Annonces</span>
    </a>
    <a href="proprietaire_messages.php" class="nav-item">
        <i class="fas fa-comment-dots"></i>
        <span>Messages</span>
    </a>
    <a href="proprietaire_myprofil.php" class="nav-item">
        <i class="fas fa-user-circle"></i>
        <span>Profil</span>
    </a>
</nav>
<script>
function toggleMenu(e, btn) {
    e.stopPropagation();
    const dropdown = btn.nextElementSibling;
    document.querySelectorAll('.dropdown-content').forEach(d => {
        if(d !== dropdown) d.style.display = 'none';
    });
    dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
}

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
});
</script>

</body>
</html>