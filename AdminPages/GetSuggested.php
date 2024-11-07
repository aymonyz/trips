<?php
include '../db.php'; // Include your database connection
include 'index.php'; 
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
  <style>
    /* تحسين شكل الجدول */
    table.table {
      background-color: #f8f9fa;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    table.table thead {
      background-color: #007bff;
      color: #fff;
    }

    table.table tbody tr:hover {
      background-color: #e9ecef;
    }

    h1 {
      color: #007bff;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
      transition: background-color 0.3s;
    }

    .btn-success:hover {
      background-color: #218838;
      border-color: #218838;
    }

    .alert {
      font-weight: bold;
      text-align: center;
      color: #495057;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h1>اقتراحات</h1>

  <?php if (!empty($suggestions)): ?>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>رقم الاقتراح</th>
          <th>اسم المستخدم</th>
          <th>اسم المكان</th>
          <th>الوصف</th>
          <th>إجراء</th> <!-- عمود الإجراءات للتوضيح -->
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
              <!-- نموذج لحذف الاقتراح -->
              <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="PlaceId" value="<?= htmlspecialchars($suggestion['PlaceId']) ?>">
                <?php if ($suggestion['Approve'] == '0'): ?>
                  <button type="submit" name="UpdateSuggestion" class="btn btn-success btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد الموافقة على هذا الاقتراح؟');">موافقة</button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info">لم يتم العثور على أي اقتراحات.</p>
  <?php endif; ?>

</div>

</body>
</html>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
