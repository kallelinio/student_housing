<?php
session_start();
include "database.php";

if (!isset($_SESSION['id_user'])) {
    die("Erreur: Session expirée. Connectez-vous.");
}

$id_proprietaire = $_SESSION['id_user'];

// استقبال البيانات مع التعامل مع الـ Checkboxes
$titre = $_POST['titre'] ?? '';
$description = $_POST['description'] ?? '';
$prix = $_POST['prix'] ?? 0;
$caution = $_POST['caution'] ?? 0;
$type = $_POST['type_logement'] ?? '';
$localisation = $_POST['localisation'] ?? '';
$distance_fac = $_POST['distance_fac'] ?? '';
$colocation = $_POST['colocation'] ?? 'Non';
$genre_admis = $_POST['genre_admis'] ?? 'Mixte';
$nb_chambres = $_POST['nb_chambres'] ?? 1;

// التحقق من الـ Checkboxes (1 if checked, 0 otherwise)
$wifi = isset($_POST['wifi']) ? 1 : 0;
$meuble = isset($_POST['meuble']) ? 1 : 0;
$chauffage = isset($_POST['chauffage']) ? 1 : 0;
$disponibilite = isset($_POST['disponibilite']) ? 1 : 0;

$date_publication = date('Y-m-d'); 

try {
    // الـ INSERT المحدث ليشمل كل الخانات الجديدة بنفس أسمائها
    $sql = "INSERT INTO annonce 
            (titre, description, prix, caution, type_logement, localisation, distance_fac, colocation, genre_admis, nb_chambres, wifi, meuble, chauffage, disponibilite, id_proprietaire, date_publication, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'En attente')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $titre, $description, $prix, $caution, $type, $localisation, 
        $distance_fac, $colocation, $genre_admis, $nb_chambres, 
        $wifi, $meuble, $chauffage, $disponibilite,
        $id_proprietaire, $date_publication
    ]);

    $id_annonce = $pdo->lastInsertId();

    // إدارة رفع الصور
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $file_name = "img_" . time() . "_" . $key . "." . $ext;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    // 1. تسجيل كل الصور في جدول images_logement
                    $stmtImg = $pdo->prepare("INSERT INTO images_logement (id_annonce, image) VALUES (?, ?)");
                    $stmtImg->execute([$id_annonce, $file_name]);
                    
                    // 2. تحديث الحقل 'image' في جدول annonce بأول صورة فقط (Main Image)
                    if ($key == 0) {
                        $up = $pdo->prepare("UPDATE annonce SET image = ? WHERE id_annonce = ?");
                        $up->execute([$file_name, $id_annonce]);
                    }
                }
            }
        }
    }

    header("Location: proprietaire_dashboard.php?status=success");
    exit();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>