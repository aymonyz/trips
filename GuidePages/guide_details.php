<?php
// Start the session
session_start();
include '../db.php'; // Include database connection

// Check if guideId is provided in the URL
if (!isset($_GET['guideId'])) {
    echo "Guide ID not specified.";
    exit();
}

// Get the guideId from the URL
$guideId = $_GET['guideId'];

// Fetch the guide's details from the database
$query = $pdo->prepare("SELECT * FROM tourguide WHERE guideId = ?");
$query->execute([$guideId]);
$guide = $query->fetch(PDO::FETCH_ASSOC);

if (!$guide) {
    echo "Guide not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل المرشد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="img/favicon.ico" rel="icon">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        a {
            margin-right: 20px;
            color: white !important;
        }
        .mrg img {
            width: 96px;
            height: 96px;
            border-radius: 100%;
            object-fit: cover;
        }
        .style {
            flex-direction: row-reverse;
            padding: 10px;
            border-radius: 20px;
        }
        .shape {
            align-items: self-end;
            display: flex;
            width: 75%;
            flex-direction: column;
            justify-content: center;
        }
        .shape a {
            align-self: end;
        }
        .style-mid {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            width: 60%;
        }
        .items {
            display: flex;
            flex-direction: row-reverse;
        }
        .bulid-icon {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }
        .bulid-icon i {
            font-size: 28px;
            color: #000000c4;
        }
        .bulid-text {
            display: flex;
            flex-direction: column;
            align-items: end;
        }
        .bulid-text span {
            margin: 0;
            padding: 0;
        }
        .lang {
            background: #4c4c4c;
            color: white;
            border-radius: 100px;
            font-weight: 500;
            padding: .35em .65em;
        }
        .new {
            padding: 10px;
        }
        .Describe {
            font-size: 16px;
            line-height: 19px;
            color: #000000;
            text-align: justify;
            margin-top: 4px;
        }
        .button-details {
            border: 1px solid #6b9312;
            border-radius: 10px;
            padding: 5px 67px;
            transition: 0.7s;
        }
        .button-details:hover {
            color: white;
            font-weight: bold;
            background-color: #6b9312;
        }
        .team-item {
            /* box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); */
        }
        .Search {
            width: 85%;
        }
        .input-group {
            flex-direction: row-reverse;
            margin-bottom: 20px;
        }
        .Search .btn {
            width: 146px;
            height: 48px;
            color: white;
            border: 0;
            background: #6b9312;
            border-radius: 15px 0px 0px 15px;
        }
        .ss{
            text-align: right;
        }
        .containerr{
        height: 370px;
        }
        .social-link {
        
        font-size: 20px;
        transition: background-color 0.3s;
        margin-bottom: 10px;
    }
    .social-link i{
        padding: 5px 15px;
        border-radius: 5px;
        
    }
    .fb{
        background-color: #3B5998;
        border-radius: 5px;
        text-align: center;
    }
    .tw{
        background-color: #38A1F3;
        border-radius: 5px;
        text-align: center;
    }
    .in{
        background-color: #C13584;
        border-radius: 5px;
        text-align: center;
    }
.flex-column{
    justify-content: center;
}
.btnn{
    background: #292929;
    border-radius: 10px;
    font-weight: 500;
    padding: 5px 10px;
    line-height: 1.5;
    font-weight: bold;
}
    </style>
</head>
<body>

<?php include '../base_nav.php'; ?>

<div class="container-xxl py-5 ">
    <div class="containerr">

        <div class="row g-8">
            <div class="col-lg-12 col-md-6 wow fadeInUp new" data-wow-delay="0.1s">
                <h3 class="ss">تفاصيل المرشد السياحي</h3>
                <div class="team-item d-flex align-items-start  style">
                    <!-- Image Section -->
                    <div class="team-img mrg">
                        <img class="img-fluid rounded-circle" src="<?php echo '../'.$guide['imageURL']; ?>" alt="Guide Image" width="96" height="96">
                    </div>

                    <!-- Guide Details -->
                    <div class="ms-4 shape">
                        <h5 class="mb-4"><?php echo $guide['name']; ?></h5>
                        <div class="style-mid">
                            <div class="items mb-2">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-share"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد المسارات</span>
                                    <span>0</span>
                                </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-plane-departure"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد الاماكن</span>
                                    <span>0</span>
                                </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-earth-europe"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد اللغات</span>
                                    <span>12</span>
                                </div>
                            </div>
                        </div>
                        <p class="mb-2 lang"><?php echo $guide['languages']; ?></p>
                        <p class="Describe mb-2"><?php echo $guide['about']; ?></p>
                        <a href="GuidePages/guide_details.php?guideId=<?php echo $guide['guideId']; ?>" class="btnn"> طلب مسار خاص  </a>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bolder  mb-4" style="text-align: center; color:#000">حسابات التواصل</h3>
                        <div class="d-flex flex-column">
                            <a href="https://twitter.com/" class="social-link tw"><i class="fa-brands fa-twitter"></i></a>
                            <a href="https://facebook.com/" class="social-link  fb"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.instagram.com/" class="social-link in"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
   

    </div>
</div>

<?php include '../footer.php'; ?>

</body>
</html>
