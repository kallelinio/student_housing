<?php
session_start();
include "database.php";

$id = $_GET['id'];

// جلب تفاصيل الإعلان مع معلومات صاحب العقار
$stmt = $pdo->prepare("SELECT a.*, u.nom as owner_name, u.telephone as owner_phone 
                       FROM annonce a 
                       JOIN UTILISATEUR u ON a.id_proprietaire = u.id_user 
                       WHERE a.id_annonce = ?");
$stmt->execute([$id]);
$annonce = $stmt->fetch();

if (!$annonce) { die("Annonce introuvable."); }

// التثبت إذا كان الإعلان في المفضلة (للطالب فقط)
$is_fav = false;
if (isset($_SESSION['id_user']) && $_SESSION['type_compte'] == "Etudiant") {
    $check_fav = $pdo->prepare("SELECT id_favori FROM favoris WHERE id_user = ? AND id_annonce = ?");
    $check_fav->execute([$_SESSION['id_user'], $id]);
    if ($check_fav->fetch()) { $is_fav = true; }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($annonce['titre']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0052FF; --bg: #f5f6fa; --white: #fff; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding-bottom: 50px;}
        
        .details-container { max-width: 900px; margin: 40px auto; background: var(--white); border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .image-box { position: relative; width: 100%; height: 450px; }
        .image-box img { width: 100%; height: 100%; object-fit: cover; }
        .back-btn { position: absolute; top: 20px; left: 20px; background: rgba(255,255,255,0.9); padding: 10px 15px; border-radius: 12px; text-decoration: none; color: #333; font-weight: 600; font-size: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        .type-tag { position: absolute; bottom: 20px; left: 20px; background: var(--primary); color: white; padding: 8px 16px; border-radius: 10px; font-weight: 600; }

        .info-box { padding: 35px; }
        .header-flex { display: flex; justify-content: space-between; align-items: flex-start; }
        .header-flex h1 { margin: 0; font-size: 28px; color: #1A202C; }
        .price-tag { font-size: 26px; color: var(--primary); font-weight: 800; margin-top: 10px; }
        
        .location { color: #718096; margin: 15px 0; font-size: 15px; }
        
        /* Grid الجديد للمعلومات التقنية */
        .specs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 25px 0; }
        .spec-item { background: #F8FAFF; padding: 15px; border-radius: 15px; border: 1px solid #E2E8F0; text-align: center; }
        .spec-item i { display: block; font-size: 20px; color: var(--primary); margin-bottom: 8px; }
        .spec-item span { font-size: 13px; font-weight: 600; color: #4A5568; }

        .description { margin-top: 30px; line-height: 1.7; color: #4A5568; }
        
        /* التجهيزات (Amenities) */
        .amenities { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 20px; }
        .amenity { font-size: 14px; background: #EBF4FF; color: var(--primary); padding: 8px 15px; border-radius: 20px; font-weight: 600; }
        .amenity.no { background: #F7FAFC; color: #A0AEC0; text-decoration: line-through; }

        .contact-section { display: flex; gap: 15px; margin-top: 40px; border-top: 1px solid #EDF2F7; padding-top: 30px; }
        .contact-btn { flex: 2; background: var(--primary); color: white; text-align: center; padding: 16px; border-radius: 15px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .contact-btn:hover { background: #0041CC; transform: translateY(-2px); }
        
        .action-btn { flex: 0.5; display: flex; align-items: center; justify-content: center; background: #F7FAFC; border-radius: 15px; text-decoration: none; font-size: 20px; color: #4A5568; transition: 0.3s; border: 1px solid #E2E8F0; }
        .action-btn.fav.active { color: #E53E3E; background: #FFF5F5; border-color: #FEB2B2; }
        .action-btn.signal:hover { background: #FFF5F5; color: #E53E3E; }
    </style>
</head>
<body>

<div class="details-container">
    <div class="image-box">
        <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i> Retour</a>
        <img src="uploads/<?php echo $annonce['image'] ? $annonce['image'] : 'default.jpg'; ?>" alt="Logement">
        <span class="type-tag"><?php echo $annonce['type_logement']; ?></span>
    </div>

    <div class="info-box">
        <div class="header-flex">
            <div>
                <h1><?php echo htmlspecialchars($annonce['titre']); ?></h1>
                <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($annonce['localisation']); ?></p>
            </div>
            <div style="text-align: right;">
                <div class="price-tag"><?php echo $annonce['prix']; ?> DT <span style="font-size: 14px; font-weight: 400; color: #718096;">/ mois</span></div>
                <?php if($annonce['caution'] > 0): ?>
                    <span style="font-size: 13px; color: #E53E3E;">Caution: <?php echo $annonce['caution']; ?> DT</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="specs-grid">
            <div class="spec-item"><i class="fas fa-bed"></i><span><?php echo $annonce['nb_chambres']; ?> Chambres</span></div>
            <div class="spec-item"><i class="fas fa-users"></i><span>Colocation: <?php echo $annonce['colocation']; ?></span></div>
            <div class="spec-item"><i class="fas fa-venus-mars"></i><span>Pour: <?php echo $annonce['genre_admis']; ?></span></div>
            <div class="spec-item"><i class="fas fa-walking"></i><span><?php echo $annonce['distance_fac']; ?></span></div>
        </div>

        <div class="description">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($annonce['description'])); ?></p>
        </div>

        <div class="amenities">
            <div class="amenity <?php echo $annonce['wifi'] ? '' : 'no'; ?>"><i class="fas fa-wifi"></i> WiFi</div>
            <div class="amenity <?php echo $annonce['meuble'] ? '' : 'no'; ?>"><i class="fas fa-couch"></i> Meublé</div>
            <div class="amenity <?php echo $annonce['chauffage'] ? '' : 'no'; ?>"><i class="fas fa-fire"></i> Chauffage</div>
        </div>

        <div class="contact-section">
            <a href="message.php?id_receiver=<?php echo $annonce['id_proprietaire']; ?>&id_annonce=<?php echo $annonce['id_annonce']; ?>" class="contact-btn">
                <i class="fas fa-comment"></i> Contacter Propriétaire
            </a>
            
            <?php if(isset($_SESSION['type_compte']) && $_SESSION['type_compte'] == "Etudiant"): ?>
                <a href="toggle_favori.php?id=<?php echo $annonce['id_annonce']; ?>" class="action-btn fav <?php echo $is_fav ? 'active' : ''; ?>">
                    <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart"></i>
                </a>
            <?php endif; ?>

            <a href="signaler_annonce.php?id=<?php echo $annonce['id_annonce']; ?>" class="action-btn signal" title="Signaler">
                <i class="fas fa-flag"></i>
            </a>
        </div>
    </div>
</div>

</body>
</html>