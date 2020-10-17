<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$event_id = intval($_GET['event_id']);
$chatroom_id = 0;
?>
<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <?php include_once('../layout/resource.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js" type="text/javascript"></script>
    <title>19资讯 - 首创赛事预测</title>
    <style type="text/css">
    	#toggle-icon {
            color: #ed1b34;
            text-align: center;
        }
	</style>
</head>
<body>
<?php include '../layout/header.php'; ?>
    <div id="match-select-outside">
    	<div class="match_select_outside">
			<div class="match_select layout1200">
				<div id="div_my_prediction" class="select_detail" style="display: none;">已预测 <span id="userPredictionNumber"></span> 笔赛事<div class="select_detail_btn" data-toggle="modal" data-target="#my_mpModal">查看</div></div>
				<div class="match_prediction-swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
					<div class="match_prediction-swiper-button-next"><i class="fas fa-chevron-right"></i></div>	
				<div class="match_prediction-swiper-container">
					<div class="swiper-wrapper">
						<?php
	                        $access_url = CURL_API_URL . '/service/prediction.php?action=match_prediction_carousel';
	                        $data = get_curl($access_url);
	                        $data = json_decode($data, true);
	                        $html = '';
	                        $num = 0;

	                        foreach ($data as $key => $value) {
	                        	if (!$event_id) {
		                        	if ($num == 0) {
		                        		$event_id = $value['id'];
		                        	}
	                        	}
	                        	$html .= '<div class="swiper-slide">';
								$html .= '<div class="match_prediction" id="match_prediction_'.$value['id'].'" style=\'cursor:pointer;\' onclick="get_prediction('.$value['id'].');">';
								$html .= '<div class="title">'.$value['league_name'].'</div>';
								$html .= '<div class="date">'.$value['match_at'].'</div>';
								$html .= '<div class="team">';
								$html .= '<div class="team_logo">';
								$html .= '<img src="'.$value['home_team_image'].'" style="height: 80px; width: 80px;">';
								$html .= '<span>'.$value['home_team_name'].'</span>';
								$html .= '</div>';
								$html .= '<div>VS</div>';
								$html .= '<div class="team_logo">';
								$html .= '<img src="'.$value['away_team_image'].'" style="height: 80px; width: 80px;">';
								$html .= '<span>'.$value['away_team_name'].'</span>';
								$html .= '</div>';
								$html .= '</div>';
								$html .= '</div>';
								$html .= '</div>';
								$num++;
	                        }
	                        echo $html;
						?>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div id="main-area" class="main_area">
    	<div class="index_p8 layout1200">
		  <div class="member_list_area">
			<div class="title_area2 style2"><span>神级预言家这样说<img class="title_area2_icon svg" src="/assets/images/icon-talk.svg" type="image/svg+xml" /> </span>
			    <div>TOP5</div>
			</div>
			<div class="member_list_outside" id="top-ten">
				<div class="member_list_item"><div class="text">数据加载中...</div></div>
			</div>
		  </div>
		  <div class="match_detail_area">
			  <div id="div_main_area">
				<?php
					$access_url = CURL_API_URL . '/service/prediction.php?action=get_prediction_info&event_id='.$event_id;
					$data = get_curl($access_url);
					$data = json_decode($data, true);
					$chatroom_id = $data['chatroom_id'];
				?>
				<div class="match_prediction ">
				  <div class="title"><?php echo $data['league_name']; ?></div>
				  <div class="date"><?php echo $data['match_at']; ?></div>
				  <div class="team">
					<div class="team_logo">
					  <img src="<?php echo $data['home_team_image']; ?>" style="height: 90px; width: 90px;" onerror="this.onerror=null;this.src='/assets/images/default_no_image.png';">
					  <span><?php echo $data['home_team_name']; ?></span></div>
					<div>
						<?php 
						if ($data['category_id'] == 1) {
							echo '第 '.$data['round'].' 轮';
						}
						else {
							echo $data['round']; 
						}
						?>
						<br>VS<br><?php echo $data['match_at']; ?>
					</div>
					<div class="team_logo">
					  <img src="<?php echo $data['away_team_image']; ?>" style="height: 90px; width: 90px;" onerror="this.onerror=null;this.src='/assets/images/default_no_image.png';">
					  <span><?php echo $data['away_team_name']; ?></span></div>
				  </div>
				</div>
				<div class="match_prediction_p2_wrapper">
				  <div class="match_prediction_p2">
					<div>
					  <div><?php echo $data['home_team_name']; ?> (主队)</div>
					  <div><?php echo $data['away_team_name']; ?> (客队)</div></div>
					<div class="right_item">
					  <?php if (in_array($data['category_id'], [1, 2])) { ?>
					  <div>让球</div>
					  <div class="handicap-bet" id="handicap_home" style="cursor:pointer;" onclick="handicap_home=1;handicap_away=0;proceed_prediction();"><?php echo $data['handicap_home_bet']; ?>
						<span><?php echo $data['handicap_home_odds']; ?></span></div>
					  <div class="handicap-bet" id="handicap_away" style="cursor:pointer;" onclick="handicap_home=0;handicap_away=1;proceed_prediction();"><?php echo $data['handicap_away_bet']; ?>
						<span><?php echo $data['handicap_away_odds']; ?></span></div>
					  <?php } ?>
					</div>
					<div class="right_item">
					  <?php if (in_array($data['category_id'], [1, 2])) { ?>
					  <div>大小</div>
					  <div class="over_under-bet" id="over_under_home" style="cursor:pointer;" onclick="over_under_home=1;over_under_away=0;proceed_prediction();"><?php echo $data['over_under_home_bet']; ?>
						<span><?php echo $data['over_under_home_odds']; ?></span></div>
					  <div class="over_under-bet" id="over_under_away" style="cursor:pointer;" onclick="over_under_home=0;over_under_away=1;proceed_prediction();"><?php echo $data['over_under_away_bet']; ?>
						<span><?php echo $data['over_under_away_odds']; ?></span></div>
					  <?php } ?>
					</div>
					<div class="right_item">
					  <div>独赢</div>
					  <div class="single-bet" id="single_home" style="cursor:pointer;" onclick="single_home=1;single_tie=0;single_away=0;proceed_prediction();">主<span><?php echo $data['single_home']; ?></span></div>
					  <?php if ($data['category_id'] == 1) { ?>
						<div class="single-bet" id="single_tie" style="cursor:pointer;" onclick="single_home=0;single_tie=1;single_away=0;proceed_prediction();">和<span><?php echo $data['single_tie']; ?></span></div>
					  <?php } ?>
					  <div class="single-bet" id="single_away" style="cursor:pointer;" onclick="single_home=0;single_tie=0;single_away=1;proceed_prediction();">客<span><?php echo $data['single_away']; ?></span></div>
					</div>
				  </div>
				  <div style="text-align:right;color:grey;">
					<small>此预测功能为提供体育爱好者休闲抒心之目的，切勿以此作任何违法用途。</small></div>
				</div>
				<div class="text" id="editor">
				  <div id="editor_note" style="display: block;">
					<?php 
					echo $data['editor_note']; 
					?>
				  </div>
				  <div id="toggle-icon" style="display: block; cursor: pointer;">预测分析
					<i class="fas fa-chevron-up" style="cursor:pointer;"></i></div>
				</div>
			  </div>
			  <?php include('../module/message.php'); ?>
		  </div>
			
		</div>
    </div>
<?php include '../layout/footer.php'; ?>
    <div class="modal" id="unlock_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
    	<div class="modal-dialog modal-dialog-centered" role="document">
		  <div class="modal-content">
		    <div class="modal-header" style="border-bottom: 0px" >
		      <h5 class="modal-title" id="exampleModalLongTitle">解锁预测</h5>
		    </div>
		    <div class="modal-body">
		      <i class="fas fa-unlock unlock " ></i>
		        <div>是否使用1张兑换光解锁预测？</div>
		    </div>
		    <div class="modal-footer"  style="justify-content: space-evenly;">
		      <button type="button" onclick="$('#unlock_popup').modal('hide');$('.modal-backdrop').remove();" class="cancel_btn" id="voucher-cancel-button">取消</button> <button type="button" class="submit_btn" id="voucher-submit-button" onclick="unlock_predictor();">解锁</button>
		    </div>
		  </div>
		</div>
    </div>
    <div class="modal" id="member_list_more" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true"></div>

    <div class="modal" id="my_mpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
    	
    </div>

	<div class="modal fade" id="tut_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			 
			  <div class="modal-body">
				<div class="tut_1">
				  <img class="tut_img" src="../../assets/images/tut/1.png">
				  <div class="tut_text">
					步骤1：筛选赛事<br> 左右滑查看您想要预测的赛事，并选择
					<img class="tut_arrow" src="../../assets/images/tut/tut_arrow.png">
					<div class="tut_button_area"><button class="cancel_btn" onclick="closeTutorialButton()">关闭</button><button class="submit_btn gotut2">下一步</button></div>
					
				  </div>
				</div>

				<div class="tut_2">
				  <img class="tut_img" src="../../assets/images/tut/2.png">
				  <div class="tut_text">
					步骤2：点选预测<br> 各栏位都能进行2/3选1的猜测，<br> 点选后将会用黄色标记选项
					<img class="tut_arrow" src="../../assets/images/tut/tut_arrow.png">
					<div class="tut_button_area"><button class="cancel_btn" onclick="closeTutorialButton()">关闭</button><button class="submit_btn gotut3">下一步</button></div>
					
				  </div>
				</div>
				<div class="tut_3">
				  <img class="tut_img" src="../../assets/images/tut/3.png">
				  <div class="tut_text">
					步骤3：查看并送出选项<br> 在此能够查看，编辑与送出您最终的预测 <br><span>*注：比赛开始前的1分钟还是能够修改预测哟！</span>
					<img class="tut_arrow" src="../../assets/images/tut/tut_arrow.png">
					<div class="tut_button_area"><button class="cancel_btn" onclick="closeTutorialButton()">关闭</button><button class="submit_btn gotut4">下一步</button></div>
					
				  </div>
				</div>
				<div class="tut_4">
				  <img class="tut_img" src="../../assets/images/tut/4.png">
				  <div class="tut_text">
					步骤4：赛事结果 <br>比赛结束后系统将自动把赛事拉入列表
					<img class="tut_arrow" src="../../assets/images/tut/tut_arrow.png">
					<div class="tut_button_area"><button class="cancel_btn" onclick="closeTutorialButton()">关闭</button><button class="submit_btn gotut5">下一步</button></div>
					
				  </div>
				</div>
				<div class="tut_5">
				  <img class="tut_img" src="../../assets/images/tut/5.png">
				  <div class="tut_text">
					不知道该选什么好？<br> 您可以参考神级预言家看专家们怎么说，<br> 甚至能跟随他们的预测哦！ <br>只要使用预测卷即可得到资料哟~
					<img class="tut_arrow" src="../../assets/images/tut/tut_arrow.png">
					<div class="tut_button_area"><button class="cancel_btn" onclick="closeTutorialButton()">关闭</button><button class="submit_btn gotut1">再看一次</button></div>
					
				  </div>
				</div>
			  </div>
		   
			</div>
		</div>
	</div>
<body>
</html>


<script type="text/javascript">
	var event_id = <?php echo $event_id; ?>;
	var top_ten_id = 0;

	var handicap_home = 0;
	var handicap_away = 0;
	var over_under_home = 0;
	var over_under_away = 0;
	var single_home = 0;
	var single_tie = 0;
	var single_away = 0;

    $(document).ready(function() {
        $("#match_prediction_" + event_id).addClass("selected");

		$(".gotut2").click(function(){
			$(".tut_1").hide();
			$(".tut_2").show();
			$("#tut_popup").animate({ scrollTop: 400}, 600);
		});
		$(".gotut3").click(function(){
			$(".tut_2").hide();
			$(".tut_3").show();
			$("#tut_popup").animate({ scrollTop: 0}, 600);
		});
		$(".gotut4").click(function(){
			$(".tut_3").hide();
			$(".tut_4").show();
		});
		$(".gotut5").click(function(){
			$(".tut_4").hide();
			$(".tut_5").show();
		});
		$(".gotut1").click(function(){
			$(".tut_5").hide();
			$(".tut_1").show();
		});

    	if (Cookies.get('tutorial') == undefined) {
    		$("#tut_popup").modal("show");
    	}

		Cookies.set('tutorial', 1, { expires: 30, path: '/' });

		var swiper = new Swiper('.match_prediction-swiper-container', {
			slidesPerView:'3.5',
			spaceBetween: 30,
			navigation: {
				nextEl: '.match_prediction-swiper-button-next',
				prevEl: '.match_prediction-swiper-button-prev',

			},
		});

        $("#toggle-icon").click(function(){
            $(this).children('.fas').toggleClass('fa-chevron-down fa-chevron-up');
            var content = this.previousElementSibling;
            if (content.style.display == "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
        get_cart_list();
        get_my_option();
        get_top_five();
    });

    function get_my_option () {
    	var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		return;
    	}
    	$("#div_my_prediction").show();
        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"get_my_option","event_id":event_id,"euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	if (response != null) {
            		if (response.handicap_home == 1) {
            			handicap_home = 1;
            			$("#handicap_home").addClass("active");
            		}
            		else {
            			handicap_home = 0;
            			$("#handicap_home").removeClass("active");
            		}
            		if (response.handicap_away == 1) {
            			handicap_away = 1;
            			$("#handicap_away").addClass("active");
            		}
            		else {
            			handicap_away = 0;
            			$("#handicap_away").removeClass("active");
            		}
            		if (response.over_under_home == 1) {
            			over_under_home = 1;
            			$("#over_under_home").addClass("active");
            		}
            		else {
            			over_under_home = 0;
            			$("#over_under_home").removeClass("active");
            		}
            		if (response.over_under_away == 1) {
            			over_under_away = 1;
            			$("#over_under_away").addClass("active");
            		}
            		else {
            			over_under_away = 0;
            			$("#over_under_away").removeClass("active");
            		}
            		if (response.single_home == 1) {
            			single_home = 1;
            			$("#single_home").addClass("active");
            		}
            		else {
            			single_home = 0;
            			$("#single_home").removeClass("active");
            		}
            		if (response.single_away == 1) {
            			single_away = 1;
            			$("#single_away").addClass("active");
            		}
            		else {
            			single_away = 0;
            			$("#single_away").removeClass("active");
            		}
            		if (response.single_tie == 1) {
            			single_tie = 1;
            			$("#single_tie").addClass("active");
            		}
            		else {
            			single_tie = 0;
            			$("#single_tie").removeClass("active");
            		}
            	}
            	else {
            		handicap_home = 0;
					handicap_away = 0;
					over_under_home = 0;
					over_under_away = 0;
					single_home = 0;
					single_tie = 0;
					single_away = 0;
					$("#handicap_home").removeClass("active");
					$("#handicap_away").removeClass("active");
					$("#over_under_home").removeClass("active");
					$("#over_under_away").removeClass("active");
					$("#single_home").removeClass("active");
					$("#single_tie").removeClass("active");
					$("#single_away").removeClass("active");
            	}
            },
            error: function () {

            },
        });
    }

    function proceed_prediction () {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		$("#login_popup").modal("show");
    		return;
    	}

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"proceed_prediction","event_id":event_id,"euid":euid,"handicap_home":handicap_home,"handicap_away":handicap_away,"over_under_home":over_under_home,"over_under_away":over_under_away,"single_home":single_home,"single_away":single_away,"single_tie":single_tie},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
			    if ($('#member_list_more').css('display') != 'none') {
		    		$('#member_list_more').modal('hide');
		    		$('.modal-backdrop').remove();
		    	}
            	if (response.status == 200) {
            		alert("已加入预测选单！\r\n记得点选右上角 “查看” 按钮，并确认送出选项哟～");
            		get_my_option();
            		get_cart_list();
            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
            	}
            },
            error: function () {

            },
        });
    }

    function get_cart_list() {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		return;
    	}
    	$.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"get_cart_list","euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	$("#userPredictionNumber").html(response.length);
            	var html = template.render($("#cart_list_tpl").html(), {"data": response});
				$("#my_mpModal").html(html);
            	$("#div_my_prediction").show();
            },
            error: function () {

            },
        });
    }

    function get_top_five() {
	    $("#top-ten").html('<div class="member_list_item"><div class="text">数据加载中...</div></div>');
		var euid = Cookies.get("euid");
		var json_data = {};
    	if (euid == undefined) {
    		json_data = {"action":"get_top_five","event_id":event_id};
    	}
    	else {
    		json_data = {"action":"get_top_five","event_id":event_id,"euid":euid};
    	}
    	$.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: json_data,
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	// console.log(response);
            	var html = template.render($("#top_five_tpl").html(), {"data": response});
            	$("#top-ten").html(html);
            },
            error: function () {

            },
        });
    }

    function get_prediction(id) {
        $("#match_prediction_" + event_id).removeClass("selected");
        $("#match_prediction_" + id).addClass("selected");
        event_id = id;

    	$.ajax({
            url: getBackendHost() + '/service/prediction.php?action=get_prediction_info&event_id='+event_id,
            type: 'get',

            success: function (response, status, xhr) {
            	var html = template.render($("#main_area_tpl").html(), {"data": response});
            	$("#div_main_area").html(html);

		        $("#toggle-icon").click(function(){
		            $(this).children('.fas').toggleClass('fa-chevron-down fa-chevron-up');
		            var content = this.previousElementSibling;
		            if (content.style.display == "block") {
		                content.style.display = "none";
		            } else {
		                content.style.display = "block";
		            }
		        });
		        get_cart_list();
		        get_my_option();
		        get_top_five();
				
				chatroom_id = response.chatroom_id;
				
				get_comments();
            },
            error: function () {

            },
        });
    }

    function predictionSuccess() {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		return;
    	}

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"confirm_prediction","euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	if (response.status == 200) {
            		$('#my_mpModal').modal('hide');
	    			$('.modal-backdrop').remove();
            		alert(response.message);
            		get_cart_list();
            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
            	}
            },
            error: function () {

            },
        });
    }

    function cancelPrediction (id) {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		return;
    	}

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"cancel_prediction","event_id":id,"euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	if (response.status == 200) {
            		get_cart_list();
            		// $("#div_mp_item_" + event_id).remove();
            		// $("#userPredictionNumber").html(parseInt($("#userPredictionNumber").html())-1);
            		get_my_option();
            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
            	}
            },
            error: function () {

            },
        });
    }

    function edit_options (id) {
    	if ($('#my_mpModal').css('display') != 'none') {
    		$('#my_mpModal').modal('hide');
    	}
	    if ($('#member_list_more').css('display') != 'none') {
    		$('#member_list_more').modal('hide');
    	}
	    $('.modal-backdrop').remove();
	    if (id != event_id) {
	    	get_prediction(id);
	    }
	    event_id = id;
	    $('html,body').animate({ scrollTop: $(".match_prediction_p2 ").offset().top - 202 }, 'slow');
    }

	function closeTutorialButton() {
	    $("#tut_popup").removeClass("in");
	    $(".modal-backdrop").remove();
	    $('body').removeClass('modal-open');
	    $('body').css('padding-right', '');
	    $("#tut_popup").hide();
	}

	function show_unlock_popup(id) {
		top_ten_id = id;
		$('#unlock_popup').modal('show');
	}

	function unlock_predictor () {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		$("#login_popup").modal("show");
    		return;
    	}

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"unlock_predictor","event_id":event_id,"euid":euid,"top_ten_id":top_ten_id},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	if (response.status == 200 || response.status == 201) {
            		$('#unlock_popup').modal('hide');
            		$('.modal-backdrop').remove();
            		binduserinfo();
            		get_top_five();
            		alert(response.message);

            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
            	}
            },
            error: function () {

            },
        });
	}

	function get_top_five_options(predictor_id, event_id) {
		var euid = Cookies.get("euid");
    	if (euid == undefined) {
    		$("#login_popup").modal("show");
    		return;
    	}

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"get_predictor_option","event_id":event_id,"euid":euid,"predictor_id":predictor_id},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	if (response.status == 200) {
	            	var html = template.render($("#top_ten_option_tpl").html(), {"data": response.data});
	            	$("#member_list_more").html(html);
	            	$('#member_list_more').modal('show');
            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
            	}
            },
            error: function () {

            },
        });
	}
</script>

<script type="text/html" id="cart_list_tpl">
	<div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	        <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLongTitle">我的预测</h5>
	        </div>
	        <div class="modal-body">
	        <div class="mp_item_area">
	          {{if (data.length == 0) }}
	          您尚未预测任何赛事
	          {{/if}}
	          {{each data value index}}
	          <div class="mp_item" id="div_mp_item_{{value.event_id}}">
	              <div class="top">
	                  <div class="title">{{value.home_team_name}} VS {{value.away_team_name}}</div>
	                  <div>{{value.league_name}}<span>{{value.match_at}}</span></div>
	              </div>
	              <div class="bottom">
	                  <table>
	                      <tr>
	                        <td>让球</td>
	                        <td>
	                          {{if (value.handicap_home > 0)}}
	                            <div>{{value.home_team_name}}<span>{{value.handicap_home_value}}</span><i class="far fa-star"></i></div>
	                          {{else if(value.handicap_away > 0)}}
	                            <div>{{value.away_team_name}}<span>{{value.handicap_away_value}}</span><i class="far fa-star"></i></div>
	                          {{/if}}
	                        </td>
	                        <td rowspan="3"><div class="edit_btn edit-match-button" onclick="edit_options({{value.event_id}});"><img src="/assets/images/edit_icon.png"><br>修<br>改</div></td>
	                      </tr>
	                      <tr>
	                        <td>大小</td>
	                        <td>
	                          {{if (value.over_under_home > 0)}}
	                            <div>{{value.home_team_name}}<span>{{value.over_under_home_value}}</span><i class="far fa-star"></i></div>
	                          {{else if(value.over_under_away > 0)}}
	                            <div>{{value.away_team_name}}<span>{{value.over_under_away_value}}</span><i class="far fa-star"></i></div>
	                          {{/if}}
	                        </td>
	                      </tr>
	                      <tr>
	                        <td>独赢</td>
	                        <td>
								{{if (value.single_home > 0)}}
								<div><span>主</span><i class="far fa-star"></i></div>
								{{else if(value.single_tie > 0)}}
								<div><span>和</span><i class="far fa-star"></i></div>
								{{else if(value.single_away > 0)}}
								<div><span>客</span><i class="far fa-star"></i></div>
								{{/if}}
	                        </td>
	                      </tr>
	                  </table>
	              </div>
	              <div class="mp_item_close" onclick="cancelPrediction({{value.event_id}})">
	                <i class="fas fa-times-circle"></i>
	              </div>
	          </div>
	          {{/each}}
	        </div>
	        </div>
	        {{if (data.length > 0) }}
	        <div class="modal-footer">
	            <button type="button" class="submit_btn" onclick="predictionSuccess()">送出</button>
	        </div>
	        {{/if}}
	    </div>
	</div>
</script>

<script type="text/html" id="top_five_tpl">
	{{if (data.length > 0)}}
	    {{each data value index}}
	    <div class="member_list_item">
	        <img src={{value.image}} style="height: 50px; width: 50px;">
	        <div class="text">
	            <div class="username">{{value.username}}</div>
	            【我的预测是...】
	            {{if (value.unlocked == "1") }}
	            <i class="fas fa-unlock unlock unlock-voucher" style='cursor:pointer;' onclick="get_top_five_options({{value.user_id}}, {{value.event_id}});"></i>
	            {{else}}
	            <i class="fas fa-lock lock lock-voucher" style='cursor:pointer;' onclick="show_unlock_popup({{value.id}});"></i>
	            {{/if}}
	        </div>
	    </div>
	    {{/each}}
	{{else}}
	    <div class="member_list_item">
	        <div class="text">
	            【暂无神级预言家预测这场比赛】
	        </div>
	    </div>
	{{/if}}
</script>

<script type="text/html" id="main_area_tpl">
    <div class="match_prediction ">
      <div class="title">{{data.league_name}}</div>
      <div class="date">{{data.match_at}}</div>
      <div class="team">
        <div class="team_logo">
          <img src="{{data.home_team_image}}" style="height: 90px; width: 90px;" onerror="this.onerror=null;this.src='/assets/images/default_no_image.png';">
          <span>{{data.home_team_name}}</span></div>
        <div>
        	{{ if (data.category_id == 1 ) }}
        	第 {{data.round}} 轮
        	{{else}}
        	{{data.round}}
        	{{/if}}
        	<br>VS<br>{{data.match_at}}
        </div>
        <div class="team_logo">
          <img src="{{data.away_team_image}}" style="height: 90px; width: 90px;" onerror="this.onerror=null;this.src='/assets/images/default_no_image.png';">
          <span>{{data.away_team_name}}</span></div>
      </div>
    </div>
    <div class="match_prediction_p2_wrapper">
      <div class="match_prediction_p2">
        <div>
          <div>{{data.home_team_name}} (主队)</div>
          <div>{{data.away_team_name}} (客队)</div></div>
        <div class="right_item">
          {{ if (data.category_id == 1 || data.category_id == 2) }}
          <div>让球</div>
          <div class="handicap-bet" id="handicap_home" style="cursor:pointer;" onclick="handicap_home=1;handicap_away=0;proceed_prediction();">
          	{{data.handicap_home_bet}}
            <span>{{data.handicap_home_odds}}</span></div>
          <div class="handicap-bet" id="handicap_away" style="cursor:pointer;" onclick="handicap_home=0;handicap_away=1;proceed_prediction();">
          	{{data.handicap_away_bet}}
            <span>{{data.handicap_away_odds}}</span></div>
          {{/if}}
        </div>
        <div class="right_item">
          {{ if (data.category_id == 1 || data.category_id == 2) }}
          <div>大小</div>
          <div class="over_under-bet" id="over_under_home" style="cursor:pointer;" onclick="over_under_home=1;over_under_away=0;proceed_prediction();">
          	{{ data.over_under_home_bet }}
            <span>{{ data.over_under_home_odds }}</span></div>
          <div class="over_under-bet" id="over_under_away" style="cursor:pointer;" onclick="over_under_home=0;over_under_away=1;proceed_prediction();">
          	{{ data.over_under_away_bet }}
            <span>{{ data.over_under_away_odds }}</span></div>
          {{/if}}
        </div>
        <div class="right_item">
          <div>独赢</div>
          <div class="single-bet" id="single_home" style="cursor:pointer;" onclick="single_home=1;single_tie=0;single_away=0;proceed_prediction();">主<span>
          {{ data.single_home }}</span></div>
          {{ if (data.category_id == 1) }}
          	<div class="single-bet" id="single_tie" style="cursor:pointer;" onclick="single_home=0;single_tie=1;single_away=0;proceed_prediction();">和<span>
          	{{ data.single_tie }}</span></div>
      	  {{/if}}
          <div class="single-bet" id="single_away" style="cursor:pointer;" onclick="single_home=0;single_tie=0;single_away=1;proceed_prediction();">客<span>
          {{ data.single_away }}</span></div>
        </div>
      </div>
      <div style="text-align:right;color:grey;">
        <small>此预测功能为提供体育爱好者休闲抒心之目的，切勿以此作任何违法用途。</small></div>
    </div>
    <div class="text" id="editor">
      <div id="editor_note" style="display: block;">
      	{{@data.editor_note}}
      </div>
      <div id="toggle-icon" style="display: block; cursor: pointer;">预测分析
        <i class="fas fa-chevron-up" style="cursor:pointer;"></i></div>
    </div>
</script>

<script type="text/html" id="top_ten_option_tpl">
<div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
    <div class="modal-header" style="border-bottom: 0px" >
      <h5 class="modal-title" id="exampleModalLongTitle">{{data.username}}这样说</h5>
    </div>
      <div class="modal-title2" >{{data.league_name}}
      {{if(data.category_id == "1")}}
      第 {{data.round}} 轮
      {{else}}
      {{data.round}}
      {{/if}} - {{data.home_team_name}} VS {{data.away_team_name}}</div>
    <div class="modal-body">
      <div class="match_prediction_p2 ">
        <div style="30%;">
          <div>{{data.home_team_name}}(主队)</div>
          <div>{{data.away_team_name}}（客队）</div>
        </div>
        <div class="right_item">
          <div>让球</div>
          <div class="handicap-bet-topTen {{data.handicap_home>0?'active activeSelected':''}}" id="handicap_home_topTen">{{data.handicap_home_bet}} <span>{{data.handicap_home_odds}}</span> </div>
          <div class="handicap-bet-topTen {{data.handicap_away>0?'active activeSelected':''}}" id="handicap_away_topTen">{{data.handicap_away_bet}} <span>{{data.handicap_away_odds}}</span> </div>
        </div>
        <div class="right_item">
          <div>大小</div>
          <div class="over_under-bet-topTen {{data.over_under_home>0?'active activeSelected':''}}" id="over_under_home_topTen">{{data.over_under_home_bet}} <span>{{data.over_under_home_odds}}</span> </div>
          <div class="over_under-bet-topTen {{data.over_under_away>0?'active activeSelected':''}}" id="over_under_away_topTen">{{data.over_under_away_bet}} <span>{{data.over_under_away_odds}}</span> </div>
        </div>
        <div class="right_item win">
          <div>独赢</div>
          <div class="single-bet-topTen {{data.single_home>0?'active activeSelected':''}}" id="single_home_topTen">主  <span>{{data.single_home_value}}</span> </div>
        {{if(data.category_id == "1")}}
          <div class="single-bet-topTen {{data.single_tie>0?'active activeSelected':''}}" id="single_tie_topTen">和  <span>{{data.single_tie_value}}</span> </div>
        {{/if}}
          <div class="single-bet-topTen {{data.single_away>0?'active activeSelected':''}}" id="single_away_topTen">客 <span>{{data.single_away_value}}</span> </div>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="justify-content: space-evenly;">
      <button type="button" class="submit_btn" onclick="edit_options({{data.event_id}});">自定</button> <button type="button" class="submit_btn" onclick="handicap_home={{data.handicap_home}};handicap_away={{data.handicap_away}};over_under_home={{data.over_under_home}};over_under_away={{data.over_under_away}};single_home={{data.single_home}};single_tie={{data.single_tie}};single_away={{data.single_away}};proceed_prediction();">跟随</button>
    </div>
  </div>
</div>
</script>