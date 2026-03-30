<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $type_compte = $_POST['type_compte'];
    $telephone = $_POST['telephone'];
    $genre = $_POST['genre'];
    
    // بيانات خاصة
    $faculte = ($type_compte == 'Etudiant') ? $_POST['faculte'] : null;
    $cin_number = ($type_compte == 'Proprietaire') ? $_POST['cin_number'] : null;
    $is_approved = ($type_compte == 'Proprietaire') ? 0 : 1; 

    $cin_image = null;
    if ($type_compte == 'Proprietaire' && isset($_FILES['cin_image']) && $_FILES['cin_image']['error'] == 0) {
        $target_dir = "uploads/cin/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $img_name = time() . '_' . basename($_FILES['cin_image']['name']);
        move_uploaded_file($_FILES['cin_image']['tmp_name'], $target_dir . $img_name);
        $cin_image = $img_name;
    }

    try {
        // التثبت من الإيميل
        $check = $pdo->prepare("SELECT id_user FROM UTILISATEUR WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            echo "<script>alert('Email déjà utilisé!'); window.history.back();</script>";
            exit();
        }

        // الإدخال في القاعدة (نفس أسماء الأعمدة اللي عندك)
        $sql = "INSERT INTO UTILISATEUR (nom, email, mot_de_passe, telephone, genre, faculte, cin_number, cin_image, is_approved, type_compte) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nom, $email, $mot_de_passe, $telephone, $genre, $faculte, $cin_number, $cin_image, $is_approved, $type_compte])) {
            echo "<script>alert('Inscription réussie!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Erreur technique.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        die("Erreur: " . $e->getMessage());
    }
}