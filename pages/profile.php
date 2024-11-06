<?php
 
// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: index.php?page=login"); // Redirect to login page if not logged in
    exit();
}

// Fetch user data from the database
$userId = $_SESSION['userId'];
$stmt = $pdo->prepare("SELECT name, emailAddress, role FROM user WHERE userId = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<div class="container mt-5">
    <h2>الملف الشخصي</h2>
    <p><strong>الاسم:</strong> <?= htmlspecialchars($user['name']); ?></p>
    <p><strong>البريد الإلكتروني:</strong> <?= htmlspecialchars($user['emailAddress']); ?></p>
    <p><strong>الدور:</strong> <?= htmlspecialchars($user['role']); ?></p>
    <a href="index.php?page=home" class="btn btn-primary">العودة إلى الصفحة الرئيسية</a>
</div>
