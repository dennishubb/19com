<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$id = intval($_GET['id']);

if ($id) {
    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&sub_category_id='.$id;
    $data = get_curl($access_url);
    $data = json_decode($data, true);
}
else {
    exit(0);
}

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
    <link rel="canonical" href="<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>">
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="main_area" style="margin-top: 20px;">
    <div class="index_p2 layout1200">
        <div class="featured_news_area" id="featured_news_banner">
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news&category_id=' . $category_id.'&sub_category_id='.$id.'&limit=1';
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
                $html .= '<div class="category"><a href="sub-category.php?id='.$data[0]['sub_category_id'].'"><div>'.$data[0]['sub_category'].'</div></a></div>';
                $html .= '</div>';
                $html .= '<a href="article.php?id='.$data[0]['id'].'" style="z-index: -1;"><img src="'.$data[0]['thumbnail_big'].'" alt="'.$data[0]['title'].'"></a>';
                $html .= '</div>';

                echo $html;
            ?>
        </div>
        <div class="latest_news_area" id="latest_news">
            <div class="title_area "><span>最新消息</span></div>
            <?php
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$id.'&limit=3';
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

    <div style="margin-top: 0px;padding-top: 0px;">
    <?php
        $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$id.'&type=latest_news&limit=5';
        $data = get_curl($access_url);
        $data = json_decode($data, true);
        $html = '';

        if (count($data) > 0) {
    ?>
        <div class="index_p5 layout1200" style="padding-top: 0px;padding-bottom: 10px;">
            <div class="category_area">
                <div class="title_area"><span>即时新闻</span></div>
                <div class="category_item_area" id="latest_news_area">
                    <?php
                        foreach ($data as $key => $value) {
                            $html .= '<a class="category_item" href="article.php?id='.$value['id'].'"><img src="'.$value['thumbnail_small2'].'" alt="'.$value['title'].'">';
                            $html .= '<div class="text">'.$value['title'].'</div>';
                            $html .= '<div class="sub_text">'.$value['active_at'].'&nbsp;'.$value['sub_category'].'</div>';
                            $html .= '</a>';
                        }
                        echo $html;
                    ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

    <?php
        $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$id.'&type=match_analytics&limit=5';
        $data = get_curl($access_url);
        $data = json_decode($data, true);
        $html = '';
        if (count($data) > 0) {
    ?>
        <div class="index_p5 layout1200" style="padding-top: 10px;padding-bottom: 10px;">
            <div class="category_area">
                <div class="title_area"><span>赛事分析</span></div>
                <div class="category_item_area" id="match_analytics_area">
                    <?php
                        foreach ($data as $key => $value) {
                            $html .= '<a class="category_item" href="article.php?id='.$value['id'].'"><img src="'.$value['thumbnail_small2'].'" alt="'.$value['title'].'">';
                            $html .= '<div class="text">'.$value['title'].'</div>';
                            $html .= '<div class="sub_text">'.$value['active_at'].'&nbsp;'.$value['sub_category'].'</div>';
                            $html .= '</a>';
                        }
                        echo $html;
                    ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

    <?php
        $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$id.'&type=team_intro&limit=5';
        $data = get_curl($access_url);
        $data = json_decode($data, true);
        $html = '';
        if (count($data) > 0) {
    ?>
        <div class="index_p5 layout1200" style="padding-top: 10px;padding-bottom: 10px;">
            <div class="category_area">
                <div class="title_area"><span>队伍介绍</span></div>
                <div class="category_item_area" id="team_intro_area">
                    <?php
                        foreach ($data as $key => $value) {
                            $html .= '<a class="category_item" href="article.php?id='.$value['id'].'"><img src="'.$value['thumbnail_small2'].'" alt="'.$value['title'].'">';
                            $html .= '<div class="text">'.$value['title'].'</div>';
                            $html .= '<div class="sub_text">'.$value['active_at'].'&nbsp;'.$value['sub_category'].'</div>';
                            $html .= '</a>';
                        }
                        echo $html;
                    ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
    </div>

    <div class="see_more index_p5 layout1200">
        <div><a href="sub-category-inner.php?id=<?php echo $id; ?>">看更多</a></div>
    </div>

    <div class="index_p6 layout1200">
        <div class="more_news_list_area" id="more_news">
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$id.'&limit=30';

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

    function redirect_to_inner() {
        window.location.href = "sub-category-inner.php?id=" + getQueryString("id");
    }

    // header_live_carousel();
    // menu_left(); //update id='menu_left'
    // menu_right();//update id='username', id='profile'

    var type = $("input[name='type']:radio").val();
    var limit = $("select[name='limit']").val();
    var pageNo = 1;

    $(document).ready(function () {
        // featured_news_banner(0, getQueryString("id"), 1);
        // latest_news(0, getQueryString("id"), 3);

        // type_news($('#latest_news_area'), 'latest_news', getQueryString("id"));
        // type_news($('#match_analytics_area'), 'match_analytics', getQueryString("id"));
        // type_news($('#team_intro_area'), 'team_intro', getQueryString("id"));

        // more_news(0, getQueryString("id"), 30);
    });


    $("input[name='type']:radio").change(function () {
        console.log($(this).val());
        type = $(this).val();
        filterCategory();
    });

    $("select[name='limit']").change(function () {
        console.log($(this).val());
        limit = $(this).val();
        filterCategory();
    });

    function pageClick(obj) {
        console.log($(obj).text());
        pageNo = $(obj).text();
        filterCategory();
    }

    function pagePrevious(obj) {
        pageNo = pageNo - 1;
        filterCategory();
    }

    function pageNext(obj) {
        pageNo = pageNo + 1;
        filterCategory();
    }

    function filterCategory() {
        console.log(type + " | " + limit);
        article_list(0, getQueryString("id"), type, limit, pageNo);
    }

</script>
</body>
</html>

