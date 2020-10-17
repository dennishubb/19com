<style>
    .make-bold {
        font-weight:bolder
    }

    .btn-reply {
        text-align: right;
        width: 100%;
        color: #ed1b34;
        font-size: 14px;
        padding-right: 15px;
        padding-bottom: 10px
    }
    #main-message-reply {
        padding-left: 30px;
    }

    .dropdown {
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }
    .dropdown div:hover {background-color: #ddd;}
</style>

<div class="comments">
  	<div class="title_area style2"><span>留言区(<span id='comment_count'>0</span>则回复)</span> 
        <div class="right_filter" >排序方式：
            <select id="comment_sort_type">
                <option value="2">热门</option>
                <option value="1">最新</option>
            </select>
        </div>
    </div>
    <div class="my_comments">
        <img id="user_img" style="height: 50px; width: 50px;">
        <input id="main_message_input" type="input" placeholder="新增回复...">
    </div>
    <div class="btn_area btn_area2 btn-reply">
        <btn id="main-message-cancel" type="button" onclick="cancel(0)"><span>取消</span></btn>
        <btn id="new" type="button" onclick="send(0)"=><span>回复</span></btn>
    </div>
	<div id="comments_area">
	
	</div>
</div>

<script>
	
	var chatroom_id = <?php echo $chatroom_id; ?>;
	var sorting = 2;
	var selfImg = window.localStorage.profile_thumbnail ? '<?php echo IMAGE_URL; ?>' + window.localStorage.profile_thumbnail : '<?php echo IMAGE_URL; ?>' + '/assets/images/default_user_image.png';
	
	$(function(){
		$("#comment_sort_type").on("change", function(){
			sorting = this.value;
			get_comments();
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
				$("#comment_count").text(response.total_records)
			},
			error: function () {

			},
		});
	}

	function deleteSelfComment(id){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			$("#login_popup").modal("show");
			return;
		}
		confirm("确定删除此信息？").then(result => {
			const confirmed = result.confirmed
			if(!confirmed){
				return;
			}
			$.ajax({
				url: getBackendHost() + '/service/message.php',
				type: 'post',
				data: {"action":"delete_comments", "euid":euid, "message_id":id},
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},

				success: function (response, status, xhr) {
					if(response.status == 200){
						get_comments();
					}else{
						alert(response.message);
					}
				},
				error: function () {

				},
			});	
		});
	}

	function replyTo(id, username){
		$("#message_input_"+id).focus();
		$("#message_input_"+id).val("@"+username);
	}
	
	function cancel(id){
		if(id == 0){
			$("#main_message_input").blur();
			$("#main_message_input").val("");
		}else{
			$("#message_input_"+id).blur();
			$("#message_input_"+id).val("");
			$("#message_input_"+id).val("");
		}
	}

	function like(id){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			$("#login_popup").modal("show");
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
			$("#login_popup").modal("show");
			return;
		}
		
		var message = "";
		
		if(parent_id == 0){
			message = $("#main_message_input").val();
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
			$("#login_popup").modal("show");
			return;
		}
		$(".report_pop").css("display", "none")
		confirm("确认举报留言为"+text+"?").then(result => {
			const confirmed = result.confirmed
			if(!confirmed){
				return;
			}
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
		});
	}
	
	var activePopUp = false;
	function reportBtn(item){
		var euid = Cookies.get("euid");
		if (euid == undefined) {
			$("#login_popup").modal("show");
			return;
		}
		$("#report_pop_"+item.id).toggle();
		activePopUp = ".report_pop";
	}

	function hideList() {
		if(activePopUp) {
			$(activePopUp).hide();
			activePopUp = false;
		}
	}

</script>

<script type="text/html" id="comments_tpl">
	{{if (data.length > 0)}}
	    {{each data value index}}
		<div class="other_comments">
			{{if user_id == value.user_id}}
			<div class="del_comments"><i class="far fa-trash-alt" onclick="deleteSelfComment(this.id)"></i></div>
			{{/if}}
			<div class="report" tabindex="-1" onblur="hideList()">
				<div class="report_btn" id="{{value.id}}" onclick="reportBtn(this)">
					<i class="fas fa-exclamation-triangle"></i>留言举报
				</div>
				<div class="report_pop dropdown" id="report_pop_{{value.id}}" style="display: none;">
					<div class='report-type' onclick="report({{value.id}}, 'ads', '垃圾广告')">垃圾广告</div>
					<div class='report-type' onclick="report({{value.id}}, 'abusive', '辱骂行为')">辱骂行为</div>
					<div class='report-type' onclick="report({{value.id}}, 'copyright', '涉嫌侵权')">涉嫌侵权</div>
					<div class='report-type' onclick="report({{value.id}}, 'politics', '反动政治')">反动政治</div>
					<div class='report-type' onclick="report({{value.id}}, 'marketingSpam', '垃圾营销')">垃圾营销</div>
				</div>
			</div>
			<div>
				<img src="<?php echo IMAGE_URL; ?>{{value.thumbnail}}" style="height: 50px; width: 50px;">
			</div>
			<div style="width: 800px;">
				<div class="name"><strong>{{@value.username}}{{if value.adminImg}}&nbsp;{{@value.adminImg}}{{/if}}</strong></div>
				<div class="comments_text">
					{{value.message}}
					<div class="btn_area">
						<btn type="button" onclick='like({{value.id}})'">
							<span id="like_{{value.id}}">
								赞({{value.like_count}})
							</span>
						</btn>
						<btn type="button" onclick="replyTo({{value.id}}, '{{value.username}}')"><span>回复</span></btn>
						<span class="times">{{value.created_at}}</span>
					</div>
					{{if (value.sub_comments.length > 0)}}
						{{each value.sub_comments subvalue subindex}}
						<div>
							<div class="other_comments">
								{{if user_id == subvalue.user_id}}
								<div class="del_comments"><i class="far fa-trash-alt" onclick="deleteSelfComment({{subvalue.id}})"></i></div>
								{{/if}}
								<div class="report" tabindex="-1" onblur="hideList()">
									<div class="report_btn" id="{{subvalue.id}}" onclick="reportBtn(this)">
										<i class="fas fa-exclamation-triangle"></i>留言举报
									</div>
									<div class="report_pop dropdown" id="report_pop_{{subvalue.id}}" style="display: none;">
										<div class='report-type' onclick="report({{subvalue.id}}, 'ads', '垃圾广告')">垃圾广告</div>
										<div class='report-type' onclick="report({{subvalue.id}}, 'abusive', '辱骂行为')">辱骂行为</div>
										<div class='report-type' onclick="report({{subvalue.id}}, 'copyright', '涉嫌侵权')">涉嫌侵权</div>
										<div class='report-type' onclick="report({{subvalue.id}}, 'politics', '反动政治')">反动政治</div>
										<div class='report-type' onclick="report({{subvalue.id}}, 'marketingSpam', '垃圾营销')">垃圾营销</div>
									</div>
								</div>
								<div>
									<img src={{subvalue.thumbnail}} style="height: 50px; width: 50px;">
								</div>
								<div>
									<div class="name"><strong>{{subvalue.username}}</strong></div>
									<div class="comments_text">
										{{subvalue.message}}
										<div class="btn_area">
											<btn type="button" onclick="like({{subvalue.id}})">
												<span id="like_{{subvalue.id}}">
													赞({{subvalue.like_count}})
												</span>
											</btn>
											<btn type="button" onclick="replyTo({{value.id}}, '{{subvalue.username}}')"><span>回复</span></btn>
											<span class="times">{{subvalue.created_at}}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{/each}}
					{{/if}}
					<!--<div>-->
						<div class="my_comments">
							<img src={{selfimg}} style="height: 50px; width: 50px;">
							<input id="message_input_{{value.id}}" type="input" placeholder="新增回复...">
						</div>
						<div class="btn_area btn_area2">
							<btn type="button" onclick="cancel({{value.id}})"><span>取消</span></btn>
							<btn type="button" onclick="send({{value.id}})"><span>回复</span></btn>
						</div>
					<!--</div>-->
				</div>
			</div>
		</div>
		{{/each}}
	{{/if}}
</script>
