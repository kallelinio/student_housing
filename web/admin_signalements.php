<?php
session_start();
include "database.php";

// 1. حماية الصفحة
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// 2. Actions (العمليات)
// حذف البلاغ فقط (إغلاق القضية)
if(isset($_GET['delete_report'])){
    $id = $_GET['delete_report'];
    $pdo->prepare("DELETE FROM signalements WHERE id_signalement=?")->execute([$id]);
    header("Location: admin_signalements.php?status=report_removed"); exit();
}

// حذف الإعلان المبلّغ عنه (وبالتالي سيحذف البلاغ تلقائياً إذا كان لديك ON DELETE CASCADE)
if(isset($_GET['delete_annonce'])){
    $id_annonce = $_GET['delete_annonce'];
    $pdo->prepare("DELETE FROM annonce WHERE id_annonce=?")->execute([$id_annonce]);
    header("Location: admin_signalements.php?status=annonce_deleted"); exit();
}

// 3. جلب البلاغات مع تفاصيل المُبلّغ والإعلان
$sql = "
    SELECT 
        s.*, 
        a.titre AS annonce_titre, 
        u.nom AS user_nom,
        u.email AS user_email
    FROM signalements s
    JOIN annonce a ON s.id_annonce = a.id_annonce
    JOIN utilisateur u ON s.id_user = u.id_user
    ORDER BY s.date_signalement DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$signalements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Signalements | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --admin-dark: #0F172A;
            --admin-blue: #0052FF;
            --bg: #F1F5F9;
            --white: #ffffff;
            --text-main: #1E293B;
            --danger: #EF4444;
            --warning: #F59E0B;
            --success: #10B981;
        }

        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); }

        /* Sidebar الموحد */
        .sidebar {
            width: 260px; height: 100vh; background: var(--admin-dark);
            color: white; position: fixed; padding: 30px 20px; box-sizing: border-box;
        }
        .sidebar h2 { font-size: 22px; font-weight: 800; margin-bottom: 40px; color: var(--admin-blue); display: flex; align-items: center; gap: 10px; }
        .sidebar a {
            display: flex; align-items: center; gap: 12px; color: #94A3B8;
            text-decoration: none; padding: 12px 15px; border-radius: 12px;
            margin-bottom: 8px; transition: 0.3s; font-weight: 600;
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: white; }

        /* Main Content */
        .main { margin-left: 260px; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        /* Notification Badge */
        .count-badge {
            background: var(--danger); color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 800;
        }

        /* Table Design */
        .table-container {
            background: white; border-radius: 24px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F8FAFC; text-align: left; padding: 18px; font-size: 13px; color: #64748B; border-bottom: 1px solid #EDF2F7; }
        td { padding: 18px; border-bottom: 1px solid #EDF2F7; font-size: 14px; vertical-align: middle; }
        
        /* User and Annonce Info */
        .info-box { display: flex; flex-direction: column; }
        .info-box .primary { font-weight: 700; color: var(--text-main); }
        .info-box .secondary { font-size: 12px; color: #94A3B8; }

        /* Reason Badge */
        .reason-text {
            background: #FFF1F2; color: #BE123C; padding: 6px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 600; border-left: 4px solid var(--danger);
        }

        /* Action Buttons */
        .actions { display: flex; gap: 10px; }
        .btn-action {
            padding: 8px 14px; border-radius: 10px; border: none; font-weight: 700;
            font-size: 12px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px;
        }
        .btn-delete-annonce { background: #FEE2E2; color: var(--danger); }
        .btn-delete-annonce:hover { background: var(--danger); color: white; }
        
        .btn-ignore { background: #F1F5F9; color: #64748B; }
        .btn-ignore:hover { background: #E2E8F0; color: var(--text-main); }

        /* Empty State */
        .empty-state { padding: 60px; text-align: center; color: #94A3B8; }
        .empty-state i { font-size: 50px; margin-bottom: 20px; opacity: 0.3; }

    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-shield-alt"></i> Admin</h2>
    <a href="admin_dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a>
    <a href="admin_manage_annonces.php"><i class="fas fa-home"></i> Annonces</a>
    <a href="admin_manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="admin_signalements.php" class="active"><i class="fas fa-flag"></i> Signalements</a>
    <a href="logout_process.php" style="margin-top: 50px; color: var(--danger);"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="main">
    <div class="header-section">
        <div>
            <h1 style="font-weight: 800; font-size: 28px; margin: 0;">Signalements</h1>
            <p style="color: #64748B; margin: 5px 0 0;">Gérer les plaintes des utilisateurs</p>
        </div>
        <div class="count-badge">
            <?php echo count($signalements); ?> Plaintes
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Annonce Ciblée</th>
                    <th>Motif du Signalement</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($signalements as $row): ?>
                <tr>
                    <td>
                        <div class="info-box">
                            <span class="primary"><?php echo htmlspecialchars($row['user_nom']); ?></span>
                            <span class="secondary"><?php echo htmlspecialchars($row['user_email']); ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="info-box">
                            <span class="primary"><?php echo htmlspecialchars($row['annonce_titre']); ?></span>
                            <span class="secondary">ID Annonce: #<?php echo $row['id_annonce']; ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="reason-text">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($row['raison']); ?>
                        </div>
                    </td>
                    <td style="color: #64748B; font-size: 13px;">
                        <?php echo date('d/m/Y H:i', strtotime($row['date_signalement'])); ?>
                    </td>
                    <td class="actions">
                        <a href="?delete_annonce=<?php echo $row['id_annonce']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette annonce ?')">
                            <button class="btn-action btn-delete-annonce">
                                <i class="fas fa-trash"></i> Supprimer l'annonce
                            </button>
                        </a>

                        <a href="?delete_report=<?php echo $row['id_signalement']; ?>" onclick="return confirm('Ignorer ce signalement ?')">
                            <button class="btn-action btn-ignore">
                                <i class="fas fa-eye-slash"></i> Ignorer
                            </button>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if(empty($signalements)): ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>Aucun signalement en attente</h3>
                            <p>Tout semble être en ordre dans votre communauté.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>