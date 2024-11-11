<?php
include '../db.php';  // Database connection
include 'index.php'; 
// التحقق من وجود طلب حذف للجولة
if (isset($_GET['deleteTour'])) {
    $tourId = $_GET['deleteTour'];
    
    try {
        $pdo->beginTransaction();
        
        // حذف الأماكن المرتبطة بالرحلة من جدول `Tour_Places`
        $deletePlacesStmt = $pdo->prepare("DELETE FROM Tour_Places WHERE tourId = :tourId");
        $deletePlacesStmt->execute(['tourId' => $tourId]);
        
        // حذف الرحلة من جدول `tour`
        $deleteTourStmt = $pdo->prepare("DELETE FROM tour WHERE tourId = :tourId");
        $deleteTourStmt->execute(['tourId' => $tourId]);
        
        $pdo->commit();
        echo "<div class='alert alert-success'>تم حذف الرحلة بنجاح.</div>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger'>حدث خطأ أثناء حذف الرحلة: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // إعادة التوجيه لتجنب تكرار الحذف عند تحديث الصفحة
    header("Location: add_tour_form.php?form=addTour");
    exit();
}
// Fetch all guides for the dropdown
$guidesStmt = $pdo->query("SELECT guideId, name FROM TourGuide");
$guides = $guidesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all places for the dropdown
$placesStmt = $pdo->query("SELECT placeId, name FROM Place");
$places = $placesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tours for displaying in the table
$toursStmt = $pdo->query("SELECT t.tourId, t.title, t.location, t.date, t.price, t.city, t.duration, g.name AS guideName 
    FROM Tour t JOIN TourGuide g ON t.guideId = g.guideId");
$tours = $toursStmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Ensure $tours is an array

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $guideId = $_POST['guideId'];
//     $location = $_POST['location'];
//     $date = $_POST['date'];
//     $price = $_POST['price'];
//     $title = $_POST['title'];
//     $description = $_POST['description'];
//     $city = $_POST['city'];
//     $duration = $_POST['duration'];
//     $places = $_POST['places'] ?? [];

//     // Handle file upload only if an image is provided
//     $imagePath = null;
//     if (!empty($_FILES['tourImage']['name'])) {
//         $imagePath = "../uploads/" . basename($_FILES['tourImage']['name']);
//         $path = "uploads/" . basename($_FILES['tourImage']['name']);
//         if (!move_uploaded_file($_FILES['tourImage']['tmp_name'], $imagePath)) {
//             echo "Done!!";
//             exit();
//         }
//     }

//     try {
//         $pdo->beginTransaction();

//         // Insert tour with image path (if available)
//         $insertQuery = $pdo->prepare("
//             INSERT INTO tour (guideId, location, date, price, imageURL, description, city, duration, title) 
//             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
//         ");
//         $insertQuery->execute([$guideId, $location, $date, $price, $path, $description, $city, $duration, $title]);

//         // Get the last inserted tour ID for the new tour
//         $tourId = $pdo->lastInsertId();

//         // Insert selected places if any are provided
//         if (!empty($places)) {
//             $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
//             foreach ($places as $placeId) {
//                 $stmtPlaces->execute([$tourId, $placeId]);
//             }
//         }
        

//         $pdo->commit();
//         echo "Tour and associated places added successfully!";
//     } catch (PDOException $e) {
//         $pdo->rollBack();
//         echo "Error adding the tour: " . htmlspecialchars($e->getMessage());
//     }

//     // Redirect to avoid re-triggering the insertion on page refresh
//     header("Location: add_tour_form.php?form=addTour");
//     exit();
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guideId = $_POST['guideId'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $duration = $_POST['duration'];
    $placeCategory = $_POST['placeCategory'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $places = $_POST['places'] ?? [];

    // Handle file upload
    $imagePath = null;
    if (!empty($_FILES['tourImage']['name'])) {
        $imagePath = "../uploads/" . basename($_FILES['tourImage']['name']);
        $path = "uploads/" . basename($_FILES['tourImage']['name']);
        if (!move_uploaded_file($_FILES['tourImage']['tmp_name'], $imagePath)) {
            echo "Done!!";
            exit();
        }
    }

    try {
        $pdo->beginTransaction();

        // Insert tour with the new fields
        $insertQuery = $pdo->prepare("
            INSERT INTO tour (guideId, location, date, price, imageURL, description, city, duration, title, placeCategory, startDate, endDate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->execute([$guideId, $location, $date, $price, $path, $description, $city, $duration, $title, $placeCategory, $startDate, $endDate]);

        // Rest of the code for handling places
        $tourId = $pdo->lastInsertId();
        if (!empty($places)) {
            $stmtPlaces = $pdo->prepare("INSERT INTO Tour_Places (tourId, placeId) VALUES (?, ?)");
            foreach ($places as $placeId) {
                $stmtPlaces->execute([$tourId, $placeId]);
            }
        }

        $pdo->commit();
        echo "Tour and associated places added successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error adding the tour: " . htmlspecialchars($e->getMessage());
    }

    header("Location: add_tour_form.php?form=addTour");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* تحسين تصميم الصفحة */
        .container {
            margin-top: 50px;
            max-width: 900px;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .btn-primary, .btn-warning, .btn-danger {
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .table {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
        }

        .table-striped tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>إضافة / تحديث رحلة</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" id="tourId" name="tourId">
        
        <div class="mb-3">
            <label for="guideId" class="form-label">اختر المرشد</label>
            <select class="form-control" id="guideId" name="guideId" required>
                <?php foreach ($guides as $guide): ?>
                    <option value="<?= $guide['guideId'] ?>"><?= htmlspecialchars($guide['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="location" class="form-label">مكان الرحلة</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        
        <div class="mb-3">
            <label for="date" class="form-label">التاريخ</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
    <label for="placeCategory" class="form-label">تصنيف المكان</label>
    <input type="text" class="form-control" id="placeCategory" name="placeCategory" required>
</div>

<div class="mb-3">
    <label for="startDate" class="form-label">تاريخ من</label>
    <input type="date" class="form-control" id="startDate" name="startDate" required>
</div>

<div class="mb-3">
    <label for="endDate" class="form-label">تاريخ إلى</label>
    <input type="date" class="form-control" id="endDate" name="endDate" required>
</div>

        
        <div class="mb-3">
            <label for="price" class="form-label">السعر</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        
        <div class="mb-3">
            <label for="title" class="form-label">عوان الانطلاق</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        
        <div class="mb-3">
            <label for="city" class="form-label">اسم المسار </label>
            <input type="text" class="form-control" id="city" name="city" required>
        </div>
        
        <div class="mb-3">
            <label for="duration" class="form-label">المدة (بالساعات)</label>
            <input type="number" class="form-control" id="duration" name="duration" required>
        </div>
        
        <div class="mb-3">
            <label for="tourImage" class="form-label">صورة الرحلة (اختياري)</label>
            <input type="file" class="form-control" id="tourImage" name="tourImage" accept="image/*">
        </div>
        <div class="mb-3">
    <label class="form-label">اختر الأماكن (يمكنك اختيار 7 أماكن كحد أقصى)</label>
    <div>
        <?php foreach ($places as $place): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="places[]" id="place<?= $place['placeId'] ?>" value="<?= $place['placeId'] ?>">
                <label class="form-check-label" for="place<?= $place['placeId'] ?>">
                    <?= htmlspecialchars($place['name']) ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>
        
        <button type="submit" class="btn btn-primary btn-block" name="addTour">حفظ الرحلة</button>
    </form>

    <h1 class="mt-5">كل الرحلات </h1>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>المكان</th>
                <th>التاريخ</th>
                <th>السعر</th>
                <th>اسم المسار</th>
                <th>المدة</th>
                <th>الدليل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?= htmlspecialchars($tour['title']) ?></td>
                    <td><?= htmlspecialchars($tour['location']) ?></td>
                    <td><?= htmlspecialchars($tour['date']) ?></td>
                    <td>$<?= number_format($tour['price'], 2) ?></td>
                    <td><?= htmlspecialchars($tour['city']) ?></td>
                    <td><?= htmlspecialchars($tour['duration']) ?> ساعة</td>
                    <td><?= htmlspecialchars($tour['guideName']) ?></td> <!-- Use guideName instead of guideId -->
                    <td>
                        <a href="edit.php?tourId=<?= $tour['tourId'] ?>" class="btn btn-warning btn-sm">تعديل</a>
                        <a href="?deleteTour=<?= $tour['tourId'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه الرحلة؟');">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maxOptions = 7; // حدد الحد الأقصى هنا
        const checkboxes = document.querySelectorAll('input[name="places[]"]');

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('input[name="places[]"]:checked').length;
                if (checkedCount > maxOptions) {
                    this.checked = false;
                    alert('يمكنك اختيار ' + maxOptions + ' أماكن كحد أقصى.');
                }
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>



