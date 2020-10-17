<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<!DOCTYPE html>
<html lang="zh-hans">
    <head >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
		<!-- <meta content="yes" name="apple-mobile-web-app-capable"> -->
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta content="telephone=no" name="format-detection">
		<meta content="email=no" name="format-detection">
        <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">
    	<link rel="stylesheet" href="/assets/css/swiper.min.css"/>
       
        <script type="text/javascript" src="/assets/js/fo_common/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="/assets/js/fo_common/bootstrap.min.js"></script>
    	<script type="text/javascript" src="/assets/js/fo_common/swiper.min.js"></script>
        <link rel="stylesheet" href="/assets/css/main.css" />
        <link rel="stylesheet" href="/assets/fontawesome-free-5.13.0-web/css/all.css" />
         
		<script type="text/javascript" src="/assets/js/art-template-master/lib/template-web.js"></script>
		<script src="/assets/js/common/utility.js"></script>
		<script src="/assets/js/fo_common/shared_new.js"></script>
	 	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js" type="text/javascript"></script>
    	<title>19资讯 - 预测历史</title>
        
		<style>
			.sort_arrow{
			color: white;font-size: 20px;margin-left: 8px;
		}
			.centerClass {
				display: block;
				margin: 0 auto;
		}
		</style>

    </head>
    <body>
		<?php include '../layout/header.php'; ?>
		 
	    <div class="main_area">
	    	<div class="profile_bg"> 
	    		<img src="/assets/images/profile_bg.jpg">
	    	</div>
	    	<div class="message_collect layout1200" id='match_history_body'> 
					<div class="title_area ">
						<span id='table_title'>预测历史</span>
						<span id='sort_msg' > </span>
						<div class="mc_filter">
							<select id='parent_sport_category' onchange='get_leagues();'>
								<option value="all">---体育类别---</option>
								<option value="1">足球</option>
								<option value="2">篮球</option>
								<option value="4">电竞</option>
							</select>
							<select id='league_list'><option value="all">---联赛---</option></select>
							<select id='year'>
								<option value="all">---赛事年份---</option>
								<?php 
									echo '<option value="'.date("Y",strtotime("-2 year")).'">'.date("Y",strtotime("-2 year")).'</option>';
									echo '<option value="'.date("Y",strtotime("-1 year")).'">'.date("Y",strtotime("-1 year")).'</option>';
									echo '<option value="'.date("Y",time()).'">'.date("Y",time()).'</option>';
								?>
							</select>
							<select id='month'>
								<option value="all">---赛事月份---</option>
								<option value="1">一月</option>
								<option value="2">二月</option>
								<option value="3">三月</option>
								<option value="4">四月</option>
								<option value="5">五月</option>
								<option value="6">六月</option>
								<option value="7">七月</option>
								<option value="8">八月</option>
								<option value="9">九月</option>
								<option value="10">十月</option>
								<option value="11">十一月</option>
								<option value="12">十二月</option>
							</select>
							<button type='button' onclick="get_result(1);">确认</button>
						</div>
					</div>
					<div id='match_history_table_body'>
						<div>
							<table class="table_style2">
								<thead>
									<tr>
										<th>预测时间 <i id="i_sorting" class="fas fa-sort-down" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
										<th>比赛时间 <i id="i_sorting" class="fas fa-sort-down" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
										<th>联赛名称</th>
										<th>让球</th>
										<th>大小</th>
										<th>独赢</th>
										<th>预测结果 <i id="i_sorting" class="fas fa-sort-down" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
										<th>状态</th>
									</tr>
								</thead>
								<tbody id="result_list">
									<tr><td colspan="8">加载中...</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php include '../layout/footer.php'; ?>
    	<svg class="inline-svg">
			<symbol id="check" viewbox="0 0 12 10">
				<polyline points="1.5 6 4.5 9 10.5 1"></polyline>
			</symbol>
		</svg>
    </body>
   
    <script>
    	var sorting1 = 2;
    	var sorting2 = 2;
    	var sorting3 = 2;
		$(document).ready(function() {
			get_result(1);
		});

		function get_leagues () {
			var category_id = $("#parent_sport_category").val();
			if (category_id == 'all') {
				$("#league_list").html('<option value="all">---联赛---</option>');
			}
			else {
		        $.ajax({
		            url: getBackendHost() + '/service/match.php',
		            type: 'get',
		            data: {"action":"get_leagues","category_id":category_id},

		            success: function (response, status, xhr) {
		            	$("#league_list").html('<option value="all">---联赛---</option>');
		            	var html = '';
		            	$.each(response, function (index, value) {
	                        html += '<option value="'+value.id+'">'+value.name_zh+'</option>';
	                    });
	                    $("#league_list").append(html);
		            },
		            error: function () {

		            },
		        });
			}
		}

		function get_result(page) {
	        var euid = Cookies.get('euid');
	        if (euid == undefined) {
	        	return;
	        }
			var category_id = $("#parent_sport_category").val();
			var league_id = $("#league_list").val();
			var year = $("#year").val();
			var month = $("#month").val();

	        $.ajax({
	            url: getBackendHost() + '/service/prediction.php',
	            type: 'post',
	            data: {"action":"get_prediction_history","category_id":category_id,"league_id":league_id,"year":year,"month":month,"sorting1":sorting1,"sorting2":sorting2,"sorting3":sorting3,"page":page,"euid":euid},
	            crossDomain: true,
	            xhrFields: {
	                withCredentials: true
	            },

	            success: function (response, status, xhr) {
	            	// console.log(response);
	            	var html = template.render($("#result_list_tpl").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page, "sorting1":sorting1, "sorting2":sorting2, "sorting3":sorting3});
                    $("#match_history_table_body").html(html);
	            },
	            error: function () {

	            },
	        });
		}

		function do_sorting1 () {
			sorting2 = 0;
			sorting3 = 0;
			if (sorting1 == 1) {
				sorting1 = 2;
			}
			else {
				sorting1 = 1;
			}
			get_result(1);
		}
		function do_sorting2 () {
			sorting1 = 0;
			sorting3 = 0;
			if (sorting2 == 1) {
				sorting2 = 2;
			}
			else {
				sorting2 = 1;
			}
			get_result(1);
		}
		function do_sorting3 () {
			sorting1 = 0;
			sorting2 = 0;
			if (sorting3 == 1) {
				sorting3 = 2;
			}
			else {
				sorting3 = 1;
			}
			get_result(1);
		}
	</script>
</html>

<script type="text/html" id="result_list_tpl">
	<div>
		<table class="table_style2">
			<thead>
				<tr>
					<th>预测时间 <i onclick="do_sorting1();" class="fas {{sorting1==2?'fa-sort-down':'fa-sort-up'}}" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
					<th>比赛时间 <i onclick="do_sorting2();" class="fas {{sorting2==2?'fa-sort-down':'fa-sort-up'}}" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
					<th>联赛名称</th>
					<th>让球</th>
					<th>大小</th>
					<th>独赢</th>
					<th>预测结果 <i onclick="do_sorting3();" class="fas {{sorting3==2?'fa-sort-down':'fa-sort-up'}}" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th>
					<th>状态</th>
				</tr>
			</thead>
			<tbody id="result_list">
				{{if (total_page == 0) }}
					<tr><td colspan="8">暂无记录</td></tr>
				{{else}}
					{{each list value index}}
						<tr>
							<td>{{value.predicted_at}}</td>
							<td>{{value.match_at}}</td>
							<td>{{value.league_name}}</td>
							<td>{{value.handicap}}</td>
							<td>{{value.over_under}}</td>
							<td>{{value.single}}</td>
							<td>
								{{if (value.win_amount > 0) }}
									<span style="color: red;">+{{value.win_amount}}</span>
								{{else if (value.win_amount < 0)}}
									<span style="color: green;">{{value.win_amount}}</span>
								{{else}}
									<span>-</span>
								{{/if}}
							</td>
							<td>{{value.status}}</td>
						</tr>
				    {{/each}}
				{{/if}}
			</tbody>
		</table>
	</div>
	<div class="pagination_area layout1200">
		<nav aria-label="Page navigation example">
			<ul class="pagination">
				<li class="page-item"><a class="page-link" href="javascript:get_result({{current_page==1?1:current_page-1}});" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                <% for(var i = 1; i <= total_page; i++){ %>
                <li class="page-item {{i==current_page?'active':''}}"><a class="page-link" href="javascript:get_result(<%=i %>);"><%=i %></a></li>
                <% } %>
				<li class="page-item"><a class="page-link" href="javascript:get_result({{current_page<total_page?current_page+1:total_page}});" aria-label="Next"><span aria-hidden="true">»</span></a></li>
			</ul>
		</nav>
  	</div>
</script>