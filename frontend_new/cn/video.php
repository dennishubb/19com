<?php

	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id=0&sub_category_id=0&type=video';
    $data = get_curl($access_url);
    $data = json_decode($data, true);
?>

<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
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

<?php
    $access_url = CURL_API_URL . '/service/news.php?action=get_popular_news&media_type=2&limit=6';
    $data = get_curl($access_url);
    $data = json_decode($data, true);
?>

<div class="main_area" style="padding-top: 0px;">

    <div class="layout1920 full_video" id="full_video_area">
        <?php
            $num = 0;
            $html = '';
            foreach ($data as $key => $value) {
                if ($num == 0) {
                    $html .= '<div class="layout1920 full_video">';
                    $html .= '<div>';
                    $html .= '<video autoplay="true" muted="false" loop="true" id="myVideo">';
                    $html .= '<source src="'.$value['video_url'].'" type="video/mp4">';
                    $html .= '</video>';
                    $html .= '</div>';
                    $html .= '<div class="text_area">';
                    $html .= '<div>预览(15 秒)</div>';
                    $html .= '<div class="title">'.$value['title'].'</div>';
                    $html .= '<div class="play_btn"><a href="article.php?id='.$value['id'].'"><i class="fas fa-play-circle"></i>播放完整影片</a></div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $num++;
                }
                else {
                    break;
                }
            }
            echo $html;
        ?>
    </div>

    <div class="index_p5 layout1200" style="margin-top: -60px;">
        <div class="category_area">
            <div class="title_area "><span>19资讯推荐视频</span></div>
            <div class="category_item_area" id="featured_video_area">
                <?php 
                    $html = '';
                    $num = 0;
                    foreach ($data as $key => $value) {
                        if ($num > 0) {
                            $html .= '<a href="article.php?id='.$value['id'].'" class="category_item">';
                            $html .= '<img src="'.$value['thumbnail_small2'].'">';
                            $html .= '<div class="sub_text">'.$value['active_at'].'</div>';
                            $html .= '<div class="text">'.$value['title'].'</div>';
                            $html .= '</a>';
                        }
                        $num++;
                    }
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="index_p5 hot_video layout1200">
        <div class="category_area">
            <div class="title_area "><span>热门影片</span></div>
            <div class="category_item_area" id="hot_video_area">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news&media_type=2&limit=4';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div class="category_item">';
                        $html .= '<img src="'.$value['thumbnail_medium3'].'">';
                        $html .= '<div class="text_area">';
                        $html .= '<div class="sub_text">'.$value['active_at'].'</div>';
                        $html .= '<div class="text">'.$value['title'].'</div>';
                        $html .= '<a href="article.php?id='.$value['id'].'" class="play_btn"><i class="fas fa-play-circle"></i>播放完整影片</a>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="index_p5 layout1200" id="dynamic_video_category">
        <?php
            $access_url_c = CURL_API_URL . '/service/site.php?action=get_category';
            $data_c = get_curl($access_url_c);
            $data_c = json_decode($data_c, true);

            $html = '';

            foreach ($data_c as $c_key => $c_value) {
                $access_url_news = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$c_value['id'].'&media_type=2&limit=5';
                $data_news = get_curl($access_url_news);
                $data_news = json_decode($data_news, true);

                if (count($data_news) > 0) {
                    $html .= '<div class="index_p5 layout1200"><div class="category_area"><div class="title_area "><span>'.$c_value['display'].'</span></div><div class="category_item_area">';
                    foreach ($data_news as $key_news => $value_news) {
                        $html .= '<a class="category_item" href="article.php?id='.$value_news['id'].'">';
                        $html .= '<img src="'.$value_news['thumbnail_small2'].'">';
                        $html .= '<div class="text">'.$value_news['title'].'</div>';
                        $html .= '<div class="sub_text">'.$value_news['active_at'].'</div>';
                        $html .= '</a>';
                    }
                    $html .= '</div></div></div>';
                }
            }
            echo $html;
        ?>
    </div>


</div>

<?php include 'layout/footer.php'; ?>

</body>
<footer>


</footer>

<script>
    $(document).ready(function () {

        // header_live_carousel();
        // menu_left(); //update id='menu_left'
        // menu_right();//update id='username', id='profile'

        // full_video();
        // featured_video();
        // hot_videos();
        // dynamic_video_category();
    });

</script>
</html>

