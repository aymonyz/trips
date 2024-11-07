<?php
session_start(); // تأكد من بدء الجلسة

include '../db.php'; 
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تأكد من وجود هوية المستخدم الحالي في الجلسة
if (!isset($_SESSION['userId'])) {
    echo "خطأ: لم يتم العثور على هوية المستخدم. يرجى تسجيل الدخول.";
    exit;
}

$currentUserId = $_SESSION['userId'];

// استعلام جلب الحجوزات للمستخدم الحالي فقط
$bookingsQuery = $pdo->prepare("
    SELECT b.*, u.name AS userName, t.title AS tourTitle 
    FROM Booking b 
    JOIN User u ON b.userId = u.userId 
    JOIN Tour t ON b.tourId = t.tourId 
    WHERE b.userId = :userId 
    ORDER BY b.bookingDate DESC
");
$bookingsQuery->execute(['userId' => $currentUserId]);
$bookings = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// معالجة طلب الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteBooking'])) {
    $bookingId = $_POST['bookingId'];
    $deleteQuery = $pdo->prepare("DELETE FROM Booking WHERE bookingId = ? AND userId = ?");
    $deleteQuery->execute([$bookingId, $currentUserId]);
    
    echo "تم حذف الحجز بنجاح!";
    // تحديث الصفحة لعرض القائمة المحدّثة
    header("Location: admin.php?form=getBooking.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب المرشد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/guideform.css">
</head>
<?php include '../base_nav.php'; ?> <!-- تضمين شريط التنقل -->
<body>

<div class="container-fluid position-relative p-0" style="margin-top: 90px;">
    <div class="container" style="max-width: 600px; margin-top: 50px;">
        <h2 class="text-center mb-4">حجوزاتي</h2>
        

        <table class="table table-striped table-hover">
            <thead class="">
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

<!-- Back to Top Button -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../lib/wow/wow.min.js"></script>
<script src="../lib/easing/easing.min.js"></script>
<script src="../lib/waypoints/waypoints.min.js"></script>
<script src="../lib/owlcarousel/owl.carousel.min.js"></script>
<script src="../lib/tempusdominus/js/moment.min.js"></script>
<script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="../js/main.js"></script>
</body>
</html>