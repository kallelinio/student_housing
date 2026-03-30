<?php
session_start();
include "database.php";

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// SQL المطور لجلب المحادثات مع بيانات الطرف الآخر وصورة الإعلان
$sql = "
SELECT 
    m.id_annonce,
    MAX(m.date) as last_date,
    u.id_user as contact_id,
    u.nom as contact_name, 
    a.titre as annonce_titre, 
    a.image as annonce_image,
    (
        SELECT contenu FROM message 
        WHERE ((expediteur = m.expediteur AND destinataire = m.destinataire) 
           OR (expediteur = m.destinataire AND destinataire = m.expediteur))
        AND id_annonce = m.id_annonce
        ORDER BY date DESC LIMIT 1
    ) as last_msg,
    (
        SELECT COUNT(*) FROM message 
        WHERE destinataire = ? 
        AND expediteur = u.id_user 
        AND id_annonce = m.id_annonce 
        AND lu = 0
    ) as unread_count
FROM message m
JOIN utilisateur u ON u.id_user = (CASE WHEN m.expediteur = ? THEN m.destinataire ELSE m.expediteur END)
JOIN annonce a ON a.id_annonce = m.id_annonce
WHERE m.destinataire = ? OR m.expediteur = ?
GROUP BY contact_id, m.id_annonce
ORDER BY last_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user, $id_user, $id_user, $id_user]); 
$conversations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --primary: #0052FF;
            --bg: #F8FAFC;
            --white: #ffffff;
            --text-main: #1A202C;
            --text-light: #718096;
            --unread-bg: #EBF4FF;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            margin: 0;
            padding-bottom: 100px;
            color: var(--text-main);
        }

        /* Header الموحد */
        .page-header {
            background: var(--white);
            padding: 40px 25px 20px;
            border-bottom: 1px solid #EDF2F7;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .stats-bar {
            padding: 15px 25px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unread-badge-total {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }

        /* قائمة المحادثات */
        .conversations-container {
            padding: 10px 20px;
        }

        .conv-card {
            background: var(--white);
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
            border: 1px solid transparent;
            position: relative;
        }

        .conv-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: #E2E8F0;
        }

        .conv-card.unread {
            background: var(--unread-bg);
        }

        /* الأفاتار */
        .avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #EDF2F7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            font-size: 18px;
            border: 2px solid white;
        }

        .unread-indicator {
            position: absolute;
            top: 0;
            right: 0;
            width: 20px;
            height: 20px;
            background: #FF385C;
            color: white;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        /* محتوى الرسالة */
        .conv-info {
            flex-grow: 1;
            min-width: 0;
        }

        .conv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .contact-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-main);
        }

        .msg-time {
            font-size: 11px;
            color: var(--text-light);
        }

        .last-msg-preview {
            font-size: 13px;
            color: var(--text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .annonce-ref {
            font-size: 11px;
            color: var(--primary);
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }

        /* صورة الإعلان الصغيرة */
        .annonce-preview {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
            opacity: 0.7;
        }

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

        .empty-chats {
            text-align: center;
            padding: 100px 20px;
            color: var(--text-light);
        }
        .empty-chats i { font-size: 50px; opacity: 0.2; margin-bottom: 15px; }
    </style>
</head>
<body>

<?php 
$total_unread = 0;
foreach($conversations as $conv) { $total_unread += $conv['unread_count']; }
?>

<header class="page-header">
    <h1>Messages</h1>
</header>

<div class="stats-bar">
    <i class="far fa-comment-dots"></i>
    <?php if($total_unread > 0): ?>
        Vous avez <span class="unread-badge-total"><?php echo $total_unread; ?></span> nouveaux messages
    <?php else: ?>
        Toutes vos conversations
    <?php endif; ?>
</div>

<div class="conversations-container">
    <?php if(count($conversations) > 0): ?>
        <?php foreach($conversations as $c): 
            $initial = strtoupper(substr($c['contact_name'], 0, 1));
            $is_unread = ($c['unread_count'] > 0);
        ?>
        <a href="message.php?id_receiver=<?php echo $c['contact_id']; ?>&id_annonce=<?php echo $c['id_annonce']; ?>" 
           class="conv-card <?php echo $is_unread ? 'unread' : ''; ?>">
            
            <div class="avatar-wrapper">
                <div class="user-avatar"><?php echo $initial; ?></div>
                <?php if($is_unread): ?>
                    <span class="unread-indicator"><?php echo $c['unread_count']; ?></span>
                <?php endif; ?>
            </div>

            <div class="conv-info">
                <div class="conv-header">
                    <span class="contact-name"><?php echo htmlspecialchars($c['contact_name']); ?></span>
                    <span class="msg-time"><?php echo date('H:i', strtotime($c['last_date'])); ?></span>
                </div>
                <div class="last-msg-preview">
                    <?php echo htmlspecialchars($c['last_msg']); ?>
                </div>
                <span class="annonce-ref">
                    <i class="fas fa-home"></i> <?php echo htmlspecialchars($c['annonce_titre']); ?>
                </span>
            </div>

            <img src="uploads/<?php echo $c['annonce_image']; ?>" class="annonce-preview" alt="Logement">
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-chats">
            <i class="fas fa-comments"></i>
            <h3>Pas encore de messages</h3>
            <p>Les messages concernant vos recherches s'afficheront ici.</p>
        </div>
    <?php endif; ?>
</div>

<nav class="bottom-nav">
    <a href="etudiants_recherche.php" class="nav-item"><i class="fas fa-search"></i><span>Explorer</span></a>
    <a href="etudiants_favoris.php" class="nav-item"><i class="far fa-heart"></i><span>Favoris</span></a>
    <a href="etudiants_message.php" class="nav-item active"><i class="fas fa-comment-dots"></i><span>Messages</span></a>
    <a href="etudiants_myprofil.php" class="nav-item"><i class="far fa-user"></i><span>Profil</span></a>
</nav>

</body>
</html>