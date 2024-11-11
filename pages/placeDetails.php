
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
    $placeDetailsUrl = "https://maps.googleapis.com/maps/api/place/details/json?place_id=" . urlencode($placeIdGoogle) . "&fields=name,formatted_address,rating,photos,reviews,geometry&key=" . $apiKey;
    $placeDetailsResponse = file_get_contents($placeDetailsUrl);
    $placeDetailsData = json_decode($placeDetailsResponse, true);

    if ($placeDetailsData['status'] === 'OK' && !empty($placeDetailsData['result'])) {
        $googlePlace = $placeDetailsData['result'];
        $name = $googlePlace['name'];
        $address = $googlePlace['formatted_address'];
        $rating = $googlePlace['rating'];
        $location = $googlePlace['geometry']['location'];
        
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
    <!-- إضافة ملف CSS مخصص -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- مكتبة Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- تضمين خرائط جوجل -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeDzl0MOiEQpnwthVENf7xDdyF5rXyRio&libraries=places&callback=initMap" async defer></script>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <?php include '../base_nav.php'; ?>
   <style>
        body {
           
          
        }
        .rating-stars {
            color: #FFD700;
        }
        .carousel-item img {
            max-height: 500px;
            object-fit: cover;
        }
        .review-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        #map {
            width: 100%;
            height: 400px;
        }
        .place-item img {
            max-width: 100%;
            height: auto;
        }
        .place-item {
            margin-bottom: 20px;
        }
        .place-item h5 {
            margin-top: 10px;
        }
        .carousel-indicators li {
            background-color: #000;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- عنوان المكان وتفاصيله -->
    <div class="mb-4">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <p class="text-muted"><?php echo htmlspecialchars($address); ?></p>
        <p><strong>التقييم العام:</strong> <?php echo htmlspecialchars($rating); ?> <span class="rating-stars">&#9733;</span>/5</p>
    </div>

    <!-- عارض الشرائح للصور -->
    <?php if (!empty($googlePhotos)): ?>
        <div id="carouselExampleIndicators" class="carousel slide mb-5" data-ride="carousel">
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
    <?php if (!empty($reviews)): ?>
        <div class="review-section mb-5">
            <h3>آراء الزوار</h3>
            <?php foreach ($reviews as $review): ?>
                <div class="media mb-4">
                    <i class="fas fa-user-circle fa-2x mr-3"></i>
                    <div class="media-body">
                        <h5 class="mt-0"><?php echo htmlspecialchars($review['author_name']); ?></h5>
                        <p class="rating-stars"><?php echo str_repeat('&#9733;', $review['rating']); ?><?php echo str_repeat('&#9734;', 5 - $review['rating']); ?></p>
                        <p><?php echo htmlspecialchars($review['text']); ?></p>
                        <small class="text-muted"><?php echo htmlspecialchars($review['relative_time_description']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- خريطة Google -->
    <h3>الموقع على الخريطة</h3>
    <div id="map"></div>

    <!-- قائمة الأماكن القريبة -->
    <div class="mt-5">
        <h3>أماكن قريبة مميزة</h3>
        <div id="places" class="row"></div>
    </div>
</div>

<!-- تضمين ملفات JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    let map;
    let service;
    let infowindow;

    function initMap() {
        const location = { lat: <?php echo $location['lat']; ?>, lng: <?php echo $location['lng']; ?> };

        map = new google.maps.Map(document.getElementById("map"), {
            center: location,
            zoom: 15,
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "<?php echo htmlspecialchars($name); ?>"
        });

        const request = {
            location: location,
            radius: '1000',
            type: ['tourist_attraction'] // أنواع الأماكن المطلوبة
        };

        service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, callback);
    }

    function callback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            const placesList = document.getElementById("places");
            results.forEach((place, index) => {
                const placeItem = document.createElement("div");
                placeItem.classList.add("col-md-4");

                const card = document.createElement("div");
                card.classList.add("card", "mb-4");

                // عرض الصورة إذا كانت متاحة
                if (place.photos && place.photos.length > 0) {
                    const photo = document.createElement("img");
                    photo.src = place.photos[0].getUrl({ maxWidth: 400, maxHeight: 200 });
                    photo.classList.add("card-img-top");
                    card.appendChild(photo);
                }

                const cardBody = document.createElement("div");
                cardBody.classList.add("card-body");

                // عرض اسم المكان
                const placeName = document.createElement("h5");
                placeName.classList.add("card-title");
                placeName.textContent = place.name;
                cardBody.appendChild(placeName);

                // عرض التقييم
                if (place.rating) {
                    const rating = document.createElement("p");
                    rating.innerHTML = `<strong>التقييم:</strong> ${place.rating} <span class="rating-stars">&#9733;</span>`;
                    cardBody.appendChild(rating);
                }

                // عرض العنوان
                const placeAddress = document.createElement("p");
                placeAddress.textContent = place.vicinity;
                cardBody.appendChild(placeAddress);

                card.appendChild(cardBody);
                placeItem.appendChild(card);
                placesList.appendChild(placeItem);
            });
        }
    }

    window.onload = initMap;
</script>
</body>
</html>
<?php include '../footer.php'; ?>
