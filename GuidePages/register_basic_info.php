<?php
include '../db.php'; // تضمين الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استقبال البيانات الأساسية من النموذج
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير كلمة المرور
    $role = "guide"; 
    $active=0;

    try {
        // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
        $checkEmailQuery = $pdo->prepare("SELECT COUNT(*) FROM user WHERE emailAddress = ?");
        $checkEmailQuery->execute([$email]);
        $emailExists = $checkEmailQuery->fetchColumn();

        if ($emailExists > 0) {
            echo "هذا البريد الإلكتروني مسجل بالفعل. يرجى استخدام بريد إلكتروني آخر.";
        } else {
            // إدخال البيانات الأساسية إلى قاعدة البيانات
            $query = $pdo->prepare("INSERT INTO user (name, emailAddress, password, role,active) VALUES (?, ?, ?, ?,?)");
            $query->execute([$name, $email, $password, $role,$active]);

            // الحصول على معرّف المرشد الجديد
            $userId = $pdo->lastInsertId();

            // حفظ معرّف المرشد في الجلسة لتمريره إلى الصفحة التالية
            session_start();
            $_SESSION['userId'] = $userId;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // توجيه المستخدم إلى صفحة استكمال البيانات
            header("Location: complete_guide_info.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "حدث خطأ أثناء التسجيل: " . $e->getMessage();
    }
}
?>
