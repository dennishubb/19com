<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');

    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id=0&sub_category_id=0&type=main';
    $data = get_curl($access_url);
    $data = json_decode($data, true);
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $data['title'] ?></title>
<meta name="description" content="<?php echo $data['description'] ?>">
<meta name="keywords" content="<?php echo $data['keywords'] ?>">
<?php
include("style_script.php");
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">
        
        <div class="body_container">
            <!-- Start Landing Banner -->
            <?php
            $bannerData = httpGet(CURL_API_URL . '/service/news.php?action=get_banner_news_h5');
            $bannerData = json_decode($bannerData, true);
            ?>
            <div class="index_banner_container">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php foreach ($bannerData as $values) { ?>
                        <div class="swiper-slide">
                            <a href="/article.php?id=<?php echo $values['id'];?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_big_h5']; ?>"></a>
                            <div class="video_desc">
                                <div class="text"></div>
                                <div class="tag"><?php if($values['media_type'] == "2"){echo '视频';}else{echo '图文';}?></div>
                            </div>
                            <div class="video_title">
                                <div class="live" style="min-width:42px;"><?php if($values['media_type'] == "2"){echo '视频';}else{echo '图文';}?></div>
                                <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"><?php echo $values['title'];?></div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <!-- End Landing Banner -->

            <!-- Start Landing Competition -->
            <session>
                <div class="session_block index_competition_session">
                    <div class="session_block_title">19赛事预测</div>
                    <?php
                    $predictionData = httpGet(CURL_API_URL . '/service/prediction.php?action=match_prediction_carousel');
                    $predictionData = json_decode($predictionData, true);
                    $num = 0;
                    $total = count($predictionData);
                    ?>
                    <div class="index_competition_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                        <?php foreach ($predictionData as $values) { ?>
                                            <?php if ($num % 2 == 0) { ?>
                                            <div class="swiper-slide">
                                                <div class="index_competition_group">
                                            <?php } ?>
                                            <div onclick="location = '/prediction.php?event_id=<?php echo $values['id']?>';">
                                                <div class="title"><?php echo $values['league_name']?></div>
                                                <div class="datetime"><?php echo $values['match_at'] ?></div>
                                                <div class="team">
                                                    <div class="main">
                                                        <div class="border"><img src="<?php echo IMAGE_URL . $values['home_team_image']?>"><!-- <div>主队</div> --></div>
                                                        <?php echo $values['home_team_name'] ?>
                                                    </div>
                                                    <div class=versus><img src="img/home/vs.png"></div>
                                                    <div class="away">
                                                        <div class="border"><img src="<?php echo IMAGE_URL . $values['away_team_image']?>"></div>
                                                        <?php echo $values['away_team_name'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                <?php if ($num % 2 == 1 || ($num == $total - 1)) { ?>
                                    </div>
                                </div>
                                <?php } ?>
                                        <?php 
                                            $num++; 
                                        } ?>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </session>
            <!-- End Landing Competition -->

			 <!-- Start Landing Fan Zone -->
			<session>
                <div class="session_block index_competition_session">
                    <div class="session_block_title">19宠粉专区</div>
                    <?php
                    $fanzoneData = httpGet(CURL_API_URL . '/service/site.php?action=get_fans_zone');
                    $fanzoneData = json_decode($fanzoneData, true);
					
                    $num = 0;
                    $total = count($fanzoneData);
					
                    ?>
                    <div class="index_fanzone_container">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                        <?php foreach ($fanzoneData as $values) { ?>
                                          
                                            <div class="swiper-slide">
                                                <div class="index_competition_group">
                                            
													<img src="<?php echo IMAGE_URL . $values['image']?>" onclick="location = '<?php echo $values['url']?>';" style='height: 150px;cursor: pointer;'>
                                
												</div>
											</div>
                              
                                        <?php } ?>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </session>
			<!-- End Landing Fan Zone -->
			
            <!-- Start Landing Hot News -->
            <session>
                <?php
                $hotnewsData = httpGet(CURL_API_URL . '/service/news.php?action=get_hot_news&limit=5');
                $hotnewsData = json_decode($hotnewsData, true);
                ?>
                <div class="session_block">
                    <div class="session_block_title">热门新闻</div>
                    <?php if(count($hotnewsData) > 0){ ?>
                    <div class="main_banner">
                        <a href="/article.php?id=<?php echo $hotnewsData[0]['id']?>"><img src="<?php echo IMAGE_URL . $hotnewsData[0]['thumbnail_big_h5'] ?>"></a>
                        <div class="floating_bottom">
                            <!--<div class="tagging">
                                <?php foreach ($hotnewsData[0]['tags'] as $tags){ ?>
                                    <a href="#"><?php echo $tags ?></a>
                                <?php } ?>
                            </div>-->
                            <div class="title"><a href="article.php?id=<?php echo $hotnewsData[0]['id']?>"><?php echo $hotnewsData[0]['title'] ?></a></div>
                            <div class="desc"><a href="article.php?id=<?php echo $hotnewsData[0]['id']?>"><?php echo $hotnewsData[0]['description'] ?></a></div>
                            <!--<div class="category"><a href="article.php?id=<?php echo $hotnewsData[0]['id']?>"><?php echo $hotnewsData[0]['sub_category'] ?></a></div>-->
                            <div class="category">
                                <a href=<?php echo ($hotnewsData[0]['sub_category']) ? "sub_category.php?sub_category=".$hotnewsData[0]['sub_category_id']."&category=".$hotnewsData[0]['category_id']:"category.php?category=".$hotnewsData[0]['category_id']?>>
                                    <?php echo $hotnewsData[0]['sub_category'] ?  $hotnewsData[0]['sub_category'] : $hotnewsData[0]['category']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="index_hotnews_thumb_list">
                        <?php foreach ($hotnewsData as $i=>$values){
                        if($i > 0){
                        ?>
                        <div>
                            <div class="tagging">
                                <a href="/article.php?id=<?php echo $values['id']?>"><img src="<?php echo IMAGE_URL .  $values['thumbnail_medium_h5'] ?>"></a>
                                <!--<div class="tagging_list">
                                    <?php foreach ($values['tags'] as $tags){ ?>
                                        <a href="#"><?php echo $tags ?></a>
                                    <?php } ?>
                                </div>-->
                            </div>
                            <div class="title"><a href="/article.php?id=<?php echo $values['id']?>"><?php echo $values['title'] ?></a></div>
                            <div class="category">
                                <a href=<?php echo ($values['sub_category']) ? "sub_category.php?sub_category=".$values['sub_category_id']."&category=".$values['category_id']:"category.php?category=".$values['category_id']?>>
                                    <?php echo $values['sub_category'] ?  $values['sub_category'] : $values['category']; ?>
                                </a>
                            </div>
                        </div>
                        <?php } } ?>
                    </div>
                    <?php } ?>
                </div>
            </session>
            <!-- Start Landing Hot News -->
			 
            <!-- Start Landing Latest News -->
            <?php
            $latestData = httpGet(CURL_API_URL . '/service/news.php?action=get_latest_news');
            $latestData = json_decode($latestData, true);
            ?>
            <session>
                <div class="session_block index_competition_session">
                    <div class="session_block_title">最新消息</div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list">
                        <?php foreach ($latestData as $value){ ?>
                            <a href="/article.php?id=<?php echo $value['id']?>">
                                <img src="<?php echo IMAGE_URL . $value['thumbnail_small_h5']; ?>">
                                <div>
                                    <div class="title"><?php echo $value['title'] ?></div>
                                    <div class="datetime"><?php echo $value['active_at'] ."  "; if($value['sub_category'] != null){echo $value['sub_category'];}?></div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </session>
            <!-- End Landing Latest News -->

            <!-- Start Landing Videos -->
            <?php
            $featureData = httpGet(CURL_API_URL . '/service/news.php?action=get_featured_video&limit=5');
            $featureData = json_decode($featureData, true);
            ?>
            <session>
                <div class="session_block index_competition_session">
                    <div class="session_block_title">精选视频</div>
                    <div class="main_banner">
                        <a href="/article.php?id=<?php echo $featureData[0]['id']?>"><img src="<?php echo IMAGE_URL . $featureData[0]['thumbnail_big_h5'] ?>"></a>
                        <div class="floating_bottom">
                            <div class="tagging">
                            </div>
                            <div class="title"><a href="/article.php?id=<?php echo $featureData[0]['id']?>"><?php echo $featureData[0]['title'] ?></a></div>
                            <div class="category"><a href="#"><?php echo $featureData[0]['sub_category'] ?></a></div>
                        </div>
                    </div>
                    <div class="index_hotnews_thumb_list">
                        <?php foreach ($featureData as $i=>$values){
                            if($i > 0){?>
                        <div>
                            <div class="tagging">
                                <a href="/article.php?id=<?php echo $values['id']?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a>
                            </div>
                            <div class="title"><a href="/article.php?id=<?php echo $values['id']?>"><?php echo $values['title'] ?></a></div>
                        </div>
                        <?php } }?>
                    </div>
                </div>
            </session>
            <!-- End Landing Landing Videos -->
            <?php
            $categoryData = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&category_id=1&sub_category_id=0');
            $categoryData = json_decode($categoryData, true);
            ?>
            <session>
                <div class="session_block_inner">
                    <div class="session_block index_football_session">
                        <div class="session_block_title">足球</div>

                        <div class="index_football_content">
                            <?php foreach ($categoryData as $values){ ?>
                                <p><a href="/article.php?id=<?php echo $values['id']?>"><?php echo $values['title'] ?></a></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $categoryData = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&category_id=2&sub_category_id=0');
            $categoryData = json_decode($categoryData, true);
            ?>
            <session>
                <div class="session_block_inner">
                    <div class="session_block index_football_session">
                        <div class="session_block_title">篮球</div>

                        <div class="index_football_content">
                            <?php foreach ($categoryData as $values){ ?>
                                <p><a href="/article.php?id=<?php echo $values['id']?>"><?php echo $values['title'] ?></a></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </session>

            <?php
            $categoryData = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&category_id=4&sub_category_id=0');
            $categoryData = json_decode($categoryData, true);
            ?>
            <session>
                <div class="session_block_inner">
                    <div class="session_block index_football_session">
                        <div class="session_block_title">电竞</div>

                        <div class="index_football_content">
                            <?php foreach ($categoryData as $values){ ?>
                                <p><a href="/article.php?id=<?php echo $values['id']?>"><?php echo $values['title'] ?></a></p>
                            <?php } ?>
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
            var indexBannerSwiper = new Swiper('.index_banner_container .swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                pagination: {
                    el: '.index_banner_container .swiper-pagination',
                },
                });
                
            var indexVideosSwiper = new Swiper('.index_videos_container .swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                pagination: {
                    el: '.index_videos_container .swiper-pagination',
                },
            });

            var indexCompetitionSwiper = new Swiper('.index_competition_container .swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                pagination: {
                    el: '.index_competition_container .swiper-pagination',
                },
            }); 
			
			var indexFanzoneSwiper = new Swiper('.index_fanzone_container .swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                pagination: {
                    el: '.index_fanzone_container .swiper-pagination',
                },
            });
        })
    </script>
</body>
</html>