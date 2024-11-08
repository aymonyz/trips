<?php
session_start(); // تأكد من بدء الجلسة
include '../db.php'; 

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تحقق من تسجيل الدخول
if (!isset($_SESSION['userId'])) {
    echo "خطأ: لم يتم العثور على هوية المستخدم. يرجى تسجيل الدخول.";
    exit;
}

// الحصول على guideId من الرابط
if (isset($_GET['guideId'])) {
    $guideId = $_GET['guideId'];
} else {
    echo "خطأ: لم يتم تحديد guideId.";
    exit;
}

// استعلام لجلب جميع بيانات الحجز وبيانات المستخدم وبيانات المرشد
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

// تنفيذ الاستعلام
$bookingsQuery->execute(['guideId' => $guideId]);
$bookings = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// معالجة طلب الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteBooking'])) {
    $bookingId = $_POST['bookingId'];
    $deleteQuery = $pdo->prepare("DELETE FROM Booking WHERE bookingId = ? AND guideId = ?");
    $deleteQuery->execute([$bookingId, $guideId]);
    
    echo "<script>alert('تم حذف الحجز بنجاح!'); window.location.href='" . $_SERVER['PHP_SELF'] . "?guideId=" . $guideId . "';</script>";
    exit;
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../base_nav.php'; ?> <!-- تضمين شريط التنقل -->

<div class="container-fluid position-relative p-0" style="margin-top: 90px;">
    <div class="container" style="max-width: 800px; margin-top: 50px;">
        <h2 class="text-center mb-4">حجوزاتي</h2>
        
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

    </div>
</div>

<?php include('../footer.php'); ?> <!-- تضمين الفوتر -->

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
