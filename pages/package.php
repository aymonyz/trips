<?php
// Include the database connection file
include 'db.php';

// الحصول على قيمة البحث من المستخدم
$searchCity = isset($_GET['city']) ? $_GET['city'] : '';
$searchDate = isset($_GET['date']) ? $_GET['date'] : '';
$searchTourName = isset($_GET['tourName']) ? $_GET['tourName'] : '';

// SQL query to fetch tours
$sql = "SELECT * FROM Tour";
$conditions = [];
$params = [];

// إذا تم إدخال المدينة، أضفها كشرط
if (!empty($searchCity)) {
    $conditions[] = "city LIKE :city";
    $params[':city'] = '%' . $searchCity . '%';
}

// إذا تم إدخال التاريخ، أضفه كشرط
if (!empty($searchDate)) {
    $conditions[] = "date = :date";
    $params[':date'] = $searchDate;
}

// إذا تم إدخال اسم المسار، أضفه كشرط
if (!empty($searchTourName)) {
    $conditions[] = "tourName LIKE :tourName";
    $params[':tourName'] = '%' . $searchTourName . '%';
}

// إضافة الشروط إلى الاستعلام إذا كانت هناك أي شروط
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $pdo->prepare($sql);

// تمرير القيم إلى الاستعلام
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- صندوق البحث -->
<div class="container mt-5">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="packages">
        <div class="row mb-4">
            <div class="col-md-4">
                <select name="city" class="form-control">
                    <option value="">اختر المدينة</option>
                    <?php
                    // قائمة المدن مع المعرفات الخاصة بها
                    $cities = [
                        2 => "الأحساء",
                        3 => "مكة المكرمة",
                        4 => "المدينة المنورة",
                        5 => "جدة",
                        8 => "الخبر",
                        9 => "الرياض",
                        10 => "الطايف",
                        11 => "وادي لجب والجبل الأسود",
                        12 => "جيزان",
                        14 => "حائل",
                        15 => "الباحة",
                        16 => "الجنوب",
                        17 => "الجوف",
                        18 => "غير معروف",
                        19 => "العلا",
                        24 => "ابها",
                        49 => "عسير",
                        66 => "جازان",
                        77 => "املج",
                        139 => "الرياض",
                        368 => "تبوك",
                        564 => "سكاكا",
                        565 => "خميس مشيط",
                        566 => "نجران",
                        568 => "جزيرة تاروت",
                        570 => "العيص"
                    ];
                    foreach ($cities as $value => $name): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($searchCity == $value) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" placeholder="التاريخ" value="<?php echo htmlspecialchars($searchDate); ?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="tourName" class="form-control" placeholder="ابحث عن اسم المسار" value="<?php echo htmlspecialchars($searchTourName); ?>">
            </div>
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary w-100">البحث</button>
            </div>
        </div>
    </form>
</div>

<!-- عرض النتائج -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">العروض</h6>
            <h1 class="mb-5">عروض رائعة</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (empty($tours)): ?>
                <p class="text-center">لا توجد نتائج مطابقة.</p>
            <?php else: ?>
                <?php foreach ($tours as $row): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="package-item">
                            <div class="overflow-hidden">
                                <img class="img-fluid" src="<?php echo htmlspecialchars($row['imageURL']); ?>" alt="Package Image">
                            </div>
                            <div class="d-flex border-bottom">
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($row['city']); ?>
                                </small>
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fa fa-calendar-alt text-primary me-2"></i><?php echo htmlspecialchars($row['date']); ?>
                                </small>
                                <small class="flex-fill text-center py-2">
                                    <i class="fa fa-user text-primary me-2"></i>2-8 أشخاص
                                </small>
                            </div>
                            <div class="text-center p-4">
                                <h3 class="mb-0">$<?php echo number_format($row['price'], 2); ?></h3>
                                <div class="mb-3">
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                    <small class="fa fa-star text-primary"></small>
                                </div>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="d-flex justify-content-center mb-2">
                                    <a href="index.php?page=tourDetails&tourId=<?php echo $row['tourId']; ?>" class="btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">المزيد</a>
                                    <a href="index.php?page=booking&tourId=<?php echo $row['tourId']; ?>" class="btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">احجز الآن</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Close the database connection
$pdo = null;
?>
