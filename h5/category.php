<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php
    include("style_script.php");

    $id = $_GET['category'];

    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id='.$id.'&sub_category_id=0';
    $data = get_curl($access_url);
    $data = json_decode($data, true);
?>
    <title><?php echo $data['title'] ?></title>
    <meta name="description" content="<?php echo $data['description'] ?>">
    <meta name="keywords" content="<?php echo $data['keywords'] ?>">
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container">
            <?php
            $hotnewsData = httpGet(CURL_API_URL . '/service/news.php?action=get_hot_news&limit=5&category_id='.$id.'');
            $hotnewsData = json_decode($hotnewsData, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="main_banner">
                        <a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><img src="<?php echo IMAGE_URL . $hotnewsData[0]['thumbnail_big_h5'] ?>"></a>
                        <div class="floating_bottom">
                            <div class="tagging">
                                <?php foreach ($hotnewsData[0]['tags'] as $values){ ?>
                                <a href="#"><?php echo $values ?></a>
                                <?php } ?>
                            </div>
                            <div class="title"><a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><?php echo $hotnewsData[0]['title'] ?></a></div>
                            <div class="desc"><a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><?php echo $hotnewsData[0]['description'] ?></a></div>
                            <div class="category"><a href="#"><?php echo $hotnewsData[0]['sub_category'] ?></a></div>
                        </div>
                    </div>

                    <div class="sub_category_container mt-10">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($hotnewsData as $i => $values){
                                    if($i > 0){?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="/article.php?id=<?php echo $values['id'];?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                    <div class="title"><a href="/article.php?id=<?php echo $values['id'];?>"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                                </div>
                                <?php } } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $latestNews = httpGet(CURL_API_URL . '/service/news.php?action=get_latest_news&category_id='.$id.'&limit=5');
            $latestNews = json_decode($latestNews, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        最新消息
                        <a href="#" class="more" style="display: none;">更多 <img src="img/arrow.png"></a>
                    </div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list">
                        <?php foreach ($latestNews as $values){ ?>
                        <a href="/article.php?id=<?php echo $values['id'];?>">
                            <img src="<?php echo IMAGE_URL . $values['thumbnail_small_h5'] ?>">
                            <div>
                                <div class="title"><?php echo $values['title'] ?></div>
                                <div class="datetime"><?php echo $values['active_at'] . ' '. $values['sub_category'] ?></div>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </session>
            <?php
            $sub_category = httpGet(CURL_API_URL . '/service/site.php?action=get_category&category_id='.$id);
            $sub_category = json_decode($sub_category, true);
            foreach ($sub_category as $sub_values){
                $sub_news = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$id.'&sub_category_id='.$sub_values['id']);
                $sub_news = json_decode($sub_news, true);
                ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        <?php echo $sub_values['display'] ?>
                        <a href="/sub_category.php?sub_category=<?php echo $sub_values['id'] ?>&category=<?php echo $id ?>" class="more">更多 <img src="img/arrow.png"></a>
                    </div>
                    <div class="break_line"></div>

                    <div class="sub_category_container mt-10">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ($sub_news as $values){ ?>
                                <div class="swiper-slide">
                                    <div class="img"><a href="/article.php?id=<?php echo $values['id'];?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a></div>
                                    <div class="title"><a href="/article.php?id=<?php echo $values['id'];?>"><?php echo $values['title'] ?></a></div>
                                    <div class="datetime"><?php echo $values['active_at'].' '. $values['sub_category'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </session>
            <?php } ?>

            <?php
            $latestNewsOthers = httpGet(CURL_API_URL . '/service/news.php?action=get_latest_news&category_id='.$id.'&limit=10');
            $latestNewsOthers = json_decode($latestNewsOthers, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        更多新闻
                        <a href="#" class="more" style="display: none;">更多 <img src="img/arrow.png"></a>
                    </div>
                    <div class="break_line"></div>

                    <div class="news_list">
                        <?php foreach ($latestNewsOthers as $values){ ?>
                        <a href="/article.php?id=<?php echo $values['id'];?>">
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
        })
    </script>
</body>
</html>