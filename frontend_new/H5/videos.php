<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 视频</title>
<?php
    include("style_script.php");
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">
        <?php
        $featureData = httpGet(CURL_API_URL . '/service/news.php?action=get_featured_video&limit=5');
        $featureData = json_decode($featureData, true);
        ?>
        <div class="body_container">
            <session>
                <div class="session_video_block">
                    <div class="main_banner video">
                        <a href="#"><img src="<?php echo IMAGE_URL . $featureData[0]['thumbnail_big_h5'] ?>"></a>
                        <div class="floating_bottom">
                            <div class="desc">预览(15 秒)</div>
                            <div class="title"><a href="/article.php?id=<?php echo $featureData[0]['id'] ?>"><?php echo $featureData[0]['title'] ?></a></div>
                            <div class="category"><a href="/article.php?id=<?php echo $featureData[0]['id'] ?>">播放完整影片</a></div>
                        </div>
                    </div>
                </div>
            </session>

            <session>
                <div class="session_block">
                    <div class="session_block_title">19资讯推荐视频</div>
                    
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">

                                <?php foreach ($featureData as $i=>$values){
                                    if($i > 0){?>
                                        <div class="swiper-slide">
                                            <div class="img"><a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                            <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                                            <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                        </div>
                                    <?php } }?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $hotNews = httpGet(CURL_API_URL . '/service/news.php?action=get_hot_news&limit=4&media_type=2');
            $hotNews = json_decode($hotNews, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">热门影片</div>
                    <?php foreach ($hotNews as $values){ ?>
                    <div class="video_session">
                        <div class="main_banner video">
                            <a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_big_h5'] ?>"></a>
                            <div class="floating_bottom">
                                <div class="desc"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                                <div class="category"><a href="/article.php?id=<?php echo $values['id'] ?>">立即观看</a></div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </session>

            <?php
            $popularNewsBasket = httpGet(CURL_API_URL . '/service/news.php?action=get_popular_news&limit=5&media_type=2&category_id=2');
            $popularNewsBasket = json_decode($popularNewsBasket, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">篮球</div>
                    
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($popularNewsBasket as $values){ ?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                    <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $popularNewsBasket = httpGet(CURL_API_URL . '/service/news.php?action=get_popular_news&limit=5&media_type=2&category_id=1');
            $popularNewsBasket = json_decode($popularNewsBasket, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">足球</div>
                    
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($popularNewsBasket as $values){ ?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                    <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $popularNewsBasket = httpGet(CURL_API_URL . '/service/news.php?action=get_popular_news&limit=5&media_type=2&category_id=4');
            $popularNewsBasket = json_decode($popularNewsBasket, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">电竞</div>
                    
                    <div class="sub_category_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($popularNewsBasket as $values){ ?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                    <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
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
        })
    </script>
</body>
</html>