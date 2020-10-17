<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
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
    	<?php include_once('layout/resource.php'); ?>
	 	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js" type="text/javascript"></script>
    	<title>19资讯 - 赛事结果</title>
        
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
		<?php include 'layout/header.php'; ?>
		 
	    <div class="main_area">
	    	<div class="profile_bg"> 
	    		<img src="/assets/images/profile_bg.jpg">
	    	</div>
	    	<div class="message_collect layout1200" id='match_history_body'> 
					<div class="title_area ">
						<span id='table_title'>赛事结果</span>
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
								<option value="">---赛事年份---</option>
								<?php 
									echo '<option value="'.date("Y",strtotime("-2 year")).'">'.date("Y",strtotime("-2 year")).'</option>';
									echo '<option value="'.date("Y",strtotime("-1 year")).'">'.date("Y",strtotime("-1 year")).'</option>';
									echo '<option value="'.date("Y",time()).'">'.date("Y",time()).'</option>';
								?>
							</select>
							<select id='month'>
								<option value="">---赛事月份---</option>
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
									<tr><th>比赛时间 <i id="i_sorting" onclick="match_sorting();" class="fas fa-sort-down" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th><th>联赛名称</th><th>比赛队伍</th><th>获胜队伍</th><th>让球</th><th>大小</th><th>独赢</th><th>预测简介</th><th id="status_th">状态</th></tr>
								</thead>
								<tbody id="result_list">
									<tr><td colspan="9">加载中...</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php include 'layout/footer.php'; ?>
    	<svg class="inline-svg">
				<symbol id="check" viewbox="0 0 12 10">
					<polyline points="1.5 6 4.5 9 10.5 1"></polyline>
				</symbol>
			</svg>
			<div class="modal" id="editorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
				    <div class="modal-content">
				        <div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">预测简介</h5></div>
				        <div class="modal-body">
					        <div id="div_note">

					        </div>
				        </div>
				    </div>
				</div>
			</div>
			
    </body>
   
    <script>
    	var sorting = 2;
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
			// $("#result_list").html('<tr><td colspan="9">加载中...</td></tr>');
			var category_id = $("#parent_sport_category").val();
			var league_id = $("#league_list").val();
			var year = $("#year").val();
			var month = $("#month").val();

	        $.ajax({
	            url: getBackendHost() + '/service/prediction.php',
	            type: 'get',
	            data: {"action":"get_match_result","category_id":category_id,"league_id":league_id,"year":year,"month":month,"sorting":sorting,"page":page},

	            success: function (response, status, xhr) {
	            	// console.log(response);
	            	var html = template.render($("#result_list_tpl").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page, "sorting":sorting});
                    $("#match_history_table_body").html(html);
	            },
	            error: function () {

	            },
	        });
		}


		function match_sorting(){
			if (sorting == 2) {
				sorting = 1;
			}
			else if (sorting == 1) {
				sorting = 2;
			}
			get_result(1);
		}

		function show_note(id) {
			$("#div_note").html($("#hid_note_"+id).val());
			$("#editorModal").modal("show");
		}
	</script>
</html>

<script type="text/html" id="result_list_tpl">
	<div>
		<table class="table_style2">
			<thead>
				<tr><th>比赛时间 <i id="i_sorting" class="fas {{sorting==2?'fa-sort-down':'fa-sort-up'}}" onclick="match_sorting();" style="cursor:pointer;color: white;font-size: 20px;margin-left: 8px;"></i></th><th>联赛名称</th><th>比赛队伍</th><th>获胜队伍</th><th>让球</th><th>大小</th><th>独赢</th><th>预测简介</th><th id="status_th">状态</th></tr>
			</thead>
			<tbody id="result_list">
				{{if (total_page == 0) }}
					<tr><td colspan="9">暂无记录</td></tr>
				{{else}}
					{{each list value index}}
						<tr>
							<td>{{value.match_at}}</td>
							<td>{{value.league_name}}</td>
							<td>{{value.home_team_name}}<br>VS<br>{{value.away_team_name}}</td>
							<td>{{value.win_team_name}}</td>
							<td>{{value.handicap}}</td>
							<td>{{value.over_under}}</td>
							<td>{{value.single}}</td>
							<td><button onclick="show_note({{value.id}})">查看</button><input type="hidden" id="hid_note_{{value.id}}" value="{{value.editor_note}}"></td>
							<td>已结束</td>
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