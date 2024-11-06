<?php
include '../db.php'; // Include your database connection

// Handle suggestion deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['UpdateSuggestion'])) {
    $suggestionId = $_POST['PlaceId']; // Change variable name to be more accurate

    try {
        // Update the status of the suggestion to approved (1)
        $updateQuery = $pdo->prepare("UPDATE place SET Approve = 1 WHERE placeId = ?");
        $updateQuery->execute([$suggestionId]);
        echo "<div class='alert alert-success'>Suggestion with ID $suggestionId approved successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error approving suggestion: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Retrieve suggestions from the database
$stmt = $pdo->prepare("
    SELECT s.SuggestId, u.userId, u.emailAddress AS username, p.PlaceId,p.Approve, p.name AS PlaceName, p.description AS PlaceDescription
    FROM suggestedplaces s
    JOIN User u ON s.userId = u.userId
    JOIN Place p ON s.placeId = p.PlaceId
");
$stmt->execute(); // Don't forget to execute the statement
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suggestions</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4">Suggestions</h1>

  <?php if (!empty($suggestions)): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Suggestion ID</th>
          <th>Username</th>
          <th>Place Name</th>
          <th>Description</th>
          <th>Action</th> <!-- Added Action column for clarity -->
        </tr>
      </thead>
      <tbody>
        <?php foreach ($suggestions as $suggestion): ?>
          <tr>
            <td><?= htmlspecialchars($suggestion['SuggestId']) ?></td>
            <td><?= htmlspecialchars($suggestion['username']) ?></td>
            <td><?= htmlspecialchars($suggestion['PlaceName']) ?></td>
            <td><?= htmlspecialchars($suggestion['PlaceDescription']) ?></td>
            <td>
              <!-- Form for deleting the suggestion -->
              <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="PlaceId" value="<?= htmlspecialchars($suggestion['PlaceId']) ?>">
                <?php if ($suggestion['Approve'] == '0'): ?>
                  <button type="submit" name="UpdateSuggestion" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to Approve this suggestion?');">Approve</button>
                <?php endif; ?>
                
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info">No suggestions found.</p>
  <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
