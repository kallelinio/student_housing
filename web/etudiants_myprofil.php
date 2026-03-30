<?php 
session_start();
include "database.php"; // تأكد من ربط قاعدة البيانات إذا لزم الأمر

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// جلب البيانات من السيسيون أو قاعدة البيانات
$nom_utilisateur = $_SESSION['user_nom'] ?? "Utilisateur";
$email_utilisateur = $_SESSION['user_email'] ?? "Email non défini";

// صنع Initials (مثلاً: "Ahmed Ben" تولي "AB")
$words = explode(" ", $nom_utilisateur);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper(substr($w, 0, 1));
}
$initials = substr($initials, 0, 2); // نكتفي بحرفين فقط
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
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

        /* Header الأزرق العصري */
        .dashboard-header {
            background: var(--primary);
            color: white;
            padding: 50px 25px 100px;
            text-align: center;
        }

        .dashboard-header h1 { margin: 0; font-size: 26px; font-weight: 800; }
        .dashboard-header p { margin: 8px 0 0; opacity: 0.8; font-size: 14px; }

        /* الـ Container اللي طالع فوق الهيدر */
        .profile-container {
            max-width: 500px;
            margin: -60px auto 0;
            padding: 0 20px;
        }

        /* كرت المعلومات الشخصية */
        .user-info-card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .avatar-circle {
            width: 90px; height: 90px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800;
            margin: 0 auto 15px;
            border: 4px solid #EBF4FF;
        }

        .user-details h3 { margin: 0; font-size: 20px; font-weight: 700; }
        .user-details p { margin: 5px 0 15px; color: var(--text-light); font-size: 14px; }

        .verified-badge {
            background: #EBF4FF; color: var(--primary);
            padding: 6px 16px; border-radius: 30px;
            font-size: 11px; font-weight: 700;
            display: inline-block;
        }

        /* قائمة الإعدادات */
        .settings-list {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 20px;
        }

        .settings-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 25px;
            text-decoration: none; color: var(--text-main);
            border-bottom: 1px solid #F1F5F9;
            transition: 0.3s;
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item:hover { background: #F8FAFC; }

        .settings-item-left { display: flex; align-items: center; gap: 15px; }
        .settings-item i { font-size: 18px; color: var(--text-light); width: 24px; text-align: center; }
        .settings-item span { font-size: 15px; font-weight: 600; }
        .settings-item .fa-chevron-right { font-size: 12px; opacity: 0.3; }

        /* زر الخروج */
        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            background: var(--white); color: var(--accent);
            padding: 18px; border-radius: 20px;
            text-decoration: none; font-weight: 700; font-size: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #FFE5E5;
            transition: 0.3s;
        }
        .logout-btn:hover { background: #FFF5F5; transform: translateY(-2px); }

        /* Bottom Nav الموحد */
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
    </style>
</head>
<body>

<div class="dashboard-header">
    <h1>Mon Profil</h1>
    <p>Gérez vos informations et préférences</p>
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
                <i class="fas fa-user-edit"></i>
                <span>Modifier le profil</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="support.php" class="settings-item">
            <div class="settings-item-left">
                <i class="fas fa-headset"></i>
                <span>Aide & Support</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="terms.php" class="settings-item">
            <div class="settings-item-left">
                <i class="fas fa-shield-alt"></i>
                <span>Confidentialité</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    <a href="logout_process.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Se déconnecter
    </a>
</div>

<nav class="bottom-nav">
    <a href="etudiants_recherche.php" class="nav-item"><i class="fas fa-search"></i><span>Explorer</span></a>
    <a href="etudiants_favoris.php" class="nav-item"><i class="far fa-heart"></i><span>Favoris</span></a>
    <a href="etudiants_message.php" class="nav-item"><i class="far fa-comment-dots"></i><span>Messages</span></a>
    <a href="etudiants_myprofil.php" class="nav-item active"><i class="fas fa-user"></i><span>Profil</span></a>
</nav>

</body>
</html>