<?php
session_start(); // تأكد من بدء الجلسة

include '../db.php'; 

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تحقق من تسجيل الدخول
if (!isset($_SESSION['userId'])) {
    echo "خطأ: لم يتم العثور على هوية المستخدم. يرجى تسجيل الدخول.";
    exit;
}

// الحصول على guideId من الجلسة أو رابط الصفحة
if (isset($_GET['guideId'])) {
    $guideId = $_GET['guideId'];
} else {
    echo "خطأ: لم يتم تحديد guideId.";
    exit;
}

// استعلام لجلب بيانات المرشد
$guidesStmt = $pdo->prepare("SELECT guideId, name FROM TourGuide WHERE guideId = ?");
$guidesStmt->execute([$guideId]);
$guide = $guidesStmt->fetch(PDO::FETCH_ASSOC);

// استعلام لجلب جميع الأماكن المتاحة
$placesStmt = $pdo->query("SELECT placeId, name FROM Place WHERE placeId NOT IN (SELECT placeId FROM suggestedplaces) AND Approve = 1;");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// استعلام لجلب الجولات الحالية الخاصة بالمرشد
$toursStmt = $pdo->prepare("SELECT t.tourId, t.title, t.location, t.date, t.price, t.city, t.duration 
    FROM Tour t WHERE t.guideId = ?");
$toursStmt->execute([$guideId]);
$tours = $toursStmt->fetchAll(PDO::FETCH_ASSOC);

// معالجة طلب إضافة جولة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTour'])) {
    // جلب بيانات الجولة من النموذج
    $location = $_POST['location'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $duration = $_POST['duration'];
    $places = $_POST['places'] ?? [];

    $imagePath = null;
    if (!empty($_FILES['tourImage']['name'])) {
        $Path = "uploads/" . basename($_FILES['tourImage']['name']);
        $imagePath = "../uploads/" . basename($_FILES['tourImage']['name']);
        if (!move_uploaded_file($_FILES['tourImage']['tmp_name'], $imagePath)) {
            echo "فشل تحميل الصورة!";
            exit();
        }
    }

    try {
        $pdo->beginTransaction();

        // إدخال الجولة الجديدة
        $insertQuery = $pdo->prepare("
            INSERT INTO tour (guideId, location, date, price, imageURL, description, city, duration, title) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->execute([$guideId, $location, $date, $price, $Path, $description, $city, $duration, $title]);

        // جلب معرف الجولة الجديدة
        $tourId = $pdo->lastInsertId();

        // ربط الجولة بالأماكن المحددة
        if (!empty($places)) {
            $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
            foreach ($places as $placeId) {
                $stmtPlaces->execute([$tourId, $placeId]);
            }
        }

        $pdo->commit();
        echo "تمت إضافة الجولة والأماكن بنجاح!";
        header("Location: add_tour_form.php?guideId=" . $guideId);
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "حدث خطأ أثناء إضافة الجولة: " . htmlspecialchars($e->getMessage());
    }
}

// معالجة طلب حذف جولة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteTour'])) {
    $tourId = $_POST['tourId'];

    try {
        // بدء المعاملة
        $pdo->beginTransaction();

        // حذف السجلات المرتبطة بالجولة من جدول Tour_Places
        $deleteTourPlacesQuery = $pdo->prepare("DELETE FROM Tour_Places WHERE tourId = ?");
        $deleteTourPlacesQuery->execute([$tourId]);

        // حذف الجولة من جدول Tour
        $deleteTourQuery = $pdo->prepare("DELETE FROM Tour WHERE tourId = ?");
        $deleteTourQuery->execute([$tourId]);

        // تأكيد العملية
        $pdo->commit();

        echo "تم حذف الجولة بنجاح!";
        header("Location: add_tour_form.php?guideId=" . $guideId);
        exit();

    } catch (PDOException $e) {
        // التراجع في حالة حدوث خطأ
        $pdo->rollBack();
        echo "حدث خطأ أثناء حذف الجولة: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الجولات</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../base_nav.php'; ?>
<div class="container mt-5">
    
    <h1 class="mb-4">إضافة جولة</h1>
    <form method="POST" enctype="multipart/form-data">
        <!-- Form fields for adding a new tour -->
        <input type="hidden" name="addTour" value="1">

        <div class="mb-3">
            <label for="guideId" class="form-label">المرشد</label>
            <input type="text" class="form-control" id="guideId" name="guideId" value="<?= htmlspecialchars($guide['name']) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">موقع الجولة</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">التاريخ</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">السعر</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">اسم المسار</label>
            <input type="text" class="form-control" id="city" name="city" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">المدة (بالساعات)</label>
            <input type="number" class="form-control" id="duration" name="duration" required>
        </div>
        <div class="mb-3">
            <label for="tourImage" class="form-label">صورة الجولة (اختياري)</label>
            <input type="file" class="form-control" id="tourImage" name="tourImage" accept="image/*">
        </div>
        
        <div class="mb-3">
            <label>اختر الأماكن</label>
            <div>
                <?php foreach ($places as $place): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="places[]" id="place<?= $place['placeId'] ?>" value="<?= $place['placeId'] ?>">
                        <label class="form-check-label" for="place<?= $place['placeId'] ?>">
                            <?= htmlspecialchars($place['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">حفظ الجولة</button>
    </form>

    <h1 class="mt-5">جميع الجولات الخاصة بالمرشد</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>الموقع</th>
                <th>التاريخ</th>
                <th>السعر</th>
                <th>المدينة</th>
                <th>المدة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?= htmlspecialchars($tour['title']) ?></td>
                    <td><?= htmlspecialchars($tour['location']) ?></td>
                    <td><?= htmlspecialchars($tour['date']) ?></td>
                    <td><?= htmlspecialchars($tour['price']) ?></td>
                    <td><?= htmlspecialchars($tour['city']) ?></td>
                    <td><?= htmlspecialchars($tour['duration']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="tourId" value="<?= htmlspecialchars($tour['tourId']) ?>">
                            <button type="submit" name="deleteTour" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه الجولة؟');">حذف</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
