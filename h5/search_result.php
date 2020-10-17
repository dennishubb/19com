<?php
        include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19.com</title>
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
            <!-- Start Landing Latest News -->
            <session>
                <div id ="searchResultList" class="session_block" style="border-top: 7px solid #f5f5f5;"></div>
            </session>
            <!-- End Landing Latest News -->
            
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>
<script>
	var page = 1;
    var stopCallingApi = null
	getSearchResult(1);
	function getSearchResult(newSearch=0) {
        if(newSearch) stopCallingApi = false
        if(stopCallingApi) return Promise.resolve()
		var data={
            action: "search",
            keyword: "<?php echo $_GET['keyword'] ?>",
            page: page
        };
		
        return $.ajax({
            url: getBackendHost() + '/service/news.php',
            type: 'GET',
            data: data,
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            }
            }).then(response => {
                if (page == 1) {
                    var html = template.render($("#searchResultTable1").html(), {"list": response.list, totalCount:response.totalcount});
                    $("#searchResultList").html(html);
                }
                else {
                    var html = template.render($("#searchResultTable2").html(), {"list": response.list});
                    $("#searchResultTableBody").append(html);
                }
                if( response.list.length < 1 ) stopCallingApi = 1
                if( $.trim( $('#searchResultList').html() ).length==0  )
					$("#searchResultList").html('无记录');
        }).catch(err => console.log(err));
	}
	
    var winH = $(window).height();
    var isLoading = false
	var scrollHandler = function () {
        if(isLoading) {
            return
        }
        var pageH = $(document.body).height();
        var scrollT = $(window).scrollTop();
        var viewableHeight = window.innerHeight;
        var totalHeight = $(document).height();
        var maxScroll = totalHeight - viewableHeight
        var scrollLoadPoint = maxScroll - 20

        if (scrollT > scrollLoadPoint ) {
            isLoading = true

            page++;
            getSearchResult()
                .then(() => isLoading = false);
        }
    };
    $(window).scroll(scrollHandler)
</script>

<script type="text/html" id="searchResultTable1">
    <div class="session_block_title">搜索词 - <?php echo $_GET['keyword'] ?><span class="more">共{{totalCount}}个结果</span></div>
        <div class="break_line"></div>
        <div class="index_latestnews_thumb_list" id="searchResultTableBody">
            {{each list value index }}
            <a href="article.php?id={{value.id}}">
                <img src="<?php echo IMAGE_URL; ?>{{ value.thumbnail_h5 }}">
                <div>
                    <div class="title">{{ value.title }}</div>
                    <div class="datetime">
                        {{ value.sub_category ? value.active_at+" "+value.sub_category : value.active_at +" "+value.category }}
                    </div>
                </div>
            </a>
            {{ /each }}
        </div>
</script>

<script type="text/html" id="searchResultTable2">
    {{each list value index }}
        <a href="article.php?id={{value.id}}">
            <img src="<?php echo IMAGE_URL; ?>{{ value.thumbnail_h5 }}">
            <div>
                <div class="title">{{ value.title }}</div>
                <div class="datetime">
                    {{ value.sub_category ? value.active_at+" "+value.sub_category : value.active_at +" "+value.category }}
                </div>
            </div>
        </a>
	{{ /each }}
</script>
