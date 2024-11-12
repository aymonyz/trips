<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php'; // تضمين الاتصال بقاعدة البيانات
include'index.php';

// التحقق من وجود طلب حذف
if (isset($_GET['removePlace'])) {
    $placeId = $_GET['removePlace'];
    
    try {
        // حذف المكان من قاعدة البيانات
        $stmt = $pdo->prepare("DELETE FROM place WHERE placeId = :placeId");
        $stmt->execute(['placeId' => $placeId]);
        echo "<div class='alert alert-success'>تم حذف المكان بنجاح.</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>حدث خطأ أثناء حذف المكان: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // إعادة التوجيه لتجنب إعادة تشغيل الحذف عند تحديث الصفحة
    header("Location: index.php?form=AddPlace");
    exit();
}

// التحقق من وجود طلب اعتماد
if (isset($_GET['approvePlace'])) {
    $placeId = $_GET['approvePlace'];
    
    try {
        // تحديث حالة الاعتماد
        $stmt = $pdo->prepare("UPDATE place SET Approve = 1 WHERE placeId = :placeId");
        $stmt->execute(['placeId' => $placeId]);
        echo "<div class='alert alert-success'>تم اعتماد المكان بنجاح.</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>حدث خطأ أثناء اعتماد المكان: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    header("Location: index.php?form=AddPlace");
    exit();
}

// التحقق من وجود طلب إضافة مكان
if (isset($_POST['addPlace'])) {
    $placeName = $_POST['placeName'];
    $placeDescription = $_POST['placeDescription'];
    $cityId = $_POST['cityId'];
    $imagePaths = [];

    // التحقق من تحميل الصور
    if (!empty($_FILES['placeImages']['name'][0])) {
        foreach ($_FILES['placeImages']['tmp_name'] as $index => $tmpName) {
            $imageName = basename($_FILES['placeImages']['name'][$index]);
            $path = 'uploads/'.$imageName;
            $imagePath = '../uploads/' . $imageName;

            // نقل الملف إلى مجلد التحميلات
            if (move_uploaded_file($tmpName, $imagePath)) {
                $imagePaths[] = $imagePath;
            } else {
                echo "<div class='alert alert-danger'>حدث خطأ أثناء تحميل الصورة رقم " . ($index + 1) . ".</div>";
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO place (name, description, imageURL, CityId, Approve) VALUES (:name, :description, :imageURL, :cityId, 1)");
            $stmt->execute([
                'name' => $placeName,
                'description' => $placeDescription,
                'imageURL' => $path,
                'cityId' => $cityId
            ]);
            echo "<div class='alert alert-success'>تمت إضافة المكان بنجاح.</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>حدث خطأ أثناء إضافة المكان: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>يرجى تحميل صورة واحدة على الأقل للمكان.</div>";
    }
}

// استرجاع جميع الأماكن مع أسماء المدن المرتبطة بها
$placeQuery = $pdo->query("
    SELECT p.placeId, p.name, p.description, c.Name AS cityName, Approve
    FROM Place p
    LEFT JOIN Cities c ON p.CityId = c.CityId
");
$places = $placeQuery->fetchAll(PDO::FETCH_ASSOC);

// استرجاع جميع المدن للاختيار في القائمة المنسدلة
$cityQuery = $pdo->query("SELECT * FROM Cities");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة الأماكن</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <h1>إضافة مكان</h1>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="placeName" class="form-label">اسم المكان</label>
      <input type="text" class="form-control" id="placeName" name="placeName" required>
    </div>
    <div class="mb-3">
      <label for="placeDescription" class="form-label">وصف المكان</label>
      <textarea class="form-control" id="placeDescription" name="placeDescription" required></textarea>
    </div>
    <div class="mb-3">
      <label for="placeImages" class="form-label">صور المكان</label>
      <input type="file" class="form-control" id="placeImages" name="placeImages[]" accept="image/*" multiple required>
    </div>
    <div class="mb-3">
      <label for="citySelect" class="form-label">المدينة</label>
      <select class="form-control" id="citySelect" name="cityId" required>
        <option value="">اختر المدينة</option>
        <?php foreach ($cities as $city): ?>
          <option value="<?= htmlspecialchars($city['CityId']) ?>">
            <?= htmlspecialchars($city['Name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-block" name="addPlace">إضافة المكان</button>
  </form>

  <h1 class="mt-5">كل الأماكن</h1>
  <?php if (!empty($places)): ?>
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th>المعرف</th>
          <th>الاسم</th>
          <th>الوصف</th>
          <th>المدينة</th>
          <th>هل تم اعتمادها؟</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($places as $place): ?>
          <tr>
            <td><?= htmlspecialchars($place['placeId']) ?></td>
            <td><?= htmlspecialchars($place['name']) ?></td>
            <td><?= htmlspecialchars($place['description']) ?></td>
            <td><?= htmlspecialchars($place['cityName'] ?? 'N/A') ?></td>
            <td><?= ($place['Approve'] == 1) ? 'نعم' : 'لا' ?></td>
            <td>
              <?php if ($place['Approve'] == 0): ?>
                <a href="?approvePlace=<?= htmlspecialchars($place['placeId']) ?>" class="btn btn-success btn-sm">اعتماد</a>
              <?php endif; ?>
              <a href="?removePlace=<?= htmlspecialchars($place['placeId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المكان؟');">حذف</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info text-center">لم يتم العثور على أماكن.</p>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
