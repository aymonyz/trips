<?php
include '../db.php'; 


// Fetch all bookings
$bookingsQuery = $pdo->prepare("SELECT b.*, u.name AS userName, t.title AS tourTitle 
                                  FROM Booking b 
                                  JOIN User u ON b.userId = u.userId 
                                  JOIN Tour t ON b.tourId = t.tourId 
                                  ORDER BY b.bookingDate DESC");
$bookingsQuery->execute();
$bookings = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteBooking'])) {
    $bookingId = $_POST['bookingId'];
    $deleteQuery = $pdo->prepare("DELETE FROM Booking WHERE bookingId = ?");
    $deleteQuery->execute([$bookingId]);
    
    echo "Booking deleted successfully!";
    // Refresh the page to show updated list of bookings
    header("Location: admin.php?form=getBooking.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Add your CSS path -->
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Bookings</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Tour Title</th>
                    <th>Booking Date</th>
                    <th>Special Request</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No bookings found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['bookingId']) ?></td>
                            <td><?= htmlspecialchars($booking['userName']) ?></td>
                            <td><?= htmlspecialchars($booking['tourTitle']) ?></td>
                            <td><?= htmlspecialchars($booking['bookingDate']) ?></td>
                            <td><?= htmlspecialchars($booking['specialRequest']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="bookingId" value="<?= htmlspecialchars($booking['bookingId']) ?>">
                                    <button type="submit" name="deleteBooking" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="path/to/bootstrap.bundle.js"></script> <!-- Add your JS path -->
</body>
</html>
