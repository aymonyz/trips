<?php
include '../db.php'; // Include your database connection
include 'index.php'; 
// Handle message deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteMessage'])) {
    $messageId = $_POST['messageId'];

    try {
        $deleteQuery = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $deleteQuery->execute([$messageId]);
        echo "<div class='alert alert-success'>Message with ID $messageId deleted successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error deleting message: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Retrieve messages from the database
$messageQuery = $pdo->query("SELECT * FROM messages");
$messages = $messageQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* تحسين تصميم الجدول */
    .table-container {
      margin-top: 40px;
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    h1 {
      color: #007bff;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
    }

    table.table {
      border-radius: 8px;
      overflow: hidden;
    }

    table.table thead {
      background-color: #007bff;
      color: #fff;
    }

    table.table tbody tr:hover {
      background-color: #e9ecef;
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      transition: background-color 0.3s;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
    }

    .alert {
      font-weight: bold;
      text-align: center;
      color: #495057;
    }
  </style>
</head>
<body>

<div class="container table-container">
  <h1>الرسائل</h1>

  <?php if (!empty($messages)): ?>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>رقم الرسالة</th>
          <th>الاسم</th>
          <th>البريد الإلكتروني</th>
          <th>الموضوع</th>
          <th>الرسالة</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $message): ?>
          <tr>
            <td><?= htmlspecialchars($message['id']) ?></td>
            <td><?= htmlspecialchars($message['name']) ?></td>
            <td><?= htmlspecialchars($message['email']) ?></td>
            <td><?= htmlspecialchars($message['subject']) ?></td>
            <td><?= htmlspecialchars($message['message']) ?></td>
            <td>
              <!-- نموذج لحذف الرسالة -->
              <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="messageId" value="<?= htmlspecialchars($message['id']) ?>">
                <button type="submit" name="deleteMessage" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه الرسالة؟');">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info">لم يتم العثور على رسائل.</p>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
