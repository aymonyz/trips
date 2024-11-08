<!-- Team Start -->
 <style>
    .mrg{
        margin-left: 30px;
    }
    .mrg img{
        width: 96px;
    height: 96px;
    border-radius: 100%;
    object-fit: cover;
    }
    .style{
        flex-direction: row-reverse;
        padding: 10px;
        border-radius: 20px;
    }
    .shape{
        align-items: self-end;
        display: flex;
        width: 75%;
        flex-direction: column;
        justify-content: center;
    }
    .shape a{
        align-self: baseline;
    }
    .style-mid{
        display: flex;
        flex-direction: row-reverse;
        justify-content: space-between;
        flex-direction: ;
        width: 60%;
    }
    .items{
        display: flex;
        flex-direction: row-reverse;
    }
    .bulid-icon{
        display: flex;
        align-items: center;
        margin-left: 10px;
        
    }
    .bulid-icon i{
        font-size: 28px;
        color: #000000c4;
    }
    .bulid-text{
        display: flex;
        flex-direction: column;
        align-items: end;
    }
    .bulid-text span{
        margin: 0;
        padding: 0;
    }
    .lang{
    background: #4c4c4c;
    color: white;
    border-radius: 100px;
    font-weight: 500;
    padding: .35em .65em;
    }
    .new{
        padding: 10px;
    }
.Describe{
    font-size: 16px;
    line-height: 19px;
    color: #000000;
    text-align: justify;
    margin-top: 4px;
}
.button-details{
    border: 1px solid #6b9312;
    border-radius: 10px;
    padding: 5px 67px;
    transition: 0.7s;
}
.button-details:hover{
    color:white;
    font-weight: bold;
    background-color: #6b9312;
}
.team-item {
   box-shadow :0px 4px 10px rgba(0, 0, 0, 0.1);
}
.Search{
    width: 85%;
 

}
.input-group{
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

 </style>
 <head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php $cityQuery = $pdo->query("SELECT CityId, Name FROM Cities");
$cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
$languageQuery = $pdo->query("SELECT DISTINCT languages FROM tourguide");
$languagesList = [];
while ($row = $languageQuery->fetch(PDO::FETCH_ASSOC)) {
    $languages = explode(', ', $row['languages']); // تقسيم النص إلى مصفوفة باستخدام الفاصلة
    $languagesList = array_merge($languagesList, $languages); // دمج اللغات مع القائمة النهائية
}
$languagesList = array_unique($languagesList); // إزالة التكرارات
?>

?>
</head>
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">دليل السفر</h6>
            <h1 class="mb-5">تعرَّف على دليلنا</h1>
        </div>
        <div class="row Search">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input name="search" id="search" type="text" class="form-control" placeholder="المرشد السياحي">
                                <select class="form-control" id="CityId" name="CityId">
                                    <option value="0" selected="selected">المدينة</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= htmlspecialchars($city['CityId']) ?>"><?= htmlspecialchars($city['Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select class="form-control" id="Language" name="Language">
                        <option value="">اللغة</option>
                        <?php foreach ($languagesList as $language): ?>
                            <option value="<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($language) ?></option>
                        <?php endforeach; ?>
                    </select>
                                <button class="btn dse" type="submit" style="border-radius: 10px 0px 0px 10px;">البحث</button>
                            </div>
                        </div>
                    </div>
        <div class="row g-8">
           
                   
            
            <?php
            // Fetch guides from the database
            $stmt = $pdo->query("SELECT guideId, name, rating, imageURL, facebook, twitter, instagram, languages,about FROM tourguide");
            

            while ($guide = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id=$guide['guideId'];
                $tourStmt = $pdo->prepare("SELECT * FROM tour WHERE guideId = ?");
                $pladeStmt = $pdo->prepare("SELECT * FROM place WHERE guideid = ?");
                $tourStmt->execute([$id]);
                $pladeStmt->execute([$id]);

                $place = $pladeStmt->fetchAll(PDO::FETCH_ASSOC); 
                $tours = $tourStmt->fetchAll(PDO::FETCH_ASSOC); 
                $tourCount = count($tours);
                $placeCount= count($place);
                $cityQuery = $pdo->query("SELECT CityId, Name FROM Cities");
                $cities = $cityQuery->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="col-lg-10 col-md-6 wow fadeInUp new" data-wow-delay="0.1s" >


                    <div class="team-item d-flex align-items-star border  style">
                        <!-- محتوي الصوره  -->
                        <div class="team-img mrg">
                            <img class="img-fluid rounded-circle" src="<?php echo $guide['imageURL']; ?>" alt="Guide Image" width="96" height="96">
                        </div>

                        <!-- باقي العناصر  -->
                        <div class="ms-4 shape" style=" ">
                            <h5 class="mb-4"><?php echo $guide['name']; ?></h5>
                            <div class="style-mid">

                            <div class="items mb-2">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-share"></i>
                                </div>
                               <div class="bulid-text">
                                    <span class="text-muted" >عدد المسارات</span>
                                    <span> <?php  echo $tourCount?></span>
                               </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-plane-departure"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted">عدد الاماكن</span>
                                    <span> <?php echo $placeCount?></span>
                               </div>
                            </div>
                            <div class="items">
                                <div class="bulid-icon">
                                    <i class="fa-solid fa-earth-europe"></i>
                                </div>
                                <div class="bulid-text">
                                    <span class="text-muted ">عدد اللغات</span>
                                    <?php 
                                        $languages = explode(', ', $guide['languages']); // تحويل النص إلى مصفوفة
                                        echo count($languages);
                                        $languagesCount= count($languages); // عرض عدد اللغات
                                        ?> لغات
                                </div>
                            </div>

                            </div>
                            <p class=" mb-2 lang"> <?php echo $guide['languages']; ?> </p>
                            <p class="Describe mb-2"> <?php echo $guide['about'] ?></p>

                            <!-- <p class="text-warning mb-2">التقييم: <?php echo str_repeat('★', $guide['rating']); ?></p> -->
                            <a href="GuidePages/guide_details.php?guideId=<?php echo $guide['guideId']; ?>&languagesCount=<?php echo $languagesCount; ?>&tourCount=<?php echo $tourCount; ?>&placeCount=<?php echo $placeCount; ?>" class="button-details">المزيد من التفاصيل</a>
                            </div>

                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<!-- Team End -->







































<!-- 
<option value="اللغة الصينية">اللغة الصينية</option>
                                        <option value="اللغة الفرنسية">اللغة الفرنسية</option>
                                        <option value="لغة الاشارة">لغة الاشارة</option>
                                        <option value="اللغة الألمانية">اللغة الألمانية</option>
                                        <option value="اللغة الإسبانية">اللغة الإسبانية</option>
                                        <option value="اللغة الروسية">اللغة الروسية</option>
                                        <option value="الأوزبكية">الأوزبكية</option>
                                        <option value="الفرنسية">الفرنسية</option>
                                        <option value="اليابانية">اليابانية</option>
                                        <option value="التركية">التركية</option>
                                        <option value="الأردية">الأردية</option>
                                        <option value="الإيطالية">الإيطالية</option>
                                        <option value="اللغة اليابانية">اللغة اليابانية</option>
                                        <option value="الانجليزيه">الانجليزيه</option>
                                        <option value="عربي">عربي</option>
                                        <option value="اللغة الصينية- الماندرين">اللغة الصينية- الماندرين</option>
                                        <option value="اليابانيه">اليابانيه</option>
                                        <option value="العربية">العربية</option>
                                        <option value="لغة الإشارة">لغة الإشارة</option>
                                        <option value="اللغةالعربية">اللغةالعربية</option>
                                        <option value="اللغة العربية ">اللغة العربية </option>
                                        <option value="English">English</option>
                                        <option value="العربية ">العربية </option>
                                        <option value="الانجلييزية ">الانجلييزية </option>
                                        <option value="اللغة الإنجليزية ">اللغة الإنجليزية </option>
                                        <option value="الاسبانية">الاسبانية</option> -->