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
$query = $pdo->prepare("
    SELECT 
        user.name AS name,
        user.emailAddress,
        tourguide.phone,
        tourguide.facebook,
        tourguide.twitter,
        tourguide.instagram
    FROM 
        tourguide
    INNER JOIN 
        user 
    ON 
        tourguide.guideId = user.userId
    WHERE 
        tourguide.guideId = ?
");

$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات من النموذج
    $name = $_POST['name'];
    $email = $_POST['emailAddress'];
    $phone = $_POST['phone'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    
    // تحديث بيانات المستخدم في جدول user
    $updateUserQuery = $pdo->prepare("UPDATE user SET name = ?, emailAddress = ? WHERE userId = ?");
    $updateUserQuery->execute([$name, $email, $userId]);
    
    // تحديث بيانات المرشد في جدول tourguide
    $updateGuideQuery = $pdo->prepare("UPDATE tourguide SET phone = ?, facebook = ?, twitter = ?, instagram = ? WHERE guideId = ?");
    $updateGuideQuery->execute([$phone, $facebook, $twitter, $instagram, $userId]);
    
    echo "تم تحديث البيانات بنجاح!";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الملف الشخصي</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
      
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
        .btn-primary:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<?php include '../base_nav.php'; ?>

<div class="container">
    <h2>تعديل الملف الشخصي</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label for="name" class="form-label">الاسم الكامل</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">رقم الجوال</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="emailAddress" class="form-label">البريد الإلكتروني</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= htmlspecialchars($user['emailAddress']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="facebook" class="form-label">فيسبوك</label>
                <input type="url" class="form-control" id="facebook" name="facebook" value="<?= htmlspecialchars($user['facebook']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="twitter" class="form-label">تويتر</label>
                <input type="url" class="form-control" id="twitter" name="twitter" value="<?= htmlspecialchars($user['twitter']) ?>">
            </div>
            <div class="col-md-6">
                <label for="instagram" class="form-label">انستقرام</label>
                <input type="url" class="form-control" id="instagram" name="instagram" value="<?= htmlspecialchars($user['instagram']) ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primaryy">تأكيد</button>
    </form>
</div>

<?php include '../footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
