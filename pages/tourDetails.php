<?php
// Include the database connection
include 'db.php';

// Get the tourId from the URL
$tourId = isset($_GET['tourId']) ? (int)$_GET['tourId'] : 0;

// Fetch the tour details
$tourStmt = $pdo->prepare("SELECT * FROM Tour WHERE tourId = :tourId");
$tourStmt->execute(['tourId' => $tourId]);
$tour = $tourStmt->fetch(PDO::FETCH_ASSOC);

// Fetch associated places for this tour
$placesStmt = $pdo->prepare("SELECT Place.* FROM Place
    JOIN Tour_Places ON Place.placeId = Tour_Places.placeId
    WHERE Tour_Places.tourId = :tourId");
$placesStmt->execute(['tourId' => $tourId]);
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Tour Details Start -->
<div class="container-xxl py-5">
    <div class="container">
        <!-- Tour Main Section -->
        <div class="row g-4">
            <!-- Tour Image and Description -->
            <div class="col-lg-7">
                <?php if ($tour): ?>
                    <div class="tour-img mb-4">
                        <img class="img-fluid w-100" src="<?php echo htmlspecialchars($tour['imageURL']); ?>" alt="Tour Image">
                    </div>
                    <h1 class="mb-3"><?php echo htmlspecialchars($tour['title']); ?></h1>
                    <p class="mb-4"><?php echo htmlspecialchars($tour['description']); ?></p>
                    <div class="d-flex mb-4">
                        <small class="me-3"><i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($tour['city']); ?></small>
                        <small class="me-3"><i class="fa fa-calendar-alt text-primary me-2"></i><?php echo htmlspecialchars($tour['duration']); ?> days</small>
                        <small><i class="fa fa-user text-primary me-2"></i>2-6 Person</small>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <a href="index.php?page=booking&tourId=<?php echo $row['tourId']; ?>" class="btn btn-primary">Book Now</a>
                    </div>
                <?php else: ?>
                    <p>Tour not found.</p>
                <?php endif; ?>
            </div>

            <!-- Tour Map and Key Highlights -->
            <div class="col-lg-5">
                <div class="tour-map mb-4">
                    <iframe class="w-100" src="https://maps.google.com/maps?q=<?php echo urlencode($tour['city']); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <h3 class="mb-3">Places to Visit</h3>
                <div class="row g-3">
                    <?php foreach ($places as $place): ?>
                        <div class="col-6">
                            <div class="place-item">
                                <img class="img-fluid mb-2" src="<?php echo htmlspecialchars($place['imageURL']); ?>" alt="Place Image">
                                <h6><?php echo htmlspecialchars($place['name']); ?></h6>
                                <p><?php echo htmlspecialchars($place['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tour Details End -->

<?php
// Close the database connection
$pdo = null;
?>
