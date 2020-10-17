<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 忘记密码</title>
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
                <a class="back" href="/index.php#login">返回</a>
                <div>忘记密码</div>
            </div>

            <div class="account_setting_container">
                <form id="forgot_password">
                    <label class="block_content"><input id="txt_forget_pwd_phone" name="phone" type="text" placeholder="手机号"></label>

                    <div class="div_imagetranscrits" style="display: flex;margin-bottom: 200px;">
                        <input id="forgetpw_captcha" name="captcha" type="text" placeholder="请输入验证码">
                        <img  src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';">
                    </div>

                    <input name="action" value="forget_password" style="display: none">
                    <div class="form_footer_button_container">
                        <button class="w-40 active" id="submit_fpassword_btn">送出</button>
                    </div>
                </form>

                <form id="reset_password" style="display: none;">
                    <label class="block_content"><input id="txt_phone" type="text" placeholder="手机号" readonly="readonly"></label>
                    <label class="block_content"><input id="txt_otp" type="text" placeholder="验证码"></label>
                    <label class="block_content"><input id="txt_password" type="text" placeholder="新密码"></label>
                    <label class="block_content"><input id="txt_password_confirm" type="text" placeholder="确认密码"></label>
                    <div class="form_footer_button_container">
                        <button class="w-40 active" onclick="">送出</button>
                    </div>
                </form>

                <div class="sentout_container">
                    <div class="img"><img src="img/sentout_icon.png"></div>
                    验证码已发送至手机，请检视手机简讯。
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
<script type="text/javascript">
    $("#submit_fpassword_btn").click(function(e){
        e.preventDefault();
        var phone = $("#txt_forget_pwd_phone").val();
        var captcha = $("#forgetpw_captcha").val();

        if (phone == "") {
            alert("请输入手机号码");
            return;
        }
        if (captcha == "") {
            alert("请输入手机号码");
            return;
        }

        $.ajax({
            url: api_domain+ '/service/user.php',
            type: 'post',
            data: {"action":"forget_password","phone":phone,"captcha":captcha},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == 200) {
                    $("#forgot_password").hide();
                    $(".sentout_container").show();
                    setTimeout(function() {
                        $("#txt_phone").val(phone);
                        $(".sentout_container").hide();
                        $("#reset_password").show();
                    }, 2000);
                }
                else {
                    alert(response.message);
                }
            },
            error: function () {
                
            },
        });
    });

    function reset_password() {
        var phone = $("#txt_phone").val();
        var password = $("#txt_password").val();
        var confirm_password = $("#txt_password_confirm").val();
        var verification_code = $("#txt_otp").val();

        if (phone == "") {
            $("#msg_sent_span").html("请输入手机号码");
            return;
        }
        if (password == "") {
            $("#msg_sent_span").html("请输入新密码");
            return;
        }
        if (confirm_password == "") {
            $("#msg_sent_span").html("请输入确认密码");
            return;
        }
        if (password != confirm_password) {
            $("#msg_sent_span").html("新密码与确认密码不一致");
            return;
        }
        if (verification_code == "") {
            $("#msg_sent_span").html("请输入验证码");
            return;
        }

        $.ajax({
            url: api_domain+ '/service/user.php',
            type: 'post',
            data: {"action":"reset_password","phone":phone,"verification_code":verification_code,"password":password},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == 200) {
                    alert(response.message);
                    // $("#login_popup").modal("show");
                }
                else {
                    alert(response.message);
                }
            },
            error: function () {
                
            },
        });
    }
</script>
</html>