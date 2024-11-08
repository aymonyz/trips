<!DOCTYPE html>
<html lang="ar">
    


<div id="google_translate_element"></div>

<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'ar',  // تعيين اللغة الافتراضية إلى العربية
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<head>
    <meta charset="utf-8">
    <title>Guide Me | أرشدني</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    


</head>

<body>

<?php
include 'db.php';

session_start(); // Only here, at the top of the main file
// Before accessing $_SESSION['role'], check if it exists
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $id=$_SESSION["userId"] ;
} else {
    // If the role is not set, you can define a default value or handle it as needed
    $role = 'guest'; // Default role or redirect to login
}

// Cache control headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// Include other files
include 'nav.php'; // `nav.php` will inherit the session
?>


    <!-- Page Content -->
    <div class="container mt-5">
    <?php
// Get the 'page' query parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Set up routing
switch($page) {
    case 'about':
        include('pages/about.php');
        break;
    case 'packages':
        include('pages/package.php');
        break;
    case 'team':
        include('pages/team.php');
        break;
    case 'contact':
        include('pages/contact.php');
        break;
    case 'tourDetails':
        include('pages/tourDetails.php');
        break;
    case 'login': 
        include('pages/login.php');
        break;
    case 'register': 
        include('pages/register.php');
        break;
    case '404':
        include('pages/404.php');
        break;
    case 'logout': 
        include('pages/logout.php');
        break;
    case 'profile': 
        include('pages/profile.php');
        break;
    case 'booking': 
        include('pages/booking.php');
        break;
    case 'Discover':
        include('pages/Discover.php');
        break;
    // csse 'placeDetails':
    //     include ('pages/placeDetails.php');
    //     break;
    case 'placeDetails':
        include('pages/placeDetails.php');
        break;
        case 'details':
            include('GuidePages/details.php');
    case 'home':
    default:
        include('pages/home.php');
}
?>

    </div>

    <!-- Include the Footer -->
    <?php include('footer.php'); ?>
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>