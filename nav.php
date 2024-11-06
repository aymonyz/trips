<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    // If the role is not set, you can define a default value or handle it as needed
    $role = 'guest'; // Default role or redirect to login
}
// Set the hero class and content based on the current page
if ($page == 'home') {
    $heroClass = 'hero-header';
    $heroBgStyle = "background-image: url('img/about-bg.jpg'); background-size: cover; background-position: center;";
    $heroTitle = 'Enjoy Your Vacation With Us';
    $heroText = 'Tempor erat elitr rebum at clita diam amet diam et eos erat ipsum lorem sit';
} else {
    $heroClass = 'hero-header-other';
    // Set background images and content for each page
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
<div class="container-fluid position-relative p-0">
<nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
    <a href="index.php" class="navbar-brand p-0">
        <h1 class="text-primary m-0"><i class="fa fa-map-marker-alt me-3"></i>Guide Me</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
                 <?php if ($role == 'admin'): ?>
                    <a href="admin.php" class="nav-item nav-link">Admin</a>
                <?php endif; ?>
                <?php if ($role == 'guide'): ?>
                    <a href="guide.php?form=addPlace" class="nav-item nav-link">Suggest Place</a>
                <?php endif; ?>
            <a href="index.php?page=home" class="nav-item nav-link <?= $page == 'home' ? 'active' : '' ?>">الرئيسة</a>
            <a href="index.php?page=Discover" class="nav-item nav-link <?= $page == 'Discover' ? 'active' : '' ?>">استكشف</a>
            <a href="index.php?page=about" class="nav-item nav-link <?= $page == 'about' ? 'active' : '' ?>">من نحن </a>
            <a href="index.php?page=packages" class="nav-item nav-link <?= $page == 'packages' ? 'active' : '' ?>">الرحلات </a>
            <a href="index.php?page=team" class="nav-item nav-link <?= $page == 'team' ? 'active' : '' ?>">المرشدين </a>
            <a href="index.php?page=contact" class="nav-item nav-link <?= $page == 'contact' ? 'active' : '' ?>">تواصل معانا </a>
        </div>
        <?php if (isset($_SESSION['userId'])): ?>
    <!-- Show Profile and Logout if logged in -->
    <?php if ($_SESSION['role'] == 'guide'): ?>
        <a href="GuidePages/profile.php" class="btn btn-primary rounded-pill py-2 px-4">الملف الشخصي</a>
    <?php elseif ($_SESSION['role'] == 'user'): ?>
        <a href="index.php?page=profile" class="btn btn-primary rounded-pill py-2 px-4">الملف الشخصي</a>
    <?php endif; ?>
    
    <a href="index.php?page=logout" class="btn btn-secondary rounded-pill py-2 px-4 ms-2">تسجيل خروج</a>
<?php else: ?>
    <!-- Show Login and Register if not logged in -->
    <a href="index.php?page=login" class="btn btn-primary rounded-pill py-2 px-4" style="margin:5px">تسجيل دخول</a>
    <a href="index.php?page=register" class="btn btn-primary rounded-pill py-2 px-4">إنشاء حساب</a>
<?php endif; ?>
    </div>
</nav>


    <!-- Hero Section -->
    <div class="container-fluid py-5 mb-5 <?= $heroClass ?>" style="<?= $heroBgStyle ?>">
        <div class="container py-5">
            <div class="row justify-content-center py-5">
                <div class="col-lg-10 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-3 text-white mb-3 animated slideInDown"><?= $heroTitle ?></h1>
                    <p class="fs-4 text-white mb-4 animated slideInDown"><?= $heroText ?></p>
                    <?php if ($page == 'home'): ?>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Navbar & Hero End -->
