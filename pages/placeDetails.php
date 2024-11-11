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
// جلب بيانات المكان من قاعدة البيانات باستخدام معرف المكان
// $stmt = $pdo->prepare("SELECT name, description, imageURL, CityId, overview FROM place WHERE placeId = :placeId");
// $stmt->execute(['placeId' => $placeId]);
// $place = $stmt->fetch(PDO::FETCH_ASSOC);

// // التحقق من وجود البيانات
// if (!$place) {
//     die("لم يتم العثور على المكان.");
// }


$stmt->execute(['placeId' => $placeId]);
$place = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق من وجود البيانات
if (!$place) {
    die("لم يتم العثور على المكان.");
}

// اسم المكان للبحث في Google Places API
$placeName = $place['name'];

// إعداد مفتاح API
$apiKey = 'AIzaSyBeDzl0MOiEQpnwthVENf7xDdyF5rXyRio';

// تنفيذ طلب لجلب بيانات المكان
$googlePlacesUrl = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . urlencode($placeName) . "&inputtype=textquery&fields=place_id&key=" . $apiKey;
$response = file_get_contents($googlePlacesUrl);
$data = json_decode($response, true);

$googlePhotos = [];
$reviews = [];
$address = "غير متوفر";
$rating = "غير متوفر";

if ($data['status'] === 'OK' && !empty($data['candidates'])) {
    $placeIdGoogle = $data['candidates'][0]['place_id'];
    $placeDetailsUrl = "https://maps.googleapis.com/maps/api/place/details/json?place_id=" . urlencode($placeIdGoogle) . "&fields=name,formatted_address,rating,photos,reviews&key=" . $apiKey;
    $placeDetailsResponse = file_get_contents($placeDetailsUrl);
    $placeDetailsData = json_decode($placeDetailsResponse, true);

    if ($placeDetailsData['status'] === 'OK' && !empty($placeDetailsData['result'])) {
        $googlePlace = $placeDetailsData['result'];
        $name = $googlePlace['name'];
        $address = $googlePlace['formatted_address'];
        $rating = $googlePlace['rating'];
        
        if (!empty($googlePlace['photos'])) {
            foreach ($googlePlace['photos'] as $photo) {
                $googlePhotos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photoreference=" . $photo['photo_reference'] . "&key=" . $apiKey;
            }
        }

        if (!empty($googlePlace['reviews'])) {
            foreach ($googlePlace['reviews'] as $review) {
                $reviews[] = [
                    'author_name' => $review['author_name'],
                    'rating' => $review['rating'],
                    'text' => $review['text'],
                    'relative_time_description' => $review['relative_time_description'],
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($name); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeDzl0MOiEQpnwthVENf7xDdyF5rXyRio&libraries=places&callback=initMap" async defer></script>

    <style>
        .rating-stars {
            color: #FFD700;
        }
        .carousel-item img {
            max-height: 400px;
            object-fit: cover;
        }
        .review-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
    
</head>
<body>

<div class="container mt-5">
    <!-- عنوان المكان وتفاصيله -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><?php echo htmlspecialchars($name); ?></h1>
            <p><?php echo htmlspecialchars($address); ?></p>
            <p><strong>التقييم العام:</strong> <?php echo htmlspecialchars($rating); ?> <span class="rating-stars">&#9733;</span>/5</p>
        </div>
    </div>

    <!-- عارض الشرائح للصور -->
    <?php if (!empty($googlePhotos)): ?>
        <div id="carouselExampleIndicators" class="carousel slide mb-4" data-ride="carousel">
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

    <!-- قسم آراء المستخدمين -->
 
</div>
<div class="container mt-5">
    <!-- خريطة Google -->
    <h3>المنطقة</h3>
    <div id="map"></div>

    <!-- قائمة الأماكن القريبة -->
    <div class="place-list mt-4">
        <h4>أفضل الأماكن القريبة</h4>
        <div id="places"></div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    let map;
    let service;
    let infowindow;

    function initMap() {
        const location = { lat: 24.467, lng: 39.611 }; // قم بتغيير الإحداثيات حسب موقعك
        map = new google.maps.Map(document.getElementById("map"), {
            center: location,
            zoom: 15,
        });

        const request = {
            location: location,
            radius: '500',
            type: ['restaurant', 'museum', 'lodging'] // نوع الأماكن المطلوبة
        };

        service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, callback);
    }

    function callback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            const placesList = document.getElementById("places");
            results.forEach((place, index) => {
                const placeItem = document.createElement("div");
                placeItem.classList.add("place-item");

                // عرض اسم المكان
                const placeName = document.createElement("h5");
                placeName.textContent = place.name;
                placeItem.appendChild(placeName);

                // عرض العنوان
                const placeAddress = document.createElement("p");
                placeAddress.textContent = place.vicinity;
                placeItem.appendChild(placeAddress);

                // عرض التقييم
                if (place.rating) {
                    const rating = document.createElement("p");
                    rating.innerHTML = `<strong>التقييم:</strong> ${place.rating} <span class="rating-stars">&#9733;</span>`;
                    placeItem.appendChild(rating);
                }

                // عرض الصورة إذا كانت متاحة
                if (place.photos && place.photos.length > 0) {
                    const photo = document.createElement("img");
                    photo.src = place.photos[0].getUrl({ maxWidth: 100, maxHeight: 100 });
                    photo.classList.add("img-thumbnail", "mb-2");
                    placeItem.appendChild(photo);
                }

                placesList.appendChild(placeItem);
            });
        }
    }

    window.onload = initMap;
</script>
</body>
</html>
