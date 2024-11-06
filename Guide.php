<?php
include 'db.php'; // Database connection

 session_start(); // Start the session

// Directory to store uploaded images
$imageDir = "uploads/";

// Function to handle file upload with larger size (10MB)
function uploadImage($file, $directory) {
    $targetFile = $directory . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "File is not an image.";
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Check file size (max 10MB)
    if ($file["size"] > 10000000) { // 10MB limit
        return "Sorry, your file is too large.";
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // Return the file path on success
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

// Fetch all places for the form dropdown
$placesStmt = $pdo->query("SELECT placeId, name FROM Place");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);


// Function to add a new tour with image upload and select guide dropdown
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTour'])) {
    $guideId = $_POST['guideId'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $duration = $_POST['duration'];
    $selectedPlaces = $_POST['places'] ?? []; // Default to empty array if not set
    $title = $_POST['title'];

    // Handle tour image upload
    $imagePath = uploadImage($_FILES['tourImage'], $imageDir);

    if (strpos($imagePath, "Sorry") === 0) {
        echo $imagePath; // Display the error message
    } else {
        try {
            $pdo->beginTransaction();

            // Insert tour with image path
            $stmt = $pdo->prepare("INSERT INTO Tour (guideId, location, date, price, imageURL, description, city, duration, title) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$guideId, $location, $date, $price, $imagePath, $description, $city, $duration, $title])) {
                $tourId = $pdo->lastInsertId();

                // Insert associated places
                $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
                foreach ($selectedPlaces as $placeId) {
                    $stmtPlaces->execute([$tourId, $placeId]);
                }

                $pdo->commit();
                echo "Tour and places added successfully!";
            } else {
                $pdo->rollBack();
                echo "Error occurred while adding the tour.";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Function to add a new place with image upload and suggest it
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSuggest'])) {
    $name = $_POST['placeName'];
    $description = $_POST['placeDescription'];
    $cityId = $_POST['cityId']; // Get the selected city ID

    // Handle place image upload
    $imagePath = uploadImage($_FILES['placeImage'], $imageDir);

    if (strpos($imagePath, "Sorry") === 0) {
        echo $imagePath; // Display the error message
    } else {
        // Insert place with image path
        $stmt = $pdo->prepare("INSERT INTO Place (name, description, imageURL, CityId) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $imagePath, $cityId])) {
            // Retrieve the last inserted place ID
            $placeId = $pdo->lastInsertId();

            // Insert the suggestion record into SuggestTable
            $userId = $_SESSION['userId']; // Assuming the user ID is stored in the session
            

            $suggestStmt = $pdo->prepare("INSERT INTO suggestedplaces (userId, placeId) VALUES (?, ?)");
            if ($suggestStmt->execute([$userId, $placeId])) {
                echo "Place and suggestion added successfully!";
            } else {
                echo "Error occurred while adding the suggestion.";
            }
        } else {
            echo "Error occurred while adding the place.";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Panel - Add Tours, Guides, and Places</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Guide Panel</h2>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'addPlace' ? 'active' : '' ?>" href="Guide.php?form=addPlace">Add Place</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'addTour' ? 'active' : '' ?>" href="Guide.php?form=addTour">Add Tour</a>
            </li>               
        </ul>

        <div class="tab-content mt-4">
            <?php
            $form = $_GET['form'] ?? 'addTour';
           if ($form === 'addTour') {
                include 'GuidePages/add_tour_form.php';
            } elseif ($form === 'addPlace') {
                include 'GuidePages/add_suggest_form.php';
            } 
            ?>
        </div>
    </div>
</body>
</html>
