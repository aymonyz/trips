<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أريد أن أصبح مرشدًا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/guideform.css">
</head>
<?php
include '../base_nav.php';
include '../db.php'; // التأكد من تضمين اتصال قاعدة البيانات

// جلب المدن من قاعدة البيانات
$cities = [];
$cityQuery = $pdo->query("SELECT CityId, Name FROM cities");
while ($row = $cityQuery->fetch(PDO::FETCH_ASSOC)) {
    $cities[] = $row;
}

// جلب الدول من قاعدة البيانات
$countries = [];
$countryQuery = $pdo->query("SELECT CountryId, Name FROM countries");
while ($row = $countryQuery->fetch(PDO::FETCH_ASSOC)) {
    $countries[] = $row;
}
?>
<body>

<div class="container-fluid position-relative p-0" style="margin-top:90px">
    <div class="container">
        <h2>أريد أن أصبح المرشد</h2>
        <form action="save_additional_info.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="license">رقم الرخصة / السجل التجاري</label>
                <input type="text" id="license" name="license" required>
            </div>
            
            <div class="form-group">
                <label for="languages">اللغات التي تتقنها</label>
                <div id="language-container">
                    <div class="input-group">
                        <input type="text" name="languages[]" placeholder="إضافة لغة">
                        <i class="fas fa-trash" onclick="removeField(this)"></i>
                    </div>
                </div>
                <button type="button" class="add-button" onclick="addLanguage()">+ إضافة لغة</button>
            </div>

            <div class="form-group">
                <label for="cities">المدن التي تغطيها</label>
                <div id="city-container">
                    <select id="city-select" class="form-control">
                        <option value="">اختر مدينة</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= htmlspecialchars($city['Name']) ?>"><?= htmlspecialchars($city['Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="add-button" onclick="addCity()">+ إضافة مدينة</button>
                    <div id="selected-cities"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="countries">الدول</label>
                <div id="country-container">
                    <select id="country-select" class="form-control">
                        <option value="">اختر دولة</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= htmlspecialchars($country['Name']) ?>"><?= htmlspecialchars($country['Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="add-button" onclick="addCountry()">+ إضافة دولة</button>
                    <div id="selected-countries"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="about">نبذة عني</label>
                <textarea id="about" name="about" rows="3"></textarea>
            </div>

            <!-- الحقول الإضافية -->
            <div class="form-group">
                <label for="facebook">رابط Facebook</label>
                <input type="url" id="facebook" name="facebook" class="form-control">
            </div>

            <div class="form-group">
                <label for="twitter">رابط Twitter</label>
                <input type="url" id="twitter" name="twitter" class="form-control">
            </div>

            <div class="form-group">
                <label for="instagram">رابط Instagram</label>
                <input type="url" id="instagram" name="instagram" class="form-control">
            </div>

            <div class="form-group">
                <label for="imageURL">تحميل صورة</label>
                <input type="file" id="imageURL" name="imageURL" class="form-control">
            </div>

            <div class="form-group">
                <label for="experience">الخبرة (بالسنوات)</label>
                <input type="number" id="experience" name="experience" class="form-control">
            </div>

            <div class="form-group">
                <label for="experience"> رقم الجوال</label>
                <input type="number" id="phone" name="phone" class="form-control">
            </div>

            <div class="form-group">
                <button type="submit">تأكيد</button>
            </div>
        </form>
    </div>
</div>

<?php include('../footer.php'); ?>
<!-- Back to Top -->
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
<script src="js/main.js"></script>

<!-- JavaScript for dynamic fields -->
<script>
    function addLanguage() {
        const container = document.getElementById('language-container');
        const div = document.createElement('div');
        div.className = 'input-group';
        div.innerHTML = `
            <input type="text" name="languages[]" placeholder="إضافة لغة">
            <i class="fas fa-trash" onclick="removeField(this)"></i>
        `;
        container.appendChild(div);
    }

    function addCity() {
        const select = document.getElementById('city-select');
        const selectedCity = select.value;
        if (selectedCity) {
            const container = document.getElementById('selected-cities');
            const div = document.createElement('div');
            div.className = 'input-group';
            div.innerHTML = `
                <input type="text" name="cities[]" value="${selectedCity}" readonly>
                <i class="fas fa-trash" onclick="removeField(this)"></i>
            `;
            container.appendChild(div);
            select.value = ''; // إعادة تعيين القائمة المنسدلة
        }
    }

    function addCountry() {
        const select = document.getElementById('country-select');
        const selectedCountry = select.value;
        if (selectedCountry) {
            const container = document.getElementById('selected-countries');
            const div = document.createElement('div');
            div.className = 'input-group';
            div.innerHTML = `
                <input type="text" name="countries[]" value="${selectedCountry}" readonly>
                <i class="fas fa-trash" onclick="removeField(this)"></i>
            `;
            container.appendChild(div);
            select.value = ''; // إعادة تعيين القائمة المنسدلة
        }
    }

    function removeField(element) {
        element.parentNode.remove();
    }
</script>

</body>
</html>
