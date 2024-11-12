<?php
include '../db.php';
session_start();
if (!isset($_SESSION['userId'])) {
  header("Location: ../index.php?=home");
  exit();
}
$role = 'guide';

$guideId = $_GET['guideId'];

// Fetch places for the specific guide
$placeQuery = $pdo->prepare("
    SELECT p.placeId, p.name, p.description, c.Name AS cityName ,p.Approve,category
    FROM Place p
    LEFT JOIN Cities c ON p.CityId = c.CityId
    WHERE p.guideId = ?
");
$placeQuery->execute([$guideId]);
$places = $placeQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all cities for the select dropdown
$cityQuery = $pdo->query("SELECT * FROM Cities");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);

// Add place if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSuggest'])) {
    $placeName = $_POST['placeName'];
    $placeDescription = $_POST['placeDescription'];
    $cityId = $_POST['cityId'];
    $category= $_POST['category'];

    // Image upload handling
    $imagePath = null;
    if (!empty($_FILES['placeImage']['name'])) {
        $imagePath = "../uploads/" . basename($_FILES['placeImage']['name']);
        $path = "uploads/" . basename($_FILES['placeImage']['name']);
        if (!move_uploaded_file($_FILES['placeImage']['tmp_name'], $imagePath)) {
            echo "Failed to upload image!";
            exit();
        }
    }

    // Insert new place into the database
    $insertPlace = $pdo->prepare("
        INSERT INTO Place (guideId, name, description, CityId, imageURL,category) 
        VALUES (?, ?, ?, ?, ?,?)
    ");
    $insertPlace->execute([$guideId, $placeName, $placeDescription, $cityId, $path,$category]);

    // Reload the page to reflect the new place in the table
    header("Location: add_suggest_form.php?guideId=" . $guideId);
    exit();
}

// Handle place deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletePlace'])) {
    $placeId = $_POST['placeId'];

    // Delete related entries in tour_places before deleting the place
    $deleteRelatedQuery = $pdo->prepare("DELETE FROM tour_places WHERE placeId = ?");
    $deleteRelatedQuery->execute([$placeId]);

    // Delete the place from the database
    $deleteQuery = $pdo->prepare("DELETE FROM Place WHERE placeId = ?");
    $deleteQuery->execute([$placeId]);

    // Reload the page to reflect the deletion
    header("Location: add_suggest_form.php?guideId=" . $guideId);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة الأماكن السياحية</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../base_nav.php'; ?>
<div class="container mt-5">
  <h1 class="mb-4">إضافة مكان سياحي</h1>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="placeName" class="form-label">اسم المكان</label>
      <input type="text" class="form-control" id="placeName" name="placeName" required>
    </div>
    <div class="mb-3">
  <label for="placeCategory" class="form-label">تصنيف المكان</label>
  <select class="form-control" id="placeCategory" name="category" required>
    <option value="">اختر التصنيف</option>
    <option value="تاريخي">تاريخي</option>
    <option value="طبيعي">طبيعي</option>
    <option value="ترفيهي">ترفيهي</option>
    <option value="ثقافي">ثقافي</option>
  </select>
</div>
    <div class="mb-3">
      <label for="placeDescription" class="form-label">وصف المكان</label>
      <textarea class="form-control" id="placeDescription" name="placeDescription" required></textarea>
    </div>
    <div class="mb-3">
      <label for="placeImage" class="form-label">صورة المكان</label>
      <input type="file" class="form-control" id="placeImage" name="placeImage" accept="image/*" required>
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
    <button type="submit" class="btn btn-primary" name="addSuggest">إضافة المكان</button>
  </form>

  <h1 class="mt-5">جميع الأماكن</h1>
  <?php if (!empty($places)): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>الرقم</th>
          <th>الاسم</th>
          <th>الوصف</th>
          <th>المدينة</th>
          <th>التنصيف</th>
          <th>هل تم اعتمادها؟</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($places as $place): ?>
          <tr>
            <td><?= htmlspecialchars($place['placeId']) ?></td>
            <td><?= htmlspecialchars($place['name']) ?></td>
            <td><?= htmlspecialchars($place['description']) ?></td>
            <td><?= htmlspecialchars($place['cityName'] ?? 'غير متوفر') ?></td>
            <td><?= htmlspecialchars($place['category']) ?></td>
            <td><?= ($place['Approve'] == 1) ? 'نعم' : 'لا' ?></td>
            <td>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="placeId" value="<?= htmlspecialchars($place['placeId']) ?>">
                <button type="submit" name="deletePlace" class="btn btn-danger" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المكان؟');">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info">لاتوجد أماكن مقترحة حاليًا</p>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
