<?php

	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&category_id=0&sub_category_id=0&type=zonghe';
    $data = get_curl($access_url);
    $data = json_decode($data, true);

    $page = intval($_GET['page']);
    if (!$page) {
        $page = 1;
    }

    $limit = intval($_GET['limit']);
    if (!$limit) {
        $limit = 10;
    }
    if (!in_array($limit, [10, 25])) {
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
    <link rel="canonical" href="<?php echo get_url(); ?>">
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="main_area" style="margin-top: 20px;">
    <div class="index_p2 layout1200">
        <div class="featured_news_area" id="featured_news_banner">
            <?php 
                $access_url = CURL_API_URL . '/service/news.php?action=get_hot_news&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&limit=1';
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
                $access_url = CURL_API_URL . '/service/news.php?action=get_category_news&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&limit=3';
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

    <div id="dynamic_sub_category">
        
    </div>

    <div class="category_filter layout1200">
        <div class="left" id="category_radio_area">
            <input class="category-filter" type="radio" name="type" onclick="change_newstype(8, 53)" <?php echo ($category_id == 8 && $sub_category_id == 53 ? 'checked="checked"' : ''); ?>><a href="/cn/category-all-inner.php?id=5&page=1&limit=<?php echo $limit; ?>">网球</a><!-- 
            <input class="category-filter" type="radio" name="type" onclick="change_newstype(6)" <?php echo ($category_id == 6 ? 'checked="checked"' : ''); ?>><a href="/cn/category-all-inner.php?id=6&page=1&limit=<?php echo $limit; ?>">羽毛球</a> -->
            <input class="category-filter" type="radio" name="type" onclick="change_newstype(7, 0)" <?php echo ($category_id == 7 ? 'checked="checked"' : ''); ?>><a href="/cn/category-all-inner.php?id=7&page=1&limit=<?php echo $limit; ?>">游泳</a>
            <input class="category-filter" type="radio" name="type" onclick="change_newstype(8, 0)" <?php echo ($category_id == 8 && $sub_category_id == 0 ? 'checked="checked"' : ''); ?>><a href="/cn/category-all-inner.php?id=8&page=1&limit=<?php echo $limit; ?>">其他</a>
        </div>
        <div class="right">
            <select class="category_filter" id="sel_pagesize" onchange="change_size();">
                <option value="10" <?php echo ($limit == 10 ? 'selected="selected"' : ''); ?>>10</option>
                <option value="25" <?php echo ($limit == 25 ? 'selected="selected"' : ''); ?>>25</option>
            </select>
        </div>
    </div>

    <div class="index_p7 layout1200">
        <div class="category_area">
            <div class="category_item_area" id="article_list">
                <?php
                    $access_url = CURL_API_URL . '/service/news.php?action=get_category_news_pagination&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&page='.$page.'&limit='.$limit;

                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data['data'] as $key => $value) {
                        $html .= '<a class="category_item" href="article.php?id='.$value['id'].'">';
                        $html .= '<img src="'.$value['thumbnail_medium4'].'" style="width:290px; height:211px;">';
                        $html .= '<div>';
                        $html .= '<div class="text">'.$value['title'].'</div>';
                        $html .= '<div class="sub_text">'.$value['description'].'</div>';
                        $html .= '<div class="date">'.$value['active_at'].'</div>';
                        $html .= '</div>';
                        $html .= '</a>';
                    }
                    echo $html;
                ?>
            </div>
        </div>

        <div class="pagination_area layout1200">
            <?php
                $html = '';
                $total_page = $data['total_page'];
                if ($total_page > 0) {
                    $html .= '<nav aria-label="Page navigation example">';
                    $html .= '<ul class="pagination">';
                    $html .= '<li class="page-item"><a class="page-link page-pagination" href="/cn/category-all-inner.php?id='.$category_id.'&sub_category_id='.$sub_category_id.'&page='.($page > 1 ? $page - 1 : 1).'&limit='.$limit.'" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
                    for ($i=1; $i <= $total_page; $i++) {
                        $html .= '<li class="page-item page-pagination"><a class="page-link" href="/cn/category-all-inner.php?id='.$category_id.'&sub_category_id='.$sub_category_id.'&page='.$i.'&limit='.$limit.'">'.$i.'</a></li>';
                    }
                    $html .= '<li class="page-item"><a class="page-link page-pagination" href="/cn/category-all-inner.php?id='.$category_id.'&sub_category_id='.$sub_category_id.'&page='.($page < $total_page ? $page + 1 : $total_page).'&limit='.$limit.'" aria-label="Next"><span aria-hidden="true">»</span></a></li>';
                    $html .= '</ul>';
                    $html .= '</nav>';
                    echo $html;
                }
            ?>
        </div>
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

    // $exclude_list = [1, 2, 3, 4];

    // var type = $("input[name='type']:checked").val();
    // var limit = $("select[name='limit']").val();
    // var pageNo = 1;

    $(document).ready(function () {

        // header_live_carousel();
        // menu_left(); //update id='menu_left'
        // menu_right();//update id='username', id='profile'

        // featured_news_banner(0, 0, 1);
        // latest_news(0, 0, 3);

        // article_radio();

        // more_news();

    });

    // function filterType($elm) {
    //     type = $($elm).val();
    //     filterCategory();
    // }

    // $("input[name='type']:radio").change(function () {
    //     console.log($(this).val());
    //     type = $(this).val();
    //     filterCategory();
    // });

    // $("select[name='limit']").change(function () {
    //     console.log($(this).val());
    //     limit = $(this).val();
    //     filterCategory();
    // });

    // function filterCategory() {
    //     if (typeof type === 'undefined') {
    //         type = $("input[name='type']:checked").val()
    //     }
    //     article_category_list(type, 0, limit);
    // }

    function change_size() {
        location = '/cn/category-all-inner.php?id=<?php echo $category_id; ?>&sub_category_id=<?php echo $sub_category_id; ?>&page=1&limit='+$('#sel_pagesize').val();
    }
    function change_newstype (type, sub_type) {
        location = '/cn/category-all-inner.php?id='+type+'&sub_category_id='+sub_type+'&page=1&limit=<?php echo $limit; ?>';
    }
</script>
</body>
</html>
