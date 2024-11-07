<?php
include '../db.php';  // Include your database connection
include 'index.php'; 
// Handle guide addition
if (isset($_POST['addGuide'])) {
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // Handle file upload
    $guideImage = $_FILES['guideImage']['name'];
    $target_dir = "uploads/"; // Ensure this directory exists
    $target_file = $target_dir . basename($guideImage);

    if (move_uploaded_file($_FILES['guideImage']['tmp_name'], $target_file)) {
        try {
            // Prepare the insert query
            $insertQuery = $pdo->prepare("
                INSERT INTO tourguide (name, languages, experience, rating, imageURL, facebook, twitter, instagram) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            // Execute the query with the provided data
            $insertQuery->execute([$name, $languages, $experience, $rating, $guideImage, $facebook, $twitter, $instagram]);
            echo "Guide added successfully!";
        } catch (PDOException $e) {
            echo "Error adding guide: " . $e->getMessage();
        }
    } else {
        echo "Done!!.";
    }
}

// Handle guide deletion
if (isset($_GET['delete'])) {
    $guideId = $_GET['delete'];

    // Check if the guide has any associated tours
    $checkToursQuery = $pdo->prepare("SELECT COUNT(*) FROM tour WHERE guideId = ?");
    $checkToursQuery->execute([$guideId]);
    $tourCount = $checkToursQuery->fetchColumn();

    if ($tourCount > 0) {
        echo "لا يمكن حذف هذا الدليل. هناك جولات مرتبطة. يرجى حذف الرحلات  أولاً.";
    } else {
        // No associated tours, proceed to delete the guide
        try {
            $deleteQuery = $pdo->prepare("DELETE FROM tourguide WHERE guideId = ?");
            $deleteQuery->execute([$guideId]);
            echo "تم حذف المرشد بنجاح!";
        } catch (PDOException $e) {
            echo "Error deleting guide: " . $e->getMessage();
        }
    }
}

// Handle guide update
if (isset($_POST['updateGuide'])) {
    $guideId = $_POST['guideId'];
    $name = $_POST['name'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $rating = $_POST['rating'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // Check if a new image is uploaded
    if (!empty($_FILES['guideImage']['name'])) {
        $guideImage = $_FILES['guideImage']['name'];
        $target_file = "uploads/" . basename($guideImage);
        $target_file1 = "../uploads/" . basename($guideImage);
        move_uploaded_file($_FILES['guideImage']['tmp_name'], $target_file1);
    } else {
        $guideImage = $_POST['existingImage'];
    }

    try {
        $updateQuery = $pdo->prepare("
            UPDATE tourguide 
            SET name = ?, languages = ?, experience = ?, rating = ?, imageURL = ?, facebook = ?, twitter = ?, instagram = ? 
            WHERE guideId = ?
        ");
        $updateQuery->execute([$name, $languages, $experience, $rating, $target_file, $facebook, $twitter, $instagram, $guideId]);
        echo "Guide updated successfully!";
    } catch (PDOException $e) {
        echo "Error updating guide: " . $e->getMessage();
    }
}

// Retrieve all guides
$guideQuery = $pdo->query("SELECT * FROM tourguide");
$guides = $guideQuery->fetchAll(PDO::FETCH_ASSOC);

$currentGuide = null;
if (isset($_GET['edit'])) {
    $guideId = $_GET['edit'];
    $guideStmt = $pdo->prepare("SELECT * FROM tourguide WHERE guideId = ?");
    $guideStmt->execute([$guideId]);
    $currentGuide = $guideStmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Guides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
            max-width: 800px;
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

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
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

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4"><?= $currentGuide ? "تحديث مشرف" : "إضافة مشرف" ?></h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="guideId" value="<?= $currentGuide['guideId'] ?? '' ?>">
        <div class="mb-3">
            <label for="name" class="form-label">اسم المشرف</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= $currentGuide['name'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="languages" class="form-label">اللغات</label>
            <input type="text" class="form-control" id="languages" name="languages" required value="<?= $currentGuide['languages'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="experience" class="form-label">الخبرة (بالسنوات)</label>
            <input type="number" class="form-control" id="experience" name="experience" required value="<?= $currentGuide['experience'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">التقييم</label>
            <input type="number" step="0.1" class="form-control" id="rating" name="rating" required value="<?= $currentGuide['rating'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="guideImage" class="form-label">صورة الدليل</label>
            <input type="file" class="form-control" id="guideImage" name="guideImage" accept="image/*">
            <?php if ($currentGuide && $currentGuide['imageURL']): ?>
                <input type="hidden" name="existingImage" value="<?= $currentGuide['imageURL'] ?>">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="facebook" class="form-label">رابط الفيسبوك</label>
            <input type="url" class="form-control" id="facebook" name="facebook" value="<?= $currentGuide['facebook'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="twitter" class="form-label">رابط تويتر</label>
            <input type="url" class="form-control" id="twitter" name="twitter" value="<?= $currentGuide['twitter'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="instagram" class="form-label">رابط انستغرام</label>
            <input type="url" class="form-control" id="instagram" name="instagram" value="<?= $currentGuide['instagram'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-block" name="<?= $currentGuide ? "updateGuide" : "addGuide" ?>">
            <?= $currentGuide ? "تحديث مشرف" : "إضافة مشرف" ?>
        </button>
    </form>

    <h1 class="mt-5">كل المشرفين </h1>
    <?php if (!empty($guides)): ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>الاسم</th>
                    <th>اللغات</th>
                    <th>الخبرة</th>
                    <th>التقييم</th>
                    <th>روابط التواصل الاجتماعي</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guides as $guide): ?>
                    <tr>
                        <td><?= htmlspecialchars($guide['guideId']) ?></td>
                        <td><?= htmlspecialchars($guide['name']) ?></td>
                        <td><?= htmlspecialchars($guide['languages']) ?></td>
                        <td><?= htmlspecialchars($guide['experience']) ?></td>
                        <td><?= htmlspecialchars($guide['rating']) ?></td>
                        <td>
                            <?php if ($guide['facebook']): ?><a href="<?= htmlspecialchars($guide['facebook']) ?>" target="_blank">فيسبوك</a><?php endif; ?>
                            <?php if ($guide['twitter']): ?><a href="<?= htmlspecialchars($guide['twitter']) ?>" target="_blank">تويتر</a><?php endif; ?>
                            <?php if ($guide['instagram']): ?><a href="<?= htmlspecialchars($guide['instagram']) ?>" target="_blank">انستغرام</a><?php endif; ?>
                        </td>
                        <td>
                            <a href="?edit=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <a href="?delete=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الدليل؟');">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-info text-center">لم يتم العثور على أدلة.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
