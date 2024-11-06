<?php
include '../db.php'; // Include your database connection

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
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4">Messages</h1>

  <?php if (!empty($messages)): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Action</th>
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
              <!-- Form for deleting the message -->
              <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="messageId" value="<?= htmlspecialchars($message['id']) ?>">
                <button type="submit" name="deleteMessage" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this message?');">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="alert alert-info">No messages found.</p>
  <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
