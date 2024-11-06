<?php
include 'db.php'; // تضمين ملف الاتصال بقاعدة البيانات

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// عرض الأخطاء لأغراض التطوير
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = ''; // متغير لتخزين رسالة الخطأ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['emailAddress'];
    $password = $_POST['password'];

    // استعلام لجلب المستخدم بناءً على البريد الإلكتروني
    $stmt = $pdo->prepare("SELECT * FROM user WHERE emailAddress = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // التحقق من صحة كلمة المرور
        if (password_verify($password, $user['password'])) {
            // تسجيل الدخول بنجاح
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            header("Location: index.php?page=home");
            exit();
        } else {
            // إذا كانت كلمة المرور غير صحيحة
            $error = "كلمة المرور غير صحيحة.";
        }
    } else {
        // إذا لم يتم العثور على مستخدم بهذا البريد الإلكتروني
        $error = "لا يوجد مستخدم مسجل بهذا البريد الإلكتروني.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>تسجيل الدخول</h2>
    <form method="POST" action="index.php?page=login">
        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" class="form-control" id="email" name="emailAddress" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
    </form>

    <?php if ($error): ?>
        <p class="text-danger mt-3"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p>ليس لديك حساب؟ <a href="index.php?page=register">سجل هنا</a></p>
    <p>هل تريد أن تصبح مرشدًا؟ <a href="GuidePages/register.php" class="btn btn-secondary mt-2">تسجيل كمرشد</a></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
