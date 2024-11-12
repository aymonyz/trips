<?php
// تضمين ملف الاتصال بقاعدة البيانات
include 'db.php';

// الحصول على التصنيف المحدد للتصفية إذا تم اختياره
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$selectedCityId = isset($_GET['cityId']) ? (int)$_GET['cityId'] : null;

// جلب قائمة التصنيفات المتاحة من قاعدة البيانات
$categoriesQuery = $pdo->query("SELECT DISTINCT category FROM place ORDER BY category ASC");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// جلب قائمة المدن المتاحة من قاعدة البيانات
$citiesQuery = $pdo->query("SELECT CityId, Name FROM Cities ORDER BY Name ASC");
$cities = $citiesQuery->fetchAll(PDO::FETCH_ASSOC);

// بناء استعلام لجلب الأماكن مع شروط المدينة والتصنيف
$sql = "SELECT p.placeId, p.name, p.description, p.ImageURL AS placeImage, c.Name AS cityName
        FROM place p
        LEFT JOIN Cities c ON p.CityId = c.CityId
        WHERE p.Approve = 1";

// إضافة شروط التصفية بناءً على المدينة والتصنيف إذا تم تحديدهما
$params = [];
if ($selectedCityId) {
    $sql .= " AND c.CityId = :cityId";
    $params[':cityId'] = $selectedCityId;
}
if ($selectedCategory) {
    $sql .= " AND p.category = :category";
    $params[':category'] = $selectedCategory;
}

$sql .= " ORDER BY p.category ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>اكتشف أماكننا</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- أزرار تصفية المدن -->
<div class="container my-4">
    <h3 class="text-center">فلترة حسب المدينة</h3>
    <div class="filter-buttons text-center">
        <a href="?page=Discover" class="btn btn-outline-primary <?= is_null($selectedCityId) ? 'active' : ''; ?>">كل المدن</a>
        <?php foreach ($cities as $city): ?>
            <a href="?page=Discover&cityId=<?= $city['CityId']; ?>&category=<?= urlencode($selectedCategory); ?>" 
               class="btn btn-outline-primary <?= ($selectedCityId == $city['CityId']) ? 'active' : ''; ?>">
                <?= htmlspecialchars($city['Name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- أزرار تصفية التصنيفات -->
<div class="container my-4">
    <h3 class="text-center">فلترة حسب التصنيف</h3>
    <div class="filter-buttons text-center">
        <a href="?page=Discover&cityId=<?= $selectedCityId; ?>" 
           class="btn btn-outline-primary <?= $selectedCategory === '' ? 'active' : ''; ?>">كل التصنيفات</a>
        <?php foreach ($categories as $category): ?>
            <a href="?page=Discover&cityId=<?= $selectedCityId; ?>&category=<?= urlencode($category['category']); ?>" 
               class="btn btn-outline-primary <?= ($selectedCategory === $category['category']) ? 'active' : ''; ?>">
                <?= htmlspecialchars($category['category']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- عرض الأماكن بناءً على المدينة والتصنيف المحددين -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center">
            <h6 class="section-title bg-white text-center text-primary px-3">أماكن</h6>
            <h1 class="mb-5">اكتشف أماكننا</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (empty($places)): ?>
                <p class="text-center">لا توجد أماكن مطابقة لهذا التصنيف أو المدينة.</p>
            <?php else: ?>
                <?php foreach ($places as $place): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="package-item">
                            <a href="/trips/pages/placeDetails.php?placeId=<?= htmlspecialchars($place['placeId']); ?>" style="text-decoration: none; color: inherit;">
                                <div class="overflow-hidden">
                                    <img class="img-fluid" style="width: 100%;" src="/trips/<?= htmlspecialchars($place['placeImage']); ?>" alt="Place Image">
                                </div>
                                <div class="text-center p-4">
                                    <h3 class="mb-0"><?= htmlspecialchars($place['name']); ?></h3>
                                    <p><?= htmlspecialchars($place['description']); ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// إغلاق الاتصال بقاعدة البيانات
$pdo = null;
?>
