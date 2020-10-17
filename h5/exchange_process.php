<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<title>19资讯 - 礼品兑换</title>
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
       
        <div class="body_container grey_bg">
            <div class="subpage_title">
                <a class="back" href="javascript:void(0);" onclick="history.go(-1);">返回</a>
                <div>查询进度</div>
            </div>

            <div class="exchange_container">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" id="exchange_year" onchange="if ($(this).val()=='-') $('#exchange_month').val('-') ">
                                    <option value="-">全部</option>
									<?php
                                        $date = (int) date('Y');
                                        $numYears = 10;
                                        for ($i=$date; $i >= $date - $numYears; $i--) {
                                            echo "<option value=\"$i\">$i</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="exchange_month">
                                    <option value="-">全部</option>
                                    <option value="1">1月</option>
                                    <option value="2">2月</option>
                                    <option value="3">3月</option>
                                    <option value="4">4月</option>
                                    <option value="5">5月</option>
                                    <option value="6">6月</option>
                                    <option value="7">7月</option>
                                    <option value="8">8月</option>
                                    <option value="9">9月</option>
                                    <option value="10">10月</option>
                                    <option value="11">11月</option>
                                    <option value="12">12月</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <button class="button_style_dark w-100" onclick="getReedemGift(1);">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="exchange_item_process_list" id="exchange_item_process_list"></div>

            </div>

        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>

<script>
	var page = 1;
	var curr_month;
	var curr_year;
	
	getReedemGift();
	//Cookies.set('euid', 'byi3KLHs5x2zUsrmOMx6I5HY5M/XU4MNCDWM23hHe9g=', { expires: 30, path: '/' });

	function getReedemGift(filter=0) {
		
		var euid = Cookies.get("euid");
		/*if (euid == undefined) {
			$("#login_popup").modal("show");
			return;
		}*/
		//var euid="byi3KLHs5x2zUsrmOMx6I5HY5M/XU4MNCDWM23hHe9g='";
		var data={"action":"get_gift_redeem_h5","euid":euid};
		
		var selected_year=$('#exchange_year').val();
		var selected_month=$('#exchange_month').val();
		
		if (selected_year!='-')
			data.year=selected_year;
		if (selected_month!='-')
			data.month=selected_month;
		
		
		
		if (filter==1){
			//alert(curr_month+selected_month+curr_year+selected_year)
			//console.log(curr_year+selected_year)
			
			if (curr_month!=selected_month || curr_year!=selected_year){
				page=1;//reset page for different filter
			}
		}
		data.page=page;
		
		curr_month=selected_month;
		curr_year=selected_year;
		
		   $.ajax({
			   url: getBackendHost() + '/service/gift.php',
			   type: 'post',
			   data: data,
			   crossDomain: true,
			   xhrFields: {
				   withCredentials: true
			   },

			   success: function (response, status, xhr) {
				  
				if (response.status == 200) {
					
					
						
						var html = template.render($("#pro_said_detail_body_tpl").html(), {"data": response.list,'list_length':response.list.length});
						
						if (filter==1)//if from new search
							$("#exchange_item_process_list").html(html);
						else
							$("#exchange_item_process_list").append(html);
					
					
						if( $.trim( $('#exchange_item_process_list').html() ).length==0  )
							$("#exchange_item_process_list").html('无记录');
					
					
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
	
	var winH = $(window).height();
	var scrollHandler = function () {
			var pageH = $(document.body).height();
			var scrollT = $(window).scrollTop();
			var aa = (pageH - winH - scrollT) / winH;
			
			if (aa < 0.02) {
				page++;
				getReedemGift();
			}
		}
		$(window).scroll(scrollHandler);
</script>

<script type="text/html" id="pro_said_detail_body_tpl">
	{{each data value index }}
		<div>
			<div class="product_details">
				<div class="image"><img src="<?php echo IMAGE_URL; ?>{{ value.thumbnail }}"></div>
				<div class="desc">
					{{ value.gift_name }}
					<div class="point">点数：{{ value.points }}</div>
				</div>
			</div>
			<div class="block_content">尺寸 / 颜色： {{ value.size == "" ? "-" : value.size}} / {{ value.color == "" ? "-" : value.color }}</div>
			<div class="block_content">数量：{{ value.quantity }}</div>
			<div class="block_content">申请日期：{{ value.created_at }}</div>
			<div class="block_content">出货状态：{{ value.status }} {{ value.remark }}</div>
		</div>
		
	{{ /each }}
	 
</script>