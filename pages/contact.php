<?php
include 'db.php'; // Include your database connection

// Initialize variables to store error messages and success messages
$successMessage = "";
$errorMessage = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Prepare and execute the SQL query
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");

    try {
        $stmt->execute([$name, $email, $subject, $message]);
        $successMessage = "Message sent successfully!";
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>

<!-- Contact Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Contact Us</h6>
            <h1 class="mb-5">اتصل بنا لأي استفسار</h1>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

  <!-- بداية قسم التواصل -->
<div class="row g-4">
    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
        <h5>تواصل معنا</h5>
        <p class="mb-4">نوفر لكم الدعم الكامل والإجابة على استفساراتكم، لا تترددوا في التواصل معنا.</p>
        <div class="d-flex align-items-center mb-4">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fa fa-map-marker-alt text-white"></i>
            </div>
            <div class="ms-3">
                <h5 class="text-primary">المكتب</h5>
                <p class="mb-0">123 شارع، نيويورك، الولايات المتحدة</p>
            </div>
        </div>
        <div class="d-flex align-items-center mb-4">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fa fa-phone-alt text-white"></i>
            </div>
            <div class="ms-3">
                <h5 class="text-primary">الهاتف</h5>
                <p class="mb-0">+012 345 67890</p>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fa fa-envelope-open text-white"></i>
            </div>
            <div class="ms-3">
                <h5 class="text-primary">البريد الإلكتروني</h5>
                <p class="mb-0">info@example.com</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="name" name="name" placeholder="اسمك" required>
                        <label for="name">اسمك</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="بريدك الإلكتروني" required>
                        <label for="email">بريدك الإلكتروني</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="الموضوع" required>
                        <label for="subject">الموضوع</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" name="message" placeholder="اترك رسالتك هنا" id="message" style="height: 100px" required></textarea>
                        <label for="message">الرسالة</label>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary w-100 py-3" type="submit">أرسل الرسالة</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<!-- نهاية قسم التواصل -->

