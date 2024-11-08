<?php


session_start();
include '../db.php';

// تحقق من تسجيل دخول العميل
if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php?=home");
    exit();
}
$guideId = $_SESSION["userId"];
$role= $_SESSION["role"];
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المرشد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 100%;
            margin: 150px auto;
            text-align: center;
            display: flex;
            justify-content: center;
        }
        .dashboard-button {
            width: 100%;
            margin: 10px 0;
            padding: 20px 200px;
            font-size: 18px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        .dashboard-button:hover {
            background-color: #f0f0f0;
        }
        .dashboard-button a {
            text-decoration: none;
            color: #333;
            display: block;
        }
        h2{
            text-align: center;
            margin-top:30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<?php include '../base_nav.php'; ?>
<h2 >لوحة تحكم المرشد</h2>
<div class="dashboard-container">
    
   <div class="top">
   <div class="dashboard-button">
        <a href="profile.php">تعديل الملف الشخصي</a>
    </div>
    <div class="dashboard-button">
        <?php
        echo '<a href="requests.php?guideId=' . $guideId . '">طلباتي</a>';?>
    </div>
   </div>
   <div class="bottom">
     
   <div class="dashboard-button">
        <a href="change_password.php">تعديل كلمة المرور</a>
    </div>
    <div class="dashboard-button">
    <?php
echo '<a href="add_tour_form.php?guideId=' . $guideId . '">المسارات</a>';
?>
</div>
   </div>
</div>
<?php include '../footer.php'; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
