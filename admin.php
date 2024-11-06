<?php
include 'db.php'; // Database connection

session_start(); // Start the session

// Check if the user is logged in and has the required role
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login or an error page
    header("Location: index.php?page=login"); // Change to your login page
    exit();
}

// Handle message deletion
if (isset($_GET['deleteMessage'])) {
    $messageId = $_GET['deleteMessage'];

    try {
        $deleteQuery = $pdo->prepare("DELETE FROM messages WHERE messageId = ?"); // Change 'messageId' to the actual ID column of your messages table
        $deleteQuery->execute([$messageId]);
        echo "Message with ID $messageId deleted successfully!";
    } catch (PDOException $e) {
        echo "Error deleting message: " . $e->getMessage();
    }

    // Redirect to avoid re-triggering the deletion on refresh
    header("Location: admin.php?form=getMessage");
    exit();
}
// Handle place deletion
if (isset($_GET['removePlace'])) { 
    $placeId = $_GET['removePlace'];
    $deleteQuery = $pdo->prepare("DELETE FROM place WHERE placeId = ?");
    $deleteQuery->execute([$placeId]);
    echo "Place deleted successfully!";
  }
// Handle place deletion
if (isset($_GET['removeCity'])) { 
    $cityId = $_GET['removeCity'];
    $deleteQuery = $pdo->prepare("DELETE FROM cities WHERE CityId = ?");
    $deleteQuery->execute([$cityId]);
    echo "City deleted successfully!";
  }
  
// Check if deleteTour is set in the URL
if (isset($_GET['deleteTour'])) {
    $tourId = $_GET['deleteTour'];

    try {
        $pdo->beginTransaction();

        // Delete related entries in tour_places
        $stmtPlaces = $pdo->prepare("DELETE FROM Tour_Places WHERE tourId = ?");
        $stmtPlaces->execute([$tourId]);

        // Now delete the tour
        $stmtTour = $pdo->prepare("DELETE FROM Tour WHERE tourId = ?");
        $stmtTour->execute([$tourId]);

        $pdo->commit();
        echo "Tour deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error deleting the tour: " . htmlspecialchars($e->getMessage());
    }

    // Redirect to avoid re-triggering the deletion on refresh
    header("Location: admin.php?form=addTour");
    exit();
}

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

// Fetch all guides for the form dropdown
$guidesStmt = $pdo->query("SELECT guideId, name FROM TourGuide");
$guides = $guidesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all places for the form dropdown
$placesStmt = $pdo->query("SELECT placeId, name FROM Place");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// Function to add a new guide with image upload and social media links
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addGuide'])) {
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'] ?? null;
    $twitter = $_POST['twitter'] ?? null;
    $instagram = $_POST['instagram'] ?? null;

    // Handle guide image upload
    $imagePath = uploadImage($_FILES['guideImage'], $imageDir);

    if (strpos($imagePath, "Sorry") === 0) {
        echo $imagePath; // Display the error message
    } else {
        // Insert guide with image path and social media links
        $stmt = $pdo->prepare("INSERT INTO TourGuide (name, languages, experience, rating, imageURL, facebook, twitter, instagram) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $languages, $experience, $rating, $imagePath, $facebook, $twitter, $instagram])) {
            echo "Guide added successfully!";
        } else {
            echo "Error occurred while adding the guide.";
        }
    }
}

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

// Function to add a new place with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPlace'])) {
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
            echo "Place added successfully!";
        } else {
            echo "Error occurred while adding the place.";
        }
    }
}

// Function to add a new place with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCity'])) {
    $name = $_POST['Name'];
    //$description = $_POST['placeDescription'];

    // Handle place image upload
    $imagePath = uploadImage($_FILES['cityImage'], $imageDir);

    if (strpos($imagePath, "Sorry") === 0) {
        echo $imagePath; // Display the error message
    } else {
        // Insert place with image path
        $stmt = $pdo->prepare("INSERT INTO Cities (Name,  imageURL) VALUES (?, ?)");
        if ($stmt->execute([$name, $imagePath])) {
            echo "City added successfully!";
        } else {
            echo "Error occurred while adding the city.";
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Tours, Guides, and Places</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Panel</h2>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'addGuide' ? 'active' : '' ?>" href="admin.php?form=addGuide">Add Guide</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'addTour' ? 'active' : '' ?>" href="admin.php?form=addTour">Add Tour</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'addPlace' ? 'active' : '' ?>" href="admin.php?form=addPlace">Add Place</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'getBooking' ? 'active' : '' ?>" href="admin.php?form=getBooking">Get Booking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'getMessage' ? 'active' : '' ?>" href="admin.php?form=getMessage">Get Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'getMessage' ? 'active' : '' ?>" href="admin.php?form=AddCity">Add City</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['form'] ?? '') === 'GetSuggested' ? 'active' : '' ?>" href="admin.php?form=GetSuggested">Suggested Places</a>
            </li>
        </ul>

        <div class="tab-content mt-4">
            <?php
            $form = $_GET['form'] ?? 'addGuide';
            if ($form === 'addGuide') {
                include 'AdminPages/add_guide_form.php';
            } elseif ($form === 'addTour') {
                include 'AdminPages/add_tour_form.php';
            } elseif ($form === 'addPlace') {
                include 'AdminPages/add_place_form.php';
            } elseif ($form === 'getBooking') {
                include 'AdminPages/getBooking.php';
            } elseif ($form === 'getMessage') {
                include 'AdminPages/getMessage.php';
            } elseif ($form === 'AddCity') {
                include 'AdminPages/add_City_form.php';
            } elseif ($form === 'GetSuggested') {
                include 'AdminPages/GetSuggested.php';
            }
            ?>
        </div>
    </div>
</body>
</html>
