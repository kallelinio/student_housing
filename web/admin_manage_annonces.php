<?php
session_start();
include "database.php";

// 1. Actions (Logic)
if(isset($_GET['approve'])){
    $id=$_GET['approve'];
    $pdo->prepare("UPDATE annonce SET statut='Publiee' WHERE id_annonce=?")->execute([$id]);
    header("Location: admin_manage_annonces.php"); exit();
}

if(isset($_GET['reject'])){
    $id=$_GET['reject'];
    $pdo->prepare("UPDATE annonce SET statut='Refusee' WHERE id_annonce=?")->execute([$id]);
    header("Location: admin_manage_annonces.php"); exit();
}

if(isset($_GET['delete'])){
    $id=$_GET['delete'];
    $pdo->prepare("DELETE FROM annonce WHERE id_annonce=?")->execute([$id]);
    header("Location: admin_manage_annonces.php"); exit();
}

// 2. Recherche & Filtres
$search = $_GET['search'] ?? "";
$filter = $_GET['statut'] ?? "";

// 3. Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// 4. Query Construction
$query = "SELECT * FROM annonce WHERE 1=1";
$params = [];

if($search != ""){
    $query .= " AND titre LIKE ?";
    $params[] = "%$search%";
}
if($filter != ""){
    $query .= " AND statut = ?";
    $params[] = $filter;
}

// Count total for pagination
$count_stmt = $pdo->prepare($query);
$count_stmt->execute($params);
$total = $count_stmt->rowCount();
$pages = ceil($total / $limit);

$query .= " ORDER BY date_publication DESC LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Annonces | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --admin-dark: #0F172A;
            --admin-blue: #0052FF;
            --bg: #F1F5F9;
            --white: #ffffff;
            --text-main: #1E293B;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
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

        /* Search Bar & Filters */
        .filter-card {
            background: white; padding: 20px; border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
            display: flex; gap: 15px; align-items: center;
        }
        .filter-card input, .filter-card select {
            padding: 10px 15px; border: 1px solid #E2E8F0; border-radius: 10px; outline: none; font-family: inherit;
        }
        .filter-card input { flex-grow: 1; }
        .btn-search {
            background: var(--admin-blue); color: white; border: none;
            padding: 10px 25px; border-radius: 10px; font-weight: 700; cursor: pointer;
        }

        /* Table Design */
        .table-container {
            background: white; border-radius: 20px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F8FAFC; text-align: left; padding: 18px; font-size: 13px; color: #64748B; border-bottom: 1px solid #EDF2F7; }
        td { padding: 18px; border-bottom: 1px solid #EDF2F7; font-size: 14px; }
        
        .img-preview {
            width: 60px; height: 45px; border-radius: 8px; object-fit: cover;
            cursor: pointer; transition: 0.2s;
        }
        .img-preview:hover { transform: scale(1.1); }

        /* Status Badges */
        .badge {
            padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 700;
        }
        .Publiee { background: #DCFCE7; color: #166534; }
        .En { background: #FEF3C7; color: #92400E; } /* "En attente" */
        .Refusee { background: #FEE2E2; color: #991B1B; }

        /* Action Buttons */
        .actions { display: flex; gap: 8px; }
        .action-btn {
            width: 35px; height: 35px; border-radius: 10px; border: none;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.2s; color: white;
        }
        .btn-approve { background: var(--success); }
        .btn-reject { background: var(--warning); }
        .btn-delete { background: var(--danger); }
        .action-btn:hover { transform: translateY(-2px); opacity: 0.9; }

        /* Pagination */
        .pagination { margin-top: 25px; display: flex; gap: 8px; justify-content: center; }
        .pagination a {
            padding: 8px 16px; background: white; border-radius: 10px;
            text-decoration: none; color: var(--text-main); font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .pagination a.active { background: var(--admin-blue); color: white; }

        /* Modal / Popup */
        .modal {
            display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px); z-index: 1000; justify-content: center; align-items: center;
        }
        .modal-content {
            background: white; padding: 30px; border-radius: 24px; width: 90%; max-width: 500px;
            position: relative; animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-content img { width: 100%; height: 250px; object-fit: cover; border-radius: 15px; margin-bottom: 20px; }
        .close-modal { position: absolute; top: 20px; right: 20px; font-size: 24px; cursor: pointer; color: #64748B; }

    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-shield-alt"></i>Admin</h2>
    <a href="admin_dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a>
    <a href="admin_manage_annonces.php" class="active"><i class="fas fa-home"></i> Annonces</a>
    <a href="admin_manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="admin_signalements.php"><i class="fas fa-flag"></i> Signalements</a>
    <a href="logout_process.php" style="margin-top: 50px; color: var(--danger);"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="main">
    <div class="header-section">
        <h1 style="font-weight: 800; font-size: 28px;">Gestion des Annonces</h1>
        <span style="color: #64748B; font-weight: 600;"><?php echo $total; ?> Annonces au total</span>
    </div>

    <form method="GET" class="filter-card">
        <i class="fas fa-search" style="color: #94A3B8;"></i>
        <input type="text" name="search" placeholder="Rechercher par titre..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="Publiee" <?php if($filter=='Publiee') echo 'selected'; ?>>Publiée</option>
            <option value="En attente" <?php if($filter=='En attente') echo 'selected'; ?>>En attente</option>
            <option value="Refusee" <?php if($filter=='Refusee') echo 'selected'; ?>>Refusée</option>
        </select>
        <button type="submit" class="btn-search">Filtrer</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Ville</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($annonces as $row): ?>
                <tr>
                    <td>
                        <img src="uploads/<?php echo $row['image']; ?>" class="img-preview" 
                             onclick="openPreview('<?php echo addslashes($row['titre']); ?>', '<?php echo addslashes($row['description']); ?>', '<?php echo $row['prix']; ?>', '<?php echo addslashes($row['localisation']); ?>', 'uploads/<?php echo $row['image']; ?>')">
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['titre']); ?></strong></td>
                    <td style="font-weight: 700; color: var(--admin-blue);"><?php echo number_format($row['prix'], 0); ?> DT</td>
                    <td><i class="fas fa-map-marker-alt" style="color: #94A3B8;"></i> <?php echo htmlspecialchars($row['localisation']); ?></td>
                    <td>
                        <span class="badge <?php echo explode(' ', $row['statut'])[0]; ?>">
                            <?php echo $row['statut']; ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?php if($row['statut'] != 'Publiee'): ?>
                        <a href="?approve=<?php echo $row['id_annonce']; ?>" title="Approuver"><button class="action-btn btn-approve"><i class="fas fa-check"></i></button></a>
                        <?php endif; ?>
                        
                        <?php if($row['statut'] == 'En attente'): ?>
                        <a href="?reject=<?php echo $row['id_annonce']; ?>" title="Refuser"><button class="action-btn btn-reject"><i class="fas fa-times"></i></button></a>
                        <?php endif; ?>

                        <a href="?delete=<?php echo $row['id_annonce']; ?>" title="Supprimer" onclick="return confirm('Supprimer définitivement ?')"><button class="action-btn btn-delete"><i class="fas fa-trash"></i></button></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&statut=<?php echo $filter; ?>" class="<?php if($page==$i) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<div id="previewModal" class="modal" onclick="closePreview()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="close-modal" onclick="closePreview()">&times;</span>
        <img id="p_image" src="">
        <h2 id="p_titre" style="margin: 0 0 10px 0; font-weight: 800;"></h2>
        <p id="p_description" style="color: #64748B; line-height: 1.6; font-size: 14px; margin-bottom: 20px;"></p>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #F1F5F9; padding-top: 15px;">
            <span style="font-weight: 800; color: var(--admin-blue); font-size: 20px;" id="p_prix"></span>
            <span style="color: #64748B; font-size: 13px;" id="p_localisation"></span>
        </div>
    </div>
</div>

<script>
function openPreview(titre, description, prix, localisation, image) {
    document.getElementById("previewModal").style.display = "flex";
    document.getElementById("p_titre").innerText = titre;
    document.getElementById("p_description").innerText = description;
    document.getElementById("p_prix").innerText = prix + " DT";
    document.getElementById("p_localisation").innerText = localisation;
    document.getElementById("p_image").src = image;
}

function closePreview() {
    document.getElementById("previewModal").style.display = "none";
}
</script>

</body>
</html>