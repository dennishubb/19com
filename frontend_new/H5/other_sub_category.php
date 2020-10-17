<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19.com</title>
<?php
    include("style_script.php");
include(__DIR__ . '/common/function.php');
?>
</head>

<body>
    <div class="main_container">
        <header>
            <?php
                include("header.php");
            ?>
        </header>
        
        <div class="body_container">
            <?php
            $billiards = httpGet('http://localhost/api/news.php?action=get_hot_news&limit=5&category_id=3');
            $billiards = json_decode($billiards,true);
            ?>
            <!-- Start Categories -->
            <session>
                <div class="session_block">
                    <div class="session_block_title">台球</div>
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($billiards as $i => $values){ ?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="#"><img src="<?php echo $values['thumbnail_small2'] ?>"></a></div>
                                    <div class="title"><a href="#"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'] . ' '. $values['category'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>
            <!-- End Categories -->

            <!-- Start Categories -->
            <session>
                <div class="session_block">
                    <div class="session_block_title">羽毛球</div>
                    <?php
                    $badminton = httpGet('http://localhost/api/news.php?action=get_hot_news&limit=5&category_id=6');
                    $badminton = json_decode($badminton,true);
                    ?>
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($badminton as $i => $values){ ?>
                                    <div class="swiper-slide">
                                        <div class="img"><a href="#"><img src="<?php echo $values['thumbnail_small2'] ?>"></a></div>
                                        <div class="title"><a href="#"><?php echo $values['title'] ?></a></div>
                                        <div class="datetime"><?php echo $values['active_at'] . ' '. $values['category'] ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>
            <!-- End Categories -->

            <!-- Start Categories -->
            <?php
            $swim = httpGet('http://localhost/api/news.php?action=get_hot_news&limit=5&category_id=7');
            $swim = json_decode($swim,true);
            if(count($swim) != 0){
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">游泳</div>
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($swim as $i => $values){ ?>
                                    <div class="swiper-slide">
                                        <div class="img"><a href="#"><img src="<?php echo $values['thumbnail_small2'] ?>"></a></div>
                                        <div class="title"><a href="#"><?php echo $values['title'] ?></a></div>
                                        <div class="datetime"><?php echo $values['active_at'] . ' '. $values['category'] ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>
            <?php } ?>
            <!-- End Categories -->

            <!-- Start Categories -->
            <?php
            $others = httpGet('http://localhost/api/news.php?action=get_hot_news&limit=5&category_id=8');
            $others = json_decode($others,true);
            if(count($others) != 0){
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">其他</div>
                    
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($others as $i => $values){ ?>
                                    <div class="swiper-slide">
                                        <div class="img"><a href="#"><img src="<?php echo $values['thumbnail_small2'] ?>"></a></div>
                                        <div class="title"><a href="#"><?php echo $values['title'] ?></a></div>
                                        <div class="datetime"><?php echo $values['active_at'] . ' '. $values['category'] ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>
            <?php } ?>
            <!-- End Categories -->
            <?php
            $latestNewsOthers = httpGet('http://localhost/api/news.php?action=get_latest_news&category_id=0&limit=3');
            $latestNewsOthers = json_decode($latestNewsOthers, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        更多新闻
                    </div>
                    <div class="break_line"></div>

                    <div class="news_list">
                        <?php foreach ($latestNewsOthers as $values){ ?>
                            <a href="#">
                                <div><?php echo $values['title'] ?></div>
                                <div><?php echo $values['active_at'] ?></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </session>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>

    <script>
        $(function(){
            $.each($('.sub_category_container .swiper-container'), function(index){
                var swiper = new Swiper($(this), {
                    slidesPerView: 2.2,
                    spaceBetween: 15,
                    freeMode: true,
                    autoHeight: true
                });
                setTimeout(function(){
                    swiper.update();
                }, 50);
            });
        });
    </script>
</body>
</html>