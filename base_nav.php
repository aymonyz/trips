<?php
 // تأكد من بدء الجلسة

// الحصول على الصفحة الحالية من الرابط أو الافتراض بأن الصفحة هي الرئيسية
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// التحقق من وجود القيم في الجلسة وتعيين قيم افتراضية
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$id = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;

// إعداد المحتوى بناءً على الصفحة الحالية
if ($page == 'home') {
    $heroClass = 'hero-header';
    $heroBgStyle = "background-image: url('img/about-bg.jpg'); background-size: cover; background-position: center;";
    $heroTitle = 'Enjoy Your Vacation With Us';
    $heroText = 'Tempor erat elitr rebum at clita diam amet diam et eos erat ipsum lorem sit';
} else {
    $heroClass = 'hero-header-other';
    switch ($page) {
        case 'about':
            $heroBgStyle = "background-image: url('img/about-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Learn More About Us';
            $heroText = 'Discover the story behind our company and our mission.';
            break;
        case 'packages':
            $heroBgStyle = "background-image: url('img/packages-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Explore Our Tour Packages';
            $heroText = 'Find the perfect tour package for your next adventure.';
            break;
        case 'tourDetails':
            $heroBgStyle = "background-image: url('img/packages-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Explore Our Tour Packages';
            $heroText = 'Find the perfect tour package for your next adventure.';
            break;
        case 'team':
            $heroBgStyle = "background-image: url('img/team-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Meet Our Travel Guides';
            $heroText = 'Our expert guides will make your journey unforgettable.';
            break;
        case 'contact':
            $heroBgStyle = "background-image: url('img/contact-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Get in Touch with Us';
            $heroText = 'We’re here to help with any questions or inquiries.';
            break;
        case 'Discover':
            $heroBgStyle = "background-image: url('img/packages-bg.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Explore Our Tour Packages';
            $heroText = 'Find the perfect tour package for your next adventure.';
            break;
        default:
            $heroBgStyle = "background-image: url('img/bg-hero.jpg'); background-size: cover; background-position: center;";
            $heroTitle = 'Welcome to Our Travel Agency';
            $heroText = 'Explore the world with us at your side.';
            break;
    }
}
?>

<!-- Navbar & Hero Start -->
<head>
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        a {
            margin-right: 20px;
            color: white !important;
        }
    </style>
</head>
<div class="container-fluid position-relative p-0">
<nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0" style="background: #14141f;">
    <a href="../index.php" class="navbar-brand p-0">
        <h1 class="text-primary m-0"><i class="fa fa-map-marker-alt me-3"></i>Guide Me</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse" style="padding: 25px;">
        <div class="navbar-nav ms-auto py-0">
            <a href="../index.php?page=home" class="nav-item nav-link <?= $page == 'home' ? 'active' : '' ?>" style="color: white;">الرئيسة</a>
            <a href="../index.php?page=Discover" class="nav-item nav-link <?= $page == 'Discover' ? 'active' : '' ?>" style="color: white;">استكشف</a>
            <a href="../index.php?page=about" class="nav-item nav-link <?= $page == 'about' ? 'active' : '' ?>" style="color: white;">من نحن</a>
            <a href="../index.php?page=packages" class="nav-item nav-link <?= $page == 'packages' ? 'active' : '' ?>" style="color: white;">الرحلات</a>
            <a href="../index.php?page=team" class="nav-item nav-link <?= $page == 'team' ? 'active' : '' ?>" style="color: white;">المرشدين</a>
            <a href="../index.php?page=contact" class="nav-item nav-link <?= $page == 'contact' ? 'active' : '' ?>" style="color: white;">تواصل معنا</a>
            <?php if ($role == 'admin'): ?>
                <a href="../AdminPages/index.php" class="nav-item nav-link" style="color: white;">المدير</a>
            <?php endif; ?>
            <?php if ($role == 'guide' && $id): ?>
                <a href="add_suggest_form.php?guideId=<?= htmlspecialchars($id) ?>" class="nav-item nav-link" style="color: white;">اقتراح مكان</a>
            <?php endif; ?>
        </div>
        <?php if (isset($_SESSION['userId']) && isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
            <a href="../index.php?page=profile" class="btn btn-primary rounded-pill py-2 px-4">الملف الشخصي</a>
            <a href="../index.php?page=logout" class="btn btn-secondary rounded-pill py-2 px-4 ms-2">تسجيل خروج</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'guide'): ?>
            <a href="dashboard-button.php" class="btn btn-primary rounded-pill py-2 px-4">الملف الشخصي</a>
            <a href="../index.php?page=logout" class="btn btn-secondary rounded-pill py-2 px-4 ms-2">تسجيل خروج</a>
        <?php endif; ?>
    </div>
</nav>
</div>
