<?php
include '../db.php'; // تضمين الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استقبال البيانات الأساسية من النموذج
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير كلمة المرور
    $role = "guide"; 

    try {
        // إدخال البيانات الأساسية إلى قاعدة البيانات
        $query = $pdo->prepare("INSERT INTO user (name, emailAddress, password, role) VALUES (?, ?, ?, ?)");
        $query->execute([$name, $email, $password, $role]);

        // الحصول على معرّف المرشد الجديد
        $userId = $pdo->lastInsertId();

        // حفظ معرّف المرشد في الجلسة لتمريره إلى الصفحة التالية
        session_start();
        $_SESSION['userId'] = $userId;
        $_SESSION['name'] = $name;

        // توجيه المستخدم إلى صفحة استكمال البيانات
        header("Location: complete_guide_info.php");
        exit();
    } catch (PDOException $e) {
        echo "حدث خطأ أثناء التسجيل: " . $e->getMessage();
    }
}
?>
