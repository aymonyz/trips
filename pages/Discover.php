<?php
// Include the database connection file
include 'db.php';

// Initialize parameters
$params = [];
$cityImage = ""; // Variable to hold the city image path
$places = []; // Initialize places array

// Check if a city search has been made
if (isset($_GET['city']) && !empty(trim($_GET['city']))) {
    $city = trim($_GET['city']);
    
    // SQL query to fetch places and city info, filtered by city name
    $sql = "SELECT p.placeId, p.name, p.description, p.ImageURL AS placeImage, c.Name AS cityName, c.ImageURL AS CityImage 
        FROM Place p
        LEFT JOIN Cities c ON p.CityId = c.CityId 
        WHERE c.Name LIKE :city 
        AND p.PlaceId NOT IN (SELECT s.placeId FROM suggestedplaces s) 
        OR p.Approve = 1"; 
    
    $params[':city'] = '%' . $city . '%'; // Wildcard search for the city name
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $places = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If a city was searched and places are found, get the first matching city image (if any)
    if (!empty($places)) {
        $cityImage = htmlspecialchars($places[0]['CityImage']); // Get the image from the first result
        $cityName = htmlspecialchars($places[0]['cityName']); // Get the city name from the first result
    }
} else {
    // SQL query to fetch all places and city info
    $sql = "SELECT p.placeId, p.name, p.description, p.ImageURL AS placeImage, c.Name AS cityName, c.ImageURL AS CityImage 
            FROM Place p
            LEFT JOIN Cities c ON p.CityId = c.CityId
            WHERE p.PlaceId NOT IN (SELECT s.placeId FROM suggestedplaces s) 
        OR p.Approve = 1";
    
    // Prepare and execute the query without filtering by city
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $places = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!-- Display City Image and Name if exists and a city was searched -->
<?php if (!empty($cityImage)): ?>
            <div class="row g-4 justify-content-center mb-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="package-item">
                        <div class="overflow-hidden">
                        <img class="img-fluid" src="/trips/<?php echo $cityImage; ?>" alt="City Image">
                        </div>
                        <div class="d-flex border-bottom">
                            <small class="flex-fill text-center border-end py-2">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo $cityName; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
<!-- Package Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">اماكن</h6>
            <h1 class="mb-5">اكتشف أماكننا</h1>
        </div>
        
        <!-- Search Form -->
        <div class="container mb-4">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="city" placeholder="Search by city" value="<?php echo htmlspecialchars($_GET['city'] ?? ''); ?>">
                    <input type="hidden" name="page" value="Discover"> <!-- Ensure the page parameter is included -->
                    <button class="btn btn-primary" type="submit">بحث</button>
                </div>
            </form>
        </div>

        
        <?php
// تعديل مسار الصورة ليزيل ../
$cityImage = str_replace("../", "", htmlspecialchars($places[0]['CityImage'])); 
?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($places as $place): ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="package-item">
                        <div class="overflow-hidden">
                        <img class="img-fluid" src="/trips/<?php echo $cityImage; ?>" alt="City Image">
                     
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
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Package End -->

<?php
// Close the database connection
$pdo = null;
?>
