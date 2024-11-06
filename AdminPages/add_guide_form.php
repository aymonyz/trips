<?php
include '../db.php';  // Include your database connection
include 'index.php'; 
// Handle guide addition
if (isset($_POST['addGuide'])) {
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // Handle file upload
    $guideImage = $_FILES['guideImage']['name'];
    $target_dir = "uploads/"; // Ensure this directory exists
    $target_file = $target_dir . basename($guideImage);

    if (move_uploaded_file($_FILES['guideImage']['tmp_name'], $target_file)) {
        try {
            // Prepare the insert query
            $insertQuery = $pdo->prepare("
                INSERT INTO tourguide (name, languages, experience, rating, imageURL, facebook, twitter, instagram) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            // Execute the query with the provided data
            $insertQuery->execute([$name, $languages, $experience, $rating, $guideImage, $facebook, $twitter, $instagram]);
            echo "Guide added successfully!";
        } catch (PDOException $e) {
            echo "Error adding guide: " . $e->getMessage();
        }
    } else {
        echo "Done!!.";
    }
}

// Handle guide deletion
if (isset($_GET['delete'])) {
    $guideId = $_GET['delete'];

    // Check if the guide has any associated tours
    $checkToursQuery = $pdo->prepare("SELECT COUNT(*) FROM tour WHERE guideId = ?");
    $checkToursQuery->execute([$guideId]);
    $tourCount = $checkToursQuery->fetchColumn();

    if ($tourCount > 0) {
        echo "Cannot delete this guide. There are associated tours. Please delete the tours first.";
    } else {
        // No associated tours, proceed to delete the guide
        try {
            $deleteQuery = $pdo->prepare("DELETE FROM tourguide WHERE guideId = ?");
            $deleteQuery->execute([$guideId]);
            echo "Guide deleted successfully!";
        } catch (PDOException $e) {
            echo "Error deleting guide: " . $e->getMessage();
        }
    }
}

// Handle guide update
if (isset($_POST['updateGuide'])) {
    $guideId = $_POST['guideId'];
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // Check if a new image is uploaded
    if (!empty($_FILES['guideImage']['name'])) {
        $guideImage = $_FILES['guideImage']['name'];
        $target_file = "uploads/" . basename($guideImage);
        $target_file1 = "../uploads/" . basename($guideImage);
        move_uploaded_file($_FILES['guideImage']['tmp_name'], $target_file1);
    } else {
        $guideImage = $_POST['existingImage'];
    }

    try {
        $updateQuery = $pdo->prepare("
            UPDATE tourguide 
            SET name = ?, languages = ?, experience = ?, rating = ?, imageURL = ?, facebook = ?, twitter = ?, instagram = ? 
            WHERE guideId = ?
        ");
        $updateQuery->execute([$name, $languages, $experience, $rating, $target_file, $facebook, $twitter, $instagram, $guideId]);
        echo "Guide updated successfully!";
    } catch (PDOException $e) {
        echo "Error updating guide: " . $e->getMessage();
    }
}

// Retrieve all guides
$guideQuery = $pdo->query("SELECT * FROM tourguide");
$guides = $guideQuery->fetchAll(PDO::FETCH_ASSOC);

$currentGuide = null;
if (isset($_GET['edit'])) {
    $guideId = $_GET['edit'];
    $guideStmt = $pdo->prepare("SELECT * FROM tourguide WHERE guideId = ?");
    $guideStmt->execute([$guideId]);
    $currentGuide = $guideStmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Guides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4"><?= $currentGuide ? "Update" : "Add" ?> Guide</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="guideId" value="<?= $currentGuide['guideId'] ?? '' ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Guide Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= $currentGuide['name'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="languages" class="form-label">Languages</label>
            <input type="text" class="form-control" id="languages" name="languages" required value="<?= $currentGuide['languages'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="experience" class="form-label">Experience (years)</label>
            <input type="number" class="form-control" id="experience" name="experience" required value="<?= $currentGuide['experience'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <input type="number" step="0.1" class="form-control" id="rating" name="rating" required value="<?= $currentGuide['rating'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="guideImage" class="form-label">Guide Image</label>
            <input type="file" class="form-control" id="guideImage" name="guideImage" accept="image/*">
            <?php if ($currentGuide && $currentGuide['imageURL']): ?>
                <input type="hidden" name="existingImage" value="<?= $currentGuide['imageURL'] ?>">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="facebook" class="form-label">Facebook Link</label>
            <input type="url" class="form-control" id="facebook" name="facebook" value="<?= $currentGuide['facebook'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="twitter" class="form-label">Twitter Link</label>
            <input type="url" class="form-control" id="twitter" name="twitter" value="<?= $currentGuide['twitter'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="instagram" class="form-label">Instagram Link</label>
            <input type="url" class="form-control" id="instagram" name="instagram" value="<?= $currentGuide['instagram'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-primary" name="<?= $currentGuide ? "updateGuide" : "addGuide" ?>">
            <?= $currentGuide ? "Update Guide" : "Add Guide" ?>
        </button>
    </form>

    <h1 class="mt-5">All Guides</h1>
    <?php if (!empty($guides)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Languages</th>
                    <th>Experience</th>
                    <th>Rating</th>
                    <th>Social Links</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guides as $guide): ?>
                    <tr>
                        <td><?= htmlspecialchars($guide['guideId']) ?></td>
                        <td><?= htmlspecialchars($guide['name']) ?></td>
                        <td><?= htmlspecialchars($guide['languages']) ?></td>
                        <td><?= htmlspecialchars($guide['experience']) ?></td>
                        <td><?= htmlspecialchars($guide['rating']) ?></td>
                        <td>
                            <?php if ($guide['facebook']): ?><a href="<?= htmlspecialchars($guide['facebook']) ?>" target="_blank">Facebook</a><?php endif; ?>
                            <?php if ($guide['twitter']): ?><a href="<?= htmlspecialchars($guide['twitter']) ?>" target="_blank">Twitter</a><?php endif; ?>
                            <?php if ($guide['instagram']): ?><a href="<?= htmlspecialchars($guide['instagram']) ?>" target="_blank">Instagram</a><?php endif; ?>
                        </td>
                        <td>
                            <a href="?edit=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guide?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-info">No guides found.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
