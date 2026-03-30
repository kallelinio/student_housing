<?php
session_start();
require_once 'database.php';

// التثبت من تسجيل الدخول
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

try {
    // جلب معلومات المستخدم
    $stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Utilisateur non trouvé.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0052FF;
            --bg: #F8FAFF;
            --white: #ffffff;
            --text-dark: #1A202C;
            --text-muted: #718096;
            --border: #E2E8F0;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; color: var(--text-dark); }
        
        .blue-header { background: var(--primary); color: white; padding: 40px 20px 80px; text-align: center; }
        
        .container { max-width: 600px; margin: -50px auto 50px; padding: 0 15px; }

        .profile-card {
            background: var(--white); border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        /* الجزء العلوي للبروفايل */
        .profile-info-header { text-align: center; margin-bottom: 30px; }
        .avatar-circle {
            width: 80px; height: 80px; background: #E0E7FF; color: var(--primary);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 30px; font-weight: bold; margin: 0 auto 10px;
        }
        .role-badge {
            display: inline-block; padding: 5px 15px; border-radius: 20px;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            background: #EBF4FF; color: var(--primary);
        }

        /* الفورم */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); }
        .form-group input {
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 12px;
            font-size: 14px; background: #FBFCFE; box-sizing: border-box;
        }
        .form-group input:read-only { background: #f1f5f9; cursor: not-allowed; }

        .btn-update {
            width: 100%; padding: 15px; background: var(--primary); color: white;
            border: none; border-radius: 12px; font-size: 16px; font-weight: 700;
            cursor: pointer; transition: 0.3s;
        }
        .btn-update:hover { opacity: 0.9; }

        .status-box {
            padding: 10px; border-radius: 10px; font-size: 13px; text-align: center; margin-top: 10px;
        }
        .status-approved { background: #DCFCE7; color: #166534; }
        .status-pending { background: #FEF3C7; color: #92400E; }
    </style>
</head>
<body>

<div class="blue-header">
    <h1>Mon Profil</h1>
    <p>Gérez vos informations personnelles</p>
</div>

<div class="container">
    <div class="profile-card">
        <div class="profile-info-header">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($user['nom'], 0, 1)); ?>
            </div>
            <h3><?php echo htmlspecialchars($user['nom']); ?></h3>
            <span class="role-badge"><?php echo $user['type_compte']; ?></span>
            
            <?php if($user['type_compte'] == 'Proprietaire'): ?>
                <div class="status-box <?php echo ($user['is_approved'] == 1) ? 'status-approved' : 'status-pending'; ?>">
                    <i class="fas <?php echo ($user['is_approved'] == 1) ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                    <?php echo ($user['is_approved'] == 1) ? 'Compte Vérifié' : 'Vérification en attente'; ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="update_profile_process.php" method="POST">
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email (Non modifiable)</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
            </div>

            <div class="form-group">
                <label>Genre</label>
                <input type="text" value="<?php echo $user['genre']; ?>" readonly>
            </div>

            <?php if($user['type_compte'] == 'Etudiant'): ?>
                <div class="form-group">
                    <label>Faculté / Université</label>
                    <input type="text" name="faculte" value="<?php echo htmlspecialchars($user['faculte']); ?>">
                </div>
            <?php endif; ?>

            <?php if($user['type_compte'] == 'Proprietaire'): ?>
                <div class="form-group">
                    <label>Numéro CIN</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['cin_number']); ?>" readonly>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-update">Enregistrer les modifications</button>
        </form>
    </div>
</div>

</body>
</html>