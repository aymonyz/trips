<?php
include '../db.php';  // Database connection
include 'index.php'; 
// Fetch all guides for the dropdown
$guidesStmt = $pdo->query("SELECT guideId, name FROM TourGuide");
$guides = $guidesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all places for the dropdown
$placesStmt = $pdo->query("SELECT placeId, name FROM Place");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tours for displaying in the table
$toursStmt = $pdo->query("SELECT t.tourId, t.title, t.location, t.date, t.price, t.city, t.duration, g.name AS guideName 
    FROM Tour t JOIN TourGuide g ON t.guideId = g.guideId");
$tours = $toursStmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Ensure $tours is an array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guideId = $_POST['guideId'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $duration = $_POST['duration'];
    $places = $_POST['places'] ?? [];

    // Handle file upload only if an image is provided
    $imagePath = null;
    if (!empty($_FILES['tourImage']['name'])) {
        $imagePath = "uploads/" . basename($_FILES['tourImage']['name']);
        if (!move_uploaded_file($_FILES['tourImage']['tmp_name'], $imagePath)) {
            echo "Done!!";
            exit();
        }
    }

    try {
        $pdo->beginTransaction();

        // Insert tour with image path (if available)
        $insertQuery = $pdo->prepare("
            INSERT INTO tour (guideId, location, date, price, imageURL, description, city, duration, title) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->execute([$guideId, $location, $date, $price, $imagePath, $description, $city, $duration, $title]);

        // Get the last inserted tour ID for the new tour
        $tourId = $pdo->lastInsertId();

        // Insert selected places if any are provided
        if (!empty($places)) {
            $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
            foreach ($places as $placeId) {
                $stmtPlaces->execute([$tourId, $placeId]);
            }
        }

        $pdo->commit();
        echo "Tour and associated places added successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error adding the tour: " . htmlspecialchars($e->getMessage());
    }

    // Redirect to avoid re-triggering the insertion on page refresh
    header("Location: admin.php?form=addTour");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Add/Update Tour</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" id="tourId" name="tourId">
        <div class="mb-3">
            <label for="guideId" class="form-label">Select Guide</label>
            <select class="form-control" id="guideId" name="guideId" required>
                <?php foreach ($guides as $guide): ?>
                    <option value="<?= $guide['guideId'] ?>"><?= $guide['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Tour Location</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" id="city" name="city" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration (hours)</label>
            <input type="number" class="form-control" id="duration" name="duration" required>
        </div>
        <div class="mb-3">
            <label for="tourImage" class="form-label">Tour Image (optional)</label>
            <input type="file" class="form-control" id="tourImage" name="tourImage" accept="image/*">
        </div>
        <div class="mb-3">
            <label for="places" class="form-label">Select Places</label>
            <select class="form-control" id="places" name="places[]" multiple required>
                <?php foreach ($places as $place): ?>
                    <option value="<?= $place['placeId'] ?>"><?= $place['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="addTour">Save Tour</button>
    </form>

    <h1 class="mt-5">All Tours</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Location</th>
                <th>Date</th>
                <th>Price</th>
                <th>City</th>
                <th>Duration</th>
                <th>Guide</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?= $tour['title'] ?></td>
                    <td><?= $tour['location'] ?></td>
                    <td><?= $tour['date'] ?></td>
                    <td><?= $tour['price'] ?></td>
                    <td><?= $tour['city'] ?></td>
                    <td><?= $tour['duration'] ?></td>
                    <td><?= $tour['guideName'] ?></td> <!-- Use guideName instead of guideId -->
                    <td>
                        <a href="edit.php?tourId=<?= $tour['tourId'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?deleteTour=<?= $tour['tourId'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this tour?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
