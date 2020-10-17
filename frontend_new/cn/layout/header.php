<?php
$is_mobile = 0;

$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
    $is_mobile = 1;
}

$category_id = 0;
$category_name = '';
$sub_category_id = 0;
$sub_category_name = '';

$is_zonghe = 0;
$is_video = 0;

if (strpos(strtolower($_SERVER['PHP_SELF']), 'category.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'sub_category.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'category-all-inner.php')) {
	$category_id = intval($_GET['id']);
	if (!$category_id) {
		exit(0);
	}
	else {
	    $access_url = CURL_API_URL . '/service/site.php?action=get_single_category&category_id=' . $category_id;
	    $data = get_curl($access_url);
	    $data = json_decode($data, true);
	    $category_name = $data['display'];
	}
}
if (strpos(strtolower($_SERVER['PHP_SELF']), 'sub-category.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'sub-category-inner.php')) {
	$sub_category_id = intval($_GET['id']);
	if (!$sub_category_id) {
		exit(0);
	}
	else {
	    $access_url = CURL_API_URL . '/service/site.php?action=get_single_category&category_id=' . $sub_category_id;
	    $data = get_curl($access_url);
	    $data = json_decode($data, true);
	    $sub_category_name = $data['display'];

	    $category_id = $data['parent_id'];
	    $access_url = CURL_API_URL . '/service/site.php?action=get_single_category&category_id=' . $category_id;
	    $data = get_curl($access_url);
	    $data = json_decode($data, true);
	    $category_name = $data['display'];
	}
}

if (strpos(strtolower($_SERVER['PHP_SELF']), 'category-all.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'category-all-inner.php')) {
	$is_zonghe = 1;
	$sub_category_id = intval($_GET['sub_category_id']);
    $access_url = CURL_API_URL . '/service/site.php?action=get_single_category&category_id=' . $sub_category_id;
    $data = get_curl($access_url);
    $data = json_decode($data, true);
    $sub_category_name = $data['display'];
}

if (strpos(strtolower($_SERVER['PHP_SELF']), 'video.php')) {
	$is_video = 1;
}

if (strpos(strtolower($_SERVER['PHP_SELF']), 'article.php')) {
	$article_id = intval($_GET['id']);
	if (!$article_id) {
		exit(0);
	}

    $access_url = CURL_API_URL . '/service/news.php?action=get_article_category&id=' . $article_id;
    // var_dump($access_url);
    $data = get_curl($access_url);
    $data = json_decode($data, true);
	
	$category_id = $data['category_id'];
	$category_name = $data['category'];
	$sub_category_id = $data['sub_category_id'];
	$sub_category_name = $data['sub_category'];

	if (in_array($category_id, [5,6,7,8])) {
		$is_zonghe = 1;
	}
}
?>


<div class="header">
<?php if ($is_mobile == 1) { ?>
<style>
.app_download_area{
    display: none;
}
@media only screen and (max-width: 812px) {
    .app_download_area {
        background: #0F1C2D;
        display: flex;
        color: white;
        font-size: 13px;
        line-height: 18px;
        align-items: center;
        padding: 0px 15px;
    }
    .app_download_area .app_icon {
        margin: 15px;
        width: 50px;
    }
    .app_download_area span {
        color: #ED1B34;
    }
    .app_download_area >div:nth-last-child(1) {
        margin-left: auto;
    }
    .app_download_area >div button {
        height: 38px;
        width: 100px;
        font-weight: 600;
        font-size: 13px;
        white-space: nowrap;
        background-color: #ED1B34;
        border: 0px;
        color: white;
        cursor: pointer;
        font-weight: 600;
        border-radius: 8px;
        padding: 7px;
        text-align: center;
        outline: none !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        display: inline-block;
    }
}
</style>

<header style="width: 100vw; display: none;" id="app_download">
    <div class="app_download_area">
        <div class="cross" onclick="close_app_download();"><img class="" src="/cn/img/cross.svg"></div>
        <div><img class="app_icon" src="/cn/img/app_icon.png"></div>
        <div>19 APP下载<br><span><a href="https://app19.app">app19.app</a></span></div>
        <div><a href="https://19app.app"><button class="btn">下载APP</button></a></div>
    </div>
</header>
<script type="text/javascript">
	var tmr_time = <?php echo strtotime(date("Y-m-d",strtotime("+1 day"))); ?>;
	var cookie_name = "app_download_closed";
	function close_app_download() {
		$("#app_download").hide();
		var Days = 7;
	    var exp = new Date();
	    exp.setTime(parseInt(tmr_time)*1000);
	    document.cookie = cookie_name + "="+ "1" + ";expires=" + exp.toGMTString();
	}

	var reg=new RegExp("(^| )"+cookie_name+"=([^;]*)(;|$)");

    if(document.cookie.match(reg) == null) {
    	$("#app_download").show();
    }
</script>
<?php } ?>
	<div class="w-100 hot_event_area" id="header_live_carousel">
		<div class="layout1200" style="min-height: 54px;">
		    <div class="hot_event_left">
		        <select id="banner_dropdown" onchange='get_live_matches();'>
		            <option value="popular">热门赛事</option>
					<option value="football">足球</option>
					<option value="basketball">篮球</option>
					<option value="1" selected="selected">NBA</option>
					<option value="3">CBA</option>
					<option value="1423">英超</option>
					<option value="1461">西甲</option>
					<option value="1469">德甲</option>
					<option value="1449">意甲</option>
					<option value="1482">法甲</option>
					<option value="1877">中超</option>
					<option value="1388">欧冠杯</option>
					<option value="1827">亚冠杯</option>
					<option value="1387">欧洲杯</option>
		        </select>
		    </div>
		    <div class="hot_event_right">
		    	<div class="swiper-container swiper-container-initialized swiper-container-horizontal">
				    <div class="swiper-wrapper" id="div_live_matches">
				    	<?php 
						    $access_url = CURL_API_URL . '/service/match.php?action=get_live_matches&league_id=1';
						    $data = get_curl($access_url);
						    $data = json_decode($data, true);
						    $total = count($data);
						    $html = '';

				    		$num = 0;
				    		foreach ($data as $key => $value) {
				    			if ($num % 3 == 0) {
						        	$html .= '<div class="swiper-slide swiper-slide-active" style="width: 497.143px;">';
						      		$html .= '<div class="hot_event_item">';
					        		$html .= '<div class="hot_event_item_left_title" style="white-space: nowrap;padding-right: 60px;">'.$value['league_name'].'</div>';
					        		$html .= '<div class="hot_event_item_right">';
				    			}

				    			$html .= '<div><div class="sub_title">'.$value['match_type_name'].'</div><div>'.$value['home_team_name'].'<span>'.$value['home_score'].'</span></div><div>'.$value['away_team_name'].'<span>'.$value['away_score'].'</span></div></div>';

				    			if (($num % 3 == 2 ) || $num == ($total - 1)) {
				    				$html .= '</div></div></div>';
				    			}
				    			$num++;
				    		}
				    		echo $html;
				    	?>
				    </div>
				  	<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
				</div>
			   	<div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"></div>
	      		<div class="swiper-button-prev swiper-button-disabled" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="true"></div>
		    </div>
		</div>
	</div>
	<div class="w-100 menu_area">
		<div class="layout1200 ">
			<div class="menu_logo"><a href="/cn"><img src="/assets/branding/logo.png" style="width: 75px;height: 30px;"></a></div>
			
			<?php if ($category_id > 0 || $is_zonghe == 1 || $is_video == 1 || strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php')) { ?>
			<div class="menu_btn">
			<?php } else { ?>
			<div class="menu_btn" style="display:none">
			<?php } ?>
				<div class="burger">
					<i class="line-1"></i>
					<i class="line-2"></i>
					<i class="line-3"></i>
				</div>
			</div>
			
			<div class="menu_left" id="menu_left" style="width: 100%;">
				<style>
					.static_menu >div.active ,.static_menu >div:hover{
					    color: #ed1b34;
						
					}

					.static_menu >div.active a{
					    display: inline-block;
					    border-bottom: 2px solid #ed1b34;
					    padding-bottom: 2px;
					}

					.scrollable{
						display: inline-block;
					    text-align: center;
					    text-decoration: none;
						padding: 10px 0px;
					}


					/* width */
					.static_menu::-webkit-scrollbar {
					  width: 10px;
					  height: 10px;
					}

					/* Track */
					.static_menu::-webkit-scrollbar-track {
					  box-shadow: inset 0 0 5px grey; 
					  border-radius: 10px;
					}
					 
					/* Handle */
					.static_menu::-webkit-scrollbar-thumb {
					  background: #cccac4; 
					  border-radius: 10px;
					}

					/* Handle on hover */
					.static_menu::-webkit-scrollbar-thumb:hover {
					  background:#9e9d99; 
					}
				</style>

				<?php if ($category_id > 0 || $is_zonghe == 1 || $is_video == 1 || strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php')) { ?>
					<div class="static_menu hide" style="overflow: auto;white-space: nowrap;">
				<?php } else { ?>
					<div class="static_menu" style="overflow: auto;white-space: nowrap;">
				<?php } ?>
					<div class="scrollable"><a href="/cn/match-prediction/index.php">赛事预测</a></div>
					<div class="scrollable <?php echo ($category_id == 1 ? 'active' : ''); ?>"><a href="/cn/category.php?id=1">足球</a></div>
					<div class="scrollable <?php echo ($category_id == 2 ? 'active' : ''); ?>"><a href="/cn/category.php?id=2">篮球</a></div>
					<div class="scrollable <?php echo ($category_id == 3 ? 'active' : ''); ?>"><a href="/cn/category.php?id=3">台球</a></div>
					<div class="scrollable <?php echo ($category_id == 4 ? 'active' : ''); ?>"><a href="/cn/category.php?id=4">电竞</a></div>
					<div class="scrollable <?php echo ($is_zonghe == 1 ? 'active' : ''); ?>"><a href="/cn/category-all.php">综合</a></div>
					<div class="scrollable <?php echo ($is_video == 1 ? 'active' : ''); ?>"><a href="/cn/video.php">视频</a></div>
				</div>
					<?php if ($category_id > 0 || $is_zonghe == 1 || $is_video == 1 || strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php')) { ?>
					<div class="sub_menu" style="">
					<?php } else { ?>
					<div class="sub_menu" style="display: none;">
					<?php } ?>
					
					<?php if (strpos(strtolower($_SERVER['PHP_SELF']), 'category-all.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'category-all-inner.php')) { ?>
						<a href="/cn/category-all.php">综合</a>
					<?php } ?>
					<?php if ($is_video == 1) { ?>
						<a href="/cn/video.php">视频</a>
					<?php } ?>
					<?php if (strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php')) { ?>
						<a href="/cn/match-prediction/index.php">赛事预测</a>
					<?php } ?>
					<?php if (strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php')) { ?>
						<a href="/cn/match_history.php">赛事结果</a>
					<?php } ?>
					<?php if ($category_id > 0) {
						if (strpos(strtolower($_SERVER['PHP_SELF']), 'category-all-inner.php')) {
							echo '&nbsp;<i class="fas fa-chevron-right"></i>&nbsp;' . $category_name;
						}
						else {
							echo '<a href="/cn/category.php?id='.$category_id.'">'.$category_name.'</a>';
						}
					} ?>
					<?php if ($sub_category_id > 0) {
						echo '&nbsp;<i class="fas fa-chevron-right"></i>&nbsp;' . $sub_category_name;
					} ?>

				</div>
			</div>
			
			<div class="menu_right">
				<div  id='gift_exchange' style='cursor: pointer;'onclick="location.href = '/cn/exchange.php';"><i class="fa fa-gift" style='color: darkslategrey;font-size: 20px;'></i></div>
				<div id="before_login_div" data-toggle="modal" data-target="#login_popup" style="display: none"><a>登入/注册</a></div>
				<div id="after_username" style="cursor: default;"></div>
				<div class="profile_btn" style="display: none;"><img id="header_container_thumbnail" src="" onclick="location.href = '/cn/profile/index.php';">
					<div class="profile_popup" id="profile-summary" style="cursor: default;">
						<div class="profile_popup_inner profile_popup_hide">
			  				<div class="profile_popup_header" >
			  					<div class="username"><img  id='profile_summary_thumbnail' style='width:50px;height:50px' src=""><span id="span_user_name"></span></div>
			  					<div class="user_detail">
			  						<div>等级：<span id="span_user_level"></span> <img id="img_user_level" src="" style='width:25px;height:25px;margin-bottom: 5px;'></div>
			  						<div><img src="/assets/images/point_icon.png">券数：<span id="span_user_vouchers"></span></div>
			  						<div><img src="/assets/images/point_icon.png">总战数：<span id="span_total_points"></span></div>
			  						<div><img src="/assets/images/point_icon.png">现有战数：<span id="span_current_points"></span></div>
									<div id='logout_div' style="padding-left: 5px" ><a onclick="logout()">登出</a></div>
			  					</div>
			  				</div>
			  				<div class="profile_popup_menu_area">
			      				<div class="profile_popup_menu">
			      					<div id='profile_div' style="position: relative" class="<?php echo (strpos(strtolower($_SERVER['PHP_SELF']), '/profile/index.php') ? 'active' : ''); ?>" onclick="location.href = '/cn/profile/index.php';">
										<img class="svg"  src="/assets/images/user_icon.svg" type="image/svg+xml" /> 
										<img class="svg"  src="/assets/images/user_icon_over.svg" type="image/svg+xml" /> 
			      						<div class="profile_popup_menu_title">
											我的首页
										</div>
									</div>
									<div id='prediction-history_div' style="position: relative" class="<?php echo (strpos(strtolower($_SERVER['PHP_SELF']), 'prediction-history/index.php') ? 'active' : ''); ?>" onclick="location.href = '/cn/prediction-history/index.php';">
										<img class="svg" src="/assets/images/cup_icon.svg" type="image/svg+xml" /> 
										<img class="svg" src="/assets/images/cup_icon_over.svg" type="image/svg+xml" /> 
										<div class="profile_popup_menu_title">
											预测历史
										</div>
									</div>
									<div id='promotion_div' style="position: relative"  class="<?php echo (strpos(strtolower($_SERVER['PHP_SELF']), 'promotion.php') ? 'active' : ''); ?>" onclick="location.href = '/cn/promotion.php';">
										<img class="svg" src="/assets/images/diamond_icon.svg" type="image/svg+xml" /> 
										<img class="svg" src="/assets/images/diamond_icon_over.svg" type="image/svg+xml" /> 
										<div class="profile_popup_menu_title">
											活动专区
										</div>
									</div>
									<div id='message-collect_div' style="position: relative"  class="<?php echo (strpos(strtolower($_SERVER['PHP_SELF']), 'message-collect/index.php') ? 'active' : ''); ?>" onclick="location.href = '/cn/message-collect/index.php';">
										<img class="svg" src="/assets/images/collect_icon.svg" type="image/svg+xml" /> 
										<img class="svg" src="/assets/images/collect_icon_over.svg" type="image/svg+xml" /> 
										<div class="profile_popup_menu_title">
											留言收藏
										</div>
									</div>
									<div id='exchange_div' style="position: relative"  class="<?php echo (strpos(strtolower($_SERVER['PHP_SELF']), 'exchange.php') ? 'active' : ''); ?>" onclick="location.href = '/cn/exchange.php';">
										<img class="svg" src="/assets/images/gift_icon.svg" type="image/svg+xml" /> 
										<img class="svg" src="/assets/images/gift_icon_over.svg" type="image/svg+xml" /> 
										<div class="profile_popup_menu_title">
											礼品兑换
										</div>
									</div>
									<div id='chaoxing_div' class="disabled" style="position: relative"  class="">
										<img class="svg" src="/assets/images/star_icon.svg" type="image/svg+xml" /> 
										<img class="svg" src="/assets/images/star_icon_over.svg" type="image/svg+xml" />
										<div class="profile_popup_menu_title">
											潮星天堂
										</div>
									</div>
			      				</div>
			      			</div>
			  			</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ($category_id > 0 || $is_zonghe == 1) { ?>
	<div class="category_menu_outside">
		<style>
		.category_menu >div.active ,.category_menu >div:hover{
		    color: #ed1b34;
		}
		.category_menu >div.active a{
		    display: inline-block;
		    border-bottom: 2px solid #ed1b34;
		    padding-bottom: 2px;
		}
		.scrollable{
		  display: inline-block;
		  
		  text-align: center;
		  padding: 14px;
		  text-decoration: none;
		}
		/* width */
		.category_menu::-webkit-scrollbar {
		  width: 10px;
		  height: 10px;
		}
		/* Track */
		.category_menu::-webkit-scrollbar-track {
		  box-shadow: inset 0 0 5px grey; 
		  border-radius: 10px;
		}
		/* Handle */
		.category_menu::-webkit-scrollbar-thumb {
		  background: #cccac4; 
		  border-radius: 10px;
		}
		/* Handle on hover */
		.category_menu::-webkit-scrollbar-thumb:hover {
		  background:#9e9d99; 
		}
		</style>
		<div class="layout1200 category_menu" style="overflow: auto;white-space: nowrap;">
			<?php 
	            $html = '';
				if ($category_id > 0 && !$is_zonghe) {
					$access_url = CURL_API_URL . '/service/site.php?action=get_category&category_id=' . $category_id; 
					$data = get_curl($access_url);
	                $data = json_decode($data, true);

	                foreach ($data as $key => $value) {
	                	$html .= '<div class="scrollable '.($value["id"] == $sub_category_id ? "active" : "").'"><a href="/cn/sub-category.php?id='.$value['id'].'">'.$value['display'].'</a></div>';
	                }
				}
				if ($is_zonghe == 1) {
					$html .= '<div class="scrollable '.(8 == $category_id && $sub_category_id == 53 ? "active" : "").'"><a href="/cn/category-all-inner.php?id=8&sub_category_id=53">网球</a></div>';
					$html .= '<div class="scrollable '.(6 == $category_id ? "active" : "").'"><a href="/cn/category-all-inner.php?id=6">羽毛球</a></div>';
					// $html .= '<div class="scrollable '.(7 == $category_id ? "active" : "").'"><a href="/cn/category-all-inner.php?id=7">游泳</a></div>';
					$html .= '<div class="scrollable '.(8 == $category_id && $sub_category_id == 0 ? "active" : "").'"><a href="/cn/category-all-inner.php?id=8">其他</a></div>';
				}
				if ($is_video == 1) {

				}
	            echo $html;
			?>
		
			<div class="search_bar" style="display:none">
				<input type="">
				<i class="fas fa-search"></i>
			</div>
		</div>
    </div>
<?php } ?>

<?php if (strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php') || strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php')) { ?>
	<div class="category_menu_outside">
	    <div class="layout1200 category_menu">
	        <div <?php echo strpos(strtolower($_SERVER['PHP_SELF']), 'match-prediction/index.php') ? "class=\"active\"" : ""; ?>><a href="/cn/match-prediction/index.php"><span>赛事预测</span></a></div>
	        <div <?php echo strpos(strtolower($_SERVER['PHP_SELF']), 'match_history.php') ? "class=\"active\"" : ""; ?>><a href="/cn/match_history.php"><span>赛事结果</span></a></div>
	        <div class="tut_btn" data-toggle="modal" data-target="#tut_popup"><i class="fas fa-question-circle"></i>教程</div>
	    </div>
	</div>
<?php } ?>
</div>

<script src="/assets/js/fo_common/plugins.bundle.js" type="text/javascript"></script>
<div class="modal fade middle" id="login_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     style="display: none;" aria-hidden="true">
     <div class="modal-dialog" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <nav>
                   <div class="nav nav-tabs" id="nav-tab" role="tablist">
                   	 <a class="nav-item nav-link active" id="nav-login-tab" data-toggle="tab" href="#nav-login" role="tab" aria-controls="nav-login" aria-selected="true">登入</a>
                       <a class="nav-item nav-link" id="nav-register-tab" data-toggle="tab" href="#nav-register" role="tab" aria-controls="nav-register" aria-selected="false">注册</a>
                      
                   </div>
               </nav>
       
               
           </div>
           <div class="modal-body">
	           <div class="tab-content" id="nav-tabContent">
		           	<div class="tab-pane fade active show" id="nav-login" role="tabpanel" aria-labelledby="nav-login-tab">
		                <input type="hidden" name='type' value='Member'>
						<input placeholder="会员ID" type="text" id="txtUsername" name='username'>
		                <input placeholder="密码" type="password" id="txtPassword" name='password'>
						<div class="cache">
							<input type="text" data-type="required" placeholder="验证码" id="txtCaptcha" name="captcha">
                            <img onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';" id="login-img-captcha" src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" style="cursor: pointer;">
						</div>
						
						<div class="forget_pw">
							<u  data-toggle="modal" data-target="#forgotpw" data-dismiss="modal" style='cursor:pointer'>忘记密码</u> 
							 /
							<u data-toggle="modal" data-target="#forgotac" data-dismiss="modal" style='cursor:pointer'>忘记账户</u>
						</div>
						<button type="button" id='login_button' onclick="userlogin();">送出</button>
			       	</div>
				    <div class="tab-pane fade " id="nav-register" role="tabpanel" aria-labelledby="nav-register-tab">
						<input placeholder="会员ID" type="text" id="txt_register_username">
		                <input placeholder="密码" type="password" id='signup_password'>
						<input placeholder="确认密码"  type="password" id='signup_confirm_password'>
					    <div class="cache">
							<input type="text" data-type="required" placeholder="验证码" id="txt_register_captcha">
                            <img onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';" id="signup-img-captcha" src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" style="cursor: pointer;">
						</div>
						<button type="button" id='signup_button'  onclick="user_register();">送出</button>
					</div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="modal fade" id="forgotpw" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">忘记密码</h5>
	        
	      </div>
			<div class="modal-body">
				<div class="">
					<input id='txt_forget_pwd_phone' placeholder="手机号" autocomplete="off">
					<div class="cache">
						<input type="text" data-type="required" placeholder="验证码" name="captcha" id="forgetpw_captcha">
						<img onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';" id="forgetpw-img-captcha" src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" style="cursor: pointer;">
					</div>
					
					<input name='new_password' id='new_password' type='password' placeholder="新密码" style='display:none'> 
					<input name='confirm_new_password' id='confirm_new_password' type='password' placeholder="确认新密码" style='display:none'> 
					<input name='verification_code' id='verification_code' placeholder="验证码" style='display:none'> 
					<div style="width: 100%;text-align: center;">
						<span id='msg_sent_span' style='display:none;padding: 10px;color: #aa0b27;' ></span>
						<button type='button' id='sms_button' onclick='forget_password()'>送出短信</button> 
						<button type='button' id='verification_button' style='display:none' onclick='reset_password()'>送出</button> 
					</div>
				</div>    
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="forgotac" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">忘记账户</h5>
	        
	      </div>
			<div class="modal-body">
				<div class="">
					<input id="txt_forget_account_phone" name='phone' placeholder="手机号" autocomplete="off">
					<div class="cache">
						<input type="text" data-type="required" placeholder="验证码" id="forgetac_captcha">
						<img id="img_forget_account_captcha" onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';" id="forgetac-img-captcha" src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" style="cursor: pointer;">
					</div>
					
					<div style="width: 100%;text-align: center;">
						<div id='forgetac_msg_sent_span' style='display:none;padding: 10px;color: #aa0b27;'></div>
						<button type='button' id='forgetac_sms_button' onclick='forget_account()'>送出短信</button> 
						<button type='button' id='forgetac_close_button' onclick="$('#forgotac').modal('hide');$('.modal-backdrop').remove();" style='display:none'>关闭</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="floating_right" style="z-index: 6">
	<div  class="floating_right_item">
		<div class="qr_code"><img src="/assets/images/19app_qr.png" >
			<a href="https://www.app19.app">19app.app</a>
			<div>iOS 用户请使用<br>Safari 开启页面</div>	
		</div>
		<div class="fr_btn qr" onmouseover="$('.floating_right_item .qr_code').css('opacity',1);$(' .floating_right_item .fr_btn.qr .hover').css('opacity',1);$(' .floating_right_item .fr_btn.qr .default').css('opacity',0)" onmouseleave="$('.floating_right_item .qr_code').css('opacity',0);$(' .floating_right_item .fr_btn.qr .hover').css('opacity',0);$(' .floating_right_item .fr_btn.qr .default').css('opacity',1)">
			<img class="svg" src="/assets/images/app_download.svg" ></img>
		</div>
		<div class="fr_btn cs">
			<a target="_blank" href="https://vm.providesupport.com/17b3rzok1ff0818qnvt5vfp4kp">
				<img  class="svg"  src="/assets/images/cs.svg" />
			</a>
		</div>
		<div class="fr_btn top">
			<a href="javascript:scrollTo(0,0);"><img  class="svg" src="/assets/images/top.svg" /></a>
		</div>
	</div>
</div>

<script language="javascript">
	var api_domain = '<?php echo CURL_BACKEND_URL; ?>';

	const convertImages = (query, callback) => {
	  const images = document.querySelectorAll(query);

	  images.forEach(image => {
	    fetch(image.src)
	    .then(res => res.text())
	    .then(data => {
	      const parser = new DOMParser();
	      const svg = parser.parseFromString(data, 'image/svg+xml').querySelector('svg');

	      if (image.id) svg.id = image.id;
	      if (image.className) svg.classList = image.classList;
	      try{
	        image.parentNode.replaceChild(svg, image);
	        }catch{
	            
	        }
	    })
	    .then(callback)
	    
	  });
	}

	var swiper_matches;
    $(document).ready(function() {
		swiper_matches = new Swiper('.swiper-container', {
			slidesPerView:'2.1',
			spaceBetween: 0,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
	    });

		$('.menu_btn').click(function() {
			$(this).toggleClass('open');
			$(".static_menu").toggleClass('hide');
			$(".sub_menu").toggleClass('hide');
		});

	    // menu_right();
		binduserinfo();

		window.onscroll = function(e) {
			if(window.pageYOffset >= 50){
				$(".floating_right").css("opacity",1);
				$(".fr_btn").trigger('mouseover');
			}
			else{
				$(".floating_right").css("opacity",0);
			}
		}
		convertImages('.svg');
	});

	function user_register() {
		var username = $.trim($("#txt_register_username").val());
		var password = $.trim($("#signup_password").val());
		var confirm_password = $.trim($("#signup_confirm_password").val());
		var captcha = $.trim($("#txt_register_captcha").val());

		if (username == "") {
			alert("用户名不可为空");
			return;
		}
		if (password == "") {
			alert("密码不可为空");
			return;
		}
		if (confirm_password == "") {
			alert("确认密码不可为空");
			return;
		}
		if (password != confirm_password) {
			alert("两次输入密码不一致");
			return;
		}
		if (captcha == "") {
			alert("验证码不可为空");
			return;
		}

		$.ajax({
            type: 'GET',
            url: getBackendHost() + '/service/user.php',
            data: {"action":"register", "username":encodeURIComponent(username),"password":encodeURIComponent(password),"confirm_password":encodeURIComponent(confirm_password),"captcha":encodeURIComponent(captcha)},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
            success: function (response, status, xhr) {
                if (response.status == 200) {
					Cookies.set('euid', response.euid, { expires: 30, path: '/' });
					location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
            },
        });
	}

    function userlogin() {
    	var username = $.trim($("#txtUsername").val());
		var password = $.trim($("#txtPassword").val());
		var captcha = $.trim($("#txtCaptcha").val());

		if (username == "") {
			alert("请输入用户名");
		}
		if (password == "") {
			alert("请输入密码");
		}
		if (captcha == "") {
			alert("请输入验证码");
		}

		$.ajax({
            type: 'GET',
            url: getBackendHost() + '/service/user.php',
            data: {"action":"login", "username":encodeURIComponent(username),"password":encodeURIComponent(password),"captcha":encodeURIComponent(captcha)},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
            success: function (response, status, xhr) {
                if (response.status == 200) {
					Cookies.set('euid', response.euid, { expires: 30, path: '/' });
					// binduserinfo();
					// $("#login_popup").hide();
					// $('.modal-backdrop').remove();
					location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
            },
        });
    }

    function binduserinfo() {
    	var euid = Cookies.get('euid');//window.localStorage.user_id;

	    if (euid != undefined) {

	        $('#before_login_div').css('display', 'none');
	        $('#username').css('display', '');
	        $('#logout_div').css('display', '');
	        $('.profile_btn').css('display', '');
	        $.ajax({
	            url: getBackendHost()+ '/service/user.php',
	            type: 'post',
	            data: {"action":"getuserinfo","euid":euid},
		        crossDomain: true,
		        xhrFields: {
		            withCredentials: true
		        },

	            success: function (response, status, xhr) {
	            	if (response.status == 200) {
		                var user_data = response.user;
		                var user_lvl_icon = '/assets/images/user_level_icon/lvl'+user_data.level_id+'_white.png';
						
		                $('#profile_summary_thumbnail').attr('src', user_data.image);
		                $('#header_container_thumbnail').attr('src', user_data.image);

		                $('#span_user_name').html(user_data.username);
		                $('#after_username').html(user_data.username);
		                $('#span_user_level').html(user_data.level);
		                $('#img_user_level').attr('src', user_lvl_icon);
		                $('#span_user_vouchers').html(parseInt(user_data.voucher));
		                $('#span_total_points').html(parseInt(user_data.total_points));
		                $('#span_current_points').html(parseInt(user_data.points));

		                window.localStorage.user_id = user_data.id;
		                window.localStorage.username = user_data.username;
		                window.localStorage.level = user_data.level;
		                window.localStorage.level_id = user_data.level_id;
		                window.localStorage.profile_thumbnail = user_data.image;
		                window.localStorage.voucher = parseInt(user_data.voucher);
		                window.localStorage.points = parseInt(user_data.points);
		                window.localStorage.weekly_points = user_data.weekly_points;
		                window.localStorage.total_points = parseInt(user_data.total_points);
		                window.localStorage.access_token = user_data.token;
	            	}
	            	else if (response.status == -201) {
	            		Cookies.remove('euid');
	            	}
	            },
	            error: function () {
	                alert('AJAX ERROR - get menu right');
	            },
	        });
	    } else {
	        $('#username').css('display', 'none');
	        $('#logout_div').css('display', 'none');
	        $('.profile_btn').css('display', 'none');
	        $('#before_login_div').css('display', '');
	    }
    }

    function get_live_matches() {
    	var league_id = $("#banner_dropdown").val();

        $.ajax({
            type: 'GET',
            url: getBackendHost() + '/service/match.php',
            data: {"action":"get_live_matches","league_id":league_id},
	        crossDomain: true,
	        xhrFields: {
	            withCredentials: true
	        },
            success: function (response, status, xhr) {
            	var html = template.render($("#live_matches_tpl").html(), {"data": response});
            	$("#div_live_matches").html(html);
            },
            error: function () {
            },
        });
    }

    function forget_account() {
		var phone = $("#txt_forget_account_phone").val();
		var captcha = $("#forgetac_captcha").val();

		if (phone == "") {
			$("#forgetac_msg_sent_span").html("请输入手机号码");
			return;
		}
		if (captcha == "") {
			$("#forgetac_msg_sent_span").html("请输入验证码");
			return;
		}

        $.ajax({
            url: getBackendHost()+ '/service/user.php',
            type: 'post',
            data: {"action":"forget_account","phone":phone,"captcha":captcha},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

            success: function (response, status, xhr) {
	            if (response.status == 200) {
	            	$("#forgetac_msg_sent_span").html(response.message);
	            	$("#forgetac_msg_sent_span").show();
	            	$("#img_forget_account_captcha").click();
            	}
            	else {
            		$("#forgetac_msg_sent_span").html(response.message);
            	}

            },
            error: function () {
                
            },
        });
    }

    function forget_password() {
		var phone = $("#txt_forget_pwd_phone").val();
		var captcha = $("#forgetpw_captcha").val();

		if (phone == "") {
			$("#msg_sent_span").html("请输入手机号码");
			return;
		}
		if (captcha == "") {
			$("#msg_sent_span").html("请输入手机号码");
			return;
		}

        $.ajax({
            url: getBackendHost()+ '/service/user.php',
            type: 'post',
            data: {"action":"forget_password","phone":phone,"captcha":captcha},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

            success: function (response, status, xhr) {
	            if (response.status == 200) {
					$("#txt_forget_pwd_phone").hide();
					$("#forgetpw_captcha").hide();
					$("#forgetpw-img-captcha").hide();
					$("#sms_button").hide();

	            	$("#new_password").show();
					$("#confirm_new_password").show();
					$("#verification_code").show();
					$("#verification_button").show();

	            	$("#msg_sent_span").html(response.message);
	            	$("#msg_sent_span").show();
	            	$("#forgetpw-img-captcha").click();
	            }
	            else {
	            	$("#msg_sent_span").html(response.message);
	            }
            },
            error: function () {
                
            },
        });
    }

    function reset_password() {
    	var phone = $("#txt_forget_pwd_phone").val();
    	var password = $("#new_password").val();
		var confirm_password = $("#confirm_new_password").val();
		var verification_code = $("#verification_code").val();

		if (phone == "") {
			$("#msg_sent_span").html("请输入手机号码");
			return;
		}
		if (password == "") {
			$("#msg_sent_span").html("请输入新密码");
			return;
		}
		if (confirm_password == "") {
			$("#msg_sent_span").html("请输入确认密码");
			return;
		}
		if (password != confirm_password) {
			$("#msg_sent_span").html("新密码与确认密码不一致");
			return;
		}
		if (verification_code == "") {
			$("#msg_sent_span").html("请输入验证码");
			return;
		}

        $.ajax({
            url: getBackendHost()+ '/service/user.php',
            type: 'post',
            data: {"action":"reset_password","phone":phone,"verification_code":verification_code,"password":password},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

            success: function (response, status, xhr) {
	            if (response.status == 200) {
	            	$("#forgotpw").modal("hide");
	            	alert(response.message);
	            	// $("#login_popup").modal("show");
	            }
	            else {
	            	$("#msg_sent_span").html(response.message);
	            }
            },
            error: function () {
                
            },
        });
    }
</script>
</div>
</div>

<script type="text/html" id="live_matches_tpl">
    {{each data value index}}
    	{{if index % 3 == 0 }}
		<div class="swiper-slide swiper-slide-active" style="width: 497.143px;">
		  <div class="hot_event_item">
		    <div class="hot_event_item_left_title" style="white-space: nowrap;padding-right: 60px;">{{value.league_name}}</div>
		    <div class="hot_event_item_right">
		{{/if}}
		      <div><div class="sub_title">{{value.match_type_name}}</div><div>{{value.home_team_name}}<span>{{value.home_score}}</span></div><div>{{value.away_team_name}}<span>{{value.away_score}}</span></div></div>
		{{if index % 3 == 2 || index == (total - 1) }}
		    </div>
		  </div>
		</div>
		{{/if}}
	{{/each}}
</script>