<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

if (is_mobile()) {
	$complete_info = intval($_GET['complete_info']);
	if ($complete_info == 1) {
		header('location: ' . H5_DOMAIN . '/account_setting.php');
	}
	else {
		header('location: ' . H5_DOMAIN . '/profile.php');
	}
}
?>
<!DOCTYPE html>
<html lang="zh-hans">
    <head >
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
	    <script type="text/javascript" src="/assets/js/fo_common/jquery-ui.min.js"></script>
	    <link rel="stylesheet" href="/assets/css/jquery-ui.min.css"/>
		<link  href="/assets/css/cropper.css" rel="stylesheet">
		<script src="/assets/js/common/cropper.js"></script>
		<script src="/assets/Chart.js-2.9.3/dist/Chart.bundle.min.js"></script>
		<link rel="stylesheet" type="text/css" href="/assets/Chart.js-2.9.3/dist/Chart.min.css">
        <title>19资讯 - 我的首页</title>
    </head>
    
    <body>
		<?php include '../layout/header.php'; ?>
	    <div class="main_area">
	    	
	    	<div class="profile_bg"> 
	    		<img id="user_profile_bg" src="">
	    	</div>
	    	<div class="profile layout1200">
	    		<div class="profile_left">
	    			<div class="profile_icon" data-toggle="modal" data-target="#upload_user_icon" onclick="cropper_destroy('new')">
						<img id="profile_thumbnail" src="">
						<div class="add_icon"><embed class="add_icon_svg" src="/assets/images/camera_icon.svg" type="image/svg+xml"></div>
					</div>
	    			
	    			<div class="username" id="div_username"></div>
	    			<div class="user_tag" id="div_userlevel"></div>
	    			<div class="profile_left_item" onclick="show_member_info();" style=" cursor: pointer;">账号设置</div>
	    			<div class="profile_left_item" data-toggle="modal" data-target="#resetpw" style=" cursor: pointer;">密码编辑</div>
	    			<div class="profile_left_item" onclick="location = '/cn/message-collect/index.php';" style=" cursor: pointer;">留言收藏</div>
	    			<div class="profile_left_item2">
	    				<div class="title">总累积战数</div>
	    				<div id="div_points"></div>
	    			</div>
	    			<div class="profile_left_item2">
						<div class="title">现有券数</div>
						<div id="div_voucher"></div>
					</div>
	    			<div class="profile_left_item2">
	    				<div class="title">现有战数</div>
	    				<div id="div_current_points"></div>
	    				<div class="float_btn" onclick="location = '/cn/exchange.php';">兑换</div>
	    			</div>
	    		</div>
	    		<div class="profile_right">
	    			<div class="title_area "><span>战绩</span></div>
	    			<div class="chart_area_btn">
	    				<div id="div_index1" class="active" onclick="change_area(1);">总览</div>
	    				<div id="div_index2" onclick="change_area(2);get_winrate_table();">胜率</div>
	    				<div id="div_index3" onclick="change_area(3);get_prediction_table();">预言家资格</div>
	    			</div>
	    			<div id="div_summary">
		    			<div class="chart_area">
			    			<div class="left_chart ">
			    				<canvas id="myChart" ></canvas>
			    			</div>
			    			<div class=" right_chart">
			    				<div>
			    					<div class="w-100" style="text-align: center;">
				    					<select id='parent_sport_category' onchange='get_leagues();'>
				    						<option value="2" selected="selected">篮球</option>
				    						<option value="1">足球</option>
				    						<option value="4">电竞</option>
				    					</select> 
				    					<select id='league_list' onchange="generateChartBar()">
				    					</select>
									</div>
			    				</div>
			    				<div class="bar_area" id="div_chart2">

			    				</div>
			    				<div style="padding-bottom: 20px; font-size: 10px;">
			    					<div>单月胜率</div>
			    					<div>主推月胜率</div>
			    					<div>神准预言家</div>
								</div>
					
			    			</div>
		    			</div>
		    			<div class="title_area "><span>预测历史</span><div class="mc_filter"><button class="more_btn" onclick="location.href = '/cn/prediction-history/index.php';">更多</button></div></div>
		    			<div class="history_table">
		    				<table class="table_style" id="table_prediction_history">
		    					
		    				</table>
		    			</div>
	    			</div>
	    			<div id="div_win_rate" style="display: none;">
	    				<div class="history_table" style="padding: 0px 75px;">
							<div class="mc_filter" style="padding-left: 0px!important;">
								<select id="parent_sport_category_rate" onchange="get_leagues_rate();">
									<option value="2" selected="selected">篮球</option>
									<option value="1">足球</option>
									<option value="4">电竞</option>
								</select>
								<select id="league_list_rate">
								</select>
								<select id="time_rate">
									<?php echo '<option value="'.date('Y/m', time()).'" selected="selected">'.date('Y/m', time()).'</option>'; ?>
									<?php echo '<option value="'.date('Y/m', strtotime('-1 month')).'">'.date('Y/m', strtotime('-1 month')).'</option>'; ?>
									<?php echo '<option value="'.date('Y/m', strtotime('-2 month')).'">'.date('Y/m', strtotime('-2 month')).'</option>'; ?>
								</select>
								<button type="button" onclick="get_winrate_table();" style="background-color: #ed1b34!important;">确认</button>
							</div>
						
							<table class="table_style" id="winrate_table">

							</table>
							<div class="tips">战绩于每日下午5点结算</div>
						</div>
	    			</div>
	    			<div id="div_qualification" style="display: none;">
	    				<div class="history_table" style="padding: 0px 75px;">
							<div class="mc_filter" style="padding-left: 0px!important;">
								<select id="parent_sport_category_prediction" onchange="get_leagues_prediction();">
									<option value="2" selected="selected">篮球</option>
									<option value="1">足球</option>
									<option value="4">电竞</option>
								</select>
								<select id="league_list_prediction">
								</select>
								<select id="time_prediction">
									<?php echo '<option value="'.date('Y/m', time()).'" selected="selected">'.date('Y/m', time()).'</option>'; ?>
									<?php echo '<option value="'.date('Y/m', strtotime('-1 month')).'">'.date('Y/m', strtotime('-1 month')).'</option>'; ?>
									<?php echo '<option value="'.date('Y/m', strtotime('-2 month')).'">'.date('Y/m', strtotime('-2 month')).'</option>'; ?>
								</select>
								<button name="search_button" type="button" onclick="get_prediction_table();" style="background-color: #ed1b34!important;">确认</button>
							</div>
							
								<table class="table_style" id="prediction_table">
								</table>
								<div class="tips">下期评选日:<?php echo date('m/2', strtotime('+1 month')); ?>, 评选期间:<?php echo date('m/01', time()).'~'.date('m/d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')); ?></div>
							</div>
	    			</div>
	    		</div>
	    	</div>
		<?php include '../layout/footer.php'; ?>
    	</div>
    	<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLongTitle">个人资料</h5>
		      
		      </div>
		      <div class="modal-body" style='text-align: center;'>
			   <span style='color:grey'>完善资料即可获得100战数和3张卷</span>
			  	<form class="kt-form" id="form">
		      		<input type="text" id='form_name' placeholder="姓名">
		      		<input type="text" id='form_phone' placeholder="手机号">
		      		<input type="text" id='form_email' placeholder="邮箱">
		      		<input type="text" id='form_address' placeholder="地址">
		      		<input type="text" id="datepicker" data-date-format='yyyy-mm-dd' placeholder="生日日期">
		      		<input type="text" id='form_weibo' placeholder="微博">
				</form>
				
		      </div>
		      <div class="modal-footer">
		       <button type="button" class="cancel_btn" onclick="$('#form').trigger('reset');">清除</button> 
			   <button type="button" class="submit_btn" onclick="update_member_info();">完成</button>
		      </div>
		    </div>
		  </div>
		</div>
    	
		
		<div class="modal fade" id="resetpw" tabindex="-1" role="dialog"  aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">重置密码</h5>
				  </div>
				  <div class="modal-body" style='text-align: center;'>
					<span style="color:grey">为了您的账号安全，建议您定期修改密码，增加您账户密码强度</span>
					<form id="reset_pw_form">
						<input id='txt_password' placeholder="新密码" type='password'>
						<input id='txt_confirmpw' placeholder="确认新密码" type='password'>
					</form>
					<div style="width: 100%;text-align: center;">
						<div>
						<button style="background-color: #797979;" onclick="$('#reset_pw_form').trigger('reset');">
							清除
						</button>
							<button  onclick="change_password();">送出</button>
						</div>
					</div>	   
				  </div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="upload_user_icon" tabindex="-1" role="dialog"  aria-hidden="true">
		   <div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">头像管理</h5>
					
				  </div>
				  <div class="modal-body">
				   
						<div style='width: 100%;padding-bottom: 15px; text-align: center;'>
							
								<span style="color:grey;">请上传150X150，不超过200K之图像</span>
						 	<img id='current_thumbnail' src="" width='150' height='150' style='display: block;max-width: 100%;margin: 0 auto;margin-top: 15px;'>
						 	
						</div>									
						
						<div style="width: 100%;text-align: center;">
							<form action="#" data-redirect="/cn/profile/" method="POST" id="user_image_form">
								<input type="hidden" name="tempfile" id='tempfile' value="">
								<input type="hidden" name="folder" id='folder' value="user_image">		
							</form>
							
							<button style="display:block;width:140px; height:30px;margin-left: 160px;border-radius: 8px;" onclick="document.getElementById('file2').click()">选择图像</button>
							<input type='file' id="file2" name="file2" accept="image/x-png,image/gif,image/jpeg" onchange='local_media_ajax_submit()' style="display:none">
						</div>
						<div style="width: 100%;text-align: center;padding:10px">
							<button class="submit_btn" id="crop_button" style='display: none;margin-left: 150px;'>裁剪</button> 
						</div>
						<div style='text-align:center'>
							<div id='cropped_preview_msg' style='display: none;padding: 10px;font-size: 18px;padding-top: 30px;'><u>头像预览</u></div>
							<div id='result' style='width: 100%;' style='display: none'>
								<img id='cropped_image' >
								<button class="submit_btn" id="upload_button" style='display: block;margin-left: 160px;'>上传</button> 
							</div>
						</div>
									   
				  </div>
				  
				</div>
			  </div>
		</div>
		
    </body>
    <footer>
   

    </footer>
	</html>
   
    <script type="text/javascript">
    var admin_accounts = ['神采熙燕','好运少女','魅炎射手','霸气姆斯','库里大蓉','怼天怼地小能手','佛系男子','火箭龟小威'];
	
	function in_array(search, array) {
	    for(var i in array){
	        if(array[i]==search){
	            return true;
	        }
	    }
	    return false;
	}
	
	$(document).ready(function() {
		$("#datepicker").datepicker();

		setTimeout(function() { 
			$('#profile_thumbnail').attr('src',window.localStorage.profile_thumbnail);
			$('#user_profile_bg').attr('src', '/assets/images/user_level_banner/lvl' + window.localStorage.level_id + '.jpg');
			$('#div_points').html(window.localStorage.total_points + ' <span>本周 +' + window.localStorage.weekly_points + '</span>');
			$('#div_current_points').html(window.localStorage.points);
			$('#div_voucher').html(window.localStorage.voucher);
			$('#div_username').html(window.localStorage.username);
		}, 1000);

		if (in_array(window.localStorage.username, admin_accounts)) {
			$('#div_userlevel').html('<img src="/assets/images/admin_icon.png" style="width: 50px;">');
		}
		else {
			$('#div_userlevel').html(window.localStorage.level + '<img src="/assets/images/user_level_icon/lvl' + window.localStorage.level_id + '_red.png" style="width:25px;height:25px;margin-bottom: 5px;">');
		}

		$('#parent_sport_category').change();
		get_prediction_history();


		var complete_info = getQueryString('complete_info');
		if (complete_info==1)
			$('#editProfileModal').modal('show');


		$('#parent_sport_category_rate').change();
		$('#parent_sport_category_prediction').change();
    });
	
	
	
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
      };
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
					
					after_media_meta(	
									{
										media_meta_data: obj,
										extra:obj.extra,
										params: {
											extra: response.extra,
										}
									}
					);
				}
			})
	});
	
	function get_prediction_history () {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }
        $('#table_prediction_history').html('<tr><td colspan="8" style="text-align: center">加载中...</td></tr>');
		var category_id = 'all';
		var league_id = 'all';
		var year = 'all';
		var month = 'all';

		var html = '<tr><th>预测时间</th><th>比赛时间</th><th>让球</th><th>大小</th><th>独赢</th><th>总得战数</th><th>状态</th><th>备注</th></tr>';

        $.ajax({
            url: getBackendHost() + '/service/prediction.php',
            type: 'post',
            data: {"action":"get_prediction_history","category_id":category_id,"league_id":league_id,"year":year,"month":month,"sorting1":2,"sorting2":2,"sorting3":2,"page":1,"euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	$.each(response.list, function (index, value) {
            		html += '<tr><td>'+value.predicted_at+'</td><td>'+value.match_at+'</td><td>'+value.handicap+'</td><td>'+value.over_under+'</td><td>'+value.single+'</td><td>'+(value.win_amount>0?'+'+value.win_amount:value.win_amount)+'</td><td>'+value.status+'</td><td>-</td></tr>';
            	});
            	$('#table_prediction_history').html(html);
            },
            error: function () {

            },
        });
	}

	function get_leagues () {
		var category_id = $("#parent_sport_category").val();

		$("#league_list").html('<option>加载中...</option>');

        $.ajax({
            url: getBackendHost() + '/service/match.php',
            type: 'get',
            data: {"action":"get_leagues","category_id":category_id},

            success: function (response, status, xhr) {
            	var html = '';
            	$.each(response, function (index, value) {
                    html += '<option value="'+value.id+'" '+(index==0?'selected="selected"':'')+'>'+value.name_zh+'</option>';
                });
                $("#league_list").html(html);
                $("#league_list").change();
            },
            error: function () {

            },
        });
	}

	function get_leagues_rate() {
		var category_id = $("#parent_sport_category_rate").val();

		$("#league_list_rate").html('');

        $.ajax({
            url: getBackendHost() + '/service/match.php',
            type: 'get',
            data: {"action":"get_leagues","category_id":category_id},

            success: function (response, status, xhr) {
            	var html = '';
            	$.each(response, function (index, value) {
                    html += '<option value="'+value.id+'" '+(index==0?'selected="selected"':'')+'>'+value.name_zh+'</option>';
                });
                $("#league_list_rate").append(html);
            },
            error: function () {

            },
        });
	}

	function get_leagues_prediction() {
		var category_id = $("#parent_sport_category_prediction").val();

		$("#league_list_prediction").html('');

        $.ajax({
            url: getBackendHost() + '/service/match.php',
            type: 'get',
            data: {"action":"get_leagues","category_id":category_id},

            success: function (response, status, xhr) {
            	var html = '';
            	$.each(response, function (index, value) {
                    html += '<option value="'+value.id+'" '+(index==0?'selected="selected"':'')+'>'+value.name_zh+'</option>';
                });
                $("#league_list_prediction").append(html);
            },
            error: function () {

            },
        });
	}

	function generateChartBar() {
		var category_id = $('#parent_sport_category').val();
		var league_id = $('#league_list').val();

        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"get_prediction_stats","category_id":category_id,"league_id":league_id,"euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	var html = template.render($("#chart2_tpl").html(), {"data": response});
				$("#div_chart2").html(html);
				showChart(parseInt(response.points)/ceilNumber(response.points)*100, parseInt(response.top_ten_count)/ceilNumber(response.top_ten_count)*100, parseInt(response.voucher)/ceilNumber(response.voucher)*100, (response.prediction_count > response.prediction_total_count?100:parseInt(response.prediction_count/response.prediction_total_count*100)), response.total_win_rate,parseInt(response.points),parseInt(response.top_ten_count),parseInt(response.voucher));
            },
            error: function () {

            },
        });
	}

	function showChart(a,b,c,d,e,f,g,h){//"战数", "预言次数", "神级兑换卷", "参与率", "总胜率",'预言次数' display
		var marksData = {
			labels: ["战数:"+f, "预言家次数:"+g, "神级兑换卷:"+h, "参与率", "总胜率"],
			datasets: [{
				label: "Student A", 
				backgroundColor: "rgba(255,74,96,0.3)",
				pointBorderWidth:"1",
				pointBackgroundColor:"rgba(255,108,126)",
				borderColor:"rgba(255,121,136,1)",
				data: [a,b,c,d,e]
			  }]
				
		};
		var ctx = $('#myChart');
		
		var myChart = new Chart(ctx, {
				  type: 'radar',
				   data: marksData,
				  options: {
				  	scale: {
						        ticks: {
						            // changes here
						          
							        max: 100,
							        min: 0
							       
						        }
						    },
						legend: {
							display: false,
							labels: {
								fontColor: 'rgb(255, 99, 132)'
							}
						},
						tooltips: {
							enabled: true,
							callbacks: {
								title: function(context,data) {
									console.log();
									return data.labels[context[0].index];
								},
								label: function(tooltipItem, data) {
								var label = '';

							  
								label += Math.round(tooltipItem.yLabel * 100) / 100;
								
								return label;
							}, scale: {
					            ticks: {
					                max: 500
					               
					            }
					        }

							}
						}
					}

		  });
	}

	function ceilNumber(number) {
	    var bite = 0;
	    if (number < 10) {
	        return 10;
	    }
	    while (number >= 10) {
	       number /= 10;
	       bite += 1;
	    }
	    return Math.ceil(number) * Math.pow(10, bite);
	}

	function change_password() {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }

		var password = $.trim($('#txt_password').val());
		var confirm_password = $.trim($('#txt_confirmpw').val());

		if (password == '' || confirm_password == '') {
			return;
		}
		if (password != confirm_password) {
			alert('密码与确认密码不一致');
			return;
		}

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"update_password","euid":euid,"password":password},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	$("#resetpw").modal("hide");
            	alert(response.message);
            },
            error: function () {

            },
        });
	}

	function show_member_info () {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"getextrainfo","euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	$('#form_name').val(response.user.name);
				$('#form_phone').val(response.user.phone);
				$('#form_email').val(response.user.email);
				$('#form_address').val(response.user.address);
				$('#datepicker').val(response.user.birth_at);
				$('#form_weibo').val(response.user.weibo);
            	$("#editProfileModal").modal("show");
            },
            error: function () {

            },
        });
	}

	function update_member_info() {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }
        var name = $.trim($('#form_name').val());
		var phone = $.trim($('#form_phone').val());
		var email = $.trim($('#form_email').val());
		var address = $.trim($('#form_address').val());
		var birth_at = $.trim($('#datepicker').val());
		var weibo = $.trim($('#form_weibo').val());

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"update_userinfo","euid":euid,"name":name,"phone":phone,"email":email,"address":address,"birth_at":birth_at,"weibo":weibo},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	$("#editProfileModal").modal("hide");
            	alert(response.message);
            },
            error: function () {

            },
        });
	}

	function change_area(index) {
		if (index == 1) {
			$('#div_index1').addClass('active');
			$('#div_index2').removeClass('active');
			$('#div_index3').removeClass('active');
			$('#div_summary').show();
			$('#div_win_rate').hide();
			$('#div_qualification').hide();
		}
		else if (index == 2) {
			$('#div_index1').removeClass('active');
			$('#div_index2').addClass('active');
			$('#div_index3').removeClass('active');
			$('#div_summary').hide();
			$('#div_win_rate').show();
			$('#div_qualification').hide();
		}
		else if (index == 3) {
			$('#div_index1').removeClass('active');
			$('#div_index2').removeClass('active');
			$('#div_index3').addClass('active');
			$('#div_summary').hide();
			$('#div_win_rate').hide();
			$('#div_qualification').show();
		}
	}

	function get_winrate_table() {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }
		var category_id =  $('#parent_sport_category_rate').val();
		var league_id = $('#league_list_rate').val();
		var time = $('#time_rate').val();

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"get_prediction_rate","euid":euid,"category_id":category_id,"league_id":league_id,"time":time},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	var html = template.render($("#win_rate_table_tpl").html(), {"data": response});
				$("#winrate_table").html(html);
            },
            error: function () {

            },
        });
	}

	function get_prediction_table() {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
        	return;
        }
		var category_id =  $('#parent_sport_category_prediction').val();
		var league_id = $('#league_list_prediction').val();
		var time = $('#time_prediction').val();

        $.ajax({
            url: getBackendHost() + '/service/user.php',
            type: 'post',
            data: {"action":"get_prediction_qualification","euid":euid,"category_id":category_id,"league_id":league_id,"time":time},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
            	var html = template.render($("#prediction_table_tpl").html(), {"data": response});
				$("#prediction_table").html(html);
            },
            error: function () {

            },
        });
	}

	//this one for preview only, when file onchange display uploaded image to crop
	function local_media_ajax_submit() {
		
		$_form = document.getElementById('user_image_form');
	    var formData = new FormData($_form);

		var file2 = $('#file2')[0].files[0];
		formData.append('file', file2);
		
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
	            //console.log(resp);
	            ////console.log(resp);
	            alert("Problem occurred while sending request.");
	        },
	    });
	}

	function after_media_meta($data) {
		
		//console.log($data);
		var json_form_obj = {
	      "image_data":{},
		 
		};
		var extra=$data.media_meta_data.extra;
		var pic_url='/'+$data.media_meta_data.url;
		
		json_form_obj['id']=window.localStorage.user_id;
		json_form_obj['url'] =$('input[name="url"]').val();
		json_form_obj.image_data.url=$data.media_meta_data.url;
		json_form_obj.image_data.name=$data.media_meta_data.name;
		json_form_obj.image_data.type=$data.media_meta_data.type;
		json_form_obj.image_data.size=$data.media_meta_data.filesize;
		////console.log($data);
		
		json_form_obj.extra=$data.extra;
	    ////console.log("after_media_meta + "+image_size);
	    ////console.log(JSON.stringify(json_form_obj));
		////console.log(link + '/api/cn/promotion');
		
		var formData = JSON.stringify(json_form_obj);
		//console.log(formData);
		 $.ajax({
	        type: 'PUT',
	        //url: getBackendHost() + $action,
	        url: link + '/api/cn/user',
	        crossDomain: true,
	        headers: getHeaders(),
	        contentType: false,
	        processData: true,
	        // contentType: "charset=utf-8",
	        data: formData,
	        success: function (response, status, xhr) {
				////console.log(image_size);
				//console.log(response);
				
	            obj = response;
				
				
				media_save(obj,extra,pic_url);
	        },
			
	        error: function () {
	           alert("Problem occurred while sending request.");
	        },
	    });
	}

	function media_save(obj,extra,pic_url){
		//var fan_id= getQueryString('id');
		var msg='';
		var redirect_url='';
		
		//console.log(obj)
		$('#tempfile').val(extra);
		$_form = document.getElementById('user_image_form');
	    var formData = new FormData($_form);
		
	    //formData.append('tempfile', obj.extra.extra)
		
		 $.ajax({
	        type: 'POST',
	       // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
		    //url: getHost()+'/assets/php/media-meta.php',
	        url:  '/assets/php/media-save.php',
	         data: formData,
	        crossDomain: true,
	        contentType: false,
	        processData: false,
			 success: function (response, status, xhr) {
	           //console.log(response);
				//console.log('yeah');
	            obj = response;
				
				
				window.localStorage.profile_thumbnail = pic_url;
				alert('成功设置新头像！');
				redirect_to(getCurrentFullUri());
				
			
	           // if (big_indicator==true && medium_indicator==true && small_indicator==true)
				//	addPromo_all($form,$data,$error_selector = $(".message_output"));
			//redirect_to("fan-zone-edit.html?id="+promo_id+"&alert-success=" + msg);
	        },
	        error: function (resp) {
	           
	            ////console.log(resp);
	             alert("Problem occurred while sending request.");
	        },
	    });
	}
  </script>

  <script type="text/html" id="chart2_tpl">
	<div>
		<div style="padding-top: calc(75px - 75px * {{data.win_rate/100}})">{{data.win_rate}}%</div>
		<img src="/assets/images/red_col.png">
	</div>
	<div>
		<div style="padding-top: calc(75px - 75px * 0)">0%</div>
		<img src="/assets/images/yellow_col.png">
	</div>
	<div>
		<div style="padding-top: calc(75px - 75px * {{data.top_ten_count/10}})">{{data.top_ten_count}}</div>
		<img src="/assets/images/gray_col.png">
	</div>
  </script>

  <script type="text/html" id="win_rate_table_tpl">
	<tr>
		<th>项目</th><th>胜场</th><th>败场</th><th>胜率[排名]</th>
	</tr>
	<tr>
		<td>让分</td><td>{{data.handicap.win_count}}</td><td>{{data.handicap.lose_count}}</td><td>{{data.handicap.rate}}%[{{data.handicap.rank}}]</td>
	</tr>
	<tr>
		<td>大小分</td><td>{{data.over_under.win_count}}</td><td>{{data.over_under.lose_count}}</td><td>{{data.over_under.rate}}%[{{data.over_under.rank}}]</td>
	</tr>
	<tr>
		<td>独赢</td><td>{{data.single.win_count}}</td><td>{{data.single.lose_count}}</td><td>{{data.single.rate}}%[{{data.single.rank}}]</td>
	</tr>
	<tr>
		<td>总胜场</td><td>{{data.total.win_count}}</td><td>{{data.total.lose_count}}</td><td>{{data.total.rate}}%[{{data.total.rank}}]</td>
	</tr>
  </script>

  <script type="text/html" id="prediction_table_tpl">
	<tr><th>项目</th><th>目前累积战绩</th><th>神级预言家资格</th><th>达到标准</th></tr>
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