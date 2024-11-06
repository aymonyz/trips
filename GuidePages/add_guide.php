<?php
include '../db.php';  // تضمين اتصال قاعدة البيانات

$currentGuide = null;
if (isset($_GET['edit'])) {
    $guideId = $_GET['edit'];
    $guideStmt = $pdo->prepare("SELECT * FROM tourguide WHERE guideId = ?");
    $guideStmt->execute([$guideId]);
    $currentGuide = $guideStmt->fetch(PDO::FETCH_ASSOC);
}

// تحديث بيانات المرشد
if (isset($_POST['updateGuide'])) {
    $guideId = $_POST['guideId'];
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // التحقق من رفع صورة جديدة
    if (!empty($_FILES['guideImage']['name'])) {
        $guideImage = $_FILES['guideImage']['name'];
        $target_file = "../uploads/" . basename($guideImage);
        move_uploaded_file($_FILES['guideImage']['tmp_name'], $target_file);
    } else {
        $guideImage = $_POST['existingImage'];
    }

    try {
        $updateQuery = $pdo->prepare("
            UPDATE tourguide 
            SET name = ?, languages = ?, experience = ?, rating = ?, imageURL = ?, facebook = ?, twitter = ?, instagram = ? 
            WHERE guideId = ?
        ");
        $updateQuery->execute([$name, $languages, $experience, $rating, $guideImage, $facebook, $twitter, $instagram, $guideId]);
        echo "تم تحديث بيانات المرشد بنجاح!";
    } catch (PDOException $e) {
        echo "حدث خطأ أثناء تحديث المرشد: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات المرشد</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">تحديث بيانات المرشد</h1>
    <?php if ($currentGuide): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="guideId" value="<?= $currentGuide['guideId'] ?? '' ?>">
            <div class="mb-3">
                <label for="name" class="form-label">اسم المرشد</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= $currentGuide['name'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="languages" class="form-label">اللغات</label>
                <input type="text" class="form-control" id="languages" name="languages" required value="<?= $currentGuide['languages'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="experience" class="form-label">الخبرة (بالسنوات)</label>
                <input type="number" class="form-control" id="experience" name="experience" required value="<?= $currentGuide['experience'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">التقييم</label>
                <input type="number" step="0.1" class="form-control" id="rating" name="rating" required value="<?= $currentGuide['rating'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="guideImage" class="form-label">صورة المرشد</label>
                <input type="file" class="form-control" id="guideImage" name="guideImage" accept="image/*">
                <?php if ($currentGuide && $currentGuide['imageURL']): ?>
                    <input type="hidden" name="existingImage" value="<?= $currentGuide['imageURL'] ?>">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="facebook" class="form-label">رابط فيسبوك</label>
                <input type="url" class="form-control" id="facebook" name="facebook" value="<?= $currentGuide['facebook'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="twitter" class="form-label">رابط تويتر</label>
                <input type="url" class="form-control" id="twitter" name="twitter" value="<?= $currentGuide['twitter'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label for="instagram" class="form-label">رابط إنستغرام</label>
                <input type="url" class="form-control" id="instagram" name="instagram" value="<?= $currentGuide['instagram'] ?? '' ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="updateGuide">تحديث المرشد</button>
        </form>
    <?php else: ?>
        <p class="alert alert-warning">لا يوجد مرشد محدد للتحديث. يُرجى اختيار مرشد من الصفحة الرئيسية.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
