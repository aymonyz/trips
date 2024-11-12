<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../db.php';

// تحقق من وجود userId في URL
if (!isset($_GET['userId'])) {
    echo "User ID غير متوفر.";
    exit();
}

// جلب userId من الرابط
$userId = $_GET['userId'];
$guideId= $_GET['guideId'];


// جلب الدول من قاعدة البيانات
$countriesQuery = $pdo->query("SELECT * FROM countries");
$countries = $countriesQuery->fetchAll(PDO::FETCH_ASSOC);

// جلب المدن بناءً على الدولة (يمكن ضبطها حسب متطلباتك)
$citiesQuery = $pdo->query("SELECT * FROM cities");
$cities = $citiesQuery->fetchAll(PDO::FETCH_ASSOC);

// جلب الأماكن بناءً على المدينة
$placesQuery = $pdo->query("SELECT * FROM place");
$places = $placesQuery->fetchAll(PDO::FETCH_ASSOC);

// عند تقديم النموذج، احفظ البيانات في قاعدة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $peopleCount = $_POST['peopleCount'];
    $category = $_POST['category'];
    $notes = $_POST['notes'];
    $countryId = $_POST['country'];
    $cityId = $_POST['city'];
    $placeId = $_POST['place'];

    // إدخال البيانات مع userId في قاعدة البيانات
    $insertQuery = $pdo->prepare("INSERT INTO custom_tours (user_id, start_date, end_date, people_count, category, notes, country_id, city_id, place_id,guideid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $insertQuery->execute([$userId, $startDate, $endDate, $peopleCount, $category, $notes, $countryId, $cityId, $placeId,$guideId]);

    $message = 'تم إرسال طلب مسار خاص بنجاح';
}

?>

<?php include '../base_nav.php'; ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مسار خاص</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-right">
            <h4 class="text-primary">إضافة مسار خاص</h4>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success text-right" role="alert" style="color: green; font-weight: bold;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="startDate">بداية المسار</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="endDate">نهاية المسار</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="peopleCount">عدد الأشخاص</label>
                        <input type="number" class="form-control" id="peopleCount" name="peopleCount" value="0" required>
                    </div>
                    <div class="form-group col-md-3">
                     <label for="category">الفئة المستهدفة</label>
                     <select class="form-control" id="category" name="category" required>
                         <option value="أفراد">أفراد</option>
                         <option value="عائلة">عائلة</option>
                     </select>
                     </div>

                </div>

                <div class="form-group">
                    <label for="notes">ملاحظات</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <h5 class="mt-4">خط سير المسار</h5>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="country">الدولة</label>
                        <select class="form-control" id="country" name="country" required>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['CountryId']; ?>"><?php echo $country['Name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="city">المدينة</label>
                        <select class="form-control" id="city" name="city" required>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['CityId']; ?>"><?php echo $city['Name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="place">المكان</label>
                        <select class="form-control" id="place" name="place" required>
                            <?php foreach ($places as $place): ?>
                                <option value="<?php echo $place['placeId']; ?>"><?php echo $place['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark btn-block">تأكيد</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include '../footer.php'; ?>
