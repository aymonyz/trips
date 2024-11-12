<?php
// Include the database connection file
include 'db.php';

// Initialize parameters
$params = [];
$cityImage = ""; // Variable to hold the city image path
$places = []; // Initialize places array

// Get the selected city ID if available
$selectedCityId = isset($_GET['cityId']) ? (int)$_GET['cityId'] : null;

// SQL query to fetch all cities for filter buttons
$citiesQuery = $pdo->query("SELECT CityId, Name FROM Cities ORDER BY Name ASC");
$cities = $citiesQuery->fetchAll(PDO::FETCH_ASSOC);

// SQL query to fetch places based on selected city
if ($selectedCityId) {
    $sql = "SELECT p.placeId, p.name, p.description, p.ImageURL AS placeImage, c.Name AS cityName, c.ImageURL AS CityImage 
            FROM Place p
            LEFT JOIN Cities c ON p.CityId = c.CityId 
            WHERE c.CityId = :cityId 
            AND (p.PlaceId NOT IN (SELECT s.placeId FROM suggestedplaces s) AND p.Approve = 1)";
    $params[':cityId'] = $selectedCityId;
} else {
    $sql = "SELECT p.placeId, p.name, p.description, p.ImageURL AS placeImage, c.Name AS cityName, c.ImageURL AS CityImage 
            FROM Place p
            LEFT JOIN Cities c ON p.CityId = c.CityId
            WHERE p.PlaceId NOT IN (SELECT s.placeId FROM suggestedplaces s) 
            AND p.Approve = 1";
}

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
    <style>
        .filter-buttons .btn {
            margin: 0 5px 10px 0;
        }
    </style>
</head>
<body>

<!-- Display City Filter Buttons -->
<div class="container my-4">
    <h3 class="text-center">فلترة حسب المدينة</h3>
    <div class="filter-buttons text-center">
        <a href="?page=Discover" class="btn btn-outline-primary <?php echo is_null($selectedCityId) ? 'active' : ''; ?>">كل المدن</a>
        <?php foreach ($cities as $city): ?>
            <a href="?page=Discover&cityId=<?php echo $city['CityId']; ?>" class="btn btn-outline-primary <?php echo ($selectedCityId == $city['CityId']) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($city['Name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Display City Image and Places -->
<?php if (!empty($places)): ?>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">اماكن</h6>
                <h1 class="mb-5"><?php echo isset($places[0]['cityName']) ? 'اكتشف أماكن في ' . htmlspecialchars($places[0]['cityName']) : 'اكتشف أماكننا'; ?></h1>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($places as $place): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="package-item">
                            <a href="/trips/pages/placeDetails.php?placeId=<?php echo htmlspecialchars($place['placeId']); ?>" style="text-decoration: none; color: inherit;">
                                <div class="overflow-hidden">
                                    <img class="img-fluid" style="width: 100%;" src="/trips/<?php echo htmlspecialchars($place['placeImage']); ?>" alt="Place Image">
                                </div>
                                <div class="d-flex border-bottom">
                                    <small class="flex-fill text-center border-end py-2">
                                        <i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($place['cityName']); ?>
                                    </small>
                                </div>
                                <div class="text-center p-4">
                                    <h3 class="mb-0"><?php echo htmlspecialchars($place['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($place['description']); ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <p class="text-center">لا توجد أماكن متاحة في هذه المدينة.</p>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$pdo = null;
?>
