
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
    <header>
        <?php
            include("header.php");
        ?>
    </header>
    <div class="main_container">
        <div id="my_mpModalPage"></div>
        <script type="text/html" id="cart_list_tpl">
            <div class="body_container grey_bg">
                <div class="subpage_title">
                    <a class="back" href="#" onclick="window.history.back()">返回</a>
                    <!--<div>我的预测与主推</div>-->
                    <div>我的预测</div>
                </div>

                <div class="profile_bg profile_subpage">
                    <div class="my_prediction_container">
                        <form>
                            <div class="my_prediction_list">
                            {{if (data.length == 0) }}
                            您尚未预测任何赛事
                            {{/if}}
                            {{each data value index}}
                                <div class="item">
                                    <div class="container">
                                        <div class="close" onclick="cancelPrediction({{value.event_id}})"></div>
                                        <div class="title_block">
                                            <div class="title">{{value.home_team_name}} <span>VS</span> {{value.away_team_name}}</div>
                                            <div class="subtitle">{{value.league_name}} <span>{{value.match_at}}</span></div>
                                        </div>
                                        
                                        <div class="content_block">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>让球</td>
                                                        <td>
                                                            {{if (value.handicap_home > 0)}}
                                                                <div>{{value.home_team_name}}<span>{{value.handicap_home_value}}</span><img src="img/star.svg" class="star"></div>
                                                            {{else if(value.handicap_away > 0)}}
                                                                <div>{{value.away_team_name}}<span>{{value.handicap_away_value}}</span><img src="img/star.svg" class="star"></div>
                                                            {{/if}}
                                                            <!--<div>多伦多猛龙<span>+1.5</span><img src="img/star_full.svg" class="star"></div>-->
                                                        </td>
                                                        <td rowspan="3"><div class="edit_btn edit-match-button" onclick="location.href='prediction.php?event_id={{value.event_id}}';"><img src="img/prediction/edit_icon.png"><br>修<br>改</div></td>
                                                    </tr>
                                                    <tr>
                                                        <td>大小</td>
                                                        <td>
                                                            <!--<div>多伦多猛龙<span>大212.5</span><img src="img/star_full.svg" class="star"></div>-->
                                                            {{if (value.over_under_home > 0)}}
                                                                <div>{{value.home_team_name}}<span>{{value.over_under_home_value}}</span><img src="img/star.svg" class="star"></div>
                                                            {{else if(value.over_under_away > 0)}}
                                                                <div>{{value.away_team_name}}<span>{{value.over_under_away_value}}</span><img src="img/star.svg" class="star"></div>
                                                            {{/if}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>独赢</td>
                                                        <td>
                                                            <!--<div><span>主</span><img src="img/star.svg" class="star"></div>-->
                                                            {{if (value.single_home > 0)}}
                                                            <div><span>主</span><img src="img/star.svg" class="star"></div>
                                                            {{else if(value.single_tie > 0)}}
                                                            <div><span>和</span><img src="img/star.svg" class="star"></div>
                                                            {{else if(value.single_away > 0)}}
                                                            <div><span>客</span><img src="img/star.svg" class="star"></div>
                                                            {{/if}}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            {{/each}}
                            </div><!-- /.my_prediction_list -->
                            {{if (data.length > 0) }}
                            <div class="form_footer_button_container">
                                <button type="button" class="w-40 active" id="submit_prediction_btn" onclick="predictionSuccessSubmit()">送出预测</button>
                            </div>
                            {{/if}}
                        </form>
                    </div>
                </div>
            </div>
        </script>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>

<script type="text/javascript">
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
                //$("#userPredictionNumber").html(response.length);
                var html = template.render($("#cart_list_tpl").html(), {"data": response});
                $("#my_mpModalPage").html(html);
                //$("#div_my_prediction").show();
            },
            error: function () {
            },
        });
    }

    function predictionSuccessSubmit() {
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
            		alert(response.message);
                    redirect_to('prediction.php');
            	}
            	else if (response.status == -201) {
            		Cookies.remove('euid');
            	}
            	else {
            		alert(response.message);
                    redirect_to('prediction.php');
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
            		//get_my_option();
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

    $(document).ready(function() {
        get_cart_list();
    })
</script>