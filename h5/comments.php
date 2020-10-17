<div id="comments" style="">
	<div class="prediction_title">
		<div>留言区（<span id='comment_count'>0</span>则回复）</div>
		<div>
			排序方式：
			<select id='comment_sort_type'>
				<option value='2'>热门</option>
				<option value='1'>最新</option>
			</select>
		</div>
	</div>

	<div class="prediction_comments_container">
		<div class="new_comments_container">
			<div class="new_comments">
				<div class="img"><img id="user_img" src="img/default_user_image.png"></div>
				<div class="comment"><input type="text" placeholder="新增回复..." class="create_new_comment" id="new"></div>
			</div>
			<div class="post_comment">
				<div><a class="post_comment_cancel">取消</a></div>
				<div><a class="post_comment_send" onclick=send(0)>回复</a></div>
			</div>
		</div>
		<div id="comments_area">
		</div>
	</div><!-- /.prediction_comments_container -->
</div>
<script>
	
	var chatroom_id = <?php echo $chatroom_id; ?>;
	var sorting = 2;
	var selfImg = window.localStorage.profile_thumbnail ? '<?php echo IMAGE_URL; ?>' + window.localStorage.profile_thumbnail : '<?php echo IMAGE_URL; ?>' + '/assets/images/default_user_image.png';
	
	$(function(){
		$(document).on("focus", ".create_new_comment", function(){
			$(".new_comments_container .post_comment").removeClass("active");
			$(this).parents(".new_comments_container").children(".post_comment").addClass("active");
		});

		$(document).on("click", ".post_comment_cancel", function(e){
			e.preventDefault();
			$(this).parents(".sub.new_comments_container").removeAttr("style");
			$(this).parents(".new_comments_container").children(".post_comment").removeClass("active");
		});

		$(document).on("click", ".new_comments_container .post_comment_send", function(e){
			e.preventDefault();
			var comment = $(this).parents(".new_comments_container").children(".new_comments").children(".comment").children(".create_new_comment").val();
			var thisId = $(this).attr("id");
			$(".new_comments_container .post_comment").removeClass("active");
			$(this).parents(".sub.new_comments_container").removeAttr("style");
			$(this).parents(".new_comments_container").children(".new_comments").children(".comment").children(".create_new_comment").val("");
		});
		
		$("#comment_sort_type").on("change", function(){
			sorting = this.value;
			get_comments();
		});

		$(document).on("click", ".report_btn", function(){
			$(this).children(".report_listing").stop().slideToggle(300);
		})
		.on("mouseleave", ".report_btn", function(){
			$(this).children(".report_listing").stop().slideUp(300);
		});
		
		$("#user_img").attr('src', selfImg);
		
		get_comments();
	});
	
	function get_comments(){
		$.ajax({
			url: getBackendHost() + '/service/message.php',
			type: 'post',
			data: {"action":"get_comments_h5","chatroom_id":chatroom_id,"sorting":sorting},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

			success: function (response, status, xhr) {
				// console.log(response);
				var html = template.render($("#comments_tpl").html(), {"data": response.list, "user_id":window.localStorage.user_id, "selfimg":selfImg});
				$("#comments_area").html(html);
				$("#comment_count").text(response.total_records);
			},
			error: function () {

			},
		});
	}

	function deleteSelfComment(id){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			return;
		}
		if (confirm("确定删除此信息？")) {
			$.ajax({
				url: getBackendHost() + '/service/message.php',
				type: 'post',
				data: {"action":"delete_comments", "euid":euid, "message_id":id},
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},

				success: function (response, status, xhr) {
					// console.log(response);
					if(response.status == 200){
						get_comments();
					}else{
						alert(response.message);
					}
				},
				error: function () {

				},
			});	
		}
	}

	function replyTo(id, username){
		$("#message_input_"+id).parents(".new_comments_container").css({display:"block"});
		$("#message_input_"+id).focus();
		$("#message_input_"+id).val("@"+username);
	}

	function like(id){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			return;
		}
		$.ajax({
			url: getBackendHost() + '/service/message.php',
			type: 'post',
			data: {"action":"thumbup_comments","euid":euid,"message_id":id},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

			success: function (response, status, xhr) {
				// console.log(response);
				if(response.status == 200){
					$("#like_"+id).text('赞('+response.like_count+')');
				}else{
					alert(response.message);
				}
				
			},
			error: function () {

			},
		});
	}
	
	function send(parent_id){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			return;
		}
		
		var message = "";
		
		if(parent_id == 0){
			message = $("#new").val();
		}else{
			message = $("#message_input_"+parent_id).val();
		}
		
		if(message.length == 0) return;
		
		$.ajax({
			url: getBackendHost() + '/service/message.php',
			type: 'post',
			data: {
				"action":"add_comments", 
				"euid":euid, 
				"chatroom_id":chatroom_id, 
				"parent_id":parent_id, 
				"message":message
			},
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},

			success: function (response, status, xhr) {
				// console.log(response);
				if(response.status == 200){
					get_comments();
				}else{
					alert(response.message);
				}
			},
			error: function () {

			},
		});
	}
	
	function report(id, type, text){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			return;
		}
		
		if (confirm("确认举报留言为"+text+"?")) {
			$.ajax({
				url: getBackendHost() + '/service/message.php',
				type: 'post',
				data: {"action":"report_comments", "euid":euid, "message_id":id, "type":type},
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},

				success: function (response, status, xhr) {
					// console.log(response);
					if(response.status == 200){
						alert(response.message);
					}else{
						alert(response.message);
					}
				},
				error: function () {

				},
			});
		}
		

	}
	
</script>

<script type="text/html" id="comments_tpl">
	{{if (data.length > 0)}}
	    {{each data value index}}
			<div class="other_comments_container">
				<div class="other_comments">
					<div class="img"><img src="<?php echo IMAGE_URL; ?>{{value.thumbnail}}"></div>
					<div class="comment">
						<div class="report_him">
							
							<a class="report_btn">
								<i class="fa fa-exclamation-triangle"></i> 举报
								<div class="report_listing">
									<div><span onclick="report({{value.id}}, 'ads', '垃圾广告')">垃圾广告</span></div>
									<div><span onclick="report({{value.id}}, 'abusive', '辱骂行为')">辱骂行为</span></div>
									<div><span onclick="report({{value.id}}, 'copyright', '涉嫌侵权')">涉嫌侵权</span></div>
									<div><span onclick="report({{value.id}}, 'politics', '反动政治')">反动政治</span></div>
									<div><span onclick="report({{value.id}}, 'marketingSpam', '垃圾营销')">垃圾营销</span></div>
								</div>
							</a>
							
							{{if user_id == value.user_id}}
								<a onclick="deleteSelfComment({{value.id}})"><i class="fa fa-trash"></i></a>
							{{/if}}
						</div>
						<div class="user">{{@value.username}} {{if value.adminImg}}&nbsp;{{@value.adminImg}}{{/if}}</div>
						<div>{{value.message}}</div>
						<div class="btn_area">
							<a id="like_{{value.id}}" onclick="like({{value.id}})">赞({{value.like_count}})</a>
							<a onclick="replyTo({{value.id}}, '{{value.username}}')"><span>回复</span></a>
							<span class="times">{{value.created_at}}</span>
						</div>
						{{if (value.sub_comments.length > 0)}}
							{{each value.sub_comments subvalue subindex}}
								<!-- reply comments -->
								<div class="sub_comment">
									<div class="other_comments">
										<div class="img"><img src="<?php echo IMAGE_URL; ?>{{subvalue.thumbnail}}"></div>
										<div class="comment">
											<div class="report_him">
												<a class="report_btn">
													<i class="fa fa-exclamation-triangle"></i> 举报
													<div class="report_listing">
														<div><span onclick="report({{subvalue.id}}, 'ads', '垃圾广告')">垃圾广告</span></div>
														<div><span onclick="report({{subvalue.id}}, 'abusive', '辱骂行为')">辱骂行为</span></div>
														<div><span onclick="report({{subvalue.id}}, 'copyright', '涉嫌侵权')">涉嫌侵权</span></div>
														<div><span onclick="report({{subvalue.id}}, 'politics', '反动政治')">反动政治</span></div>
														<div><span onclick="report({{subvalue.id}}, 'marketingSpam', '垃圾营销')">垃圾营销</span></div>
													</div>
												</a>
												{{if user_id == subvalue.user_id}}
													<a onclick="deleteSelfComment({{subvalue.id}})"><i class="fa fa-trash"></i></a>
												{{/if}}
											</div>
											<div class="user">{{subvalue.username}}</div>
											<div>{{subvalue.message}}</div>
											<div class="btn_area">
												<a id="like_{{subvalue.id}} "onclick="like({{subvalue.id}})">赞({{subvalue.like_count}})</a>
												<a onclick="replyTo({{subvalue.parent_id}}, '{{subvalue.username}}')"><span>回复</span></a>
												<span class="times">{{value.created_at}}</span>
											</div>
										</div>
									</div>
								</div>
							{{/each}}
						{{/if}}

						<!-- New reply comments -->
						<div class="sub new_comments_container">
							<div class="new_comments">
								<div class="img"><img src={{selfimg}}></div>
								<div class="comment"><input type="text" placeholder="新增回复..." id="message_input_{{value.id}}" class="create_new_comment"></div>
							</div>
							<div class="post_comment">
								<div><a class="post_comment_cancel">取消</a></div>
								<div><a class="post_comment_send" onclick=send({{value.id}})>回复</a></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{{/each}}
	{{/if}}
</script>