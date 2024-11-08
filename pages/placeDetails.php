<?php
// تضمين ملف الاتصال بقاعدة البيانات
include '../db.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// الحصول على معرف المكان placeId من رابط URL
$placeId = isset($_GET['placeId']) ? (int)$_GET['placeId'] : 0;

// جلب بيانات المكان من قاعدة البيانات باستخدام معرف المكان
$stmt = $pdo->prepare("SELECT name, description, imageURL, CityId FROM place WHERE placeId = :placeId");
$stmt->execute(['placeId' => $placeId]);
$place = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق من وجود البيانات
if (!$place) {
    die("لم يتم العثور على المكان.");
}

// اسم المدينة أو المكان للبحث في Google Places API
$placeName = $place['name'];

// إعداد مفتاح API
$apiKey = '258385a12b8c34dcd209ad9fc54e73a23890445ce22e678f0da53219d6d0e9de'; 

// تنفيذ طلب لجلب بيانات المكان
$googlePlacesUrl = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . urlencode($placeName) . "&inputtype=textquery&fields=photos,formatted_address,name,rating&key=" . $apiKey;
$response = file_get_contents($googlePlacesUrl);
$data = json_decode($response, true);

// التحقق من جلب البيانات
$googlePhotos = [];
if ($data['status'] === 'OK' && !empty($data['candidates'])) {
    $googlePlace = $data['candidates'][0];
    $name = $googlePlace['name'] ?? 'غير متوفر';
    $address = $googlePlace['formatted_address'] ?? 'غير متوفر';
    $rating = $googlePlace['rating'] ?? 'غير متوفر';

    // جمع مراجع الصور من Google
    if (!empty($googlePlace['photos'])) {
        foreach ($googlePlace['photos'] as $photo) {
            $googlePhotos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photoreference=" . $photo['photo_reference'] . "&key=" . $apiKey;
        }
    }
} else {
    $name = $place['name'];
    $address = "العنوان غير متوفر";
    $rating = "غير متوفر";
}

include '../nav.php';
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل المكان | <?php echo htmlspecialchars($name); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .rating-stars {
            color: #FFD700;
        }
        .review-section {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><?php echo htmlspecialchars($name); ?></h2>
            </div>
            <div class="card-body">
                <p class="text-muted"><?php echo htmlspecialchars($place['description']); ?></p>
                <p><strong>العنوان:</strong> <?php echo htmlspecialchars($address); ?></p>
                <p><strong>التقييم العام:</strong> <?php echo htmlspecialchars($rating); ?> <span class="rating-stars">&#9733;</span>/5</p>

                <!-- عارض الشرائح للصور -->
                <?php if (!empty($googlePhotos)): ?>
                    <div id="carouselExampleIndicators" class="carousel slide mt-4" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <?php foreach ($googlePhotos as $index => $photo): ?>
                                <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                            <?php endforeach; ?>
                        </ol>
                        <div class="carousel-inner">
                            <?php foreach ($googlePhotos as $index => $photo): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo $photo; ?>" class="d-block w-100" alt="صورة من خرائط Google">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">السابق</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">التالي</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- قسم آراء المستخدمين -->
        <div class="review-section">
            <h3 class="mt-5">آراء المستخدمين</h3>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">أحمد العلي</h5>
                    <p class="card-text">تجربة رائعة! كان المكان هادئًا وجميلًا، وأنصح بزيارته.</p>
                    <p><strong>التقييم:</strong> 5 <span class="rating-stars">&#9733;</span></p>
                    <p class="text-muted">منذ يومين</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">فاطمة محمد</h5>
                    <p class="card-text">مكان جميل ولكن مزدحم قليلاً خلال أوقات الذروة.</p>
                    <p><strong>التقييم:</strong> 4 <span class="rating-stars">&#9733;</span></p>
                    <p class="text-muted">منذ أسبوع</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">محمد عبد الله</h5>
                    <p class="card-text">خدمة رائعة ومرافق نظيفة، أوصي بزيارته للجميع.</p>
                    <p><strong>التقييم:</strong> 5 <span class="rating-stars">&#9733;</span></p>
                    <p class="text-muted">منذ شهر</p>
                </div>
            </div>
        </div>
    </div>
<?php
include '../footer.php';
?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
