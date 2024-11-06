<?php
 include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    // Redirect to login page if not logged in
    header('Location: index.php?page=login');
    exit;
}

// Get logged-in user data
$userId = $_SESSION['userId'];
$userQuery = $pdo->prepare("SELECT * FROM user WHERE userId = ?");
$userQuery->execute([$userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Get tour data based on passed tour ID
$tourId = isset($_GET['tourId']) ? $_GET['tourId'] : null;
if ($tourId) {
    $tourQuery = $pdo->prepare("SELECT * FROM Tour WHERE tourId = ?");
    $tourQuery->execute([$tourId]);
    $tour = $tourQuery->fetch(PDO::FETCH_ASSOC);
} else {
    echo "Tour not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $specialRequest = $_POST['specialRequest'];

    $bookingQuery = $pdo->prepare("INSERT INTO Booking (userId, tourId, bookingDate, specialRequest) VALUES (?, ?, ?, ?)");
    $bookingQuery->execute([$userId, $tourId, $date, $specialRequest]);

    echo "Booking confirmed!";
    exit;
}
?>

<!-- Booking Form -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="booking p-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-6 text-white">
                    <h6 class="text-white text-uppercase">Booking</h6>
                    <h1 class="text-white mb-4">Online Booking</h1>
                    <p class="mb-4">Enjoy your trip to <?php echo htmlspecialchars($tour['title']); ?>.</p>
                </div>
                <div class="col-md-6">
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-transparent" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-transparent" id="email" value="<?php echo htmlspecialchars($user['emailAddress']); ?>" readonly>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating date" id="date3" data-target-input="nearest">
                                    <input type="date" name="date" class="form-control bg-transparent datetimepicker-input" id="datetime" placeholder="Date & Time" required>
                                    <label for="datetime">Date & Time</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-transparent" id="destination" value="<?php echo htmlspecialchars($tour['title']); ?>" readonly>
                                    <label for="destination">Destination</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="specialRequest" class="form-control bg-transparent" placeholder="Special Request" id="message" style="height: 100px"></textarea>
                                    <label for="message">Number of persons & Special Request</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-light w-100 py-3" type="submit">Book Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>