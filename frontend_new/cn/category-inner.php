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


    <script type="text/javascript" src="../../assets/js/art-template-master/lib/template-web.js"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap.min.css">
    <script type="text/javascript" src="../../assets/js/fo_common/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../../assets/js/fo_common/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../assets/js/fo_common/swiper.min.js"></script>

    <link rel="stylesheet" href="../../assets/css/swiper.min.css"/>
    <link rel="stylesheet" href="../../assets/css/main.css"/>
    <link rel="stylesheet" href="../../assets/fontawesome-free-5.13.0-web/css/all.css"/>

    <script src="../../assets/js/common/utility.js"></script>
    <script src="../../assets/js/fo_common/shared.js"></script>
    <script src="../../assets/js/fo_logic/featured-news.js"></script>
    <script src="../../assets/js/fo_logic/latest-news.js"></script>
    <script src="../../assets/js/fo_logic/dynamic-sub-category.js"></script>
    <script src="../../assets/js/fo_logic/article-list.js"></script>
    <script src="../../assets/js/fo_logic/more-news.js"></script>

    <title><?php echo $data['title'] ?></title>
    <meta name="description" content="<?php echo $data['description'] ?>">
    <meta name="keywords" content="<?php echo $data['keywords'] ?>">
    <link rel="canonical" href="<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?>">
</head>
<body>
<div class="header">
	<div class="category_menu_outside "></div> 
</div>


<div class="main_area">
    <div class="index_p2 layout1200">
        <div class="featured_news_area" id="featured_news_banner">
        </div>
        <div class="latest_news_area" id="latest_news"></div>
    </div>

    <div id="dynamic_sub_category"></div>

    <div class="category_filter  layout1200">
        <div class="left">
            <input class="category-filter" type="radio" name="type" value="latest_news"
                   checked="checked">
            即时新闻
            <input class="category-filter" type="radio" name="type" value="match_analytics">
            赛事分析
            <input class="category-filter" type="radio" name="type" value="team_intro">
            队伍介绍
        </div>
        <div class="right">
            <select class="category_filter" name="limit">
                <option value="10" selected>10</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    <div class="index_p7 layout1200">
        <div class="category_area">
            <div class="category_item_area" id="article_list">
            </div>
        </div>

        <div class="pagination_area layout1200">
        </div>
    </div>

    <div class="index_p6 layout1200">
        <div class="more_news_list_area" id="more_news"></div>
    </div>

</div>

<div class="footer"></div>


<!--GENERAL-->
<script type="text/javascript">

    // header_live_carousel();
    menu_left(); //update id='menu_left'
    menu_right();//update id='username', id='profile'

    var type = $("input[name='type']:radio").val();
    var limit = $("select[name='limit']").val();
    var pageNo = 1;
	var categoryType = 'main';

    $(document).ready(function () {
        featured_news_banner(getQueryString("id"), 0, 1);
        latest_news(getQueryString("id"), 0, 3);
        article_list(getQueryString("id"), 0, type, limit, pageNo);
        more_news(getQueryString("id"), 0, 30);
    });

    $("input[name='type']:radio").change(function () {
        type = $(this).val();
        filterCategory();
    });

    $("select[name='limit']").change(function () {
        limit = $(this).val();
        filterCategory();
    });

    function pageClick(obj) {
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
        article_list(getQueryString("id"), 0, type, limit, pageNo);
    }

</script>

<div class="modal fade middle" id="login_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     style="display: none;" aria-hidden="true">
</div>
</body>
</html>