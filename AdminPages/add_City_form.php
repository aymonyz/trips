<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php'; // تضمين الاتصال بقاعدة البيانات
include 'index.php'; 

if (isset($_GET['guideId'])) {
  $guideId = $_GET['guideId'];
}
if (isset($_GET['removeCity'])) {
    $cityId = $_GET['removeCity'];
    try {
        // تعطيل التحقق من القيود المفتاحية الخارجية مؤقتًا
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // حذف الصفوف المرتبطة في جدول tour_places عبر الأماكن المرتبطة بالمدينة
        $deleteTourPlacesStmt = $pdo->prepare("DELETE FROM tour_places WHERE placeId IN (SELECT placeId FROM place WHERE CityId = :cityId)");
        $deleteTourPlacesStmt->execute(['cityId' => $cityId]);

        // حذف الأماكن المرتبطة بالمدينة
        $deletePlacesStmt = $pdo->prepare("DELETE FROM place WHERE CityId = :cityId");
        $deletePlacesStmt->execute(['cityId' => $cityId]);

        // بعد حذف الأماكن المرتبطة، قم بحذف المدينة
        $stmt = $pdo->prepare("DELETE FROM cities WHERE CityId = :cityId");
        $stmt->execute(['cityId' => $cityId]);

        // إعادة تمكين التحقق من القيود المفتاحية الخارجية
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        echo "<div class='alert alert-success'>تم حذف المدينة بنجاح.</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>حدث خطأ أثناء حذف المدينة: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // إعادة التوجيه لتجنب تكرار الحذف عند التحديث
    header("Location: index.php?form=AddCity");
    exit();
}

if (isset($_POST['addCity'])) {
    $name = $_POST['Name'];

    // التحقق من تحميل الصورة
    if (isset($_FILES['cityImage']) && $_FILES['cityImage']['error'] == 0) {
        // تحديد مسار الحفظ
        $imageName = basename($_FILES['cityImage']['name']);
        
        $imagePath = '../uploads/' . $imageName;
        $path='uploads/'.$imageName;

        // التحقق من نقل الملف
        if (move_uploaded_file($_FILES['cityImage']['tmp_name'], $imagePath)) {
            $stmt = $pdo->prepare("INSERT INTO cities (Name, ImageURL) VALUES (:name, :imagePath)");
            $stmt->execute(['name' => $name, 'imagePath' => $path]);
            echo "<div class='alert alert-success'>تم رفع الصورة بنجاح.</div>";
            exit();
        } else {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء نقل الصورة إلى مجلد 'uploads'.</div>";
            echo "<pre>";
            print_r(error_get_last());
            echo "</pre>";
        }
    } else {
        // التحقق من نوع الخطأ في حالة فشل الرفع
        switch ($_FILES['cityImage']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "<div class='alert alert-warning'>حجم الملف يتجاوز الحد الأقصى المسموح به.</div>";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "<div class='alert alert-warning'>تم تحميل جزء فقط من الملف.</div>";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "<div class='alert alert-warning'>لم يتم اختيار ملف للتحميل.</div>";
                break;
            default:
                echo "<div class='alert alert-danger'>حدث خطأ غير متوقع أثناء رفع الملف.</div>";
                break;
        }
    }
}

// استرجاع جميع المدن لعرضها في الجدول
$cityQuery = $pdo->query("SELECT * FROM cities where");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Cities</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
  <h1 class="mb-4">صفحة إضافة المدن </h1>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="Name" class="form-label">اسم المدينة</label>
      <input type="text" class="form-control" id="Name" name="Name" required>
    </div>
    
    <div class="mb-3">
      <label for="cityImage" class="form-label">صورة المدينة</label>
      <input type="file" class="form-control" id="CityImage" name="cityImage" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-primary w-100" name="addCity">إضافة مدينة</button>
  </form>

  <h1 class="mt-5">إضافة مدينة</h1>
  <?php if (!empty($cities)): ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cities as $city): ?>
          <tr>
            <td><?= htmlspecialchars($city['CityId']) ?></td>
            <td><?= htmlspecialchars($city['Name']) ?></td>
            <td><img src="../<?= htmlspecialchars($city['ImageURL']) ?>" alt="City Image" class="img-thumbnail"></td>
            <td>
              <a href="?removeCity=<?= htmlspecialchars($city['CityId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this city?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info text-center">No cities found.</p>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
