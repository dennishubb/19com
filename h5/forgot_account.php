<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 找回账户</title>
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
                <a class="back" href="index.php#login">返回</a>
                <div>忘记账户</div>
            </div>

            <div class="account_setting_container">
                <form id="forgot_account">
                    <label class="block_content"><input id="txt_forget_account_phone" name="phone" type="text" placeholder="手机号"></label>


                <div class="div_imagetranscrits" style="display: flex;margin-bottom: 200px;">
                    <input id="forgetac_captcha" name="captcha" type="text" placeholder="请输入验证码">
                    <img  src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';">
                </div>

                    <input name="action" value="forget_account" style="display: none">
                    <div class="form_footer_button_container">
                        <button class="w-40 active" id="submit_faccount_btn">送出</button>
                    </div>
                </form>

                <div class="sentout_container">
                    <div class="img"><img src="img/sentout_icon.png"></div>
                    账户信息已发送至手机，请检视手机简讯。
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>

<script type="text/javascript">
    $("#submit_faccount_btn").click(function(e){
        e.preventDefault();

        var phone = $("#txt_forget_account_phone").val();
        var captcha = $("#forgetac_captcha").val();

        if (phone == "") {
            alert("请输入手机号码");
            return;
        }
        if (captcha == "") {
            alert("请输入验证码");
            return;
        }

        $.ajax({
            url: api_domain + '/service/user.php',
            type: 'post',
            data: {"action":"forget_account","phone":phone,"captcha":captcha},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == 200) {
                    $(".sentout_container").show();
                    $("form").hide();
                }
                else {
                    alert(response.message);
                }

            },
            error: function () {
                
            },
        });


    });
</script>
</html>