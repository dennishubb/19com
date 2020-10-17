<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$article = array();

if (!intval($_GET['id'])) {
    exit(0);
}

$article_id = intval($_GET['id']);

$access_url = CURL_API_URL . '/service/news.php?action=get_article&id='.$article_id;
$article = get_curl($access_url);
$article = json_decode($article, true);
$chatroom_id = $article['chatroom_id'];

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

    <title><?php echo $article['title'] ?></title>
    <meta name="description" content="<?php echo $article['description'] ?>">
    <meta name="keywords" content="<?php echo $article['keywords'] ?>">
    <link rel="canonical" href="<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>">
    <?php
        if ($article['media_type'] == 2) {
    ?>
    <link href="/assets/css/video-js.css" rel="stylesheet">
    <script src="/assets/js/video.js"></script>
    <?php
        }
    ?>
</head>

<body>
<?php include 'layout/header.php'; ?>
<div class="main_area" style="margin-top: 20px;">
    <div class="index_p9 layout1200">
        <div class="featured_news_area">
            <div class="main_banner">
                <?php

                echo '<div>';

                echo '<div class="tab">';
                foreach ($article['tags'] as $tag) {
                    echo '<div><a href="tags.php?tags=' . $tag . '">' . $tag . '</a></div>';
                }
                echo '</div>';

                echo '<div class="title">' . $article['title'] . '</div>';

                echo '<div class="title_bottom">';
                echo '<span>' . $article['active_at'] . '</span>';
                echo '<span>来自：' . $article['author'] . '</span>';
                echo '<span>访问：' . $article['view_count'] . '</span>';
                echo '</div>';

                echo '</div>';

$content = $article['content'];
// $exp = "/<a(.*?)(\.mp4)(.*?)>(.*?)<\/a>/i";
// preg_match_all($exp, $content, $matches);

// if (count($matches[0]) > 0) {
//     for ($i=0; $i < count($matches[0]); $i++) { 
//         $content = str_replace($matches[0][$i], '<video width="640" height="360" controls><source src="'.$matches[4][$i].'" type="video/mp4">Your browser does not support the video tag.</video>', $content);
//     }
// }
                echo '<div id="div_content" class="main_text">' . $content . '</div>';

                ?>
            </div>
            <?php include('module/message.php'); ?>
        </div>
        <div class="latest_news_area" id="latest_news">
            <div class="title_area "><span>最新消息</span></div>
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$sub_category_id;

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

    <div class="index_p5 layout1200">
        <div class="category_area" id="featured_news">
            <div class="title_area">
                <span>19资讯精选新闻</span>
            </div>
            <div class="category_item_area">
    
            <?php
                $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&limit=5';
                $data = get_curl($access_url);
                $data = json_decode($data, true);
                $html = '';

                foreach ($data as $key => $value) {
                    $html .='<a class="category_item" href="article.php?id='.$value["id"].'">';
                    $html .='<img src="'.$value["thumbnail_small2"].'" alt="'.$value["title"].'" style="width: 200px; height: 115px;">';
                    $html .='<div class="text">'.$value["title"].'</div><div class="sub_text">'.$value["active_at"].'&nbsp;'.$value['category'].'</div>';
                    $html .='</a>';
                }
                echo $html;
            ?>
            </div>
        </div>
    </div>

    <div class="index_p6 layout1200">
        <div class="more_news_list_area" id="more_news">
            <div class="title_area "><span>更多新闻</span></div>
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&limit=30';

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

<script type="text/javascript">
    $(document).ready(function () {
    });
</script>
</body>
</html>