
<?php
// Start the session to use session variables
session_start();

// فرضاً أن userId تم تخزينه في الجلسة بعد تسجيل دخول المستخدم
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
} else {
    echo "User ID غير متوفر.";
    exit();
}
?>


<?php
// Start the session

include '../db.php'; // Include database connection

// Check if guideId is provided in the URL
if (!isset($_GET['guideId'])) {
    echo "Guide ID not specified.";
    exit();
}
if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php?=home");
    exit();
}

// Get the guideId from the URL
$guideId = $_GET['guideId'];
$languagesCount = $_GET['languagesCount'];
$tourCount = $_GET['tourCount'];
$placeCount = $_GET['placeCount'];
$starRating = $_GET['rating'];

// Fetch the guide's details from the database
$query = $pdo->prepare("SELECT * FROM tourguide WHERE guideId = ?");
$query->execute([$guideId]);
$guide = $query->fetch(PDO::FETCH_ASSOC);

if (!$guide) {
    echo "Guide not found.";
    exit();
}

// Handle new review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['userId'])) {
        header("Location: ../index.php?page=login");
        exit();
    }
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['userId']; // assuming user is logged in and session contains user_id

    // Insert review into database
    $stmt = $pdo->prepare("INSERT INTO review (userId, guideId, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $guideId, $rating, $comment]);
}

// Fetch reviews for the guide
$reviewsQuery = $pdo->prepare("SELECT * FROM review WHERE guideId = ? ORDER BY created_at DESC");
$reviewsQuery->execute([$guideId]);
$reviews = $reviewsQuery->fetchAll(PDO::FETCH_ASSOC);
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
.rating-stars {
    color: #FFD700;
}
    </style>
</head>
<body>

<?php include '../base_nav.php'; ?>

<div class="container-xxl py-5">
    <div class="containerr">

        <div class="row g-8">
            <div class="col-lg-12 col-md-6 wow fadeInUp new" data-wow-delay="0.1s">
                <h3 class="ss">تفاصيل المرشد السياحي</h3>
                <div class="team-item d-flex align-items-start style">
                    <!-- Image Section -->
                    <div class="team-img mrg">
                        <img class="img-fluid rounded-circle" src="<?php echo '../'.$guide['imageURL']; ?>" alt="Guide Image" width="96" height="96">
                    </div>

                    <!-- Guide Details -->
                    <div class="ms-4 shape">
                        <h5 class="mb-4"> <?php
                        echo str_repeat('⭐', $starRating); 
                        ?>
                       </span><?php echo $guide['name']; ?> </h5>
                     
                      

                        <div class="style-mid">
                            <div class="items mb-2">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-share"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد المسارات</span>
                                    <span><?php echo $tourCount?></span>
                                </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-plane-departure"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد الاماكن</span>
                                    <span><?php echo $placeCount ?></span>
                                </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-earth-europe"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد اللغات</span>
                                    <span><?php echo $languagesCount?></span>
                                </div>
                            </div>
                            
                        </div>
                        <p class="mb-2 lang"><?php echo $guide['languages']; ?></p>
                        <p class="Describe mb-2"><?php echo $guide['about']; ?></p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bolder  mb-4" style="text-align: center; color:#000">حسابات التواصل</h3>
                        <div class="d-flex flex-column">
                            <a href="<?php echo $guide['twitter']?>" class="social-link tw"><i class="fa-brands fa-twitter"></i></a>
                            <a href="<?php echo $guide['facebook']?>" class="social-link fb"><i class="fa-brands fa-facebook"></i></a>
                            <a href="<?php echo $guide['instagram']?>" class="social-link in"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <a href="add_masar.php?userId=<?php echo $userId; ?>&guideId=<?php echo $_GET['guideId']; ?>"  class="btn custom-masar-btn">طلب مسار خاص</a>

<style>
    /* تنسيقات الزر المخصص */
    .custom-masar-btn {
        float: right;
        background-color: #333333; /* اللون الرمادي الداكن */
        color: #ffffff; /* لون النص الأبيض */
        border-radius: 5px; /* حواف مستديرة قليلاً */
        padding: 10px 20px; /* مساحة داخلية للزر */
        font-weight: bold; /* خط عريض */
        text-align: center; /* محاذاة النص في الوسط */
        display: inline-block; /* جعل الزر يبدو كعنصر كتلة */
        border: none; /* إزالة الحدود الافتراضية */
    }
    /* تأثير التمرير */
    .custom-masar-btn:hover {
        background-color: #555555; /* لون أغمق عند تمرير الفأرة */
        color: #ffffff; /* التأكد من أن النص يظل أبيض */
        text-decoration: none; /* إزالة أي خط تحت النص عند التمرير */
    }
</style>

            </div>
        </div>

        <!-- Form for adding a new review -->
        <div class="mt-4" style="text-align: right;">
            <h4>إضافة تقييم وتعليق</h4>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="rating">التقييم:</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="5">5 - ممتاز</option>
                        <option value="4">4 - جيد جداً</option>
                        <option value="3">3 - جيد</option>
                        <option value="2">2 - مقبول</option>
                        <option value="1">1 - ضعيف</option>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label for="comment">التعليق:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary mt-3">إرسال</button>
            </form>
        </div>

        <!-- Displaying reviews -->
        <div class="mt-5" style="text-align: right;">
    <h4>التعليقات والتقييمات</h4>
    <?php if ($reviews): ?>
        <?php 
        // أولاً نعرض أول 10 تعليقات فقط
        $totalReviews = count($reviews);
        $showLimit = 10;
        ?>
        
        <div id="reviewContainer">
            <?php foreach ($reviews as $index => $review): ?>
                <div class="card mb-3 review-card" style="<?php echo ($index >= $showLimit) ? 'display: none;' : ''; ?>">
                    <div class="card-body">
                        <h5 class="card-title">التقييم: <span style="color:gold;"><?php echo str_repeat('&#9733;', $review['rating']); ?> / 5</span></h5>
                        <p class="card-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <p class="text-muted">تاريخ الإضافة: <?php echo htmlspecialchars($review['created_at']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalReviews > $showLimit): ?>
            <button id="loadMore" class="btn btn-primary">عرض المزيد</button>
        <?php endif; ?>
        
    <?php else: ?>
        <p>لا توجد تعليقات بعد.</p>
    <?php endif; ?>
</div>

<script>
// جافاسكريبت لعرض المزيد من التعليقات عند الضغط على الزر
document.getElementById("loadMore").addEventListener("click", function() {
    // جلب جميع التعليقات
    let hiddenReviews = document.querySelectorAll(".review-card[style*='display: none;']");
    
    // عدد التعليقات التي سيتم عرضها دفعة واحدة
    let loadCount = 10;
    
    for (let i = 0; i < loadCount; i++) {
        if (hiddenReviews[i]) {
            hiddenReviews[i].style.display = "block";
        }
    }

    // إذا لم يبقى تعليقات مخفية، قم بإخفاء الزر
    if (document.querySelectorAll(".review-card[style*='display: none;']").length === 0) {
        document.getElementById("loadMore").style.display = "none";
    }
});
</script>


    </div>
</div>



</body>
</html>
