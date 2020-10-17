<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
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
    <script type="text/javascript" src="/assets/js/art-template-master/lib/template-web.js"></script>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" type="text/css" />
    <script type="text/javascript" src="/assets/js/fo_common/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/assets/js/fo_common/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="/assets/css/jquery-ui.min.css"/>
    <link rel="stylesheet" href="/assets/css/main.css"/>
    <link rel="stylesheet" href="/assets/fontawesome-free-5.13.0-web/css/all.css" />
	<script type="text/javascript" src="/assets/js/fo_common/swiper.min.js"></script>
	<link rel="stylesheet" href="/assets/css/swiper.min.css"/>
    <script src="/assets/js/common/utility.js"></script>
    <script type="text/javascript" src="/assets/js/fo_common/shared_new.js"></script>
    <title>19资讯 - 留言收藏</title>
</head>
    <body>
		<?php include '../layout/header.php'; ?>
        <div class="main_area">
			<div class="profile_bg">
				<img src="/assets/images/profile_bg.jpg">
			</div>
			<div class="message_collect layout1200">
				<div class="title_area ">
					<span>留言收藏</span>
					<div class="mc_filter">
						<input id="datepicker" data-date-format='yyyy-mm-dd' placeholder="日期" onchange="get_message(1);">
						<select id="bulk_edit">
							<option value="">批量操作</option>
							<option value="bulk_cancel">取消收藏</option>
						</select>
						<button onclick="bulk_cancel();">确认</button>
					</div>
				</div>
				<div id="div_message_list">

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
	<script type="text/javascript">
		var sorting = 2;
		var current_page = 1;

	    $(document).ready(function() {
	    	$("#datepicker").datepicker();
	    	get_message(1);
	    });

	    function get_message(page) {
	        var euid = Cookies.get('euid');
	        if (euid == undefined) {
	        	return;
	        }
	    	var date = $("#datepicker").val();

	        $.ajax({
	            url: getBackendHost() + '/service/message.php',
	            type: 'post',
	            data: {"action":"get_collected_message","date":date,"page":page,"euid":euid},
	            crossDomain: true,
	            xhrFields: {
	                withCredentials: true
	            },

	            success: function (response, status, xhr) {
	            	var html = template.render($("#message_tpl").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page, "sorting":sorting});
                    $("#div_message_list").html(html);
                    current_page = page;
	            },
	            error: function () {

	            },
	        });
	    }

	    function bulk_cancel () {
	    	if ($("#bulk_edit").val() == "bulk_cancel") {
			    confirm('确定取消收藏吗？').then(result => {
			        const confirmed = result.confirmed
			        if(!confirmed){
			            return;
			        }
			        else {
				        var euid = Cookies.get('euid');
				        if (euid == undefined) {
				        	return;
				        }

			    		var ids = '';
			    		$("input[type=checkbox]:checkbox:checked").each(function() {
			    			ids += $(this).attr('value') + ',';
			    		});
			    		ids = ids.substring(0, ids.length - 1);
			    		
				        $.ajax({
				            url: getBackendHost() + '/service/message.php',
				            type: 'post',
				            data: {"action":"cancel_colleted_message","ids":ids,"euid":euid},
				            crossDomain: true,
				            xhrFields: {
				                withCredentials: true
				            },

				            success: function (response, status, xhr) {
				            	alert(response.message);
				            	get_message(current_page);
				            },
				            error: function () {

				            },
				        });
			        }
			    });
	    	}
	    }
	</script>

	<script type="text/html" id="message_tpl">
		<table class="table_style2">
			<tr>
				<th width="5%">选取</th>
				<th width="15%">收藏时间<i id="toggle-icon" class="fas {{sorting==2?'fa-sort-down':'fa-sort-up'}}" style='cursor:pointer;color: white;font-size: 20px;margin-left: 8px;' onclick="sortByDate()"></i></th><th width="40%">留言内容</th><th width="40%">文章</th>
			</tr>
			{{if (total_page == 0) }}
			<tr><td colspan='9'>暂无记录</td></tr>
			{{else}}
				{{each list value index}}
				<tr>
					<td onclick="if($(this).children('input').attr('checked')){$(this).children('input').removeAttr('checked');}else{$(this).children('input').attr('checked', 'true');}">
						<input class="inp-cbx item" value="{{value.id}}" type="checkbox">
						<label class="cbx" for="{{value.id}}">
							<span>
								<svg width="12px" height="10px">
									<use xlink:href="#check"></use>
								</svg>
							</span>
						</label>
					</td>
					<td>{{value.created_at}}</td>
					<td>{{value.message}}</td>
					<td>{{value.article_title}}</td>
				</tr>
				{{/each}}
			{{/if}}
		</table>
		<div class="pagination_area layout1200">
			<nav aria-label="Page navigation example">
		      <ul class="pagination">
		        <li class="page-item"><a class="page-link page-pagination" href="javascript:get_message({{current_page==1?1:current_page-1}});" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
		        <% for(var i = 1; i <= total_page; i++){ %>
		        	<li class="page-item page-pagination  {{i==current_page?'active':''}}"><a class="page-link" href="javascript:get_message(<%=i %>);"><%=i %></a></li>
		        <% } %>
		        <li class="page-item"><a class="page-link page-pagination" href="javascript:get_message({{current_page<total_page?current_page+1:total_page}});" aria-label="Next"><span aria-hidden="true">»</span></a></li>
		      </ul>
		    </nav>
		</div>
	</script>
</html>

