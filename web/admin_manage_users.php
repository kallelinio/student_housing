<?php
session_start();
include "database.php";

// 1. حماية الصفحة: التأكد من أن المستخدم مسجل دخول وأنه "Admin"
// ملاحظة: تأكد أنك تخزن نوع الحساب في السيسيون عند تسجيل الدخول باسم 'type_compte'
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// جلب معرف الأدمن الحالي بأمان لتجنب الـ Undefined array key
$current_admin_id = $_SESSION['id_user'] ?? null;

// 2. Action: Supprimer utilisateur
if(isset($_GET['delete'])){
    $id_to_delete = $_GET['delete'];
    
    // منع الأدمن من حذف نفسه برمجياً
    if($id_to_delete != $current_admin_id) {
        $pdo->prepare("DELETE FROM utilisateur WHERE id_user=?")->execute([$id_to_delete]);
        header("Location: admin_manage_users.php?msg=deleted"); 
        exit();
    }
}

// 3. Recherche & Pagination
$search = $_GET['search'] ?? "";
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// 4. Query: جلب المستخدمين مع إحصائياتهم
// استخدمت Subqueries لضمان دقة الأرقام لكل مستخدم
$sql = "
SELECT 
    u.*,
    (SELECT COUNT(*) FROM annonce WHERE id_proprietaire = u.id_user) AS total_annonces,
    (SELECT COUNT(*) FROM favoris WHERE id_user = u.id_user) AS total_favoris
FROM utilisateur u
WHERE u.nom LIKE ? OR u.email LIKE ?
ORDER BY u.id_user DESC
LIMIT $start, $limit
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب المجموع الكلي من أجل الـ Pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE nom LIKE ? OR email LIKE ?");
$count_stmt->execute(["%$search%", "%$search%"]);
$total_rows = $count_stmt->fetchColumn();
$pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs | Admin Panel</title>
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
            --student-color: #8B5CF6; 
            --owner-color: #10B981;   
        }

        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); }

        /* Sidebar */
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

        /* Search Bar */
        .search-card {
            background: white; padding: 15px 20px; border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
            display: flex; gap: 15px; align-items: center;
        }
        .search-card input {
            flex-grow: 1; padding: 12px; border: 1px solid #E2E8F0; border-radius: 12px; outline: none; font-family: inherit;
        }
        .btn-search {
            background: var(--admin-blue); color: white; border: none;
            padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer;
        }

        /* Table Design */
        .table-container {
            background: white; border-radius: 24px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F8FAFC; text-align: left; padding: 18px; font-size: 13px; color: #64748B; border-bottom: 1px solid #EDF2F7; }
        td { padding: 18px; border-bottom: 1px solid #EDF2F7; font-size: 14px; }
        
        /* User Info */
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%; background: #EBF4FF;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: var(--admin-blue); border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* Role Badges */
        .badge {
            padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; text-transform: uppercase;
        }
        .role-etudiant { background: #F5F3FF; color: var(--student-color); }
        .role-proprietaire { background: #ECFDF5; color: var(--owner-color); }
        .role-admin { background: #EFF6FF; color: var(--admin-blue); }

        /* Action Buttons */
        .btn-delete {
            width: 38px; height: 38px; border-radius: 10px; border: none;
            background: #FEE2E2; color: var(--danger);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.2s;
        }
        .btn-delete:hover { background: var(--danger); color: white; transform: scale(1.05); }

        /* Stats Tags */
        .stat-tag {
            font-size: 12px; color: #64748B; background: #F1F5F9;
            padding: 4px 10px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;
        }

        /* Pagination */
        .pagination { margin-top: 30px; display: flex; gap: 8px; justify-content: center; }
        .pagination a {
            padding: 10px 18px; background: white; border-radius: 12px;
            text-decoration: none; color: var(--text-main); font-weight: 700;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .pagination a.active { background: var(--admin-blue); color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-shield-alt"></i> Admin</h2>
    <a href="admin_dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a>
    <a href="admin_manage_annonces.php"><i class="fas fa-home"></i> Annonces</a>
    <a href="admin_manage_users.php" class="active"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="admin_signalements.php"><i class="fas fa-flag"></i> Signalements</a>
    <a href="logout_process.php" style="margin-top: 50px; color: var(--danger);"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="main">
    <div class="header-section">
        <h1 style="font-weight: 800; font-size: 28px;">Gestion Utilisateurs</h1>
        <span style="color: #64748B; font-weight: 600;"><?php echo $total_rows; ?> Comptes au total</span>
    </div>

    <form method="GET" class="search-card">
        <i class="fas fa-search" style="color: #94A3B8;"></i>
        <input type="text" name="search" placeholder="Rechercher par nom ou email..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-search">Rechercher</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Activité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $row): 
                    $role_class = "role-" . strtolower($row['type_compte']);
                    $initial = strtoupper(substr($row['nom'], 0, 1));
                ?>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar"><?php echo $initial; ?></div>
                            <div>
                                <div style="font-weight: 700;"><?php echo htmlspecialchars($row['nom']); ?></div>
                                <div style="font-size: 11px; color: #94A3B8;">ID: #<?php echo $row['id_user']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="color: #64748B;"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <span class="badge <?php echo $role_class; ?>">
                            <?php echo $row['type_compte']; ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <span class="stat-tag" title="Annonces"><i class="fas fa-home"></i> <?php echo $row['total_annonces']; ?></span>
                            <span class="stat-tag" title="Favoris"><i class="fas fa-heart"></i> <?php echo $row['total_favoris']; ?></span>
                        </div>
                    </td>
                    <td>
                        <?php 
                        // تم الإصلاح هنا: استخدام المتغير المحلي الذي تأكدنا من وجوده في بداية الملف
                        if($row['id_user'] != $current_admin_id): ?>
                            <a href="?delete=<?php echo $row['id_user']; ?>" onclick="return confirm('Supprimer cet utilisateur et toutes ses données ?')">
                                <button class="btn-delete" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                            </a>
                        <?php else: ?>
                            <span style="font-size: 12px; color: #94A3B8; font-style: italic; font-weight: 600;">(Vous)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if(empty($users)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748B;">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="<?php if($page==$i) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>