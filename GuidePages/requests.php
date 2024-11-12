<?php
session_start(); // Start the session
include '../db.php'; 

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    echo "خطأ: لم يتم العثور على هوية المستخدم. يرجى تسجيل الدخول.";
    exit;
}

// Get guideId from the URL
if (isset($_GET['guideId'])) {
    $guideId = $_GET['guideId'];
} else {
    echo "خطأ: لم يتم تحديد guideId.";
    exit;
}

// Query for regular bookings
$bookingsQuery = $pdo->prepare("
    SELECT 
        b.bookingId, 
        b.bookingDate, 
        b.specialRequest, 
        u.name AS userName, 
        t.title AS tourTitle, 
        g.name AS guideName, 
        g.experience, 
        g.rating, 
        g.languages 
    FROM 
        Booking b 
    JOIN 
        User u ON b.userId = u.userId 
    JOIN 
        Tour t ON b.tourId = t.tourId 
    JOIN 
        TourGuide g ON t.guideId = g.guideId 
    WHERE 
        b.guideId = :guideId 
    ORDER BY 
        b.bookingDate DESC
");

// Execute the query
$bookingsQuery->execute(['guideId' => $guideId]);
$bookings = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Query for special routes "مسارات خاصة" with names of city, place, and country
$specialRoutesQuery = $pdo->prepare("
    SELECT 
        sr.tour_id, 
        sr.created_at, 
        p.name AS place_name, 
        c.Name AS city_name, 
        co.Name AS country_name, 
        sr.notes, 
        sr.category, 
        sr.people_count, 
        sr.end_date, 
        sr.start_date 
    FROM 
        custom_tours sr
    JOIN 
        place p ON sr.place_id = p.placeId
    JOIN 
        cities c ON sr.city_id = c.CityId
    JOIN 
        countries co ON sr.country_id = co.CountryId
    WHERE 
        sr.guideid = :guideId 
    ORDER BY 
        sr.created_at DESC
");

// Execute the special routes query
$specialRoutesQuery->execute(['guideId' => $guideId]);
$specialRoutes = $specialRoutesQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request for bookings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deleteBooking'])) {
        $bookingId = $_POST['bookingId'];
        $deleteBookingQuery = $pdo->prepare("DELETE FROM Booking WHERE bookingId = ? AND guideId = ?");
        $deleteBookingQuery->execute([$bookingId, $guideId]);
        
        echo "<script>alert('تم حذف الحجز بنجاح!'); window.location.href='" . $_SERVER['PHP_SELF'] . "?guideId=" . $guideId . "';</script>";
        exit;
    }
    // Handle delete request for special routes
    if (isset($_POST['deleteRoute'])) {
        $routeId = $_POST['routeId'];
        $deleteRouteQuery = $pdo->prepare("DELETE FROM custom_tours WHERE tour_id = ? AND guideid = ?");
        $deleteRouteQuery->execute([$routeId, $guideId]);
        
        echo "<script>alert('تم حذف المسار بنجاح!'); window.location.href='" . $_SERVER['PHP_SELF'] . "?guideId=" . $guideId . "';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../base_nav.php'; ?> <!-- Navigation bar -->

<div class="container-fluid position-relative p-0" style="margin-top: 90px;">
    <div class="container" style="max-width: 800px; margin-top: 50px;">
        <h2 class="text-center mb-4">حجوزاتي</h2>
        
        <!-- Regular Bookings Table -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>رقم الحجز</th>
                    <th>اسم المستخدم</th>
                    <th>عنوان الجولة</th>
                    <th>تاريخ الحجز</th>
                    <th>طلب خاص</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="6" class="text-center">لم يتم العثور على حجوزات.</td>
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
                                    <button type="submit" name="deleteBooking" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الحجز؟');">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Special Routes Table -->
        <h2 class="text-center mt-5 mb-4">مسارات خاصة</h2>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>تاريخ الإنشاء</th>
                    <th>المكان</th>
                    <th>المدينة</th>
                    <th>البلد</th>
                    <th>ملاحظات</th>
                    <th>التصنيف</th>
                    <th>عدد الأشخاص</th>
                    <th>تاريخ النهاية</th>
                    <th>تاريخ البداية</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($specialRoutes)): ?>
                    <tr>
                        <td colspan="10" class="text-center">لم يتم العثور على مسارات خاصة.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($specialRoutes as $route): ?>
                        <tr>
                            <td><?= htmlspecialchars($route['created_at']) ?></td>
                            <td><?= htmlspecialchars($route['place_name']) ?></td>
                            <td><?= htmlspecialchars($route['city_name']) ?></td>
                            <td><?= htmlspecialchars($route['country_name']) ?></td>
                            <td><?= htmlspecialchars($route['notes']) ?></td>
                            <td><?= htmlspecialchars($route['category']) ?></td>
                            <td><?= htmlspecialchars($route['people_count']) ?></td>
                            <td><?= htmlspecialchars($route['end_date']) ?></td>
                            <td><?= htmlspecialchars($route['start_date']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="routeId" value="<?= htmlspecialchars($route['tour_id']) ?>">
                                    <button type="submit" name="deleteRoute" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المسار؟');">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../footer.php'); ?> <!-- Footer -->

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
