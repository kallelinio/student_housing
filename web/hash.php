<?php
// حط كلمة السر اللي تحب عليها هنا
$password = 'admin123'; 

// التشفير
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Le mot de passe haché est : <br> " . $hashed_password;
?>