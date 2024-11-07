<?php
include '../db.php'; // اتصال قاعدة البيانات
include 'index.php';

// التحقق من وجود tourId لتحميل بيانات الرحلة للتعديل
$currentTour = null;
$tourPlaces = []; // لتخزين الأماكن المرتبطة بالرحلة الحالية

if (isset($_GET['tourId'])) {
    $tourId = $_GET['tourId'];

    // جلب بيانات الرحلة الحالية من قاعدة البيانات
    $stmt = $pdo->prepare("SELECT * FROM tour WHERE tourId = :tourId");
    $stmt->execute(['tourId' => $tourId]);
    $currentTour = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentTour) {
        echo "<div class='alert alert-danger'>الرحلة غير موجودة.</div>";
        exit();
    }

    // جلب الأماكن المرتبطة بالرحلة
    $tourPlacesStmt = $pdo->prepare("SELECT placeId FROM Tour_Places WHERE tourId = :tourId");
    $tourPlacesStmt->execute(['tourId' => $tourId]);
    $tourPlaces = $tourPlacesStmt->fetchAll(PDO::FETCH_COLUMN); // نستخدم FETCH_COLUMN لجلب placeId فقط
}

// جلب جميع الأدلة للاختيار
$guidesStmt = $pdo->query("SELECT guideId, name FROM TourGuide");
$guides = $guidesStmt->fetchAll(PDO::FETCH_ASSOC);

// جلب جميع الأماكن للاختيار
$placesStmt = $pdo->query("SELECT placeId, name FROM Place");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// التحقق من وجود طلب تعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTour'])) {
    $guideId = $_POST['guideId'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $duration = $_POST['duration'];
    $places = $_POST['places'] ?? [];

    // تحميل الصورة في حال تم رفع صورة جديدة
    $imagePath = $currentTour['imageURL'];
    if (!empty($_FILES['tourImage']['name'])) {
        $imagePath = "../uploads/" . basename($_FILES['tourImage']['name']);
        if (!move_uploaded_file($_FILES['tourImage']['tmp_name'], $imagePath)) {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء رفع الصورة.</div>";
            exit();
        }
    }

    try {
        $pdo->beginTransaction();

        // تحديث بيانات الرحلة
        $updateQuery = $pdo->prepare("
            UPDATE tour SET guideId = ?, location = ?, date = ?, price = ?, imageURL = ?, description = ?, city = ?, duration = ?, title = ? 
            WHERE tourId = ?
        ");
        $updateQuery->execute([$guideId, $location, $date, $price, $imagePath, $description, $city, $duration, $title, $tourId]);

        // تحديث الأماكن المرتبطة بالرحلة
        $deletePlacesStmt = $pdo->prepare("DELETE FROM Tour_Places WHERE tourId = ?");
        $deletePlacesStmt->execute([$tourId]);

        if (!empty($places)) {
            $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
            foreach ($places as $placeId) {
                $stmtPlaces->execute([$tourId, $placeId]);
            }
        }

        $pdo->commit();
        echo "<div class='alert alert-success'>تم تحديث الرحلة بنجاح!</div>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger'>حدث خطأ أثناء تحديث الرحلة: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // إعادة التوجيه لتجنب إعادة التقديم عند تحديث الصفحة
    header("Location: add_tour_form.php?form=editTour&tourId=" . $tourId);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الرحلة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1>تعديل الرحلة</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tourId" value="<?= htmlspecialchars($tourId) ?>">

        <div class="mb-3">
            <label for="guideId" class="form-label">اختر المرشد</label>
            <select class="form-control" id="guideId" name="guideId" required>
                <?php foreach ($guides as $guide): ?>
                    <option value="<?= $guide['guideId'] ?>" <?= isset($currentTour['guideId']) && $currentTour['guideId'] == $guide['guideId'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($guide['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">مكان الرحلة</label>
            <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($currentTour['location']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">التاريخ</label>
            <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($currentTour['date']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">السعر</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($currentTour['price']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($currentTour['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($currentTour['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="city" class="form-label">المدينة</label>
            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($currentTour['city']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">المدة (بالساعات)</label>
            <input type="number" class="form-control" id="duration" name="duration" value="<?= htmlspecialchars($currentTour['duration']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="tourImage" class="form-label">صورة الرحلة (اختياري)</label>
            <input type="file" class="form-control" id="tourImage" name="tourImage" accept="image/*">
            <?php if ($currentTour['imageURL']): ?>
                <img src="<?= htmlspecialchars($currentTour['imageURL']) ?>" alt="Tour Image" style="width: 100px; margin-top: 10px;">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="places" class="form-label">اختر الأماكن</label>
            <select class="form-control" id="places" name="places[]" multiple>
                <?php foreach ($places as $place): ?>
                    <option value="<?= $place['placeId'] ?>" <?= in_array($place['placeId'], $tourPlaces) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($place['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" name="updateTour">تحديث الرحلة</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
