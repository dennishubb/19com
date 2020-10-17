<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$data = array('title' => '', 'description' => '', 'keywords' => '');

$id = intval($_GET['id']);

if ($id) {
    $access_url = CURL_API_URL . '/service/site.php?action=get_seo_info&sub_category_id='.$id;
    $data = get_curl($access_url);
    $data = json_decode($data, true);
}
else {
    exit(0);
}

$type = $_GET['type'];
if (!$type) {
    $type = 'latest_news';
}
if (!in_array($type, ['latest_news', 'match_analytics', 'team_intro'])) {
    exit(0);
}

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
    <link rel="canonical" href="<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?>">
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
                $html .= '<div class="category"><a href="sub-category.php?id='.$data[0]['sub_category_id'].'">'.$data[0]['sub_category'].'</a></div>';
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

    <div id="dynamic_sub_category"></div>

    <div class="category_filter  layout1200">
        <div class="left">
            <input class="category-filter" type="radio" name="type" onclick="change_newstype('latest_news')" <?php echo ($type == 'latest_news' ? 'checked="checked"' : ''); ?>><a href="/cn/sub-category-inner.php?id=<?php echo $sub_category_id; ?>&type=latest_news&page=1&limit=<?php echo $limit; ?>">即时新闻</a>
            <input class="category-filter" type="radio" name="type" onclick="change_newstype('match_analytics')" <?php echo ($type == 'match_analytics' ? 'checked="checked"' : ''); ?>><a href="/cn/sub-category-inner.php?id=<?php echo $sub_category_id; ?>&type=match_analytics&page=1&limit=<?php echo $limit; ?>">赛事分析</a>
            <input class="category-filter" type="radio" name="type" onclick="change_newstype('team_intro')" <?php echo ($type == 'team_intro' ? 'checked="checked"' : ''); ?>><a href="/cn/sub-category-inner.php?id=<?php echo $sub_category_id; ?>&type=team_intro&page=1&limit=<?php echo $limit; ?>">队伍介绍</a>
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
                    $access_url = CURL_API_URL . '/service/news.php?action=get_category_news_pagination&category_id='.$category_id.'&sub_category_id='.$id.'&type='.$type.'&page='.$page.'&limit='.$limit;
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
                    $html .= '<li class="page-item"><a class="page-link page-pagination" href="/cn/sub-category-inner.php?id='.$sub_category_id.'&type='.$type.'&page='.($page > 1 ? $page - 1 : 1).'&limit='.$limit.'" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
                    for ($i=1; $i <= $total_page; $i++) {
                        $html .= '<li class="page-item page-pagination"><a class="page-link" href="/cn/sub-category-inner.php?id='.$sub_category_id.'&type='.$type.'&page='.$i.'&limit='.$limit.'">'.$i.'</a></li>';
                    }
                    $html .= '<li class="page-item"><a class="page-link page-pagination" href="/cn/sub-category-inner.php?id='.$sub_category_id.'&type='.$type.'&page='.($page < $total_page ? $page + 1 : $total_page).'&limit='.$limit.'" aria-label="Next"><span aria-hidden="true">»</span></a></li>';
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
    function change_size() {
        location = '/cn/sub-category-inner.php?id=<?php echo $sub_category_id; ?>&type=<?php echo $type; ?>&page=1&limit='+$('#sel_pagesize').val();
    }

    function change_newstype (type) {
        location = '/cn/sub-category-inner.php?id=<?php echo $sub_category_id; ?>&type='+type+'&page=1&limit=<?php echo $limit; ?>';
    }
</script>

</body>
</html>
