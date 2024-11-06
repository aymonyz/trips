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
        <h2 class="text-center mb-4">إنشاء حساب المرشد</h2>
        <form action="register_basic_info.php" method="POST">
            <div class="form-group mb-3">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary w-100">التالي</button>
            </div>
        </form>
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
