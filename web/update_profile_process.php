<?php
session_start();
require_once 'database.php';

// 1. التثبت من تسجيل الدخول
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. استقبال البيانات العامة
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    
    try {
        // 3. نجلب نوع الحساب
        $stmtCheck = $pdo->prepare("SELECT type_compte FROM UTILISATEUR WHERE id_user = ?");
        $stmtCheck->execute([$id_user]);
        $user = $stmtCheck->fetch();

        if (!$user) {
            die("Utilisateur non trouvé.");
        }

        // تحديد الصفحة التي سيرجع إليها المستخدم حسب نوع حسابه
        // استعملنا نفس المنطق اللي موجود في قاعدة البيانات (Etudiant أو غيره)
        if ($user['type_compte'] === 'Etudiant') {
            $redirect_url = 'etudiants_myprofil.php';
        } else {
            $redirect_url = 'proprietaire_myprofil.php';
        }

        // 4. تحضير الـ SQL حسب نوع الحساب
        if ($user['type_compte'] === 'Etudiant' && isset($_POST['faculte'])) {
            $faculte = trim($_POST['faculte']);
            $sql = "UPDATE UTILISATEUR SET nom = ?, telephone = ?, faculte = ? WHERE id_user = ?";
            $params = [$nom, $telephone, $faculte, $id_user];
        } else {
            $sql = "UPDATE UTILISATEUR SET nom = ?, telephone = ? WHERE id_user = ?";
            $params = [$nom, $telephone, $id_user];
        }

        // 5. تنفيذ التحديث
        $stmtUpdate = $pdo->prepare($sql);
        
        if ($stmtUpdate->execute($params)) {
            // تحديث الاسم في السيسيون
            $_SESSION['nom_user'] = $nom; 

            echo "<script>
                alert('Profil mis à jour avec succès !');
                window.location.href = '$redirect_url';
            </script>";
        } else {
            echo "<script>
                alert('Erreur lors de la mise à jour.');
                window.location.href = '$redirect_url';
            </script>";
        }

    } catch (PDOException $e) {
        die("Erreur base de données : " . $e->getMessage());
    }
} else {
    // في حالة الدخول المباشر، نرجعه لصفحة تسجيل الدخول أو صفحة عامة بما أننا لا نعرف نوعه بعد
    header("Location: login.php");
    exit();
}
?>