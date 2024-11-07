<?php
session_start();
include '../db.php';

// تحقق من تسجيل دخول المستخدم
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$message = ""; // لإظهار رسالة نجاح أو خطأ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استقبال البيانات من النموذج
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // التحقق من تطابق كلمة المرور الجديدة مع تأكيدها
    if ($newPassword !== $confirmPassword) {
        $message = "كلمة المرور الجديدة وتأكيد كلمة المرور غير متطابقتين.";
    } else {
        // جلب كلمة المرور الحالية من قاعدة البيانات للتحقق منها
        $stmt = $pdo->prepare("SELECT password FROM user WHERE userId = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && password_verify($currentPassword, $user['password'])) {
            // تحديث كلمة المرور الجديدة بعد التحقق
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE user SET password = ? WHERE userId = ?");
            $updateStmt->execute([$hashedPassword, $userId]);
            $message = "تم تحديث كلمة المرور بنجاح!";
        } else {
            $message = "كلمة المرور الحالية غير صحيحة.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل كلمة المرور</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .containerr{
           
            margin: 61px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-label {
            font-weight: bold;
            color: #555;
        }
        .form-control {
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .btn-primaryy {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #333;
            border: none;
            border-radius: 5px;
        }
        .btn-primaryy:hover {
            background-color: #555;
            color: white;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>

<?php include '../base_nav.php'; ?>

<div class="containerr">
    <h2>تعديل كلمة المرور</h2>
    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'غير') !== false ? 'error-message' : '' ?>"><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="current_password" class="form-label">كلمة المرور السابقة</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">كلمة المرور الجديدة</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
           
        </div>
        <div class="md-3">
        <label for="confirm_password" class="form-label">أعد كلمة المرور</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
       
        <button type="submit" class="btn btn-primaryy">تأكيد</button>
    </form>
</div>

<?php include '../footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
