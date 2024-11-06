<?php
// Include the database connection file
include 'db.php';

// SQL query to fetch all tours
$sql = "SELECT * FROM Tour";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Package Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Packages</h6>
            <h1 class="mb-5">Awesome Packages</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($tours as $row): ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="package-item">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="<?php echo htmlspecialchars($row['imageURL']); ?>" alt="Package Image">
                        </div>
                        <div class="d-flex border-bottom">
                            <small class="flex-fill text-center border-end py-2">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($row['city']); ?>
                            </small>
                            <small class="flex-fill text-center border-end py-2">
                                <i class="fa fa-calendar-alt text-primary me-2"></i><?php echo htmlspecialchars($row['duration']); ?> days
                            </small>
                            <small class="flex-fill text-center py-2">
                                <i class="fa fa-user text-primary me-2"></i>2-8 Persons
                            </small>
                        </div>
                        <div class="text-center p-4">
                            <h3 class="mb-0">$<?php echo number_format($row['price'], 2); ?></h3>
                            <div class="mb-3">
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                                <small class="fa fa-star text-primary"></small>
                            </div>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="d-flex justify-content-center mb-2">
                                <a href="index.php?page=tourDetails&tourId=<?php echo $row['tourId']; ?>" class="btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Read More</a>
                                <a href="index.php?page=booking&tourId=<?php echo $row['tourId']; ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Book Now</a>
                            </div>
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
