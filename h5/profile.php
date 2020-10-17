<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link rel="stylesheet" type="text/css" href="css/bootstrap.modal.css">
<title>19资讯 - 我的首页</title>
<?php
include("style_script.php");
?>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<link  href="/css/cropper.css" rel="stylesheet">
<script src="/js/cropper.js"></script>

<style>
.img-wrap {    
    //position: relative;
   // max-width:350px;
}


.img-wrap .icon-wrap,
.img-wrap .fa {
    opacity:0;
    position: absolute;
    top:0;
    right:0;
    bottom:0;
    left:0;
    margin:auto;
    width:15px;
    height:15px;
	
	left: calc(50% - 40%);
	bottom: -151.918px;
	size:200px;
    transition:all 0.2s ease;
	color:white
}

.img-wrap .fa {
    opacity:0;
}

.img-wrap:hover .icon-wrap,
.img-wrap:hover .fa {
    opacity:1;
}
</style>
</head>

<body onload='user_camera_position();$(".profile_header .icon").css("bottom",-$(".profile_header .icon").height()/2);$(".profile_name_container").css("padding-top",$(".profile_header .icon").height()/2 + 8);'>
<?php
include("header.php");
?>
    <div class="main_container">
        <?php
        $euid = rawurldecode($_COOKIE['euid']) ;
        $param = array('euid'=>$euid);
        $userInfo = httpPost(CURL_API_URL . '/service/user.php?action=getuserinfo',$param);
        $userInfoExtra = httpPost(CURL_API_URL . '/service/user.php?action=getextrainfo',$param);
        $userInfo = json_decode($userInfo, true);
        $userInfoExtra = json_decode($userInfoExtra, true);
        ?>

        <div class="body_container">
            <div class="profile_header" onclick=' showThumbnailModal();'>
                <?php if($userInfo['user']['level_id'] > 0){ ?>
                <img class="bg" src="/img/level/<?php echo $userInfo['user']['level_id']; ?>.jpg">
                <?php } ?>
				
                <div class="img-wrap">
					<img class="icon" src="<?php echo IMAGE_URL . $userInfo['user']['image'] ?>">
					<div class="icon-wrap"><i class="fa fa-camera" aria-hidden="true"></i></div>
				</div>
            </div>

            <div class="profile_name_container">
                <div><?php echo $userInfo['user']['username'] ?></div>
                <div class="level"><?php echo $userInfo['user']['level']?></div>

                <div class="account_manage_container">
                    <button class="active" onclick="window.location = 'account_setting.php'">账号设置</button>
                    <button onclick="window.location = 'change_password.php'">密码编辑</button>
                    <button onclick="window.location = 'my_comments.php'">留言收藏</button>
                </div>
            </div>

            <div class="point_exchange_container session_block">
                <div>
                    <div>总累积战数</div>
                    <div class="point"><?php echo intval($userInfo['user']['total_points'])?></div>
                    <div class="weekly">本周 <?php echo intval($userInfo['user']['weekly_points'])?></div>
                </div>
                <div>
                    <div>现有战数</div>
                    <div class="point"><?php echo intval($userInfo['user']['points'])?></div>
                </div>
                <div>
                    <button onclick="window.location='/exchange.php'">兑换</button>
                </div>
            </div>

            <div class="profile_session">
                <div class="session_block_title">战绩</div>
            </div>

            <div class="profile_bg">
                <div class="profile_submenu_container">
                    <div><button class="active" data-id="overview">总览</button></div>
                    <div onclick="getWinchanges();"><button data-id="winchanges">胜率</button></div>
                    <div onclick="getProphet();"><button data-id="prophet">预言家资格</button></div>
                </div>
            </div>

            <div class="profile_bg profile_subpage" id="profile_overview">
                <div class="profile_chart">
                    <canvas id="chartjs-radar-canvas"></canvas>
                </div>

                <div class="profile_graph">
					<select class="w-60" id="category_list" onchange="loadLeaguest(this.value);">
						<option value="2" selected="selected">篮球</option>
						<option value="1">足球</option>
						<option value="4">电竞</option>
					</select>
                    <select class="w-60" id="leaguest_list" onchange="getLeaguestData();"></select>

                    <canvas id="chartjs-bar-canvas"></canvas>
                </div>

                <div class="profile_session">
                    <div class="session_block_title">预测历史 <a href="/my_prophet.php">更多</a></div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td>预测时间</td>
                                <td>比赛时间</td>
                                <td>让球</td>
                                <td>大小</td>
                                <td>独赢</td>
                                <td>总得战数</td>
                                <td>状态</td>
                            </tr>
                        </thead>
                        <tbody id="history_list">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="profile_bg profile_subpage" style="display: none;" id="profile_winchanges">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select id="profile_winchanges_category" class="w-100" onchange="updateLeaguest('winchanges');">
                                    <option value="2" selected="selected">篮球</option>
                                    <option value="1">足球</option>
                                    <option value="4">电竞</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_winchanges_leaguest">

                                </select>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" id="profile_winchanges_year">
                                    <?php
                                    $date = (int) date('Y');
                                    $numYears = 3;
                                    for ($i=$date; $i >= $date - $numYears; $i--) {
                                        echo "<option value=\"$i\" ".($i==$date?'selected="selected"':'').">$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_winchanges_month">
                                    <option value="1" <?php echo (date('n') == 1 ? 'selected="selected"' : ''); ?>>1月</option>
                                    <option value="2" <?php echo (date('n') == 2 ? 'selected="selected"' : ''); ?>>2月</option>
                                    <option value="3" <?php echo (date('n') == 3 ? 'selected="selected"' : ''); ?>>3月</option>
                                    <option value="4" <?php echo (date('n') == 4 ? 'selected="selected"' : ''); ?>>4月</option>
                                    <option value="5" <?php echo (date('n') == 5 ? 'selected="selected"' : ''); ?>>5月</option>
                                    <option value="6" <?php echo (date('n') == 6 ? 'selected="selected"' : ''); ?>>6月</option>
                                    <option value="7" <?php echo (date('n') == 7 ? 'selected="selected"' : ''); ?>>7月</option>
                                    <option value="8" <?php echo (date('n') == 8 ? 'selected="selected"' : ''); ?>>8月</option>
                                    <option value="9" <?php echo (date('n') == 9 ? 'selected="selected"' : ''); ?>>9月</option>
                                    <option value="10" <?php echo (date('n') == 10 ? 'selected="selected"' : ''); ?>>10月</option>
                                    <option value="11" <?php echo (date('n') == 11 ? 'selected="selected"' : ''); ?>>11月</option>
                                    <option value="12" <?php echo (date('n') == 12 ? 'selected="selected"' : ''); ?>>12月</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <button class="button_style_dark w-100" onclick="getWinchanges();">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td width="25%">项目</td>
                                <td width="25%">胜场</td>
                                <td width="25%">败场</td>
                                <td width="25%">胜率 [排名]</td>
                            </tr>
                        </thead>
                        <tbody id="profile_winchanges_result">
                        </tbody>
                    </table>
                    <div class="notice">战绩于每日下午五点计算</div>
                </div>
            </div>

            <div class="profile_bg profile_subpage" style="display: none;" id="profile_prophet">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" onchange="updateLeaguest('prophet');" id="profile_prophet_category">
                                    <option value="2" selected="selected">篮球</option>
                                    <option value="1">足球</option>
                                    <option value="4">电竞</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_prophet_leaguest">
                                </select>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block w-100">
                                <select class="w-100" id="profile_prophet_time">
                                    <?php echo '<option value="'.date('Y/m', time()).'" selected="selected">'.date('Y/m', time()).'</option>'; ?>
                                    <?php echo '<option value="'.date('Y/m', strtotime('-1 month')).'">'.date('Y/m', strtotime('-1 month')).'</option>'; ?>
                                    <?php echo '<option value="'.date('Y/m', strtotime('-2 month')).'">'.date('Y/m', strtotime('-2 month')).'</option>'; ?>
                                </select>
                            </div>
                            <div class="content_block w-50">
                                <button class="button_style_dark w-100" onclick="getProphet();">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td width="25%">项目</td>
                                <td width="25%">累积战绩</td>
                                <td width="25%">神级预言家</td>
                                <td width="25%">达到标准</td>
                            </tr>
                        </thead>
                        <tbody id="profile_prophet_result">

                        </tbody>
                    </table>
                    <div class="notice">下期评选日:<?php echo date('m/2', strtotime('+1 month')); ?>, 评选期间:<?php echo date('m/01', time()).'~'.date('m/d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')); ?></div>
                </div>
            </div>

        </div>
    </div>

	<div class="modal" id="thumbnailModal" tabindex="-1" role="dialog"  aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
		   <div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle" style='margin-top: 0px;'>头像管理</h5></div>
						<div class="modal-body">
						   
								<div style='width: 100%;padding-bottom: 15px; text-align: center;'>
									
										<span style="color:grey;">请上传150X150，不超过200K之图像</span>
									<img id='current_thumbnail' src="" width='150' height='150' style='display: block;max-width: 100%;margin: 0 auto;margin-top: 15px;'>
									
								</div>									
								
								<div style="width: 100%;text-align: center;">
									<form action="#" method="POST" id="user_image_form">
										<input type="hidden" name="tempfile" id='tempfile' value="">
										<input type="hidden" name="folder" id='folder' value="user_image">		
									</form>
									
									<button style="display:block;width:100px; margin:auto;border-radius: 8px;" onclick="document.getElementById('file2').click()">选择图像</button>
									<input type='file' id="file2" name="file2" accept="image/x-png,image/gif,image/jpeg,image/jpg;capture=camera" onchange='local_media_ajax_submit()' style="display:none">
									
								</div>
								<div style="width: 100%;text-align: center;padding:10px">
									<button class="submit_btn" id="crop_button" style='display: none;margin:auto;'>裁剪</button> 
								</div>
								<div style='text-align:center'>
									<div id='cropped_preview_msg' style='display: none;padding: 10px;font-size: 18px;padding-top: 30px;'><u>头像预览</u></div>
									
									<div id='result' style='width: 100%;'>
										<img id='cropped_image' >
										<button class="submit_btn" id="upload_button" style='display: none;margin:auto;'>上传</button>
									</div>
									
									<div style="width: 100%;text-align: center;padding:10px">
										<button style='display:block; margin:auto;' onclick='$("#thumbnailModal").modal("hide");'>关闭</button>
									</div>
									
									
								</div>
											   
						</div>
				  
				</div>
			  </div>
			  
	</div>
	
	
    <?php
        include("footer.php");
    ?>
    <script>
        var num = 0;
        $(function(){
            loadLeaguest(2);
            updateLeaguest('winchanges');
            updateLeaguest('prophet');
            getLeaguestData();

            var year = new Date().getFullYear();
            var month = parseInt(new Date().getMonth())+1;
            getHistyoryList(0,0,year,month);
        });
		
		function user_camera_position(){
			var bottom=-$(".profile_header .icon").height()+140;
			var left=-$(".profile_header .icon").width()/4+10;
			var camera_icon_fontsize=(-(left)+10)+'px';
			
			$(".icon-wrap").css("bottom",bottom);
			$(".icon-wrap").css("left",left);
			$(".icon-wrap").css("font-size",camera_icon_fontsize);	
		}
		
		function showThumbnailModal(){ 
			$('#current_thumbnail').attr('src',window.localStorage.profile_thumbnail);
			$("#thumbnailModal").modal("show");
		}
		
        function loadLeaguest(cvalue){
            var url = api_domain+'/service/match.php';
            $.ajax({
                method: "GET",
                url: url,
                data:{action:'get_leagues',category_id:cvalue},
                async: false,
                success: function(data)
                {
                    if(data != null || data != '') {
						$("#leaguest_list").html('');
                        for (const [key, value] of Object.entries(data)) {
                            $("#leaguest_list").append('<option value='+cvalue+'_'+data[key]['id']+' '+(num==0?'selected="selected"':'')+'>' + data[key]['name_zh'] + '</option>');
                            num++;
                        }
						getLeaguestData();
                    }
                },
                error: function (request, status, error) {
                }
            });
        }

        function getLeaguestData(){
            var leaguestValue = document.getElementById('leaguest_list').value ;
            var cid = leaguestValue.split("_")[0];
            var lid = leaguestValue.split("_")[1];
            var euid = Cookies.get('euid');
            var url = api_domain+'/service/user.php';
            $.ajax({
                method: "POST",
                url: url,
                data:{action:'get_prediction_stats',category_id:cid,league_id:lid,euid:euid},
                async: false,
                success: function(data)
                {
                    if(data != null || data != '') {
                        setTimeout(function() {
                            var dataGenerate = [data['prediction_total_count'],data['prediction_count'],data['voucher'],data['win_rate'],data['total_win_rate']];
                            //console.log(dataGenerate);
                            generateChat(dataGenerate);
                            var dataGenerate = [data.win_rate,0,data.top_ten_count];
                            //console.log(dataGenerate);
                            generateChatBar(dataGenerate);
                        }, 1000);
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });

            // var time = year + '/' + month;
            // $.ajax({
            //     method: "POST",
            //     url: url,
            //     data:{action:'get_prediction_rate',category_id:cid,league_id:lid,euid:euid,time:time} ,
            //     success: function(data)
            //     {
            //         console.log(data);
            //         if(data != null || data != '') {
            //             // var dataGenerate = [data.win_rate,data['handicap']['rate'],data['total']['rate']];
            //             // generateChatBar(dataGenerate);
            //         }
            //     },
            //     error: function (request, status, error) {
            //         alert(request.responseText);
            //     }
            // });

        }

        function generateChat(value){
            var data = {
                labels: ["战数", "预言次数", "神級兌換券", "参与率", "总胜率"], // Radar Chart Label
                datasets: [{ data: value, // Radar Chart Data
                    borderColor: "#ff4a60",
                    borderWidth: 1,
                    backgroundColor: "rgba(255, 108, 126, 0.5)",
                    pointBorderWidth: 5,
                    pointBorderColor: "#ff6c7e"
                }]
            };

            var options = {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                scale: {
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        stepSize: 0,
                        display: false,
                        maxTicksLimit: 1,
                    },
                    gridLines: {
                        color: "#858585"
                    },
                    angleLines: {
                        color: '#858585'
                    }
                }
            };

            var ctx = document.getElementById("chartjs-radar-canvas");
            var myRadarChart = new Chart(ctx, {
                type: 'radar',
                data: data,
                options: options,
            });
        }

        function generateChatBar(value){
            var dataBar = {
                labels: ["单月胜率", "主推月胜率", "神准预言家"], // Radar Chart Label
                datasets: [{ data: value, // Radar Chart Data
                    backgroundColor: ["#ee243c", "#fcbc0a", "#969696", "#0113ff", "#00f75d", "#f700ee", "#00eef7"]
                }]
            };

            var optionsBar = {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        display: false,
                        ticks: {
                            min: 0,
                            max: (dataBar.datasets[0].data.max() + 30)
                        },
                        gridLines: {
                            display: false
                        }
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            color: "rgba(0, 0, 0, 0)",
                            display: false
                        }
                    }]
                },
                animation: {
                    duration: 1,
                    onComplete: function () {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                        ctx.textAlign = 'center';
                        ctx.fillStyle = "rgba(0, 0, 0, 1)";
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index];
                                if(index < 2){
                                    data = data + "%";
                                }
                                ctx.fillText(data, bar._model.x, bar._model.y - 5);
                            });
                        });
                    }
                }
            };

            var ctxBar = document.getElementById("chartjs-bar-canvas").getContext('2d');
            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: dataBar,
                options: optionsBar,
            });
        }

        function getHistyoryList(cid,lid,year,month){
            var url = api_domain+'/service/prediction.php';
            var euid = Cookies.get('euid');
            $("#history_list").empty();
            $.ajax({
                method: "POST",
                url: url,
                data:{action:'get_prediction_history',category_id:cid,league_id:lid,year:year,month:month,euid:euid} ,
                success: function(data)
                {
                    if(data != null || data != '') {
                        var listData = data['list'];
                        for (const [key, value] of Object.entries(listData)) {
                            var match_at = value['match_at'].split(" ");
                            var created_at = value['created_at'].split(" ");
                            // var handicap = value['handicap'].split(" ");
                            $('#history_list').append('<tr>' +
                                '<td>'+created_at[0]+' <div>'+created_at[1]+'</div></td>' +
                                '<td>'+match_at[0]+'<div>'+match_at[1]+'</div></td>' +
                                '<td>'+value['handicap']+'</td>' +
                                '<td>'+value['over_under']+'</td>' +
                                '<td>'+value['single']+'</td>' +
                                '<td>'+value['win_amount']+'</td>' +
                                '<td>'+value['status']+'</td>' +
                                '</tr>');
                        }
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        }


	function getWinchanges(){
		var url = api_domain+'/service/user.php';
		var cid = document.getElementById('profile_winchanges_category').value ;
		var lid = document.getElementById('profile_winchanges_leaguest').value ;
		var time = document.getElementById('profile_winchanges_year').value + '/'+ document.getElementById('profile_winchanges_month').value;
		var euid = decodeURIComponent(getCookie('euid'));
		$("#profile_winchanges_result").empty();
		$.ajax({
			method: "POST",
			url: url,
			data:{action:'get_prediction_rate',category_id:cid,league_id:lid,time:time,euid:euid} ,
			success: function(data)
			{
				var html = '<tr><td>让分</td><td>'+ data.handicap.win_count + '</td> <td>'+ data.handicap.lose_count +'</td> <td>'+ data.handicap.rate +'% ['+ data.handicap.rank +']</td></tr><tr><td>大小</td><td>'+ data.over_under.win_count + '</td> <td>'+ data.over_under.lose_count +'</td> <td>'+ data.over_under.rate +'% ['+ data.over_under.rank +']</td></tr><tr><td>独赢</td><td>'+ data.single.win_count + '</td> <td>'+ data.single.lose_count +'</td> <td>'+ data.single.rate +'% ['+ data.single.rank +']</td></tr><tr><td>总胜场</td><td>'+ data.total.win_count + '</td> <td>'+ data.total.lose_count +'</td> <td>'+ data.total.rate +'% ['+ data.total.rank +']</td></tr>';
				$('#profile_winchanges_result').html(html);
			},
			error: function (request, status, error) {
				alert(request.responseText);
			}
		});
	}

	function getProphet(){
		var url = api_domain+'/service/user.php';
		var cid = document.getElementById('profile_prophet_category').value ;
		var lid = document.getElementById('profile_prophet_leaguest').value ;
		var time = document.getElementById('profile_prophet_time').value;
		var euid = decodeURIComponent(getCookie('euid'));
		$("#profile_prophet_result").empty();
		$.ajax({
			method: "POST",
			url: url,
			data:{action:'get_prediction_qualification',category_id:cid,league_id:lid,time:time,euid:euid} ,
			success: function(data)
			{
				var html = template.render($("#prediction_table_tpl").html(), {"data": data});
				$("#profile_prophet_result").html(html);
			},
			error: function (request, status, error) {
				alert(request.responseText);
			}
		});
	}
    
	function cropper_destroy(type){//type=new means close modal and reopen again
		var image = document.getElementById('current_thumbnail');
		var cropper = new Cropper(image); 
		cropper.destroy(); 
		cropper = null;
		
		$('.cropper-container').remove();
		//console.log(type)
		//show current profile pic
		if (type=='new'){
			$('#current_thumbnail').removeClass('cropper-hidden');
			$('#current_thumbnail').attr('src',window.localStorage.profile_thumbnail);
			
			$('#crop_button').css('display','none');
			$('#cropped_preview_msg').css('display','none');
			$('#result').css('display','none');
			
			$('#file').val('');
		}
   }
   
   function show_cropper(new_image_url){
		var image = document.getElementById('current_thumbnail');
		var button = document.getElementById('crop_button');
		var result = document.getElementById('result');
		var croppable = false;
		
		$('#crop_button').css('display','block');
		
		
		
		var cropper = new Cropper(image, {
			aspectRatio: 1,
			viewMode: 1,
			ready: function () {
			  croppable = true;
			},
		  });
		
		
		button.onclick = function () {
			var croppedCanvas;
			var roundedCanvas;
			var roundedImage;

			if (!croppable) {
			  return;
			}

			// Crop
			croppedCanvas = cropper.getCroppedCanvas();

			// Round
			roundedCanvas = getRoundedCanvas(croppedCanvas);

			// Show
			//roundedImage = document.getElementById('cropped_image');
			//roundedImage.src = roundedCanvas.toDataURL();
			
			//console.log(roundedImage)
			$('#result').css('display','block');
			$('#cropped_image').attr('src',roundedCanvas.toDataURL())
			$('#cropped_image').css('width','200px');
			$('#cropped_image').css('padding-bottom','10px');
			
			$('#cropped_preview_msg').css('display','inline-block');
			$('#upload_button').css('display','block');
      };
	}
	
	function getRoundedCanvas(sourceCanvas) {
      var canvas = document.createElement('canvas');
      var context = canvas.getContext('2d');
      var width = sourceCanvas.width;
      var height = sourceCanvas.height;

      canvas.width = width;
      canvas.height = height;
      context.imageSmoothingEnabled = true;
      context.drawImage(sourceCanvas, 0, 0, width, height);
      context.globalCompositeOperation = 'destination-in';
      context.beginPath();
      context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
      context.fill();
      return canvas;
    }

	function base64ToBlob(base64, mime){
		mime = mime || '';
		var sliceSize = 1024;
		var byteChars = window.atob(base64);
		var byteArrays = [];

		for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
			var slice = byteChars.slice(offset, offset + sliceSize);

			var byteNumbers = new Array(slice.length);
			for (var i = 0; i < slice.length; i++) {
				byteNumbers[i] = slice.charCodeAt(i);
			}

			var byteArray = new Uint8Array(byteNumbers);

			byteArrays.push(byteArray);
		}

		return new Blob(byteArrays, {type: mime});
	} 
	//this one for preview only, when file onchange display uploaded image to crop
	function local_media_ajax_submit() {
		
		$_form = document.getElementById('user_image_form');
	    var formData = new FormData($_form);

		var file2 = $('#file2')[0].files[0];
		formData.append('file', file2);
		
		if (file2==undefined)
			return
		
		$.ajax({
	        type: 'POST',
	        url:  getHost() +'/assets/php/media-meta.php',
	        contentType: false,
	        cache: false,
	        processData: false,
	         data: formData,
	        success: function (response, status, xhr) {

	            obj = response;

	            var temp_image_url='/upload/media/user_image/_temp/'+obj.name;
				$('#current_thumbnail').attr('src',temp_image_url)
				cropper_destroy();
				show_cropper(temp_image_url);
	        },
	        error: function (resp) {
	           alert("Problem occurred while sending request.");
	        },
	    });
	}
	
	//call local media ajax here(media-meta)
    $("#upload_button").click(function(){
		var url = "url/action";                
			var image = $('#cropped_image').attr('src');
			
			
			var base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "");
			var blob = base64ToBlob(base64ImageContent, 'image/png');     
	
			var formData = new FormData();
			formData.append('upload', blob);
			formData.append('folder', 'user_image');
			
	

			$.ajax({
				url: getHost() +'/assets/php/media-meta.php',
				type: "POST", 
				cache: false,
				contentType: false,
				processData: false,
				data: formData, 
				success: function (response, status, xhr) {
					//console.log(response);
					obj = response; 
					
					after_media_meta({
										media_meta_data: obj,
										extra:obj.extra,
									});
				}
			})
	});
	
	function after_media_meta($data) {
		
		var euid = Cookies.get('euid');
		if (euid == undefined) {
        	return;
        }
		var pic_url='/'+$data.media_meta_data.url;
		
		var formData = new FormData();
		
		formData.append('action', 'update_user_image');
		formData.append('euid', euid);
		formData.append('image_data[url]', $data.media_meta_data.url);
		formData.append('image_data[name]', $data.media_meta_data.name);
		formData.append('image_data[type]', $data.media_meta_data.type);
		formData.append('image_data[size]', $data.media_meta_data.filesize);
		
		 $.ajax({
	        type: 'POST',
	        url: getBackendHost() + '/service/user.php',
	        cache: false,
			contentType: false,
			processData: false,
	        data: formData,
	        success: function (response, status, xhr) {
				//console.log(response);
	            obj = response;
				
				window.localStorage.profile_thumbnail = pic_url;
				alert('成功设置新头像！');
				redirect_to(getCurrentFullUri());
				
				//media_save($data.extra,pic_url); // seems movefile done in media-meta, so commented media-save
	        },
			
	        error: function () {
	           alert("Problem occurred while sending request.");
	        },
	    });
	}

	function media_save(extra,pic_url){
		var msg='';
		var redirect_url='';
		
		$('#tempfile').val(extra);
		$_form = document.getElementById('user_image_form');
	    var formData = new FormData($_form);
		
		 $.ajax({
	        type: 'POST',
			url:  '/assets/php/media-save.php',
	        data: formData,
	        crossDomain: true,
	        contentType: false,
	        processData: false,
			 success: function (response, status, xhr) {
				//console.log(response);
	            obj = response;
				
				
				window.localStorage.profile_thumbnail = pic_url;
				
				alert('成功设置新头像！');
				
				//console.log(extra)
				redirect_to(getCurrentFullUri());
				
	        },
	        error: function (resp) {
	             alert("Problem occurred while sending request.");
	        },
	    });
	}
	</script>

    <script>
        $(function(){
            var v = [0,0,0,0,0];
            var v2 = [0,0,0];
            generateChat(v);
            generateChatBar(v2);
            $(".profile_submenu_container button").click(function(e){
                e.preventDefault();

                var thisData = $(this).data("id");
                $(".profile_submenu_container button").removeClass("active");
                $(this).addClass("active");
                $(".profile_subpage").stop().slideUp(300);
                $("#profile_"+thisData).stop().slideDown(300);
            })
        })
    </script>

  <script type="text/html" id="prediction_table_tpl">
    <tr>
        <td>本赛季胜率</td>
        <td>{{data.season_rate}}%</td>
        <td>{{data.top_ten_season_rate}}%</td>
        <td>
            {{if (data.top_ten_season_rate=='-')}}
            ㄨ
            {{else}}
            {{data.season_rate>=data.top_ten_season_rate?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
    <tr>
        <td>单月最低预测次数</td>
        <td>{{data.total_count}}</td>
        <td>{{data.top_ten_prediction_count}}</td>
        <td>
            {{if (data.top_ten_prediction_count=='-')}}
            ㄨ
            {{else}}
            {{data.total_count>=data.top_ten_prediction_count?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
    <tr>
        <td>单月总胜率</td>
        <td>{{data.rate}}%</td>
        <td>{{data.top_ten_rate}}%</td>
        <td>
            {{if (data.top_ten_rate=='-')}}
            ㄨ
            {{else}}
            {{data.rate>=data.top_ten_rate?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
  </script>
</body>
</html>