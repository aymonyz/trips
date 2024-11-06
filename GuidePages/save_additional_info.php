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

    // تحقق من وجود القيم وتحديد قيمة افتراضية في حالة عدم وجودها
    $languages = isset($_POST['languages']) ? implode(', ', $_POST['languages']) : '';
    $cities = isset($_POST['cities']) ? implode(', ', $_POST['cities']) : '';
    $countries = isset($_POST['countries']) ? implode(', ', $_POST['countries']) : '';
    $about = $_POST['about'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $experience = $_POST['experience'];
    $phone= $_POST['phone'];

    // معالجة رفع الصورة
    $imageURL = null;
    if (isset($_FILES['imageURL']) && $_FILES['imageURL']['error'] == 0) {
        $targetDir = "../uploads/";
        $imageURL = $targetDir . basename($_FILES['imageURL']['name']);
        
        if (move_uploaded_file($_FILES['imageURL']['tmp_name'], $imageURL)) {
            // مسار الصورة سيتم تخزينه في قاعدة البيانات
            $imageURL = "uploads/" . basename($_FILES['imageURL']['name']);
        } else {
            echo "حدث خطأ أثناء رفع الصورة.";
            exit();
        }
    }

    try {
        // إدخال البيانات الإضافية في جدول tourguide
        $query = $pdo->prepare("
            INSERT INTO tourguide (guideId, name, license_number, languages, cities, countries, about, facebook, twitter, instagram, imageURL, experience,phone) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)
        ");
        
        // تنفيذ الاستعلام مع المعطيات
        $query->execute([$userId, $name, $license_number, $languages, $cities, $countries, $about, $facebook, $twitter, $instagram, $imageURL, $experience, $phone]);

        echo "تم حفظ البيانات بنجاح!";
        // إعادة توجيه المستخدم إلى صفحة النجاح أو الرئيسية بعد حفظ البيانات
        header("Location: main.php");
        exit();
    } catch (PDOException $e) {
        echo "حدث خطأ أثناء حفظ البيانات: " . $e->getMessage();
    }
}
?>
