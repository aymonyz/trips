
<?php
// include "../nav.php";
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Navigation Bar</title>
    <style>
        /* تصميم شريط التنقل */
        .navbar {
            background: linear-gradient(90deg, #007bff, #0056b3);
            overflow: hidden;
            display: flex;
            justify-content: center;
            padding: 15px 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }

        .navbar a {
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, transform 0.3s;
            margin: 0 12px;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
        }

        .navbar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 300%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s;
            transform: skewX(-30deg);
        }

        /* تأثير التحويم */
        .navbar a:hover::before {
            left: 100%;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.1);
        }

        /* نمط العنصر النشط */
        .navbar a.active {
            background-color: #0056b3;
            color: #fff;
            transform: scale(1.1);
        }

        /* تأثير بسيط للانتقال */
        .navbar a:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <!-- شريط التنقل -->
    <div class="navbar">
        <a href="add_City_form.php" class="active">إضافة مدينة</a>
        <a href="add_guide_form.php">إضافة مشرف</a>
        <a href="add_place_form.php">إضافة مكان</a>
        <a href="add_tour_form.php">الجولات</a>
        <a href="getBooking.php">أدلة السفر</a>
        <a href="getMessage.php">اتصل بنا</a>
        <a href="GetSuggested.php">xاقتراحات</a>
        <a href="../index.php">الرجوع للموقع</a>
    </div>

</body>
</html>
