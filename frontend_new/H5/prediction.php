<?php
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<?php 
    include("style_script.php"); 
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 首创赛事预测</title>
<?php
	
	$event_id = intval($_GET['event_id']);
	$chatroom_id = 0;
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">zZz

        <div class="body_container">
            <div class="prediction_container">
                <div class="prediction_menu">
                    <button class="active" onclick="window.location='prediction.php'">赛事预测</button>
                    <button class="button_style_grey" onclick="window.location='prediction_result.php'" style="cursor:pointer;">赛事结果</button>
                    <a href="#" class="prediction_guide"><img src="img/question_mark_icon.png">教学</a>
                </div>

                <div class="prediction_selected" id="div_my_prediction" style="display:none;">
                    <div>已选择 <span id="prediction_selected"></span> 笔预测</div>
                    <button onclick="window.location='my_prediction.php'" style="cursor:pointer;">查看</button>
                </div>

                <div class="prediction_team_container">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
							<?php
								$access_url = CURL_API_URL . '/service/prediction.php?action=match_prediction_carousel';
								$data = get_curl($access_url);
								$data = json_decode($data, true);
								$html = '';
								$selected = "";
								$num = 0;

								foreach ($data as $key => $value) {
									if (!$event_id) {
										if ($num == 0) {
											$event_id = $value['id'];
											$selected = "selected";
										}
									}else if($event_id == $value['id']){
										$selected = "selected";
									}
									
									if($num % 2 == 0){
										$html .= '<div class="swiper-slide">';
										$html .= '<div class="index_competition_group">';
									}

									$html .= '<div class="match_prediction '.$selected.'" id="match_prediction_'.$value['id'].'" style=\'cursor:pointer;\' onclick="get_prediction('.$value['id'].');">';
									$html .= '<div class="title">'.$value['league_name'].'</div>';
									$html .= '<div class="datetime">'.$value['match_at'].'</div>';
									$html .= '<div class="team">';
									$html .= '<div class="main">';
									$html .=	'<div class="border"><img src="'.IMAGE_URL.$value['home_team_image'].'"></div>'.$value['home_team_name'].'</div>';
									$html .= '<div class=versus><img src="img/home/vs.png"></div>';
									$html .= '<div class="away"><div class="border"><img src="'.IMAGE_URL.$value['away_team_image'].'"></div>'.$value['away_team_name'].'</div></div>';
									$html .= '</div>';
									
									if($num % 2 == 1 || $num == (count($data) - 1)){
										$html .= '</div>';
										$html .= '</div>';
									}
									
									$selected = "";
									$num++;
								}
								echo $html;
							?>

<!--
                            <div class="swiper-slide">
                                <div class="index_competition_group">
                                    <div>
                                        <div class="title">德甲联赛</div>
                                        <div class="datetime">06-09 19:00:00</div>
                                        <div class="team">
                                            <div class="main">
                                                <div class="border"><img src="img/home/team.png"><div>主队</div></div>
                                                奥格斯堡
                                            </div>
                                            <div class=versus><img src="img/home/vs.png"></div>
                                            <div class="away">
                                                <div class="border"><img src="img/home/team.png"></div>
                                                拜仁慕尼黑
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="title">德甲联赛</div>
                                        <div class="datetime">06-09 19:00:00</div>
                                        <div class="team">
                                            <div class="main">
                                                <div class="border"><img src="img/home/team.png"><div>主队</div></div>
                                                奥格斯堡
                                            </div>
                                            <div class=versus><img src="img/home/vs.png"></div>
                                            <div class="away">
                                                <div class="border"><img src="img/home/team.png"></div>
                                                拜仁慕尼黑
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
-->

                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

				<div id="div_main_area">
					<div class="prediction_team_picked">
						<?php
							$access_url = CURL_API_URL . '/service/prediction.php?action=get_prediction_info&event_id='.$event_id;
							$data = get_curl($access_url);
							$data = json_decode($data, true);
							$chatroom_id = $data['chatroom_id'];
						?>
						<div class="title"><?php echo $data['league_name']; ?></div>
						<div class="prediction_team_picked_container">
							<div>
								<div class="img"><img src="<?php echo IMAGE_URL.$data['home_team_image']; ?>"></div>
								<div><?php echo $data['home_team_name']; ?><br>（主队）</div>
							</div>
							<div>
								<div>
									<?php 
									if ($data['category_id'] == 1) {
										echo '第 '.$data['round'].' 轮';
									}
									else {
										echo $data['round']; 
									}
									?>
								</div>
								<div class="datetime"><?php echo $data['match_at']; ?></div>
								<div><img src="img/prediction/prediction_vs.png"></div>
							</div>
							<div>
								<div class="img"><img src="<?php echo IMAGE_URL.$data['away_team_image']; ?>"></div>
								<div><?php echo $data['away_team_name']; ?><br>（客队）</div>
							</div>
						</div>
					</div>

					<div class="prediction_bet">
						<div class="block_content_row">
							<div class="block_content title"></div>
							<div class="block_content title">让球</div>
							<div class="block_content title">大小</div>
							<div class="block_content title">独赢</div>
						</div>

						<div class="block_content_row">
							<div class="block_content"><?php echo $data['home_team_name']; ?><br>（主队）</div>
							<?php if (in_array($data['category_id'], [1, 2])) { ?>
								<div class="block_content">
									<div class="bet_box handicap-bet" data-id="handicap_home" id="handicap_home">
										<div class="bet"><?php echo $data['handicap_home_bet']; ?></div>
										<div class="num"><?php echo $data['handicap_home_odds']; ?></div>
									</div>
								</div>
								<div class="block_content">
									<div class="bet_box over_under-bet" data-id="over_under_home" id="over_under_home">
										<div class="bet"><?php echo $data['over_under_home_bet']; ?></div>
										<div class="num"><?php echo $data['over_under_home_odds']; ?></div>
									</div>
								</div>
							<?php } else { ?>
								<div class="block_content"></div>
								<div class="block_content"></div>
							<?php } ?>
							<div class="block_content">
								<div class="bet_box single-bet" data-id="single_home" id="single_home">
									<div class="bet">主</div>
									<div class="num"><?php echo $data['single_home']; ?></div>
								</div>
							</div>
						</div>

						<div class="block_content_row">
							<div class="block_content"><?php echo $data['away_team_name']; ?><br>（客队）</div>
							<?php if (in_array($data['category_id'], [1, 2])) { ?>
							<div class="block_content">
								<div class="bet_box handicap-bet" data-id="handicap_away" id="handicap_away">
									<div class="bet"><?php echo $data['handicap_away_bet']; ?></div>
									<div class="num"><?php echo $data['handicap_away_odds']; ?></div>
								</div>
							</div>
							<div class="block_content">
								<div class="bet_box over_under-bet" data-id="over_under_away" id="over_under_away">
									<div class="bet"><?php echo $data['over_under_away_bet']; ?></div>
									<div class="num"><?php echo $data['over_under_away_odds']; ?></div>
								</div>
							</div>
							<?php } else { ?>
								<div class="block_content"></div>
								<div class="block_content"></div>
							<?php } ?>
							<div class="block_content">
								<div class="bet_box single-bet" data-id="single_away" id="single_away">
									<div class="bet">客</div>
									<div class="num"><?php echo $data['single_away']; ?></div>
								</div>
							</div>
						</div>

						<?php if ($data['category_id'] == 1) { ?>
						<div class="block_content_row">
							<div class="block_content"></div>
							<div class="block_content"></div>
							<div class="block_content"></div>
							<div class="block_content">
								<div class="bet_box single-bet" data-id="single_tie" id="single_tie">
									<div class="bet">和</div>
									<div class="num"><?php echo $data['single_tie']; ?></div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="prediction_analysis_container">
						<?php 
						echo $data['editor_note']; 
						?>
						<div class="prediction_analysis_accordion">预测分析 <span class="fa fa-chevron-down"></span></div>
					</div>
				</div>
				
				<?php include("comments.php"); ?>

                <div class="pro_prediction_title">
                    <div>神级预言家这样说
                        <img src="img/chat_icon.svg">
                    </div>
                    <div>TOP5</div>
                </div>

                <div class="pro_prediction_list">
<!--
                    <div onclick="window.location='pro_prediction_said.php'">
                        <div class="img"><img src="img/default_user_image.png"></div>
                        <div class="comment">
                            <div class="title">张路 <img src="img/prediction/metal_icon.png"> 8胜1负</div>
                            <div class="desc">
                                <div>皇家马德里vs皇家社会</div>
                                <div>我的预言是...</div>
                            </div>
                        </div>
                        <div class="lock">
                            <div class="pro_prediction_lock_icon"></div>
                        </div>
                    </div>
                    <div onclick="window.location='pro_prediction_said.php'">
                        <div class="img"><img src="img/default_user_image.png"></div>
                        <div class="comment">
                            <div class="title">张路 <img src="img/prediction/metal_icon.png"> 8胜1负</div>
                            <div class="desc">
                                <div>皇家马德里vs皇家社会</div>
                                <div>我的预言是...</div>
                            </div>
                        </div>
                        <div class="lock">
                            <div class="pro_prediction_lock_icon locked"></div>
                        </div>
                    </div>
-->
                </div>

                <script>
					
					var chatroom_id = <?php echo $chatroom_id; ?>;
					var event_id = <?php echo $event_id; ?>;
					var top_ten_id = 0;

					var handicap_home = 0;
					var handicap_away = 0;
					var over_under_home = 0;
					var over_under_away = 0;
					var single_home = 0;
					var single_tie = 0;
					var single_away = 0;
					
                    $(function(){
                        var swiper = new Swiper('.prediction_team_container .swiper-container', {
                            slidesPerView: 1,
                            autoHeight: true,
                            spaceBetween: 10,
                            pagination: {
                                el: '.prediction_team_container .swiper-pagination',
                            },
                        });
                        setTimeout(function(){
                            swiper.update();
                        }, 500);

                        $(".prediction_team_container .index_competition_group > div").click(function(){
                            $(".prediction_team_container .index_competition_group > div").removeClass("selected");
                            $(this).addClass("selected");
                        });

                        $(document).on("click", ".handicap-bet", function(){
                            $(".handicap-bet").removeClass("active");
                            $(this).addClass("active");
                            var thisId = $(this).data("id");
							if(thisId == 'handicap_home'){ handicap_home = 1; handicap_away = 0; }
							else if (thisId == 'handicap_away') { handicap_away = 1; handicap_home = 0; }
							proceed_prediction();
                            //alert(thisId)
                        })

                        $(document).on("click", ".over_under-bet", function(){
                            $(".over_under-bet").removeClass("active");
                            $(this).addClass("active");
                            var thisId = $(this).data("id");
							if(thisId == 'over_under_home'){ over_under_home = 1; over_under_away = 0; }
							else if (thisId == 'over_under_away') { over_under_away = 1; over_under_home = 0; }
							proceed_prediction();
                            //alert(thisId)
                        })

                        $(document).on("click", ".single-bet", function(){
                            $(".single-bet").removeClass("active");
                            $(this).addClass("active");
                            var thisId = $(this).data("id");
							if(thisId == 'single_home'){ single_home = 1; single_away = 0; single_tie = 0;}
							else if (thisId == 'single_away') { single_away = 1; single_home = 0; single_tie = 0; }
							else if (thisId == 'single_tie') { single_tie = 1; single_away = 0; single_home = 0; }
							proceed_prediction();
                            //alert(thisId)
                        })

                        $(".prediction_analysis_accordion").click(function(){
                            $(this).toggleClass("active");

                            if($(this).hasClass("active")){
                                $(".prediction_analysis_container > p").slideDown(300);
                            }
                            else{
                                $(".prediction_analysis_container > p:not(:first-child)").slideUp(300);
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
							url: api_domain + '/service/prediction.php',
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
							//$("#login_popup").modal("show");
							return;
						}

						$.ajax({
							url: api_domain + '/service/prediction.php',
							type: 'post',
							data: {"action":"proceed_prediction","event_id":event_id,"euid":euid,"handicap_home":handicap_home,"handicap_away":handicap_away,"over_under_home":over_under_home,"over_under_away":over_under_away,"single_home":single_home,"single_away":single_away,"single_tie":single_tie},
							crossDomain: true,
							xhrFields: {
								withCredentials: true
							},

							success: function (response, status, xhr) {
//								if ($('#member_list_more').css('display') != 'none') {
//									$('#member_list_more').modal('hide');
//									$('.modal-backdrop').remove();
//								}
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
							url: api_domain + '/service/prediction.php',
							type: 'post',
							data: {"action":"get_cart_list","euid":euid},
							crossDomain: true,
							xhrFields: {
								withCredentials: true
							},

							success: function (response, status, xhr) {
								$("#prediction_selected").html(response.length);
								$("#div_my_prediction").show();
							},
							error: function () {

							},
						});
					}

					function get_top_five() {
						var euid = Cookies.get("euid");
						var json_data = {};
						if (euid == undefined) {
							json_data = {"action":"get_top_five","event_id":event_id};
						}
						else {
							json_data = {"action":"get_top_five","event_id":event_id,"euid":euid};
						}
						$.ajax({
							url: api_domain + '/service/prediction.php',
							type: 'post',
							data: json_data,
							crossDomain: true,
							xhrFields: {
								withCredentials: true
							},

							success: function (response, status, xhr) {
								// console.log(response);
								var html = template.render($("#top_five_tpl").html(), {"data": response, "home_team_name": "<?php echo $data['home_team_name']; ?>", "away_team_name": "<?php echo $data['away_team_name']; ?>", "event_id":event_id});
								$(".pro_prediction_list").html(html);
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
							url: api_domain + '/service/prediction.php?action=get_prediction_info&event_id='+event_id,
							type: 'get',

							success: function (response, status, xhr) {
								var html = template.render($("#main_area_tpl").html(), {"data": response});
								$("#div_main_area").html(html);

								get_cart_list();
								get_my_option();
								get_top_five();
								
								$(".prediction_analysis_accordion").click(function(){
									$(this).toggleClass("active");

									if($(this).hasClass("active")){
										$(".prediction_analysis_container > p").slideDown(300);
									}
									else{
										$(".prediction_analysis_container > p:not(:first-child)").slideUp(300);
									}
								});
								chatroom_id = response.chatroom_id;
								get_comments();
								//window.message_chatroom_id = response.chatroom_id;
								//message();
							},
							error: function () {

							},
						});
					}
					
					function show_unlock_popup(id) {
						top_ten_id = id;
						if (confirm('是否使用1张兑换光解锁预测？')) {
							unlock_predictor();
						} else {

						}
					}
					
					function unlock_predictor () {
						var euid = Cookies.get("euid");
						if (euid == undefined) {
							//$("#login_popup").modal("show");
							return;
						}

						$.ajax({
							url: api_domain + '/service/prediction.php',
							type: 'post',
							data: {"action":"unlock_predictor","event_id":event_id,"euid":euid,"top_ten_id":top_ten_id},
							crossDomain: true,
							xhrFields: {
								withCredentials: true
							},

							success: function (response, status, xhr) {
								if (response.status == 200 || response.status == 201) {
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
                </script>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>

<script type="text/html" id="top_five_tpl">
	{{if (data.length > 0)}}
	    {{each data value index}}
			<div style="cursor:pointer;" {{if (value.unlocked == "1") }} onclick="window.location='pro_prediction_said.php?event_id={{event_id}}&predictor_id={{value.user_id}}'" {{else}} onclick="show_unlock_popup({{value.id}})" {{/if}}>
				<div class="img"><img src={{value.image}}></div>
				<div class="comment">
					<div class="title">{{value.username}}</div>
					<div class="desc">
						<div>{{home_team_name}} vs {{away_team_name}}</div>
						<div>我的预言是...</div>
					</div>
				</div>
				<div class="lock">
					<div class="pro_prediction_lock_icon {{if (value.unlocked == "0") }}locked{{/if}}"></div>
				</div>
			</div>
	    {{/each}}
	{{else}}
		<div>
			【暂无神级预言家预测这场比赛】
		</div>
	{{/if}}
</script>

<script type="text/html" id="main_area_tpl">
	<div class="prediction_team_picked">
		<div class="title">{{data.league_name}}</div>
		<div class="prediction_team_picked_container">
			<div>
				<div class="img"><img src=<?php echo IMAGE_URL; ?>{{data.home_team_image}}></div>
				<div>{{data.home_team_name}}<br>（主队）</div>
			</div>
			<div>
				<div>
					{{if data.category_id == 1}} 第 {{data.round}} 轮 {{else}} {{data.round}} {{/if}}
				</div>
				<div class="datetime">{{data.match_at}}</div>
				<div><img src="img/prediction/prediction_vs.png"></div>
			</div>
			<div>
				<div class="img"><img src=<?php echo IMAGE_URL; ?>{{data.away_team_image}}></div>
				<div>{{data.away_team_name}}<br>（客队）</div>
			</div>
		</div>
	</div>

	<div class="prediction_bet">
		<div class="block_content_row">
			<div class="block_content title"></div>
			<div class="block_content title">让球</div>
			<div class="block_content title">大小</div>
			<div class="block_content title">独赢</div>
		</div>

		<div class="block_content_row">
			<div class="block_content">{{data.home_team_name}}<br>（主队）</div>
			{{if data.category_id == 1 || data.category_id == 2}}
				<div class="block_content">
					<div class="bet_box handicap-bet" data-id="handicap_home" id="handicap_home">
						<div class="bet">{{data.handicap_home_bet}}</div>
						<div class="num">{{data.handicap_home_odds}}</div>
					</div>
				</div>
				<div class="block_content">
					<div class="bet_box over_under-bet" data-id="over_under_home" id="over_under_home">
						<div class="bet">{{data.over_under_home_bet}}</div>
						<div class="num">{{data.over_under_home_odds}}</div>
					</div>
				</div>
			{{else}}
				<div class="block_content"></div>
				<div class="block_content"></div>
			{{/if}}
			<div class="block_content">
				<div class="bet_box single-bet" data-id="single_home" id="single_home">
					<div class="bet">主</div>
					<div class="num">{{data.single_home}}</div>
				</div>
			</div>
		</div>

		<div class="block_content_row">
			<div class="block_content">{{data.away_team_name}}<br>（客队）</div>
			{{if data.category_id == 1 || data.category_id == 2}}
			<div class="block_content">
				<div class="bet_box handicap-bet" data-id="handicap_away" id="handicap_away">
					<div class="bet">{{data.handicap_away_bet}}</div>
					<div class="num">{{data.handicap_away_odds}}</div>
				</div>
			</div>
			<div class="block_content">
				<div class="bet_box over_under-bet" data-id="over_under_away" id="over_under_away">
					<div class="bet">{{data.over_under_away_bet}}</div>
					<div class="num">{{data.over_under_away_odds}}</div>
				</div>
			</div>
			{{else}}
				<div class="block_content"></div>
				<div class="block_content"></div>
			{{/if}}
			<div class="block_content">
				<div class="bet_box single-bet" data-id="single_away" id="single_away">
					<div class="bet">客</div>
					<div class="num">{{data.single_away}}</div>
				</div>
			</div>
		</div>
		{{if data.category_id == 1}}
		<div class="block_content_row">
			<div class="block_content"></div>
			<div class="block_content"></div>
			<div class="block_content"></div>
			<div class="block_content">
				<div class="bet_box single-bet" data-id="single_tie" id="single_tie">
					<div class="bet">和</div>
					<div class="num">{{data.single_tie}}</div>
				</div>
			</div>
		</div>
		{{/if}}
	</div>

	<div class="prediction_analysis_container">
		{{@data.editor_note}}
		<div class="prediction_analysis_accordion">预测分析 <span class="fa fa-chevron-down"></span></div>
	</div>
</script>

<script type="text/html" id="comments_tpl">

</script>