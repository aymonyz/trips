<!-- Team Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">دليل السفر</h6>
            <h1 class="mb-5">تعرَّف على دليلنا</h1>
        </div>
        <div class="row g-4">
            <?php
            // Fetch guides from the database
            $stmt = $pdo->query("SELECT name, rating, imageURL, facebook, twitter, instagram FROM tourguide");
            while ($guide = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="<?php echo $guide['imageURL']; ?>" alt="Guide Image">
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -19px;">
                            <?php if ($guide['facebook']) : ?>
                                <a class="btn btn-square mx-1" href="<?php echo $guide['facebook']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if ($guide['twitter']) : ?>
                                <a class="btn btn-square mx-1" href="<?php echo $guide['twitter']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if ($guide['instagram']) : ?>
                                <a class="btn btn-square mx-1" href="<?php echo $guide['instagram']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0"><?php echo $guide['name']; ?></h5>
                            <small><?php echo $guide['rating']; ?></small>
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
