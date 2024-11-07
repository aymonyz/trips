<?php
session_start();
include '../db.php';

// تحقق من تسجيل دخول العميل
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];

// جلب بيانات العميل من قاعدة البيانات
$query = $pdo->prepare("SELECT * FROM user WHERE userId = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الصفحة الرئيسية للعميل</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>

<?php include '../base_nav.php'; ?>

<div class="container mt-5">
    <h2>مرحبًا، <?= htmlspecialchars($user['name']) ?></h2>
    <p>هذه هي صفحتك الرئيسية. يمكنك تعديل بياناتك الشخصية من هنا.</p>
    <a href="profile.php" class="btn btn-primary">تعديل الملف الشخصي</a>
</div>

<?php include '../footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
