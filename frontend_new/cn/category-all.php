<?php

	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id=0&sub_category_id=0&type=zonghe';
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

<div class="main_area" style="margin-top: 20px;">

    <div class="index_p2 layout1200">
        <div class="featured_news_area" id="featured_news_banner">
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news&category_id=9999&limit=1';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

                $html .= '<div class="main_banner" style="width: 680px;height: 383px;">';
                $html .= '<div class="floating_bottom" style="z-index: 1;">';
                $html .= '<div class="tab">';
                foreach ($data[0]['tags'] as $tags) {
                    $html .= '<div><a href="tags.php?tags='.$tags.'">'.$tags.'</a></div>';
                }
                $html .= '</div>';
                $html .= '<a href="article.php?id='.$data[0]['id'].'"><div class="title">'.$data[0]['title'].'</div></a>';
                $html .= '<a href="article.php?id='.$data[0]['id'].'"><div class="text">'.$data[0]['description'].'</div></a>';
                $html .= '<div class="category"><a href="sub-category.php?id='.$data[0]['category_id'].'"><div>'.$data[0]['category'].'</div></a></div>';
                $html .= '</div>';
                $html .= '<a href="article.php?id='.$data[0]['id'].'" style="z-index: -1;"><img src="'.$data[0]['thumbnail_big'].'" alt="'.$data[0]['title'].'"></a>';
                $html .= '</div>';

                echo $html;
            ?>
        </div>
        <div class="latest_news_area" id="latest_news">
            <div class="title_area "><span>最新消息</span></div>
            <?php
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id=9999&limit=3';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

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

    <div class="index_p5 layout1200" id="dynamic_category">
        <?php
            // $access_url_c = CURL_API_URL . '/service/site.php?action=get_category&category_id=' . $id; 
            $data_c = '[{"id":"5","display":"乒乓球"},{"id":"6","display":"羽毛球"},{"id":"7","display":"游泳"},{"id":"8","display":"其他"}]';//get_curl($access_url_c);
            $data_c = json_decode($data_c, true);
            $html = '';

            foreach ($data_c as $c_key => $c_value) {
                $access_url_news = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$c_value['id'].'&limit=5';
                $data_news = get_curl($access_url_news);
                $data_news = json_decode($data_news, true);//var_dump($data_news);

                if (count($data_news) > 0) {
                    $html .= '<div class="index_p5 layout1200" style="padding-top: 0px;padding-bottom: 0px;margin-top:0px;margin-bottom: 20px;">';
                    $html .= '<div class="category_area">';
                    $html .= '<div class="title_area "><span>'.$c_value['display'].'</span></div>';
                    $html .= '<div class="category_item_area">';

                    foreach ($data_news as $news_key => $news_value) {
                        $html .= '<a class="category_item" href="article.php?id='.$news_value['id'].'">';
                        $html .= '<img src="'.$news_value['thumbnail_small2'].'" alt="'.$news_value['title'].'">';
                        $html .= '<div class="text">'.$news_value['title'].'</div><div class="sub_text">'.$news_value['active_at'].'&nbsp;'.$news_value['sub_category'].'</div>';
                        $html .= '</a>';
                    }

                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            }
            echo $html;
        ?>
    </div>

    <div class="see_more layout1200">
        <div><a href="/cn/category-all-inner.php?id=5">看更多</a></div>
    </div>

    <div class="index_p6 layout1200">
        <div class="more_news_list_area" id="more_news">
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id=9999&limit=30';

                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

                $total = count($data);

                $num = 0;

                foreach ($data as $key => $value) {
                    if ($num % 10 == 0) {
                        $html .= '<div class="list">';
                    }
                    $html .= '<a class="list_item" href="article.php?id='.$value['id'].'"><div>'.$value['title'].'</div><div>'.date('Y-m-d', strtotime($value['active_at'])).'</div></a>';
                    if ($num % 10 == 9 || $num == ($total - 1)) {
                        $html .= '</div>';
                    }
                    $num++;
                }

                if (count($data) <= 20) {
                    $html .= '<div class="list"></div>';
                }
                echo $html;
            ?>
        </div>
    </div>

</div>
<?php include 'layout/footer.php'; ?>

<!--GENERAL-->
<script type="text/javascript">

    // function redirect_to_inner() {
    //     window.location.href = "category-all-inner.php";
    // }

    // $exclude_list = [1, 2, 3, 4];

    $(document).ready(function () {

        // header_live_carousel();
        // menu_left(); //update id='menu_left'
        // menu_right();//update id='username', id='profile'

        // featured_news_banner(0, 0, 1);
        // latest_news(0, 0, 3);

        // dynamic_category();

        // more_news();

    });

</script>
</body>
</html>
