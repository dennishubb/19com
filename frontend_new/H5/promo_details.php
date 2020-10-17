<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");

    $id = intval($_GET['id']);
    if (!$id) {
        exit();
    }

	$access_url = CURL_API_URL . '/service/promotion.php?action=get_promotion&id='.$id;
	$promo_data = get_curl($access_url);
	$promo_data = json_decode($promo_data, true);

?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 活动专区</title>
<?php
    include("style_script.php");
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container grey_bg">

            <div class="subpage_title">
                <div class=""><a class="back" href="javascript:void(0);" onclick="history.go(-1);">返回</a></div>
                <div class=""><?php echo $promo_data['name'] ?></div>
            </div>

            <div class="promo_details_container">
                <div class=""><?php echo $promo_data['introduction'] ?></div>
            </div>
            
			<?php if($promo_data['sign_up'] != 1){ ?>
				<div id="promoModal">
					<form id="promo-modal-form">
						<input type="hidden" id="promo-modal-id" name="promotion_id" value="0">
						<div class="promo_pop_inner">
							<div style="text-align: center;">
								<button type="button" style="padding: 12px;" class="submit_btn" id="promo-modal-submit" onclick="redeem_promo('<?php echo $id;?>');"> 申請
								</button>
							</div>
						</div>
					</form>
				</div>
			<?php } ?>
            
            <div class="long_term_promo">
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/1-380X200.png"></div>
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/2-380X200.png"></div>
                <div class=""><img src="<?php echo IMAGE_URL ?>/assets/images/3-380X200.png"></div>
            </div>

        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>
<script>
    function redeem_promo(promotion_id) {
        if (promotion_id != 0) {
            var euid = Cookies.get('euid');
			
			if (euid == undefined) {
				return;
			}
			
			$("#promo-modal-submit").attr("disabled", "disabled");

            $.ajax({
                url: api_domain + '/service/promotion.php',
                type: 'post',
                data: {"action":"redeem_promotion","promotion_id":promotion_id,"euid":euid},
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },

                success: function (response, status, xhr) {
                    if (response.status == 200) {
						alert(response.message);
						window.history.back();
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
