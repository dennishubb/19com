<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

$user_id = intval($_GET['predictor_id']);
$event_id = intval($_GET['event_id']);

?>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>19.com</title>
	<?php
		include("style_script.php");
	?>
	
	<style>
		.display_none{
			display: none;
		}
	</style>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">
		<div id='pro_detail_body'></div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>

<script>

var user_id = <?php echo $user_id; ?>;
var event_id = <?php echo $event_id; ?>;

get_top_five_options(user_id,event_id);

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
			   console.log(response.data)
				if (response.status == 200) {
					var data=response.data;
					var pro_match_round_name=data.league_name+' '+data.round+'-'+data.home_team_name+' VS '+data.away_team_name;
					
					var html = template.render($("#pro_said_detail_body_tpl").html(), {"data": response.data});
					
					$("#pro_detail_body").html(html);
					
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
			//console.log(response);

			if (response.status == 200) {
				alert("已加入预测选单！\r\n记得点选右上角 “查看” 按钮，并确认送出选项哟～");
				redirect_to('prediction.php?event_id='+event_id);

			}
			else if (response.status == -201) {
				Cookies.remove('euid');
			}
			else {
				alert(response.message);
				redirect_to('prediction.php?event_id='+event_id);
			}
		},
		error: function () {

		},
	});
}
	
	
</script>

<script type="text/html" id="pro_said_detail_body_tpl">
        <div class="body_container grey_bg" >
            <div class="subpage_title">
                <a class="back" href="#" onclick="window.history.back()">返回</a>
                <div>{{data.username}}这样说</div>
            </div>

            <div class="pro_prediction_match">{{data.league_name}}
      {{if(data.category_id == "1")}}
      第 {{data.round}} 轮
      {{else}}
      {{data.round}}
      {{/if}} - {{data.home_team_name}} VS {{data.away_team_name}}</div>

            <div class="profile_bg profile_subpage">
                
                    <div class="pro_prediction_container">

                        <div class="prediction_bet">
                            <div class="block_content_row">
                                <div class="block_content title"></div>
                                <div class="block_content title">让球</div>
                                <div class="block_content title">大小</div>
                                <div class="block_content title">独赢</div>
                            </div>
                            
                            <div class="block_content_row">
                                <div class="block_content"><span id='pro_home_team'></span>（主队）</div>
                                <div class="block_content">
                                    <div class="bet_box handicap-bet {{data.handicap_home>0?'active activeSelected':''}}" {{data.category_id==4?"style=display:none":""}} >
                                        <div class="bet">{{data.handicap_home_bet}}</div>
                                        <div class="num">{{data.handicap_home_odds}}</div>
                                    </div>
                                </div>
                                <div class="block_content " >
                                    <div class="bet_box over_under-bet {{data.over_under_home>0?'active activeSelected':''}}" {{data.category_id==4?"style=display:none":""}} >
                                        <div class="bet">{{data.over_under_home_bet}}</div>
                                        <div class="num">{{data.over_under_home_odds}}</div>
                                    </div>
                                </div>
                                <div class="block_content">
                                    <div class="bet_box single-bet {{data.single_home>0?'active activeSelected':''}}">
                                        <div class="bet">主</div>
                                        <div class="num">{{data.single_home_value}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="block_content_row">
                                <div class="block_content"><span id='pro_away_team'></span>（客队）</div>
                                <div class="block_content">
                                    <div class="bet_box handicap-bet {{data.handicap_away>0?'active activeSelected':''}}" {{data.category_id==4?"style=display:none":""}} >
                                        <div class="bet">{{data.handicap_away_bet}}</div>
                                        <div class="num">{{data.handicap_away_odds}}</div>
                                    </div>
                                </div>
                                <div class="block_content">
                                    <div class="bet_box over_under-bet {{data.over_under_away>0?'active activeSelected':''}}" {{data.category_id==4?"style=display:none":""}} >
                                        <div class="bet">{{data.over_under_away_bet}}</div>
                                        <div class="num">{{data.over_under_away_odds}}</div>
                                    </div>
                                </div>
                                <div class="block_content">
                                    <div class="bet_box single-bet {{data.single_away>0?'active activeSelected':''}}">
                                        <div class="bet">客</div>
                                        <div class="num">{{data.single_away_value}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="block_content_row"  {{data.category_id==1?"":"style=display:none"}} >
                                <div class="block_content"></div>
                                <div class="block_content"></div>
                                <div class="block_content"></div>
                                <div class="block_content">
                                    <div class="bet_box single-bet {{data.single_tie>0?'active activeSelected':''}}">
                                        <div class="bet">和</div>
                                        <div class="num">{{data.single_tie_value}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form_footer_button_container">
                            <button class="w-40 active" onclick="redirect_to('prediction.php?event_id='+event_id);" style='cursor:pointer'>自定</button>
                            <button class="w-40 active" onclick="handicap_home={{data.handicap_home}};handicap_away={{data.handicap_away}};over_under_home={{data.over_under_home}};over_under_away={{data.over_under_away}};single_home={{data.single_home}};single_tie={{data.single_tie}};single_away={{data.single_away}};proceed_prediction();" style='cursor:pointer'>跟随</button>
                        </div>

                    </div>
                
            </div>
        </div>
	</script>