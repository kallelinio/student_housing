<?php
session_start();
include "database.php";

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_receiver = $_GET['id_receiver'] ?? null;
$id_annonce = $_GET['id_annonce'] ?? null;

if(!$id_receiver || !$id_annonce){
    die("Erreur: données manquantes !");
}

// جلب اسم الشخص اللي نحكي معاه
$stmt_user = $pdo->prepare("SELECT nom FROM UTILISATEUR WHERE id_user=?");
$stmt_user->execute([$id_receiver]);
$receiver_data = $stmt_user->fetch();

// جلب عنوان الإعلان وصورته
$stmt_ann = $pdo->prepare("SELECT titre, image FROM annonce WHERE id_annonce=?");
$stmt_ann->execute([$id_annonce]);
$annonce_data = $stmt_ann->fetch();

// جلب الميساجات
$sql = "SELECT * FROM message 
        WHERE ((expediteur=? AND destinataire=?) OR (expediteur=? AND destinataire=?))
        AND id_annonce=?
        ORDER BY date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user, $id_receiver, $id_receiver, $id_user, $id_annonce]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chat - <?php echo htmlspecialchars($receiver_data['nom']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --primary: #0052FF; --bg: #f0f2f5; --white: #ffffff; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); }

        .chat-container { 
            max-width: 800px; margin: 20px auto; background: var(--white); 
            height: 90vh; display: flex; flex-direction: column; 
            border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;
        }

        /* Header المطور */
        .chat-header { 
            padding: 15px 20px; background: var(--primary); color: white; 
            display: flex; align-items: center; gap: 15px; 
        }
        .back-link { color: white; text-decoration: none; font-size: 18px; }
        .receiver-info h4 { margin: 0; font-size: 16px; }
        .receiver-info p { margin: 0; font-size: 12px; opacity: 0.8; }

        /* منطقة الرسائل */
        .chat-messages { 
            flex: 1; padding: 20px; overflow-y: auto; 
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); /* خلفية خفيفة */
            background-blend-mode: overlay; background-color: #f0f2f5;
        }

        .message { 
            margin-bottom: 15px; padding: 12px 16px; border-radius: 18px; 
            max-width: 70%; position: relative; font-size: 14px; line-height: 1.4;
        }
        .sent { 
            background: var(--primary); color: white; margin-left: auto; 
            border-bottom-right-radius: 4px; 
        }
        .received { 
            background: white; color: #333; margin-right: auto; 
            border-bottom-left-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .time { font-size: 10px; margin-top: 5px; opacity: 0.7; display: block; text-align: right; }

        /* الفورم */
        .chat-form { 
            padding: 20px; background: white; border-top: 1px solid #eee; 
            display: flex; gap: 10px; align-items: center;
        }
        .chat-form input { 
            flex: 1; padding: 12px 20px; border-radius: 25px; 
            border: 1px solid #ddd; outline: none; background: #f8f9fa;
        }
        .chat-form button { 
            background: var(--primary); color: white; border: none; 
            width: 45px; height: 45px; border-radius: 50%; 
            cursor: pointer; transition: 0.3s;
        }
        .chat-form button:hover { transform: scale(1.1); background: #0041CC; }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <a href="javascript:history.back()" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <div class="receiver-info">
            <h4><?php echo htmlspecialchars($receiver_data['nom']); ?></h4>
            <p>Sujet: <?php echo htmlspecialchars($annonce_data['titre']); ?></p>
        </div>
    </div>

    <div class="chat-messages" id="chatBox">
        <?php foreach($messages as $msg): ?>
            <div class="message <?php echo ($msg['expediteur'] == $id_user) ? 'sent' : 'received'; ?>">
                <?php echo htmlspecialchars($msg['contenu']); ?>
                <span class="time"><?php echo date('H:i', strtotime($msg['date'])); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <form class="chat-form" method="POST" action="send_message.php">
        <input type="hidden" name="id_receiver" value="<?php echo $id_receiver; ?>">
        <input type="hidden" name="id_annonce" value="<?php echo $id_annonce; ?>">
        
        <input type="text" name="message" id="msgInput" placeholder="Écrire un message..." required autocomplete="off">
        <button type="submit">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>

<script>
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;

    // تحسين تجربة المستخدم: وضع الفوكيس على الإدخال
    document.getElementById('msgInput').focus();
</script>

</body>
</html>