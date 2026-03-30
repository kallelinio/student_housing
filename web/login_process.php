<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. نجيبو البيانات وننظفوها من أي فراغات زايدة (Trim)
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // 2. تعبئة الـ Session
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['user_nom'] = $user['nom'];   
        $_SESSION['user_email'] = $user['email']; 
        $_SESSION['type_compte'] = $user['type_compte'];

        // 3. تحويل الـ Role لحروف صغيرة باش نتفاداو مشاكل الـ Case Sensitivity
        $role = strtolower(trim($user['type_compte'])); 

        // 4. الـ Redirection الصحيحة
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
            exit(); 
        } elseif ($role === 'proprietaire') {
            header("Location: proprietaire_dashboard.php");
            exit();
        } elseif ($role === 'etudiant') {
            header("Location: etudiants_recherche.php");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }

    } else {
        // في حالة غلط في المعلومات
        echo "<script>
                alert('Email ou mot de passe incorrect.');
                window.location.href='login.php';
              </script>";
        exit();
    }
}
?>