<header style="position: fixed; width: 100%; background: white; z-index: 2;">
    <div class="header_container floating">
        <div class="header_logo"><a href="index.php"><img src="img/logo.png"></a></div>
        <div class="header_scorer">
            <div class="watch_scorer_btn" id="watch_scorer_btn">即时比分 <img src="../img/arrow.svg" class="arrow_down"></div>
        </div>
        <div class="header_menu" id="global_menu">
            <span class="line top"></span>
            <span class="line middle"></span>
            <span class="line bottom"></span>
        </div>
    </div>
    <div class="header_break_line"></div>
    <div class="filter_container" style="height: 47px;">
		<div class="search_box">
			<div class="fa fa-search" id="search_box"></div>
			<!--<input type="text" name="header_search" id="header_search" onkeyup="search()"/>-->
			<input type="text" name="header_search" id="header_search"/>
		</div>
        <div class="submenu_container">
            <div class="swiper-container">
                <div class="swiper-wrapper" id="original_header">
                    <div class="swiper-slide"><a href="/prediction.php" <?php echo strpos(strtolower($_SERVER['PHP_SELF']), '/prediction.php')!==false ?' class="active"':''; ?>>赛事预测</a></div>
                    <div class="swiper-slide"><a href="/index.php" <?php echo strpos(strtolower($_SERVER['PHP_SELF']), '/index.php')!==false ?' class="active"':''; ?>>推荐</a></div>
                    <?php
                        $access_url = CURL_API_URL . '/service/site.php?action=get_category&category_id=2'; 
                        $data = get_curl($access_url);
                        $data = json_decode($data, true);

                        foreach ($data as $key => $value) {
                            echo '<div class="swiper-slide"><a href="/sub_category.php?sub_category='.$value["id"].'&category=2" '.((strpos(strtolower($_SERVER['PHP_SELF']), '/sub_category.php')!==false||strpos(strtolower($_SERVER['PHP_SELF']), '/article.php')!==false)&&($_GET['sub_category']==$value["id"]||$article_sub_category_id==$value["id"]) ?' class="active"':'') . '>'.$value['display'].'</a></div>';
                        }

                        $access_url = CURL_API_URL . '/service/site.php?action=get_category&category_id=1'; 
                        $data = get_curl($access_url);
                        $data = json_decode($data, true);

                        foreach ($data as $key => $value) {
                            echo '<div class="swiper-slide"><a href="/sub_category.php?sub_category='.$value["id"].'&category=1" '.((strpos(strtolower($_SERVER['PHP_SELF']), '/sub_category.php')!==false||strpos(strtolower($_SERVER['PHP_SELF']), '/article.php')!==false)&&($_GET['sub_category']==$value["id"]||$article_sub_category_id==$value["id"]) ?' class="active"':'') . '>'.$value['display'].'</a></div>';
                        }
                    ?>
                    <div class="swiper-slide"><a href="/videos.php" <?php echo strpos(strtolower($_SERVER['PHP_SELF']), '/videos.php')!==false ?' class="active"':''; ?>>视频</a></div>
                </div>
            </div>
        </div>
    </div>
	<script>
		$("input").keypress(function(e){
        var key = $.trim($(this).val());
        if(e.keyCode === 13) {
					redirect_to("/search_result.php?keyword="+$("#header_search").val());
        }
    })
	</script>
</header>