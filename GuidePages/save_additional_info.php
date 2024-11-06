<?php
session_start();
include '../db.php'; // تضمين الاتصال بقاعدة البيانات

// تحقق من أن المستخدم لديه معرّف المستخدم في الجلسة
if (!isset($_SESSION['userId'])) {
    echo "يجب عليك التسجيل أولاً!";
    exit();
}

$userId = $_SESSION['userId'];
$name = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استقبال البيانات من النموذج
    $license_number = $_POST['license'];
    $languages = implode(', ', $_POST['languages']); // تأكد من صحة اسم الحقل 'languages'
    $cities = implode(', ', $_POST['cities']);
    $countries = implode(', ', $_POST['countries']);
    $about = $_POST['about'];

    try {
        // إدخال البيانات الإضافية في جدول tourguide
        $query = $pdo->prepare("
            INSERT INTO tourguide (guideId, name, license_number, languages, cities, countries, about) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        // تنفيذ الاستعلام مع المعطيات
        $query->execute([$userId, $name, $license_number, $languages, $cities, $countries, $about]);

        echo "تم حفظ البيانات بنجاح!";
        // إعادة توجيه المستخدم إلى صفحة النجاح أو الرئيسية بعد حفظ البيانات
        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        echo "حدث خطأ أثناء حفظ البيانات: " . $e->getMessage();
    }
}
?>
