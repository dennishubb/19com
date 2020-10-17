<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
?>
<!DOCTYPE html>
<html lang="zh-hans">
<head>
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
    <?php include_once('layout/resource.php'); ?>
    <title>19资讯 - 礼品兑换</title>
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="main_area">

    <div class="profile_bg">
        <img src="../assets/images/exchange_bg.png">
    </div>

    <div class="exchange layout1200">
        <div class="exchange_areas">

            <div class="exchange_top">

                <form id="filter_gift">
                    <div class="mc_filter">
                        <span>类别</span>
                        <select class="" id="gift_category" name="category_id" onchange="bindsub();">
                            <option selected="selected" value="all">全部</option>
                            <?php
                                $access_url = CURL_API_URL . '/service/site.php?action=get_category&type=gift';
                                $data = get_curl($access_url);
                                $data = json_decode($data, true);

                                foreach ($data as $key => $value) {
                                    echo '<option value="'.$value['id'].'">'.$value['display'].'</option>';
                                }
                            ?>
                        </select>
                        <select id="gift_sub_category" name="sub_category_id">
                            <option selected="selected" value="all">全部</option>
                        </select>
                        <span>战数</span>
                        <input id="points_from" style="margin-right:0px " value="">
                        <span >至</span>
                        <input id="points_to" value="">
                        <button type="button" onclick="page=1;hot_tag='';giftlist('');">确认</button>
                    </div>
                </form>
                <div class="hot_tag">
                    热门类别 :
                    <span id="tags_area">
                        <?php
                            $access_url = CURL_API_URL . '/service/site.php?action=get_hot_gift_category';
                            $data = get_curl($access_url);
                            $data = json_decode($data, true);

                            foreach ($data as $value) {
                                echo '<div style="margin-left: 2px;margin-right: 2px;"><a href="javascript:page=1;giftlist(\''.$value.'\')">'.$value.'</a></div>';
                            }
                        ?>
                    </span>
                </div>
                <div class="progress_area">
                    <div class="top">
                        现有战数：<span id="user_current_points">-</span>
                    </div>
                    <button class="bottom" type="button" onclick="redeemProgress(1);">
                        <i class="fas fa-search"></i>查询进度
                    </button>
                </div>

            </div>

            <div class="exchange_bottom" id="exchange_list">
                <div class="exchange_item_top">
                    <span id="span_gift_category">全部</span>
                    <div>
                        <select name="sorting" id="gift_sorting" onchange="gift_sort();">
                            <option value="1">战数低 &gt; 高</option>
                            <option value="2">战数高 &gt; 低</option>
                        </select>
                    </div>
                </div>
                <?php
                    $access_url = CURL_API_URL . '/service/gift.php?action=get_gift_list';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data['list'] as $key => $value) {
                        $html .= '<div class="exchange_item">';
                        $html .= '<div class="image">';
                        $html .= '<div>';
                        $html .= '<img src="'.$value['thumbnail'].'" style="width: 350px !important;height: 240px !important;">';
                        $html .= '<div class="text" style="width: 350px;height: 240px;">';
                        $html .= '<div>'.$value['name'].'<div>兑换战数：'.intval($value['points']).' 点</div></div>';
                        $html .= '</div>';
                        $html .= '<div class="description" style="width: 100%">'.$value['name'].'</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="exchange_btn" onclick="redeem('.$value['id'].')">兑换</div>';
                        $html .= '</div>';
                    }
                    echo $html;
                ?>
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="exchangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="modal_form">
                    <input type="hidden" name="gift_id" value="">
                    <table class="table_style2">
                        <tr>
                            <th>商品名称</th>
                            <th>尺寸/规格</th>
                            <th>颜色</th>
                            <th>数量</th>
                            <th>兑换</th>
                        </tr>
                        <tr>
                            <td id="modal_product_name"></td>
                            <td>
                                <select name="size" id="modal_size">

                                </select>
                            </td>
                            <td>
                                <select name="color" id="modal_colour">
                                </select>
                            </td>
                            <td>
                                <select name="quantity" id="modal_quantity">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" id="btnRedeem" onclick="makeorder();">兑换</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="progressModalBody">
                
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
</body>
<script type="text/javascript">
    $(document).ready(function () {
        if (Cookies.get('euid') != undefined) {
            $("#user_current_points").html(parseInt(window.localStorage.points));
        }
        else {
            $("#user_current_points").html(0);
        }
    });

    function bindsub() {
        var parent_category_id = $("#gift_category").val();
        var html = '<option selected="selected" value="all">全部</option>';

        if (parent_category_id == 'all') {
            $("#gift_sub_category").html('<option selected="selected" value="all">全部</option>');
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

    var category_id = 0;
    var sub_category_id = 0;
    var points_from = '';
    var points_to = '';
    var sorting = 1;
    var hot_tag = '';
    var page = 1;

    function giftlist(tag) {
        
        
        if (tag == "") {
            category_id = $("#gift_category").val();
            sub_category_id = $("#gift_sub_category").val();
            points_from = $("#points_from").val();
            points_to = $("#points_to").val();
        }
        else {
            hot_tag = tag;
            $("#gift_category").val("all");
            $("#gift_sub_category").val("all");
            $("#points_from").val("");
            $("#points_to").val("");
        }
        sorting = $("#gift_sorting").val();

        var category = $("#gift_category").find("option:selected").text();
        var sub_category = $("#gift_sub_category").find("option:selected").text();

        if (category_id == 'all') {
            category_id = 0;
        }

        if (sub_category_id == 'all') {
            sub_category_id = 0;
        }

        $.ajax({
            type: 'GET',
            url: getBackendHost() + '/service/gift.php',
            async: false,
            data: {"action":"get_gift_list","category_id":category_id,"sub_category_id":sub_category_id,"points_from":points_from,"points_to":points_to,"sorting":sorting,"hot_tag":encodeURIComponent(hot_tag),"page":page},
            success: function (response, status, xhr) {
                var html = '';
                if (page == 1) {
                    html = template.render($("#gift_list_tpl").html(), {"data": response.list, "category": category, "sub_category": sub_category, "hot_tag":hot_tag});
                    $("#exchange_list").html(html);
                }
                else {
                    html = template.render($("#gift_list_tpl2").html(), {"data": response.list, "category": category, "sub_category": sub_category, "hot_tag":hot_tag});
                    $("#exchange_list").append(html);
                }
                
                $("#gift_sorting").val(sorting);
                page = response.page;
            },
            error: function () {
            },
        });
    }

    function gift_sort () {
        page = 1;
        giftlist(hot_tag);
    }

    var is_loading = false;
    var winH = $(window).height();

    $(window).scroll(function () {
        if(is_loading) return;
        var pageH = $(document.body).height();
        var scrollT = $(window).scrollTop();
        var aa = (pageH - winH - scrollT) / winH;
        if (aa < 0.02) {
            is_loading = true;
            page++;
            giftlist(hot_tag);
            is_loading = false;
        }
    });

    function redeemProgress(page) {
        var euid = Cookies.get('euid');
        if (euid == undefined) {
            $('#username').css('display', 'none');
            $('#logout_div').css('display', 'none');
            $('.profile_btn').css('display', 'none');
            $('#before_login_div').css('display', '');
        }
        else {
            $.ajax({
                url: getBackendHost() + '/service/gift.php',
                type: 'post',
                data: {"action":"get_gift_redeem","euid":euid,"page":page},
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },

                success: function (response, status, xhr) {
                    if (response.status == 200) {
                        // console.log(response.list);
                        var html = template.render($("#gift_redeem_tpl").html(), {"list": response.list, "current_page": response.current_page, "total_page": response.total_page});
                        $("#progressModalBody").html(html);
                        if ($("#progressModal").css("display") == "none") {
                            $("#progressModal").modal("show");
                        }
                    }
                    else if (response.status == -201) {
                        Cookies.remove('euid');
                    }
                },
                error: function () {

                },
            });
        }
    }

    var redeem_gift_id = 0;

    function redeem(gift_id) {
        redeem_gift_id = gift_id;
        $.ajax({
            url: getBackendHost() + '/service/gift.php',
            type: 'get',
            data: {"action":"get_gift","id":gift_id},

            success: function (response, status, xhr) {
                // console.log(response);
                $("#modal_product_name").html(response.name);
                if (response.size.length == 0) {
                    $("#modal_size").html("<option value='0'>暂无尺寸可选择</option>");
                }
                else {
                    var html = '';
                    $.each(response.size, function (index, value) {
                        html += '<option value="'+value+'">'+value+'</option>';
                    });
                    $("#modal_size").html(html);
                }
                if (response.color.length == 0) {
                    $("#modal_colour").html("<option value='0'>暂无颜色可选择</option>");
                }
                else {
                    var html = '';
                    $.each(response.color, function (index, value) {
                        html += '<option value="'+value+'">'+value+'</option>';
                    });
                    $("#modal_colour").html(html);
                }
                $("#exchangeModal").modal("show");
            },
            error: function () {

            },
        });
    }

    function makeorder() {
        $("#btnRedeem").attr("disabled","disabled");
        if (redeem_gift_id == 0) {
            return;
        }

        var euid = Cookies.get('euid');
        var size = $("#modal_size").val();
        var color = $("#modal_colour").val();
        var quantity = $("#modal_quantity").val();

        $.ajax({
            url: getBackendHost() + '/service/gift.php',
            type: 'post',
            data: {"action":"gift_redeem","gift_id":redeem_gift_id,"euid":euid,"size":encodeURIComponent(size),"color":encodeURIComponent(color),"quantity":quantity},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == -203) {
                    alert(response.message);
                    location = "/cn/profile/index.html?complete_info=1";
                }
                else if (response.status == -201) {
                    Cookies.remove("euid");
                }
                else if (response.status == -204) {
                    alert(response.message);
                }
                else if (response.status == 200) {
                    binduserinfo();
                    alert(response.message);
                    // $("#exchangeModal").hide();
                    // $('.modal-backdrop').remove();

                    $("#user_current_points").html(parseInt($("#user_current_points").html())-parseInt(response.item_points));
                }
                $("#btnRedeem").attr("disabled", false);
            },
            error: function () {

            },
        });
    }
</script>

<script type="text/html" id="gift_list_tpl">
    <div class="exchange_item_top">
        {{if hot_tag != ""}}
        热门 / {{hot_tag}}
        {{else}}
        {{category}} / {{sub_category}}
        {{/if}}
        <div>
            <select name="sorting" id="gift_sorting" onchange="gift_sort();">
                <option value="1">战数低 &gt; 高</option>
                <option value="2">战数高 &gt; 低</option>
            </select>
        </div>
    </div>
    {{each data value index}}
        <div class="exchange_item">
            <div class="image">
                <div>
                    <img src="{{value.thumbnail}}" style="width: 350px !important;height: 240px !important;">
                    <div class="text" style="width: 350px;height: 240px;">
                        <div>{{value.name}}<div>兑换战数：{{value.points}} 点</div></div>
                    </div>
                    <div class="description" style="width: 100%">{{value.name}}</div>
                </div>
            </div>
            <div class="exchange_btn" onclick="redeem({{value.id}});">兑换</div>
        </div>
    {{/each}}
</script>

<script type="text/html" id="gift_list_tpl2">
    {{each data value index}}
        <div class="exchange_item">
            <div class="image">
                <div>
                    <img src="{{value.thumbnail}}" style="width: 350px !important;height: 240px !important;">
                    <div class="text" style="width: 350px;height: 240px;">
                        <div>{{value.name}}<div>兑换战数：{{value.points}} 点</div></div>
                    </div>
                    <div class="description" style="width: 100%">{{value.name}}</div>
                </div>
            </div>
            <div class="exchange_btn" onclick="redeem({{value.id}});">兑换</div>
        </div>
    {{/each}}
</script>

<script type="text/html" id="gift_redeem_tpl">
    <table class="table_style2">
        <tr>
            <th>会员ID</th>
            <th>申请日期</th>
            <th>礼品(尺寸颜色)</th>
            <th>数量</th>
            <th>出货状态</th>
            <th>备注</th>
        </tr>
        {{ each list value index }}
        <tr>
            <td>{{ value.user_name }}</td>
            <td>{{ value.created_at }}</td>
            <td>{{ value.gift_name }} <br> {{ value.size == "" ? "-" : value.size}} / {{ value.color == "" ? "-" : value.color }}</td>
            <td>{{ value.quantity }}</td>
            <td>{{ value.status }}</td>
            <td>{{ value.remark }}</td>
        </tr>
        {{ /each }}
    </table>
    <div class="pagination_area w-100">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link page-pagination" href="javascript:redeemProgress({{current_page==1?1:current_page-1}});" aria-label="Previous">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>
                <% for(var i = 1; i <= total_page; i++){ %>
                <li class="page-item page-pagination {{current_page == i ? 'active' : ''}}"><a class="page-link" href="javascript:redeemProgress(<%= i %>);"><%= i %></a></li>
                <% } %>
                <li class="page-item">
                    <a class="page-link page-pagination" href="javascript:redeemProgress({{current_page<total_page?current_page+1:total_page}});" aria-label="Next">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</script>
</html>