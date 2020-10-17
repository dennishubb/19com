<?php
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.modal.css">
<?php 
    include("style_script.php"); 
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<title>19资讯 - 赛事结果</title>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container">
            <div class="prediction_container">
                <div class="prediction_menu">
                    <button class="button_style_grey" onclick="window.location='prediction.php'">赛事预测</button>
                    <button class="active" onclick="window.location='prediction_result.php'">赛事结果</button>
                    <a href="#" class="prediction_guide"><img src="img/question_mark_icon.png">教学</a>
                </div>

                <div>
                    <div class="profile_subpage_filter_container">
                        <div class="profile_subpage_filter_content">
                            <div class="content_block_row">
                                <div class="content_block">
									<select id='parent_sport_category' onchange='get_leagues();'>
										<option value="all">体育类别</option>
										<option value="1">足球</option>
										<option value="2">篮球</option>
										<option value="4">电竞</option>
									</select>
                                </div>
                                <div class="content_block">
									<select class="w-100" id='league_list'><option value="all">联赛</option></select>
                                </div>
                            </div>

                            <div class="content_block_row">
                                <div class="content_block">
                                    <select class="w-100" id='year'>
										<option value="">年份</option>
										<?php 
											echo '<option value="'.date("Y",strtotime("-2 year")).'">'.date("Y",strtotime("-2 year")).'</option>';
											echo '<option value="'.date("Y",strtotime("-1 year")).'">'.date("Y",strtotime("-1 year")).'</option>';
											echo '<option value="'.date("Y",time()).'">'.date("Y",time()).'</option>';
										?>
                                    </select>
                                </div>
                                <div class="content_block">
									<select class="w-100" id='month'>
										<option value="">月份</option>
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
                                </div>
                                <div class="content_block">
                                    <button class="button_style_dark w-100" onclick="get_result(1);">确认</button>
                                </div>
                            </div>
                        </div>
                    </div>
					<div id="result_table">
					
					</div>
                </div>
            </div>
        </div>
    </div>
	
	<div class="modal" id="editorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">预测简介</h5></div>
				<div class="modal-body">
					<div id="div_note">

					</div>
					<button style='display:block; margin:auto;' onclick="close_modal()">关闭</button>
				</div>
			</div>
		</div>
	</div>

    <?php
        include("footer.php");
    ?>
	
	<script>
		
		var sorting = 2;
		var page = 1;
		var is_loading = false;

		$(function(){
			get_result(1);
		});
		
		function get_result(page_no) {
			
			if(is_loading) return;
			is_loading = true;
			
			if(page_no > 0){ 
				page = page_no;
			}
			
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
					if (page == 1) {
						var html = template.render($("#result_list_tpl").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page, "sorting":sorting});
                    	$("#result_table").html(html);
					}
					else {
						var html = template.render($("#result_list_tpl2").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page, "sorting":sorting});
						$("#result_table_body").append(html);
					}
					page = response.current_page;
					is_loading = false;
	            	
	            },
	            error: function () {

	            },
	        });
		}
		
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
		
		function close_modal(){
			$("#editorModal").modal("hide");
		}

		function delHtmlTag (str){
			return str.replace(/&nbsp;/g, " ").replace(/<br>/g, "\n").replace(/<[^>]+>/g,"");
		}
		
		var winH = $(window).height();
		var scrollHandler = function () {
			var pageH = $(document.body).height();
			var scrollT = $(window).scrollTop();
			var aa = (pageH - winH - scrollT) / winH;
			if (aa < 0.02) {
				page++;
				get_result(page);
			}
		}
		$(window).scroll(scrollHandler);
						
	</script>
</body>
</html>
<script type="text/html" id="result_list_tpl">
	<div class="prediction_row">
		<div class="table_container">
			<table style="width: 700px;">
				<thead>
					<tr>
						<td width="10%">比赛时间</td>
						<td width="9%">联赛名称</td>
						<td width="9%">比赛队伍</td>
						<td width="9%">获胜队伍</td>
						<td width="9%">让球</td>
						<td width="9%">大小</td>
						<td width="9%">独赢</td>
						<td width="9%">预测简介</td>
						<td width="9%">状态</td>
					</tr>
				</thead>
				<tbody id="result_table_body">
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
	</div>
</script>

<script type="text/html" id="result_list_tpl2">
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
</script>