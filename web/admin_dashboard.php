<?php
session_start();
include "database.php";

// التثبت من أن المستخدم أدمن (اختياري حسب نظامك)
// if($_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

/* إحصائيات محسنة */
$total_users = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$total_annonces = $pdo->query("SELECT COUNT(*) FROM annonce")->fetchColumn();
$annonces_pending = $pdo->query("SELECT COUNT(*) FROM annonce WHERE statut='En attente'")->fetchColumn();
$total_signalements = $pdo->query("SELECT COUNT(*) FROM message WHERE contenu LIKE '%alerte%' OR contenu LIKE '%signaler%'")->fetchColumn(); // مثال بسيط

// جلب آخر 5 إعلانات مسجلة للمراجعة السريعة
$stmt_recent = $pdo->query("SELECT a.*, u.nom FROM annonce a JOIN utilisateur u ON a.id_proprietaire = u.id_user ORDER BY date_publication DESC LIMIT 5");
$recent_annonces = $stmt_recent->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Control Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --admin-dark: #0F172A;
            --admin-blue: #0052FF;
            --bg: #F1F5F9;
            --white: #ffffff;
            --text-main: #1E293B;
            --accent: #F43F5E; /* وردي للتنبيهات */
        }

        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); }

        /* Sidebar المطور */
        .sidebar {
            width: 260px; height: 100vh; background: var(--admin-dark);
            color: white; position: fixed; padding: 30px 20px;
            box-sizing: border-box;
        }
        .sidebar h2 { 
            font-size: 22px; font-weight: 800; margin-bottom: 40px; 
            display: flex; align-items: center; gap: 10px; color: var(--admin-blue);
        }
        .sidebar a {
            display: flex; align-items: center; gap: 12px; color: #94A3B8;
            text-decoration: none; padding: 12px 15px; border-radius: 12px;
            margin-bottom: 8px; transition: 0.3s; font-weight: 600;
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: white; }
        .sidebar a i { width: 20px; }
        .logout { margin-top: 50px; color: var(--accent) !important; }

        /* Main Content */
        .main { margin-left: 260px; padding: 40px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-top h1 { font-weight: 800; font-size: 28px; margin: 0; }

        /* Cards Grid */
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .card {
            background: var(--white); padding: 25px; border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 20px;
        }
        .card-icon {
            width: 50px; height: 50px; border-radius: 15px;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .blue-icon { background: #EBF4FF; color: var(--admin-blue); }
        .orange-icon { background: #FFF7ED; color: #F97316; }
        .red-icon { background: #FFF1F2; color: var(--accent); }

        .card-info h3 { margin: 0; font-size: 14px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-info p { margin: 5px 0 0; font-size: 24px; font-weight: 800; }

        /* Table Section */
        .recent-section { margin-top: 40px; background: white; padding: 25px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .recent-section h2 { font-size: 18px; font-weight: 800; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #F1F5F9; color: #64748B; font-size: 13px; }
        td { padding: 15px 12px; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        
        .status-badge {
            padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 700;
        }
        .pending { background: #FEF3C7; color: #92400E; }
        .published { background: #DCFCE7; color: #166534; }

        .btn-view { color: var(--admin-blue); text-decoration: none; font-weight: 700; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-shield-alt"></i> Admin</h2>
    <a href="admin_dashboard.php" class="active"><i class="fas fa-grid-2"></i> Dashboard</a>
    <a href="admin_manage_annonces.php"><i class="fas fa-home"></i> Annonces</a>
    <a href="admin_manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="admin_signalements.php"><i class="fas fa-flag"></i> Signalements 
        <?php if($total_signalements > 0): ?> <span style="background:var(--accent); color:white; padding:2px 7px; border-radius:50%; font-size:10px; margin-left:auto;"><?php echo $total_signalements; ?></span> <?php endif; ?>
    </a>
    <a href="logout_process.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="main">
    <div class="header-top">
        <h1>Dashboard</h1>
        <div style="font-size: 14px; color: #64748B; font-weight: 600;">
            <i class="far fa-calendar-alt"></i> <?php echo date('d M Y'); ?>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="card-icon blue-icon"><i class="fas fa-users"></i></div>
            <div class="card-info">
                <h3>Utilisateurs</h3>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon orange-icon"><i class="fas fa-home"></i></div>
            <div class="card-info">
                <h3>Annonces</h3>
                <p><?php echo $total_annonces; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon red-icon"><i class="fas fa-clock"></i></div>
            <div class="card-info">
                <h3>En attente</h3>
                <p><?php echo $annonces_pending; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon blue-icon" style="background: #F0F9FF; color: #0EA5E9;"><i class="fas fa-flag"></i></div>
            <div class="card-info">
                <h3>Signalements</h3>
                <p>0</p>
            </div>
        </div>
    </div>

    <div class="recent-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Dernières Annonces</h2>
            <a href="admin_manage_annonces.php" class="btn-view">Voir tout</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Annonce</th>
                    <th>Propriétaire</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_annonces as $a): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($a['titre']); ?></strong></td>
                    <td><?php echo htmlspecialchars($a['nom']); ?></td>
                    <td><?php echo number_format($a['prix'], 0); ?> DT</td>
                    <td>
                        <span class="status-badge <?php echo ($a['statut'] == 'En attente') ? 'pending' : 'published'; ?>">
                            <?php echo $a['statut']; ?>
                        </span>
                    </td>
                    <td><a href="admin_manage_annonces.php?id=<?php echo $a['id_annonce']; ?>" class="btn-view">Gérer</a></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($recent_annonces)): ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">Aucune donnée disponible.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>