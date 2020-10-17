<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19.com</title>
<?php
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
    include("style_script.php");
	
	$category_id = $_GET['category'];
	$sub_category_id = $_GET['sub_category'];
	$type = $_GET['type'];
?>
</head>
<body>
	<?php include 'header.php'; ?>
    <div class="main_container">
        <div class="body_container">
            <!-- Start Landing Latest News -->
            <session>	
				<div class="session_block" style="border-top: 7px solid #f5f5f5;" id="more_block">
                    <div class="session_block_title">
						<?php 
							switch($type){
								case "latest_news":
									echo "即时新闻";
									break;
								case "match_analytics":
									echo "赛事分析";
									break;
								case "team_intro":
									echo "队伍介绍";
									break;
							} 
						?>
					</div>
                    <div class="break_line"></div>

                    <div class="index_latestnews_thumb_list" id='more_list'>
						<?php
							$access_url = CURL_API_URL . '/service/news.php?action=get_category_news_pagination&category_id='.$category_id.'&sub_category_id='.$sub_category_id.'&type='.$type.'&page=1';
							$data = get_curl($access_url);
							$data = json_decode($data, true);
							$html = '';
						
							foreach ($data['data'] as $key => $value) {
								$html .= '<a href="article.php?id='.$value['id'].'">';
								$html .= '<img src="'.IMAGE_URL.''.$value['thumbnail_small_h5'].'"><div>';
								$html .= '<div class="title">'.$value['title'].'</div>';
								$html .= '<div class="datetime">'.$value['active_at'].' '.$value['category'].'</div></div></a>';
							}
							echo $html;
						?>
                    </div>
                </div>
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
	var type = '<?php echo $type; ?>';
	var category_id = <?php echo $category_id; ?>;
	var sub_category_id = <?php echo $sub_category_id; ?>;
	var is_loading = false;
	var end_of_page = false;
	
	function getMoreNews(page_no){
		
		if(is_loading) return;
		is_loading = true;
		
		if(page_no > 0){ 
			page = page_no;
		}

		$.ajax({
			url: getBackendHost() + '/service/news.php',
			type: 'get',
			data: {"action":"get_category_news_pagination", "category_id":category_id, "sub_category_id":sub_category_id, type:type, "page":page},
			dataType: "json",
			crossDomain: true,
			async:false,
			xhrFields: {
				withCredentials: true
			},
			success: function (response, status, xhr) {
				if (xhr.status == 200) {
					
					var html = template.render($("#more_tpl").html(), {"data": response.data, "image_url":'<?php echo IMAGE_URL ?>'});
					if(page == 1){
						$("#more_list").html(html);
					}else{
						$("#more_list").append(html);
					}
					
					if(page > 1 && response.data.length == 0){
						end_of_page = true;
					}
					
					page = response.current_page;
					is_loading = false;
				}
				else {
					alert(response.message);
				}
			  },
			  error: function(res) {

			  }
		});

	}
	
	var winH = $(window).height();
	var scrollHandler = function () {
		var pageH = $(document.body).height();
		var scrollT = $(window).scrollTop();
		var aa = (pageH - winH - scrollT) / winH;

		if (aa < 0.02 && !end_of_page) {
			page++;
			console.log(page);
			getMoreNews(page);
		}
	}
	$(window).scroll(scrollHandler);
	
</script>

<script type="text/html" id="more_tpl">
	{{each data value index }}
		<a href="article.php?id={{value.id}}">
            <img src="{{image_url}}{{value.thumbnail_small_h5}}">
                <div>
                    <div class="title">{{value.title}}</div>
                    <div class="datetime">{{value.active_at}} {{value.category}}</div>
                </div>
        </a>
	{{/each}}
</script>