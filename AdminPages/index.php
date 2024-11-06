

 
           <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Navigation Bar</title>
    <style>
        /* تصميم شريط التنقل */
        .navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 16px;
            text-transform: uppercase;
            transition: background-color 0.3s, color 0.3s;
            margin: 0 10px;
            border-radius: 4px;
        }

        /* لون الخلفية عند التحويم */
        .navbar a:hover {
            background-color: #575757;
            color: #fff;
        }

        /* نمط العنصر النشط */
        .navbar a.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <!-- شريط التنقل -->
    <div class="navbar">
        <a href="add_City_form.php" class="active">Home</a>
        <a href="add_guide_form.php">إضضافة رحلة </a>
        <a href="add_place_form.php">إضافة المكان </a>
        <a href="add_tour_form.php">Tours</a>
        <a href="getBooking.php">Travel Guides</a>
        <a href="getMessage.php">Contact</a>
        <a href="GetSuggested.php">Suggestions</a>
    </div>

</body>
</html>

         
    