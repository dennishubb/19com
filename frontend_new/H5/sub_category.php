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
    $scid = $_GET['sub_category'];
    $cid = $_GET['category'];
    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&sub_category_id='.$scid;
    $data = get_curl($access_url);
    $data = json_decode($data, true);
    include("style_script.php");
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
            $hotnewsData = httpGet(CURL_API_URL . '/service/news.php?action=get_hot_news&limit=1&category_id='.$cid.'&sub_category_id='.$scid);
            $hotnewsData = json_decode($hotnewsData, true);
            ?>
            <session>
                <div class="session_block">
                    <div class="main_banner">
                        <a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><img src="<?php echo IMAGE_URL . $hotnewsData[0]['thumbnail_big_h5'] ?>"></a>
                        <div class="floating_bottom">
                            <div class="tagging">
                                <?php foreach ($hotnewsData[0]['tags'] as $values){ ?>
                                    <a href=""><?php echo $values ?></a>
                                <?php } ?>
                            </div>
                            <div class="title"><a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><?php echo $hotnewsData[0]['title'] ?></a></div>
                            <div class="desc"><a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><?php echo $hotnewsData[0]['description'] ?></a></div>
                            <div class="category"><a href="/article.php?id=<?php echo $hotnewsData[0]['id'];?>"><?php echo $hotnewsData[0]['sub_category'] ?></a></div>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $data = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&limit=5&category_id='.$cid.'&sub_category_id='.$scid.'&type=latest_news');
            $data = json_decode($data, true);
            ?>
            <?php if(count($data) > 0){?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        即时新闻
                       <!-- <a href="#" class="more">更多 <img src="img/arrow.png"></a> -->
                    </div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list">
                        <?php foreach ($data as $i => $values){ ?>
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
            <?php } ?>
            <?php
            $match_analytics = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&limit=5&category_id='.$cid.'&sub_category_id='.$scid.'&type=match_analytics');
            $match_analytics = json_decode($match_analytics, true);
            ?>
            <?php if(count($match_analytics) > 0){?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        赛事分析
                        <!--<a href="#" class="more">更多 <img src="img/arrow.png"></a>-->
                    </div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list">
                        <?php foreach ($match_analytics as $i => $values){ ?>
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
            <?php } ?>
            <?php
            $team_intro = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&limit=5&category_id='.$cid.'&sub_category_id='.$scid.'&type=team_intro');
            $team_intro = json_decode($team_intro, true);
            ?>
            <?php if(count($team_intro) > 0){?>
            <session>
                <div class="session_block">
                    <div class="session_block_title">
                        球队介绍
                        <!-- <a href="#" class="more">更多 <img src="img/arrow.png"></a> -->
                    </div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list">
                        <?php foreach ($team_intro as $i => $values){ ?>
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
            <?php } ?>
            <?php
            $latestNewsOthers = httpGet(CURL_API_URL . '/service/news.php?action=get_latest_news&category_id=0&limit=10');
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
</body>
</html>