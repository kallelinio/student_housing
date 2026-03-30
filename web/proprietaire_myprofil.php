<?php 
session_start();
include "database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// جلب بيانات المالك
$nom_utilisateur = $_SESSION['user_nom'] ?? "Propriétaire";
$email_utilisateur = $_SESSION['user_email'] ?? "Email non défini";

// صنع Initials (مثلاً: "Ahmed Ben" تولي "AB")
$words = explode(" ", $nom_utilisateur);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper(substr($w, 0, 1));
}
$initials = substr($initials, 0, 2); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #0052FF;
            --accent: #FF385C;
            --bg: #F8FAFC;
            --white: #ffffff;
            --text-main: #1A202C;
            --text-light: #718096;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            margin: 0; padding: 0;
            color: var(--text-main);
            padding-bottom: 110px;
        }

        /* Header الأزرق العصري الخاص بالمالك */
        .dashboard-header {
            background: var(--primary);
            color: white;
            padding: 60px 25px 120px;
            text-align: center;
            border-radius: 0 0 40px 40px; /* انحناء خفيف في الأسفل */
        }

        .dashboard-header h1 { margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .dashboard-header p { margin: 8px 0 0; opacity: 0.8; font-size: 14px; }

        /* الـ Container البارز */
        .profile-container {
            max-width: 500px;
            margin: -80px auto 0;
            padding: 0 20px;
        }

        /* كرت المعلومات الشخصية */
        .user-info-card {
            background: var(--white);
            border-radius: 30px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .avatar-circle {
            width: 95px; height: 95px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 34px; font-weight: 800;
            margin: 0 auto 18px;
            border: 5px solid #EBF4FF;
            box-shadow: 0 5px 15px rgba(0, 82, 255, 0.2);
        }

        .user-details h3 { margin: 0; font-size: 22px; font-weight: 800; color: var(--text-main); }
        .user-details p { margin: 6px 0 18px; color: var(--text-light); font-size: 14px; }

        .verified-badge {
            background: #EBF4FF; color: var(--primary);
            padding: 7px 18px; border-radius: 30px;
            font-size: 11px; font-weight: 800;
            display: inline-flex; align-items: center; gap: 6px;
        }

        /* قائمة الإعدادات */
        .settings-list {
            background: var(--white);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 25px;
        }

        .settings-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 22px 25px;
            text-decoration: none; color: var(--text-main);
            border-bottom: 1px solid #F1F5F9;
            transition: 0.2s;
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item:active { background: #F8FAFC; }

        .settings-item-left { display: flex; align-items: center; gap: 18px; }
        .settings-item i.menu-icon { font-size: 18px; color: var(--text-light); width: 24px; text-align: center; }
        .settings-item span { font-size: 15px; font-weight: 600; }
        .settings-item .fa-chevron-right { font-size: 12px; color: #CBD5E0; }

        /* زر الخروج */
        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 12px;
            background: var(--white); color: var(--accent);
            padding: 20px; border-radius: 24px;
            text-decoration: none; font-weight: 800; font-size: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #FFE5E5;
            transition: 0.2s;
        }
        .logout-btn:active { background: #FFF5F5; transform: scale(0.98); }

        /* Bottom Nav المخصص للمالك */
        .bottom-nav {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 400px;
            background: rgba(26, 32, 44, 0.95); backdrop-filter: blur(12px);
            display: flex; justify-content: space-around; padding: 14px;
            border-radius: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            z-index: 1000;
        }

        .nav-item { text-decoration: none; color: #718096; display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .nav-item i { font-size: 20px; }
        .nav-item span { font-size: 10px; font-weight: 700; }
        .nav-item.active { color: white; }
    </style>
</head>
<body>

<div class="dashboard-header">
    <h1>Mon Profil</h1>
    <p>Gérez vos annonces et vos paramètres</p>
</div>

<div class="profile-container">
    <div class="user-info-card">
        <div class="avatar-circle">
            <?php echo $initials; ?>
        </div>
        <div class="user-details">
            <h3><?php echo htmlspecialchars($nom_utilisateur); ?></h3>
            <p><?php echo htmlspecialchars($email_utilisateur); ?></p>
            <span class="verified-badge"><i class="fas fa-check-circle"></i> Propriétaire vérifié</span>
        </div>
    </div>

    <div class="settings-list">
        <a href="edit_profile.php" class="settings-item">
            <div class="settings-item-left">
                <i class="fas fa-user-edit menu-icon"></i>
                <span>Modifier mon profil</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="support.php" class="settings-item">
            <div class="settings-item-left">
                <i class="fas fa-headset menu-icon"></i>
                <span>Aide & Support</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="terms.php" class="settings-item">
            <div class="settings-item-left">
                <i class="fas fa-shield-alt menu-icon"></i>
                <span>Confidentialité</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    <a href="logout_process.php" class="logout-btn">
        <i class="fas fa-power-off"></i> Se déconnecter
    </a>
</div>

<nav class="bottom-nav">
    <a href="proprietaire_dashboard.php" class="nav-item">
        <i class="fas fa-chart-pie"></i><span>Stats</span>
    </a>
    <a href="proprietaire_myannonces.php" class="nav-item">
        <i class="fas fa-house-user"></i><span>Annonces</span>
    </a>
    <a href="proprietaire_messages.php" class="nav-item">
        <i class="fas fa-comment-dots"></i><span>Messages</span>
    </a>
    <a href="proprietaire_myprofil.php" class="nav-item active">
        <i class="fas fa-user-circle"></i><span>Profil</span>
    </a>
</nav>

</body>
</html>