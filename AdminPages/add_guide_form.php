<?php
include '../db.php';  // Include your database connection
include 'index.php'; 

// Retrieve all guides and their activation status from users table
$guideQuery = $pdo->query("
    SELECT tourguide.*, user.active 
    FROM tourguide 
    JOIN user ON tourguide.guideId = userId
");
$guides = $guideQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle guide activation
if (isset($_GET['activate'])) {
    $guideId = $_GET['activate'];
    try {
        // تحديث قيمة الحقل active إلى 1 لتفعيل الحساب في جدول users
        $activateQuery = $pdo->prepare("UPDATE user SET active = 1 WHERE userId = ?");
        $activateQuery->execute([$guideId]);
        echo "تم تفعيل حساب المرشد بنجاح!";
        header("Location: " . $_SERVER['PHP_SELF']);

    } catch (PDOException $e) {
        echo "Error activating guide: " . $e->getMessage();
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
        echo "لا يمكن حذف هذا الدليل. هناك جولات مرتبطة. يرجى حذف الرحلات أولاً.";
    } else {
        // No associated tours, proceed to delete the guide
        try {
            $deleteGuideQuery = $pdo->prepare("DELETE FROM tourguide WHERE guideId = ?");
            $deleteGuideQuery->execute([$guideId]);

            // Delete from users table
            $deleteUserQuery = $pdo->prepare("DELETE FROM user WHERE userId = ?");
            $deleteUserQuery->execute([$guideId]);

            echo "تم حذف المرشد بنجاح!";
            header("Location: " . $_SERVER['PHP_SELF']);

        } catch (PDOException $e) {
            echo "Error deleting guide: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المرشدين</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1 class="mt-5">كل المرشدين </h1>
    <?php if (!empty($guides)): ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>الاسم</th>
                    <th>اللغات</th>
                    <th>الخبرة (بالسنوات)</th>
                    <th>التقييم</th>
                    <th>صورة الدليل</th>
                    <th>روابط التواصل الاجتماعي</th>
                    <th>حالة التفعيل</th>
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
                        <td><img src="../<?= htmlspecialchars($guide['imageURL']) ?>" alt="صورة المرشد" style="width: 50px; height: auto;"></td>
                        <td>
                            <?php if ($guide['facebook']): ?><a href="<?= htmlspecialchars($guide['facebook']) ?>" target="_blank">فيسبوك</a><?php endif; ?>
                            <?php if ($guide['twitter']): ?><a href="<?= htmlspecialchars($guide['twitter']) ?>" target="_blank">تويتر</a><?php endif; ?>
                            <?php if ($guide['instagram']): ?><a href="<?= htmlspecialchars($guide['instagram']) ?>" target="_blank">انستغرام</a><?php endif; ?>
                        </td>
                        <td>
                            <?= $guide['active'] == 1 ? "مفعل" : "غير مفعل"; ?>
                        </td>
                        <td>
                            <?php if ($guide['active'] == 0): ?>
                                <a href="?activate=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-success btn-sm">تفعيل الحساب</a>
                            <?php endif; ?>
                            <a href="?delete=<?= htmlspecialchars($guide['guideId']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الدليل؟');">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-info text-center">لم يتم العثور على مرشدين.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
