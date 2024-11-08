<?php
// Include the database connection
include 'db.php';

// Get the tourId from the URL
$tourId = isset($_GET['tourId']) ? (int)$_GET['tourId'] : 0;

// Fetch the tour details along with the guide details
$tourStmt = $pdo->prepare("
    SELECT Tour.*, TourGuide.name AS guideName, TourGuide.imageURL AS guideImage, TourGuide.rating AS guideRating 
    FROM Tour 
    JOIN TourGuide ON Tour.guideId = TourGuide.guideId 
    WHERE Tour.tourId = :tourId
");
$tourStmt->execute(['tourId' => $tourId]);
$tour = $tourStmt->fetch(PDO::FETCH_ASSOC);

// Fetch associated places for this tour
$placesStmt = $pdo->prepare("
    SELECT Place.* 
    FROM Place 
    JOIN Tour_Places ON Place.placeId = Tour_Places.placeId 
    WHERE Tour_Places.tourId = :tourId
");
$placesStmt->execute(['tourId' => $tourId]);
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// استبدل `localhost/trips/uploads/` بمسار الصور الصحيح
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الجولة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="css/style.css" rel="stylesheet">

</head>
<body>

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Tour Image and Description -->
            <div class="col-lg-7">
                <?php if ($tour): ?>
                    <div class="tour-img mb-4">

                        


                        <img class="img-fluid" src="<?php echo htmlspecialchars( $tour['imageURL']); ?>" alt="Tour Image">

                    </div>
                    <h1 class="mb-3"><?php echo htmlspecialchars($tour['title']); ?></h1>
                    <p class="mb-4"><?php echo htmlspecialchars($tour['description']); ?></p>
                    <div class="d-flex mb-4">
                        <small class="me-3"><i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($tour['city']); ?></small>
                        <small class="me-3"><i class="fa fa-calendar-alt text-primary me-2"></i><?php echo htmlspecialchars($tour['duration']); ?> days</small>
                        <small><i class="fa fa-user text-primary me-2"></i>2-6 Persons</small>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <a href="index.php?page=booking&tourId=<?php echo $tour['tourId']; ?>" class="btn btn-primary">احجز الآن</a>
                    </div>
                <?php else: ?>
                    <p>لم يتم العثور على الجولة.</p>
                <?php endif; ?>
            </div>
            <!-- Tour Guide Details -->
            <div class="col-lg-5">
                <div class="tour-guide-details mb-4 text-center">
                    <h3 class="mb-3">المرشد السياحي</h3>
                    <?php if (!empty($tour['guideImage'])): ?>
    <img src="<?php echo htmlspecialchars($tour['guideImage']); ?>" alt="Guide Image" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px;">
<?php else: ?>
    <p>الصورة غير متوفرة</p>
<?php endif; ?>



                    <h5><?php echo htmlspecialchars($tour['guideName']); ?></h5>
                    <div class="mb-2">
                        <span>التقييم:</span>
                        <span class="text-warning"><?php echo str_repeat('★', (int)$tour['guideRating']); ?><?php echo str_repeat('☆', 5 - (int)$tour['guideRating']); ?></span>
                    </div>
                    <p>تقييم <?php echo htmlspecialchars($tour['guideRating']); ?>/5</p>
                </div>

                <!-- Tour Map and Key Highlights -->
                <div class="tour-map mb-4">
                    <iframe class="w-100" src="https://maps.google.com/maps?q=<?php echo urlencode($tour['city']); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

                <h3 class="mb-3">الأماكن لزيارتها</h3>
                <div class="row g-3">
                    <?php foreach ($places as $place): ?>
                        <div class="col-6">
                            <div class="place-item">
                            <?php if (!empty($place['imageURL'])): ?>

                                
    <img class="img-fluid" src="<?php echo htmlspecialchars($place['imageURL']); ?>" alt="Place Image">

    

<?php else: ?>
    <p>الصورة غير متوفرة</p>


                                <?php endif; ?>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$pdo = null;
?>
