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
    <title>19资讯 - 优惠活动</title>
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="main_area" style="padding: 0px;">

    <div class="promo layout1920">
        <div class="promo_bg"><img src="../../assets/images/promo_bg.jpg">
            <div class="promo_title"><img src="../../assets/images/promo_title.png"></div>
        </div>


        <div class="promo_inner layout1200">
            <div class="promo_img" id="promotion_fixed">
                <a href="javascript:void(0)">
                    <img style="width: 300px;height: 158px;" src="../../assets/images/1-380X200.png">
                </a>
                <a href="javascript:void(0)">
                    <img style="width: 300px;height: 158px;" src="../../assets/images/2-380X200.png">
                </a>
                <a href="javascript:void(0)">
                    <img style="width: 300px;height: 158px;" src="../../assets/images/3-380X200.png">
                </a>
            </div>
        </div>

        <div id="promotion_carousel">
            <div class="promo_swiper">
                <?php
                    $access_url = CURL_API_URL . '/service/promotion.php?action=promotion_list';
                    $data = get_curl($access_url);
                    $data = json_decode($data, true);
                    $html = '';

                    foreach ($data as $key => $value) {
                        $html .= '<div class="promo_swiper_item">';
                        if ($value['display_method'] == 'url') {
                            $html .= '<a href="'.$value['url'].'">';
                        }
                        $html .= '<img style="width: 574px;height: 390px;" src="'.$value['thumbnail_big'].'">';
                        if ($value['display_method'] == 'url') {
                            $html .= '</a>';
                        }
                        $html .= '<div class="promo_bottom">';
                        $html .= '<div class="title">'.$value['name'].'<button type="button" onclick="javascript:show_promo_detail('.$value['id'].');">查看</button></div>';
                        $html .= '<div class="text">'.$value['introduction'].'</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }

                    echo $html;
                ?>
            </div>
            <div class="arrow_prev" onclick="$('.promo_swiper').theta_carousel('moveBack')">
                <img src="../../assets/images/arrow_right.png">
            </div>
            <div class="arrow_next" onclick="$('.promo_swiper').theta_carousel('moveForward')">
                <img src="../../assets/images/arrow_right.png">
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="promoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <div id="promo-modal-title"></div>
                </h5>
            </div>
            <div class="modal-body">
                <div class="promo_pop_inner">
                    <div id="promo-modal-content"></div>
                    <div style="text-align: center;">
                        <button type="button" class="submit_btn" id="promo-modal-submit" onclick="redeem_promo();">
                            申请
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
</body>

<script type="text/javascript" src="/assets/js/fo_common/theta-carousel.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        //fixed_promotion_list();
        // promotion_list();

        var count = Math.floor($('.promo_swiper_item').length);
        var roundedNumber = Math.ceil(count / 2) - 1;
        //console.log(count);
        //console.log(roundedNumber);

        $('.promo_swiper').theta_carousel({
            distance: 104,
            numberOfElementsToDisplayRight: 2,
            numberOfElementsToDisplayLeft: 2,
            fallback: 'never',
            scaleZ: 1.01,
            selectedIndex: 0,
            mousewheelEnabled: false
        });

    });

    var promotion_id = 0;
    function show_promo_detail (id) {
        promotion_id = id;
        $.ajax({
            url: getBackendHost() + '/service/promotion.php',
            type: 'get',
            data: {"action":"get_promotion","id":id},

            success: function (response, status, xhr) {
                $("#promo-modal-title").html(response.name);
                $("#promo-modal-content").html(response.introduction);
                if (response.sign_up == 1) {
                    $("#promo-modal-submit").hide();
                }
                else {
                    $("#promo-modal-submit").show();
                }
                $("#promoModal").modal("show");
            },
            error: function () {

            },
        });
    }

    function redeem_promo() {
        if (promotion_id != 0) {
            $("#promo-modal-submit").attr("disabled", "disabled");

            var euid = Cookies.get('euid');

            $.ajax({
                url: getBackendHost() + '/service/promotion.php',
                type: 'post',
                data: {"action":"redeem_promotion","promotion_id":promotion_id,"euid":euid},
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },

                success: function (response, status, xhr) {
                    if (response.status == 200) {
                        $("#promoModal").modal("hide");
                        window.alert(response.message);
                    }
                    else if (response.status == -201) {
                        Cookies.remove("euid");
                    }
                    else {
                        alert(response.message);
                    }
                    $("#promo-modal-submit").attr("disabled", false);
                },
                error: function () {

                },
            });
        }
    }
</script>
</html>
