<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<style>
    .image:after {
        display: none!important;
    }
    .image .zoom{
        display: block;
        position: absolute;
        top: 10px;
        right: 10px;
        background: url("img/products/zoom.png") center center no-repeat;
        background-size: 100% 100%;
        width: 20px;
        height: 20px;
    }
</style>
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
        <div class="body_container grey_bg">

            <div class="exchange_container">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">

                            <div class="content_block w-100">
                                <div class="record_point">现有战绩：<span id="user_current_points">-</span></div>
                            </div>
                            <div class="content_block w-50">
                                <button class="active check_progress" onclick="window.location='exchange_process.php'"><span class="fa fa-search"></span> 查询进度</button>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block">
                            <select class="w-100" id="gift_category" name="category_id" onchange="bindsub();">
                            <option selected="selected" value="">全部</option>
                            <?php
                                $access_url = CURL_API_URL . '/service/site.php?action=get_category&type=gift';
                                $data = get_curl($access_url);
                                $data = json_decode($data, true);

                                foreach ($data as $key => $value) {
                                    echo '<option value="'.$value['id'].'">'.$value['display'].'</option>';
                                }
                            ?>
                        </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="gift_sub_category" name="sub_category_id">
                                    <option selected="selected" value="">全部</option>
                                </select>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block w-100">
                                <select id="pointRange" class="w-100">
                                    <option value="">全部</option>
                                    <option value="1000-50000">1,000 ~ 50,000</option>
                                    <option value="50000-100000">50,000 ~ 100,000</option>
                                    <option value="100000-150000">100,000 ~ 150,000</option>
                                    <option value="150000-200000">150,000 ~ 200,000</option>
                                </select>
                            </div>
                            <div class="content_block w-50">
                                <button type="button" class="button_style_dark w-100" onclick="page=1;hot_tag='';giftlist('',1);">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="exchange_tagging_container">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                                $access_url = CURL_API_URL . '/service/site.php?action=get_hot_gift_category';
                                $data = get_curl($access_url);
                                $data = json_decode($data, true);

                                foreach ($data as $value) {
                                    echo '<div class="swiper-slide hotTag-swiper-slide"><a class="tag hotTagLabel" href="javascript:page=1;giftlist(\''.$value.'\',1)">#'.$value.'</a></div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="exchange_item_list" id="exchange_item_list">
                    <?php
                    $access_url = CURL_API_URL . '/service/gift.php?action=get_gift_list';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    foreach ($data['list'] as $key => $value) { ?>
                        <div>
                            <div class="image">
                                <img src='<?php echo IMAGE_URL . $value['thumbnail'] ?>' onError="this.onerror=null;this.src='/assets/images/grey.gif';">
                                <div class="zoom" onclick="openImage('<?php echo IMAGE_URL . $value['thumbnail'] ?>')"></div>
                            </div>
                            <div class="desc">
                                <div class="products_desc"></div>
                                <div class="point">
                                    <div><?php echo $value['name'] ?></div>
                                    <div>点数：<?php echo intval($value['points']) ?></div>
                                </div>
                            </div>
                            <div class="btn">
                                <a type="button" href="#" class="exchange_now" onclick="pickedListHtml(<?php echo $value['id'] ?>)">兑换</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            </div>

            <div class="exchange_floating_tab" onclick='$(".form_footer_button_container").css("position", "fixed");'>兑换品</div>

            <div class="exchange_pick_container">
                <div class="subpage_title">
                    <a class="back" id="close_exchange_pick" onclick='$(".form_footer_button_container").css("position", "sticky");'>返回</a>
                    <div>兑换</div>
                </div>

                <form>
                    <div class="exchange_pick_list" id="exchange_pick_list">
                    </div>

                    <div class="form_footer_button_container">
                        <div>兑换点数共：<span id="total_exchanged_point">0</span></div>
                        <button class="w-40 active" id="submit_exchange_btn" disabled>兑换</button>
                    </div>
                <form>
            </div>
        </div>
    </div>
    <?php
        include("footer.php");
    ?>
</body>
<script type="text/html" id="gift_list_tpl2">
    {{each data value index}}
        <div>
            <div class="image">
                <img src="<?php echo IMAGE_URL; ?>{{value.thumbnail}}" onError="this.onerror=null;this.src='/assets/images/grey.gif';">
                <div class="zoom" onclick="openImage('<?php echo IMAGE_URL; ?>{{value.thumbnail}}')"></div>
            </div>
            <div class="desc">
                <div class="products_desc"></div>
                <div class="point">
                    <div>{{value.name}}</div>
                    <div>点数：{{value.points}}</div>
                </div>
            </div>
            <div class="btn">
                <a type="button" href="#" class="exchange_now" onclick="pickedListHtml({{value.id}})">兑换</a>
            </div>
        </div>
    {{/each}}
</script>
</html>

<script type="text/javascript">
    var pickItemIdAry = []
    var pickItemListAry = []
    var page = 1
    var hot_tag = '';
    var stopCallingApi = null
    giftlist("", 1);
    var euid = Cookies.get("euid");
    //var euid = "byi3KLHs5x2zUsrmOMx6I5HY5M/XU4MNCDWM23hHe9g='"; //hardcoded euid
    var totalExchangePoint = 0
    $(document).ready(function () {
        if ((euid != undefined) && (window.localStorage.points)) {
            $("#user_current_points").html(parseInt(window.localStorage.points));
        }
        else {
            $("#user_current_points").html(0);
        }
        var swiper = new Swiper('.exchange_tagging_container .swiper-container', {
            slidesPerView: 'auto',
            autoHeight: true,
            spaceBetween: 20,
            freeMode: true
        });
        setTimeout(function(){
            swiper.update();
        }, 500);

        $('.hotTag-swiper-slide a:first').addClass('active');

        $(".hotTagLabel").click(function() {
            $(".hotTagLabel").removeClass("active")
            $(this).addClass("active");
        });

        $("#close_exchange_pick").click(function(e){
            e.preventDefault();
            $(".exchange_pick_container").toggleClass("active");
            $("html, body").toggleClass("noscroll");
        });
        $(document).on("click", "#exchange_pick_list .item .close", function(e){
            e.preventDefault();
            var thisId = $(this).parent().data("id");
            var thisPoint = $(this).parent().data("point");
            
            if($("#quantity_"+thisId).val() != ""){
                totalExchangePoint = totalExchangePoint - (thisPoint * $("#quantity_"+thisId).val());
                $("#total_exchanged_point").html(numberAddCommas(totalExchangePoint));
            }

            $(this).parent().remove();
            pickItemIdAry.splice($.inArray(thisId, pickItemIdAry) ,1);
            pickItemListAry = pickItemListAry.filter(each => {
                return each.Id != thisId;
            })
            if(pickItemIdAry.length > 0){
                $("#submit_exchange_btn").removeAttr("disabled").removeProp("disabled");
                $(".exchange_floating_tab").addClass("active");
            }
            else{
                $("#submit_exchange_btn").attr("disabled", "disabled").prop("disabled", true);
                $(".exchange_floating_tab").removeClass("active");
            }
        });

        $(document).on("change", "select[id*='quantity_']", function(){
            var thisValue = $(this).val();
            var thisOldValue = $(this).data("val");
            var thisPoint = $(this).parents(".item").data("point");

            if(thisOldValue != null){
                totalExchangePoint = totalExchangePoint - (thisPoint * thisOldValue);
            }
            totalExchangePoint = totalExchangePoint + (thisPoint * thisValue);
            $("#total_exchanged_point").html(numberAddCommas(totalExchangePoint));
        }).on("click", "select[id*='quantity_']", function(){
            $(this).data("val", $(this).val());
        });

        $(".exchange_floating_tab").click(function(){
            $(".exchange_pick_container").addClass("active");
            $("html, body").addClass("noscroll");
        });

        $("#submit_exchange_btn").click(function(e){
            e.preventDefault();
            
            var dataPost = [], checkSubmit = true;
            $.each(pickItemIdAry, function(index, value){
                if($("#style_color_"+value).val() != null && $("#quantity_"+value).val() != null){
                    if ($("#style_color_"+value).val() != "-") {
                        var styleData = $("#style_color_"+value).val().split("/")
                        styleData[0] == "-" ? size="" : size = styleData[0] 
                        styleData[1] == "-" ? color="" : color = styleData[1] 
                    }else {
                        var size=""
                        var color=""
                    }
                    dataPost.push({id: value, size: size, color: color, quantity: $("#quantity_"+value).val()});
                }
                else{
                    checkSubmit = false;
                }
            });
            if(checkSubmit){
                var confirmPurchase = confirm("确认兑换吗？")
                if(confirmPurchase)  redeemGiftH5(dataPost);
            }
            else{
                alert("请选择尺寸/规格/颜色和数量。");
            }
        })
    });

    function redeemGiftH5(dataPost) {
        var data = {
            "action": "gift_redeem_h5",
            "euid": euid,
            gift_redeem: {},
        }
        $.each(dataPost, function(index, value){
            data.gift_redeem[index] = {
                id: value.id,
                size: value.size,
                color: value.color,
                quantity: value.quantity
            }
        })
        $.ajax({
            url: getBackendHost() + '/service/gift.php',
            type: 'post',
            data: data,
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == -203) {
                    alert(response.message);
                    redirect_to("/account_setting.php");
                }
                else if (response.status == -201) {
                    Cookies.remove("euid");
                }
                else if (response.status == -204) {
                    alert(response.message);
                }
                else if (response.status == 200) {
                    dataPost = []
                    pickItemIdAry = []
                    totalExchangePoint = 0
                    $("#exchange_pick_list").html("");
                    $("#submit_exchange_btn").attr("disabled", "disabled").prop("disabled", true);
                    $("#total_exchanged_point").html(totalExchangePoint);
                    alert(response.message);
                    $(".exchange_floating_tab").removeClass("active");
                    $("#user_current_points").html(parseInt($("#user_current_points").html())-parseInt(response.item_points));
                    $(".exchange_pick_container").toggleClass("active");
                    $("html, body").toggleClass("noscroll");
                }
            },
            error: function (err) {
            },
        });
        
    }
    function openImage(imageUrl){
        window.open(imageUrl, "_blank")
    }

    function bindsub() {
        var parent_category_id = $("#gift_category").val();
        var html = '<option selected="selected" value="">全部</option>';

        if (parent_category_id == '') {
            $("#gift_sub_category").html('<option selected="selected" value="">全部</option>');
        }
        else {
            $.ajax({
                type: 'GET',
                url: getBackendHost() + '/service/site.php',
                data: {"action":"get_category", "category_id":parent_category_id, "type":"gift"},
                success: function (response, status, xhr) {
                    $.each(response, function (index, value) {
                        html += '<option value="'+value.id+'">'+value.display+'</option>';
                    });
                    $("#gift_sub_category").html(html);
                },
                error: function () {
                },
            });
        }
    }

    function giftlist(tag=null, newSearch=0) {
        var category_id = $("#gift_category").val();
        var sub_category_id = $("#gift_sub_category").val();
        var pointRange = ($("#pointRange").val()).split("-");
        var points_from = pointRange[0];
        var points_to = pointRange[1];
        if(tag) {
            hot_tag = tag;
            $("#gift_category").val("");
            $("#gift_sub_category").val("");
            points_from = "";
            category_id = "";
            sub_category_id = "";
            points_to = "";
        }else {
            hot_tag = "";
            $(".hotTagLabel").removeClass("active")
        }
        var category = $("#gift_category").find("option:selected").text();
        var sub_category = $("#gift_sub_category").find("option:selected").text();

        if(newSearch) stopCallingApi = false
        if(stopCallingApi) return Promise.resolve()
        return $.ajax({
            type: 'GET',
            url: getBackendHost() + '/service/gift.php',
            async: false,
            data: {"action":"get_gift_list","category_id":category_id,"sub_category_id":sub_category_id,"points_from":points_from,"points_to":points_to,"hot_tag":encodeURIComponent(hot_tag),page:page},
        }).then(response => {
                if(newSearch) $("#exchange_item_list").html("")
                var html = '';
                html = template.render($("#gift_list_tpl2").html(), {"data": response.list, "category": category, "sub_category": sub_category, "hot_tag":hot_tag, page:page});
                if (newSearch && response.list.length > 0)
					$("#exchange_item_list").html(html);
				else if(response.list.length > 0)
                    $("#exchange_item_list").append(html);
                else if(response.list.length == 0) {
                    stopCallingApi = 1
                }
        }).catch(err => console.log(err));
    }
    var winH = $(window).height();
    var isLoading = false
	var scrollHandler = function () {
            if(isLoading) {
                return
            }
			var pageH = $(document.body).height();
            var scrollT = $(window).scrollTop();
            var viewableHeight = window.innerHeight;
            var totalHeight = $(document).height();
            var maxScroll = totalHeight - viewableHeight
            var scrollLoadPoint = maxScroll - 20

			if (scrollT > scrollLoadPoint ) {
                isLoading = true

                page++;
                giftlist(hot_tag, "",stopCallingApi)
                    .then(() => isLoading = false);
			}
		}
    ;
    $(window).scroll(scrollHandler)
    function pickedListHtml(id){
        $(".form_footer_button_container").css("position", "fixed");
        if (!pickItemIdAry.includes(id)) {
            pickItemIdAry.push(id)
            $.ajax({
                url: getBackendHost() + 'service/gift.php',
                type: 'GET',
                data: {"action":"get_gift_h5","id":id},
                crossDomain: true,
                success: function (value, status, xhr) {
                    var html = '';
                    html = `<div class="item" data-id="${id}" data-point="${value.points}">
                            <div class="close"></div>
                            <div class="image"><img src="<?php echo IMAGE_URL; ?>${value.thumbnail}" onError="this.onerror=null;this.src='/assets/images/grey.gif';"></div>
                            <div class="desc">
                            <div class="products_desc"></div>
                            <div class="point">
                                <div>${value.name}</div>
                                <div>点数：${numberAddCommas(value.points)}</div>
                            </div>
                            <div>
                                <select id="style_color_${id}">`
                                    if((value.size.length < 1) && (value.color.length < 1)) {
                                        html += `<option value="-" selected>暂无尺寸/规格/颜色选择</option>`;
                                    }else {
                                        html += `<option value="" disabled selected>尺寸/规格/颜色</option>`;
                                        if((value.size && value.size.length) && (value.color && value.color.length)) {
                                            for (i = 0; i < value.size.length; i++) {
                                                for (j = 0; j < value.color.length; j++) {
                                                    html += `<option value="${value.size[i]} / ${value.color[j]}">${value.size[i]} / ${value.color[j]}</option>`;
                                                }
                                            }
                                        }else if((value.size && value.size.length) && (value.color.length < 1)) {
                                            for (j = 0; j < value.size.length; j++) {
                                                html += `<option value="${value.size[j]}/-">${value.size[j]}</option>`;
                                            }
                                        }else if((value.size.length < 1) && (value.color && value.color.length)) {
                                            for (j = 0; j < value.color.length; j++) {
                                                html += `<option value="-/${value.color[j]}">${value.color[j]}</option>`;
                                            }
                                        }
                                    }
                    html +=     `</select>
                            </div>
                            <div>
                                <select id="quantity_${id}">
                                    <option value="" disabled selected>数量</option>`;
                                    for(var loop = 1; loop <= 10; loop++){
                                        html += `<option name ="quantity" value="${loop}">${loop}</option>`;
                                    }
                    html +=     `</select>
                            </div>
                        </div>
                    </div>`;
                    $("#exchange_pick_list").append(html);
                    $("#submit_exchange_btn").removeAttr("disabled").removeProp("disabled");
                    $(".exchange_floating_tab").addClass("active");
                },
                error: function () {
                },
            });
        }
        $(".exchange_pick_container").toggleClass("active");
        $("html, body").addClass("noscroll");
    }

</script>