<?php
session_start();
include 'database.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); exit();
}

$id_proprietaire = $_SESSION['id_user'];

// 1. عدد الرسائل المستلمة
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM message WHERE destinataire = ?");
$stmt->execute([$id_proprietaire]);
$total_messages = $stmt->fetch()['total'];

// 2. عدد المرات التي أضيفت فيها إعلاناته للمفضلة
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM favoris f
    INNER JOIN annonce a ON f.id_annonce = a.id_annonce
    WHERE a.id_proprietaire = ?
");
$stmt->execute([$id_proprietaire]);
$total_favoris = $stmt->fetch()['total'];

// 3. الإعلانات النشطة (آخر 3)
$stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_proprietaire = ? AND statut = 'Publiee' ORDER BY id_annonce DESC LIMIT 3");
$stmt->execute([$id_proprietaire]);
$annonces = $stmt->fetchAll();

// 4. النشاطات الأخيرة
$stmtMsg = $pdo->prepare("SELECT 'message' AS type, contenu AS texte, date FROM message WHERE destinataire = ? ORDER BY date DESC LIMIT 2");
$stmtMsg->execute([$id_proprietaire]);
$recent_messages = $stmtMsg->fetchAll();

$stmtFav = $pdo->prepare("
    SELECT 'favori' AS type, a.titre AS texte, f.id_favori AS sort_date
    FROM favoris f
    INNER JOIN annonce a ON f.id_annonce = a.id_annonce
    WHERE a.id_proprietaire = ?
    ORDER BY f.id_favori DESC LIMIT 2
");
$stmtFav->execute([$id_proprietaire]);
$recent_favs = $stmtFav->fetchAll();

$activities = array_merge($recent_messages, $recent_favs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Propriétaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #0052FF;
            --success: #00BA88;
            --danger: #F65050;
            --bg: #F8FAFC;
            --white: #ffffff;
            --text-main: #1A202C;
            --text-light: #718096;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            margin: 0;
            padding-bottom: 110px;
            color: var(--text-main);
        }

        /* Header عصري */
        .page-header {
            background: var(--primary);
            color: white;
            padding: 50px 25px 90px;
        }

        .page-header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .page-header p { margin: 5px 0 0; opacity: 0.8; font-size: 14px; }

        .container {
            max-width: 500px;
            margin: -50px auto 0;
            padding: 0 20px;
        }

        /* شبكة الإحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 24px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .icon-box {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .icon-msg { background: #EBF4FF; color: var(--primary); }
        .icon-fav { background: #FFF0F0; color: var(--danger); }

        .stat-val { font-size: 22px; font-weight: 800; margin: 0; }
        .stat-label { font-size: 12px; color: var(--text-light); font-weight: 600; }

        /* الأقسام */
        .dashboard-card {
            background: var(--white);
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            margin-bottom: 20px;
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 { font-size: 17px; font-weight: 700; margin: 0; }
        .view-all { font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600; }

        /* قائمة الإعلانات */
        .annonce-mini-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #F1F5F9;
        }
        .annonce-mini-item:last-child { border-bottom: none; }

        .mini-img { width: 50px; height: 50px; border-radius: 12px; object-fit: cover; }
        .mini-info { flex-grow: 1; min-width: 0; }
        .mini-info h4 { margin: 0; font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mini-info p { margin: 2px 0 0; font-size: 11px; color: var(--text-light); }

        .status-dot {
            width: 8px; height: 8px; border-radius: 50%; background: var(--success);
            box-shadow: 0 0 0 3px #E6F8F3;
        }

        /* قائمة النشاطات */
        .activity-row {
            display: flex; gap: 12px; padding: 12px 0;
            align-items: flex-start;
        }
        .act-icon {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0;
        }
        .act-msg { background: #EBF4FF; color: var(--primary); }
        .act-fav { background: #FFF0F0; color: var(--danger); }
        .act-text { font-size: 13px; line-height: 1.4; color: #4A5568; }
        .act-text b { color: var(--text-main); }

        /* Floating Action Button */
        .fab-add {
            position: fixed; bottom: 100px; right: 25px;
            width: 60px; height: 60px; background: var(--primary);
            color: white; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-size: 24px;
            box-shadow: 0 10px 25px rgba(0,82,255,0.4);
            text-decoration: none; z-index: 999;
            transition: 0.3s;
        }
        .fab-add:active { transform: scale(0.9); }

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

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Ravi de vous revoir !</p>
</div>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon-box icon-msg"><i class="fas fa-envelope"></i></div>
            <p class="stat-val"><?php echo $total_messages; ?></p>
            <p class="stat-label">Messages reçus</p>
        </div>
        <div class="stat-card">
            <div class="icon-box icon-fav"><i class="fas fa-heart"></i></div>
            <p class="stat-val"><?php echo $total_favoris; ?></p>
            <p class="stat-label">Coups de coeur</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h2>Mes annonces</h2>
            <a href="proprietaire_myannonces.php" class="view-all">Gérer tout</a>
        </div>
        
        <?php foreach($annonces as $a): ?>
        <div class="annonce-mini-item">
            <img src="uploads/<?php echo $a['image']; ?>" class="mini-img">
            <div class="mini-info">
                <h4><?php echo htmlspecialchars($a['titre']); ?></h4>
                <p><?php echo htmlspecialchars($a['localisation']); ?> • <?php echo $a['prix']; ?> DT</p>
            </div>
            <div class="status-dot"></div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($annonces)): ?>
            <p style="text-align:center; color:var(--text-light); font-size:13px;">Aucune annonce active.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h2>Activité récente</h2>
        </div>
        
        <div class="activity-list">
            <?php foreach($activities as $act): ?>
            <div class="activity-row">
                <div class="act-icon <?php echo $act['type'] == 'message' ? 'act-msg' : 'act-fav'; ?>">
                    <i class="fas <?php echo $act['type'] == 'message' ? 'fa-comment' : 'fa-heart'; ?>"></i>
                </div>
                <div class="act-text">
                    <?php if($act['type'] == 'message'): ?>
                        Nouveau message : "<b><?php echo htmlspecialchars(substr($act['texte'], 0, 40)); ?>...</b>"
                    <?php else: ?>
                        Quelqu'un a ajouté <b><?php echo htmlspecialchars($act['texte']); ?></b> à ses favoris.
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<a href="proprietaire_publish_annonce.php" class="fab-add" title="Publier une annonce">
    <i class="fas fa-plus"></i>
</a>
<nav class="bottom-nav">
    <a href="proprietaire_dashboard.php" class="nav-item active">
        <i class="fas fa-chart-pie"></i>
        <span>Stats</span>
    </a>
    <a href="proprietaire_myannonces.php" class="nav-item">
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

</body>
</html>