<?php
include '../db.php'; // Include your database connection

// // Retrieve all places
// $placeQuery = $pdo->query("SELECT * FROM place");
// $places = $placeQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all places with their corresponding city names
$placeQuery = $pdo->query("
    SELECT p.placeId, p.name, p.description, c.Name AS cityName
    FROM Place p
    LEFT JOIN Cities c ON p.CityId = c.CityId
");
$places = $placeQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all cities for the select dropdown
$cityQuery = $pdo->query("SELECT * FROM Cities");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Places</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4">Add Place</h1>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="placeName" class="form-label">Place Name</label>
      <input type="text" class="form-control" id="placeName" name="placeName" required>
    </div>
    <div class="mb-3">
      <label for="placeDescription" class="form-label">Place Description</label>
      <textarea class="form-control" id="placeDescription" name="placeDescription" required></textarea>
    </div>
    <div class="mb-3">
      <label for="placeImage" class="form-label">Place Image</label>
      <input type="file" class="form-control" id="placeImage" name="placeImage" accept="image/*" required>
    </div>

    <div class="mb-3">
      <label for="citySelect" class="form-label">City</label>
      <select class="form-control" id="citySelect" name="cityId" required>
        <option value="">Select City</option>
        <?php foreach ($cities as $city): ?>
          <option value="<?= htmlspecialchars($city['CityId']) ?>">
            <?= htmlspecialchars($city['Name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" name="addSuggest">Add Place</button>
  </form>

  <h1 class="mt-5">All Places</h1>
  <?php if (!empty($places)): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>City</th> <!-- New column for City -->
          
        </tr>
      </thead>
      <tbody>
        <?php foreach ($places as $place): ?>
          <tr>
            <td><?= htmlspecialchars($place['placeId']) ?></td>
            <td><?= htmlspecialchars($place['name']) ?></td>
            <td><?= htmlspecialchars($place['description']) ?></td>
            <td><?= htmlspecialchars($place['cityName'] ?? 'N/A') ?></td> <!-- Display city name -->
            
          </tr>
        <?php endforeach; ?>
      </tbody>
</table>
  <?php else: ?>
    <p class="alert alert-info">No places found.</p>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>