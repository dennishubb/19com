<?php

$_SERVER['HTTP_HTTP_GEO_IPCOUNTRY'] = trim($_SERVER['HTTP_HTTP_GEO_IPCOUNTRY']);    
if($_SERVER['HTTP_HTTP_GEO_IPCOUNTRY'] == 'GB') 
{   
 header('Location: http://19bet.co.uk/');   
 exit;  
}else if($_SERVER['HTTP_HTTP_GEO_IPCOUNTRY'] == 'ID')   
{   
 header('Location: https://19idn.com/');    
 exit;  
}else if($_SERVER['HTTP_HTTP_GEO_IPCOUNTRY'] == 'TH')   
{   
 header('Location: https://19thai.com/');   
 exit;  
}   
$allow_ips = ["27.122.13.199"   
,"27.122.13.200"    
,"27.122.13.40" 
,"27.122.13.63" 
,"27.122.13.204"    
,"27.122.13.206 "   
,"43.226.16.201"    
,"103.119.142.248"  
,"60.251.180.205"   
,"111.251.117.148"  
,"118.169.140.195"  
,"123.194.161.113"  
,"111.90.158.20"    
,"117.53.155.110"   
,"10.11.33.26"  
,"10.11.33.70"  
,"111.90.140.138"   
,"111.90.159.143"   
,"210.75.240.11"    
,"206.123.144.140"  
,"103.18.245.66"    
,"103.18.245.111"   
,"216.83.56.224"    
,"172.94.59.82" 
,"36.230.165.152"   
,"120.232.150.98"]; 
if(!empty($_SERVER['HTTP_X_FORWARDED_IP'])) 
{   
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_IP'];  
}   
if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))    
{   
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR']; 
}   
$ip=$_SERVER["REMOTE_ADDR"];    

#if (!in_array($ip, $allow_ips)){   
# header('Location: /cn/maintenance.html'); 
# exit; 
# }

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id=0&sub_category_id=0&type=main';
$data = get_curl($access_url);
$data = json_decode($data, true);

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">

    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <!-- <meta content="yes" name="apple-mobile-web-app-capable"> -->
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <?php include_once('layout/resource.php'); ?>
    <title><?php echo $data['title'] ?></title>
    <meta name="description" content="<?php echo $data['description'] ?>">
    <meta name="keywords" content="<?php echo $data['keywords'] ?>">
    <link rel="canonical" href="<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?>">
</head>
<body>
<?php include 'layout/header.php'; ?>
<span id='isindex' style='display:none'>1</span>
<div class="main_area">
    <div class="index_p1 layout1200">
        <div class="fans_area" id='fans_area'>
            <div class="title_area "><span><a href="promotion.php">19宠粉专区</a></span></div>
            <div class=""></div>
            <?php 
                $access_url = CURL_API_URL . '/service/site.php?action=get_fans_zone';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

                foreach ($data as $key => $value) {
                    $html .= '<div class="image"><a href="' . $value['url'] . '" target="_blank"><img src="' . $value['image'] . '" style="width: 380px;height:160px"></a></div><div class=""></div>';
                }

                echo $html;
            ?>
        </div>

        <div class="match_prediction_area" id='match_prediction_carousel'>
            <div class="title_area ">
                <span><a href='/cn/match-prediction/'>19赛事预测</a></span> 
            </div>

            <div class="match_prediction_swiper-button">
                 <div class="match_prediction-swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="match_prediction-swiper-button-next"><i class="fas fa-chevron-right"></i></div>
               
            </div>
            <div class="match_prediction-swiper-container">
                <div class="swiper-wrapper">
                    <?php 
                        $access_url = CURL_API_URL . '/service/prediction.php?action=match_prediction_carousel';
                        $data = get_curl($access_url);
                        $data = json_decode($data, true);
                        $html = '';
                        $total = count($data);

                        if ($total >= 6) {
                            $num = 0;
                            foreach ($data as $key => $value) {
                                if ($num % 2 == 0) {
                                    $html .= '<div class="swiper-slide exist_checker">';
                                }
                                
                                $html .= '<div class="match_prediction" onclick="location.href = \'/cn/match-prediction/index.php?event_id='.$value['id'].'\' " style=\'cursor:pointer\'>';
                                $html .= '<div class="title">'.$value['league_name'].'</div>';
                                $html .= '<div class="date">'.$value['match_at'].'</div>';
                                $html .= '<div class="team">';
                                $html .= '<div class="team_logo" style="width: 190px;"">';
                                $html .= '<img src="'.$value['home_team_image'].'" style="height: 82px; width: 82px;" />';
                                $html .= '<span style="white-space: nowrap;">'.$value['home_team_name'].'</span>';
                                $html .= '</div>';
                                $html .= '<div>VS</div>';
                                $html .= '<div class="team_logo" style="width: 190px;">';
                                $html .= '<img src="'.$value['away_team_image'].'" style="height: 82px; width: 82px;"/>';
                                $html .= '<span style="white-space: nowrap;"><br>'.$value['away_team_name'].'</span>';
                                $html .= '</div>';
                                $html .= '</div>';
                                $html .= '</div>';

                                if ($num % 2 == 1 || ($num == $total - 1)) {
                                    $html .= '</div>';
                                }

                                $num++;
                            }
                        }
                        else {
                            foreach ($data as $key => $value) {
                                $html .= '<div class="swiper-slide exist_checker less6">';
                                $html .= '<div class="match_prediction" onclick="location.href = \'/cn/match-prediction/index.php?event_id='.$value['id'].'\'" style="cursor:pointer">';
                                $html .= '<div class="title">'.$value['league_name'].'</div>';
                                $html .= '<div class="date">'.$value['match_at'].'</div>';
                                $html .= '<div class="team">';
                                $html .= '<div class="team_logo" style="width: 190px;">';
                                $html .= '<img src="'.$value['home_team_image'].'" style="height: 82px; width: 82px;" />';
                                $html .= '<span style="white-space: nowrap;">'.$value['home_team_name'].'</span>';
                                $html .= '</div>';
                                $html .= '<div>VS</div>';
                                $html .= '<div class="team_logo" style="width: 190px;">';
                                $html .= '<img src="'.$value['away_team_image'].'" style="height: 82px; width: 82px;"/>';
                                $html .= '<span style="white-space: nowrap;"><br>'.$value['away_team_name'].'</span>';
                                $html .= '</div>';
                                $html .= '</div>';
                                $html .= '</div>';
                                $html .= '</div>';
                            }
                        }
                        echo $html;
                    ?>
                </div>
                <!-- Add Arrows -->
                <script>
                    var swiper = new Swiper('.swiper-container', {
                      slidesPerView:'2.1',
                       
                      spaceBetween: 0,
                       navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                      },
                     
                    });
                     var swiper = new Swiper('.match_prediction-swiper-container', {

                      navigation: {
                        nextEl: '.match_prediction-swiper-button-next',
                        prevEl: '.match_prediction-swiper-button-prev',
                      },
                    });
                </script>
            </div>
        </div>
        
        <div class="rank" id="shooter-ranking-table">
            <div class="rank" id="shooter-ranking-table"><div class="main_tab" id="maintab">
    <div class="active" id="epl" onclick="change_tabs(this)">英超</div>
    <div class="" id="nba" onclick="change_tabs(this)">NBA</div>
</div>
<div class="sub_tab" id="subtab">
    <div class="active" id="ranking" onclick="change_tabs(this)">积分榜</div>
    <div id="shooter" onclick="change_tabs(this)">射手榜</div>
</div>
<div class="rank_list">
    <?php 
        $access_url = CURL_API_URL . '/service/match.php?action=get_league_ranking';
        $data = get_curl($access_url);
        $data = json_decode($data, true);

        $nba_team_html = '';
        $nba_shooter_html = '';
        $epl_team_html = '';
        $epl_shooter_html = '';

        if (isset($data['nba_team_ranking'])) {
            $num = 1;
            foreach ($data['nba_team_ranking'] as $key => $value) {
                $nba_team_html .= "<tr><td><span>{$num}</span></td><td>{$value['team_name']}</td><td>{$value['won']}/{$value['lost']}</td></tr>";
                $num++;
            }
            if ($num < 10) {
                for ($i=0; $i <= (10-$num); $i++) { 
                    $nba_team_html .= "<tr><td></td><td></td><td></td></tr>";
                }
            }
        }

        if (isset($data['nba_shooter_ranking'])) {
            $num = 1;
            foreach ($data['nba_shooter_ranking'] as $key => $value) {
                $nba_shooter_html .= "<tr><td><span>{$num}</span></td><td>{$value['player_name']}</td><td>{$value['team_name']}</td><td>{$value['points_avg']}</td></tr>";
                $num++;
            }
            if ($num < 10) {
                for ($i=0; $i <= (10-$num); $i++) { 
                    $nba_shooter_html .= "<tr><td></td><td></td><td></td><td></td></tr>";
                }
            }
        }

        if (isset($data['epl_team_ranking'])) {
            $num = 1;
            foreach ($data['epl_team_ranking'] as $key => $value) {
                $epl_team_html .= "<tr><td><span>{$num}</span></td><td>{$value['team_name']}</td><td>{$value['won']}/{$value['draw']}/{$value['lost']}</td><td>{$value['points']}</td></tr>";
                $num++;
            }
            if ($num < 10) {
                for ($i=0; $i <= (10-$num); $i++) { 
                    $epl_team_html .= "<tr><td></td><td></td><td></td><td></td></tr>";
                }
            }
        }

        if (isset($data['epl_shooter_ranking'])) {
            $num = 1;
            foreach ($data['epl_shooter_ranking'] as $key => $value) {
                $epl_shooter_html .= "<tr><td><span>{$num}</span></td><td>{$value['player_name']}</td><td>{$value['team_name']}</td><td>{$value['goals']}({$value['penalty']})</td></tr>";
                $num++;
            }
            if ($num < 10) {
                for ($i=0; $i <= (10-$num); $i++) { 
                    $epl_shooter_html .= "<tr><td></td><td></td><td></td><td></td></tr>";
                }
            }
        }
    ?>
    <table id="epl_ranking">
        <tbody>
            <tr>
                <th>排名</th>
                <th>球队</th>
                <th>胜平负</th>
                <th>积分</th>
            </tr>
            <?php echo $epl_team_html; ?>
        </tbody>
    </table>
    <table id="epl_shooter" style="display: none">
        <tbody>
            <tr>
                <th>排序</th>
                <th>球员</th>
                <th>球队</th>
                <th>进球(点球)</th>
            </tr>
            <?php echo $epl_shooter_html; ?>
        </tbody>
    </table>
    <table id="nba_ranking" style="display: none">
        <tbody>
            <tr>
                <th>排名</th>
                <th>球队</th>
                <th>胜负</th>
            </tr>
            <?php echo $nba_team_html; ?>
        </tbody>
    </table>
    <table id="nba_shooter" style="display: none">
        <tbody>
            <tr>
                <th>排序</th>
                <th>球员</th>
                <th>球队</th>
                <th>场均得分</th>
            </tr>
            <?php echo $nba_shooter_html; ?>
        </tbody>
    </table>
    </div>
    </div>
        </div>
    </div>

    <div class="index_p2 layout1200">
        <div class="featured_news_area">
            <div class="title_area "><span>19资讯精选新闻</span></div>
            <div id="featured_news_banner">
                <?php
                    $num = 0;
                    $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $total = count($data);
                    $html = '';
                    // $exp = "/(.*?)(\.mp4)/";

                    foreach ($data as $key => $value) {
                        // preg_match_all($exp, $value['thumbnail'], $matches);
                        if ($num == 0) {
                            $html .= '<div class="main_banner" style="width: 680px;height: 383px;">';
                            $html .= '<div class="floating_bottom" style="z-index: 1;">';
                            $html .= '<div class="tab">';
                            foreach ($value['tags'] as $tags) {
                                $html .= '<div><a href="tags.php?tags='.$tags.'">'.$tags.'</a></div>';
                            }
                            $html .= '</div>';
                            $html .= '<a href="article.php?id='.$value['id'].'">';
                            $html .= '<div class="title">'.$value['title'].'</div>';
                            $html .= '</a>';
                            $html .= '<a href="article.php?id='.$value['id'].'">';
                            $html .= '<div class="text">'.$value['description'].'</div>';
                            $html .= '</a>';
                            if ($value['sub_category_id'] > 0) {
                                $html .= '<div class="category"><a href="sub-category.php?id='.$value['sub_category_id'].'"><div>'.$value['sub_category'].'</div></a></div>';
                            }
                            else {
                                $html .= '<div class="category"><a href="category.php?id='.$value['category_id'].'">'.$value['category'].'</a></div>';
                            }
                            $html .= '</div>';
                            $html .= '<a href="article.php?id='.$value['id'].'" style="z-index: -1;">';
                            // if (count($matches[0]) > 0) {
                            //     $html .= '<video style="width: 680px !important;height: 383px !important;"><source src="'.$value['thumbnail'].'" type="video/mp4"></video>';
                            // }
                            // else {
                                $html .= '<img src="'.$value['thumbnail_big'].'" alt="'.$value['title'].'">';
                            // }
                            $html .= '</a>';
                            $html .= '</div>';
                        }
                        else {
                            if ($num == 1) {
                                $html .= '<div class="sub_banner_area">';
                            }

                            $html .= '<div>';
                            $html .= '<div class="image" data-extra="true"><a href="article.php?id='.$value['id'].'">';
                            // if (count($matches[0]) > 0) {
                            //     $html .= '<video style="width: 200px !important;height: 133px !important;"><source src="'.$value['thumbnail'].'" type="video/mp4"></video>';
                            // }
                            // else {
                                $html .= '<img src="'.$value['thumbnail_medium'].'" alt="'.$value['title'].'"></a>';
                            // }
                            $html .= '<div class="tab">';

                            foreach ($value['tags'] as $tags) {
                                $html .= '<div><a href="tags.php?tags='.$tags.'">'.$tags.'</a></div>';
                            }

                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '<a href="article.php?id='.$value['id'].'"><div class="text">'.$value['title'].'</div></a>';
                            $html .= '<div class="category">';
                            if ($value['sub_category_id'] > 0) {
                                $html .= '<a href="sub-category.php?id='.$value['sub_category_id'].'"><div>'.$value['sub_category'].'</div></a>';
                            }
                            else {
                                $html .= '<a href="category.php?id='.$value['category_id'].'"><div>'.$value['category'].'</div></a>';
                            }
                            $html .= '</div>';
                            $html .= '</div>';

                            if ($num == ($total - 1)) {
                                $html .= '</div>';
                            }
                        }
                        $num++;
                    }
                    echo $html;
                ?>
            </div>
        </div>
        <div class="latest_news_area" id="latest_news">
            <div class="title_area "><span>最新消息</span></div>
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';
                // $exp = "/(.*?)(\.mp4)/";

                foreach ($data as $key => $value) {
                    // preg_match_all($exp, $value['thumbnail'], $matches);

                    $html .= '<a class="news_item" href="article.php?id='.$value['id'].'">';
                    $html .= '<div class="image">';
                    // if (count($matches[0]) > 0) {
                    //     $html .= '<video style="width: 120px; height: 80px;"><source src="'.$value['thumbnail'].'" type="video/mp4"></video>';
                    // }
                    // else {
                        $html .= '<img src="'.$value['thumbnail_small'].'" alt="'.$value['title'].'">';
                    // }
                    $html .= '</div>';
                    $html .= '<div class="text">';
                    $html .= '<div>'.$value['title'].'</div>';
                    $html .= '<div>'.$value['active_at'].'&nbsp;'.$value['category'].'</div></div></a>';
                }
                echo $html;
            ?>
        </div>
    </div>

    <div class="index_p3_outside">
        <div class="index_p3 layout1200">
            <div class="index_p3 layout1200">
                <div class="video_area">
                    <div class="title_area "><span><a href='/cn/video.php'>19资讯精选视频</a></span></div>
                    <div class="video_item_area" id="featured_video_index">
                        <?php
                            $access_url = CURL_API_URL . '/service/news.php?action=get_featured_video';
                            $data = get_curl($access_url);
                            $data = json_decode($data, true);
                            $html = '';

                            foreach ($data as $key => $value) {
                                $html .= '<a href="article.php?id='.$value['id'].'" class="video_item">';
                                $html .= '<img src="'.$value['thumbnail_medium2'].'" alt="'.$value['title'].'">';
                                $html .= '<div class="text">'.$value['title'].'</div>';
                                $html .= '</a>';
                            }
                            echo $html;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="index_p4 layout1200">
        <div class="news_list_area">
            <div class="title_area">
                <span><a href='/cn/category.php?id=1'>足球新闻</a></span>
            </div>
            <div class="list" id="football_more_news">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news&category_id=1&limit=10';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div><a href="article.php?id='.$value['id'].'">'.$value['title'].'</a></div>';
                    }
                    echo $html;
                ?>
            </div>
        </div>
        <div class="news_list_area">
            <div class="title_area">
                <span><a href='/cn/category.php?id=2'>篮球新闻</a></span>
            </div>
            <div class="list" id="basketball_more_news">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news&category_id=2&limit=10';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div><a href="article.php?id='.$value['id'].'">'.$value['title'].'</a></div>';
                    }
                    echo $html;
                ?>
            </div>
        </div>
        <div class="news_list_area">
            <div class="title_area">
                <span><a href='/cn/category.php?id=4'>电竞新闻</a></span>
            </div>
            <div class="list" id="egaming_more_news">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news&category_id=4&limit=10';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div><a href="article.php?id='.$value['id'].'">'.$value['title'].'</a></div>';
                    }
                    echo $html;
                ?>
            </div>
        </div>
        <div class="news_list_area">
            <div class="title_area">
                <span><a href='/cn/category-all.php'>其他新闻</a></span>
            </div>
            <div class="list" id="other_more_news">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_latest_news&category_id=9999&limit=10';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div><a href="article.php?id='.$value['id'].'">'.$value['title'].'</a></div>';
                    }
                    echo $html;
                ?>
            </div>
        </div>
    </div>

</div>

<?php include 'layout/footer.php'; ?>

</body>
</html>

<script>
    $(document).ready(function () {
        // featured_video_index();
        
        // category_more_news('#football_more_news', 1, 0, [], 10);
        // category_more_news('#basketball_more_news', 2, 0, [], 10);
        // category_more_news('#egaming_more_news', 4, 0, [], 10);
        // category_more_news('#other_more_news', 0, 0, [1, 2, 4], 10);

    });

    function change_tabs(event) {
        if (event.parentNode.id == 'maintab' && event.id == 'epl') {
            $('#epl').addClass('active');
            $('#nba').removeClass('active');
            if ($('#ranking').hasClass('active')) {
                $('#epl_ranking').show();
                $('#epl_shooter').hide();
                $('#nba_ranking').hide();
                $('#nba_shooter').hide();
            } else {
                $('#epl_ranking').hide();
                $('#epl_shooter').show();
                $('#nba_ranking').hide();
                $('#nba_shooter').hide();
            }
        } else if (event.parentNode.id == 'maintab' && event.id == 'nba') {
            $('#epl').removeClass('active');
            $('#nba').addClass('active');
            if ($('#ranking').hasClass('active')) {
                $('#epl_ranking').hide();
                $('#epl_shooter').hide();
                $('#nba_ranking').show();
                $('#nba_shooter').hide();
            } else {
                $('#epl_ranking').hide();
                $('#epl_shooter').hide();
                $('#nba_ranking').hide();
                $('#nba_shooter').show();
            }
        } else if (event.parentNode.id == 'subtab' && event.id == 'ranking') {
            $('#ranking').addClass('active');
            $('#shooter').removeClass('active');
            if ($('#epl').hasClass('active')) {
                $('#epl_ranking').show();
                $('#epl_shooter').hide();
                $('#nba_ranking').hide();
                $('#nba_shooter').hide();
            } else {
                $('#epl_ranking').hide();
                $('#epl_shooter').hide();
                $('#nba_ranking').show();
                $('#nba_shooter').hide();
            }
        } else if (event.parentNode.id == 'subtab' && event.id == 'shooter') {
            $('#ranking').removeClass('active');
            $('#shooter').addClass('active');
            if ($('#epl').hasClass('active')) {
                $('#epl_ranking').hide();
                $('#epl_shooter').show();
                $('#nba_ranking').hide();
                $('#nba_shooter').hide();
            } else {
                $('#epl_ranking').hide();
                $('#epl_shooter').hide();
                $('#nba_ranking').hide();
                $('#nba_shooter').show();
            }
        }
    }

    var swiper = new Swiper('.swiper-container', {
        slidesPerView: '2.1',

        spaceBetween: 0,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

    });
    var swiper = new Swiper('.match_prediction-swiper-container', {
        navigation: {
            nextEl: '.match_prediction-swiper-button-next',
            prevEl: '.match_prediction-swiper-button-prev',
        },
    });
</script>