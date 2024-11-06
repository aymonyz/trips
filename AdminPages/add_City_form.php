<?php
include '../db.php'; // Include your database connection


// Retrieve all places
$cityQuery = $pdo->query("SELECT * FROM cities");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Cities</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4">Add City</h1>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="Name" class="form-label">City Name</label>
      <input type="text" class="form-control" id="Name" name="Name" required>
    </div>
    
    <div class="mb-3">
      <label for="cityImage" class="form-label">Place Image</label>
      <input type="file" class="form-control" id="CityImage" name="cityImage" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-primary" name="addCity">Add Place</button>
  </form>

  <h1 class="mt-5">All Cities</h1>
  <?php if (!empty($cities)): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cities as $city): ?>
          <tr>
            <td><?= htmlspecialchars($city['CityId']) ?></td>
            <td><?= htmlspecialchars($city['Name']) ?></td>
            
            <td>
              <a href="?removeCity=<?= htmlspecialchars($city['CityId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this City?');">Delete</a>
              </td>
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