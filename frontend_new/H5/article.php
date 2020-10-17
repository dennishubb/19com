<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');

    $id = $_GET['id'];
    $articleData = httpGet(CURL_API_URL . '/service/news.php?action=get_article&id='. $id);
    $articleData = json_decode($articleData, true);

    $article_category_id = $articleData['category_id'];
    $article_sub_category_id = $articleData['sub_category_id'];
    $chatroom_id = $articleData['chatroom_id'];
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo $articleData['title'] ?></title>
    <meta name="description" content="<?php echo $articleData['description'] ?>">
    <meta name="keywords" content="<?php echo $articleData['keywords'] ?>">
    <?php
    include("style_script.php");
    ?>
    <?php
        if ($articleData['media_type'] == 2) {
    ?>
    <link href="<?php echo IMAGE_URL; ?>/assets/css/video-js.css" rel="stylesheet">
    <script src="<?php echo IMAGE_URL; ?>/assets/js/video.js"></script>
    <?php
        }
    ?>
</head>
<body>
<?php
include("header.php");
?>
<div class="main_container">

    <div class="body_container break_line">
        <session>
            <div class="session_block">
                <div class="article_title"><?php echo $articleData['title'] ?></div>
                <div class="article_writer"><?php echo $articleData['author'] ?></div>

                <div class="article_time_visit">
                    <div><?php echo $articleData['active_at'] ?></div>
                    <div>访问：<?php echo $articleData['view_count'] ?></div>
                </div>
                <div class="article_content">
                    <?php 

                    $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
                    $articleData['content'] = preg_replace($pregRule, '<img src="'.IMAGE_URL.'${1}">', $articleData['content']);
                    echo $articleData['content'];
                    ?>
                </div>

                <div class="article_tagging">
                    <?php foreach ($articleData['tags'] as $tags){?>
                        <a href="#"><?php echo $tags ?></a>
                    <?php } ?>
                </div>

                <div class="article_social">
                    <div><a target="_blank" href="http://service.weibo.com/share/share.php?appkey=&title=<?php echo $articleData['title'] ?>&url=<?php echo get_url(); ?>&style=simple"><img src="img/article/icWzWb.png"></a></div>
                    <div><a target="_blank" href="https://twitter.com/share?text=<?php echo $articleData['title'] ?>&url=<?php echo get_url(); ?>"><img src="img/article/icWzTw.png"></a></div>
                    <div><a href="#"><img src="img/article/icWzSend.png"></a></div>
                    <div>
                        <a id="social_more">
                            <img src="img/article/icWzMore.png">
                            <div class="article_social_listing">
                                <div><span><img src="img/article/icWzWb.png"></span></div>
                                <div><span><img src="img/article/icWzTw.png"></span></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </session>

        <session>
            <?php include("comments.php"); ?>
        </session>

        <session>
            <?php
            $categoryData = httpGet(CURL_API_URL . '/service/news.php?action=get_category_news&category_id='. $articleData['category_id'].'&sub_category_id='. $articleData['sub_category_id']);
            $categoryData = json_decode($categoryData, true);
            ?>
            <div class="session_block">
                <div class="session_block_title">
                    其他相关新闻
                </div>

                <div class="index_latestnews_thumb_list">
                    <?php foreach ($categoryData as $values){ ?>
                        <a href="article.php?id=<?php echo $values['id'] ?>">
                            <img src="<?php echo IMAGE_URL . $values['thumbnail_small_h5'] ?>">
                            <div>
                                <div class="title"><?php echo $values['title'] ?></div>
                                <div class="datetime"><?php echo $values['active_at']. " " . $values['sub_category']; ?></div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </session>

        <session>
            <div class="session_block">
                <div class="session_block_title">
                    19资讯精选新闻
                </div>
                <?php
                $hotnewsData = httpGet(CURL_API_URL . '/service/news.php?action=get_hot_news&limit=4');
                $hotnewsData = json_decode($hotnewsData, true);
                ?>
                <div class="index_hotnews_thumb_list">
                    <?php foreach ($hotnewsData as $i=>$values){ ?>
                    <div>
                        <div class="tagging">
                            <a href="/article.php?id=<?php echo $values['id'] ?>"><img src="<?php echo IMAGE_URL . $values['thumbnail_medium_h5'] ?>"></a>
                        </div>
                        <div class="title"><a href="/article.php?id=<?php echo $values['id'] ?>"><?php echo $values['title'] ?></a></div>
                        <div class="datetime"><?php echo $values['active_at'] ."  "; if($values['sub_category'] != null){echo $values['sub_category'];}?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </session>

        <session>
            <?php
            $latestData = httpGet(CURL_API_URL . '/service/news.php?action=get_latest_news');
            $latestData = json_decode($latestData, true);
            ?>
            <div class="session_block">
                <div class="session_block_title">
                    更多新闻
                </div>

                <div class="news_list">
                    <?php foreach ($latestData as $values){ ?>
                        <a href="/article.php?id=<?php echo $values['id'] ?>">
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

<script>
    $(function(){
        $(document).on("click", "#social_more", function(e){
            e.preventDefault();
            $(this).children(".article_social_listing").stop().slideToggle(300);
        })
            .on("mouseleave", "#social_more", function(){
                $(this).children(".article_social_listing").stop().slideUp(300);
            });

        $(document).on("click", ".report_btn", function(){
            $(this).children(".report_listing").stop().slideToggle(300);
        })
            .on("mouseleave", ".report_btn", function(){
                $(this).children(".report_listing").stop().slideUp(300);
            });

        /* Start comment box */
        $(document).on("focus", ".create_new_comment", function(){
            $(".new_comments_container .post_comment").removeClass("active");
            $(this).parents(".new_comments_container").children(".post_comment").addClass("active");
        });

        $(document).on("click", ".post_comment_cancel", function(e){
            e.preventDefault();
            $(this).parents(".sub.new_comments_container").removeAttr("style");
            $(this).parents(".new_comments_container").children(".post_comment").removeClass("active");
        });

        $(document).on("click", ".main_post_comment_send", function(e){
            e.preventDefault();
            console.log(1);
            var comment = $(this).parents(".new_comments_container").children(".new_comments").children(".comment").children(".create_new_comment").val();
            console.log(comment);
            var thisId = $(this).attr("id");
            console.log(thisId);
            $(".new_comments_container .post_comment").removeClass("active");
            $(this).parents(".sub.new_comments_container").removeAttr("style");
            $(this).parents(".new_comments_container").children(".new_comments").children(".comment").children(".create_new_comment").val("");

            var url = api_domain+'/service/message.php';
            var articleId = <?php echo $id; ?> ;
            var chartId = <?php echo $articleData['chatroom_id'] ?>;
            var euid = Cookies.get("euid");

            $.ajax({
                method: "POST",
                url: url,
                data:{action:'add_comments',type:'article',message:comment,parent_id:thisId,article_id:articleId,chatroom_id:chartId,euid:euid} ,
                success: function(data)
                {
                    console.log(data);
                    if(data != null || data != ''){
                        alert(data['message']);
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        });
        /* End comment box */
    });

    function deleteSelfComment(id){
        var url = api_domain+'/service/message.php';
        var euid = Cookies.get("euid");
        $.ajax({
            method: "POST",
            url: url,
            data:{action:'delete_comments',message_id:id,euid:euid} ,
            success: function(data)
            {
                console.log(data);
                if(data != null || data != ''){
                    alert(data['message']);
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });
    }

    function replyTo(id){
        $("#message_input_"+id).parents(".new_comments_container").css({display:"block"});
        $(".sub_comment_area").css({display:"none"});
        $("#message_input_"+id).focus();

    }

    function replyToSub(id){
        var parentId = id.split("_")[0];
        var childId = id.split("_")[1];
        $("#message_input_"+parentId).parents(".new_comments_container").css({display:"block"});
        $(".main_comment_area").css({display:"none"});
        $("#message_input_"+parentId).focus();
    }

    function replyMessage(id){
        var url = api_domain+'/service/message.php';
        var articleId = <?php echo $id; ?> ;
        var chartId = <?php echo $articleData['chatroom_id'] ?>;

        var comment = document.getElementById("message_input_"+id).value;//$("#message_input_"+id).value ;
        var euid = Cookies.get("euid");

        $.ajax({
            method: "POST",
            url: url,
            data:{action:'add_comments',type:'article',message:comment,parent_id:id,article_id:articleId,chatroom_id:chartId,euid:euid} ,
            success: function(data)
            {
                if(data != null || data != ''){
                    alert(data['message']);
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });
    }

    function count(id) {
        var url = api_domain+'/service/message.php';
        var euid = Cookies.get("euid");
        $.ajax({
            method: "POST",
            url: url,
            data:{action:'thumbup_comments',euid:euid,message_id:id} ,
            success: function(data)
            {
                console.log(data);
                if(data != null || data != ''){
                    alert(data['message']);
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });
    }

    function report(type,id){
        $(".report_him .report_listing").stop().slideUp(1);
        alert("delete " + id);
        var url = api_domain+'/service/message.php';
        var euid = Cookies.get("euid");
        $.ajax({
            method: "POST",
            url: url,
            data:{action:'delete_comments',euid:euid,message_id:id} ,
            success: function(data)
            {
                if(data != null || data != ''){
                    alert(data['message']);
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });
    }
</script>
</body>
</html>