<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19.com</title>
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
                <div>重置密码</div>
            </div>

            <div class="account_setting_container">
                <form id="reset_password">
                    <label class="block_content"><input name="phone" type="text" placeholder="注册手机号"></label>
                    <label class="block_content"><input name="verification_code" type="text" placeholder="短信验证码"></label>
                    <label class="block_content"><input name="password" type="password" placeholder="新密码"></label>
                    <label class="block_content" style="margin-bottom: 150px;"><input name="confirm_password" type="password" placeholder="确认新密码"></label>
                    <input name="action" value="reset_password" style="display: none" >
                    <div class="form_footer_button_container">
                        <button class="w-40 active" type="submit" id="submit_rpassword_btn">送出</button>
                    </div>
                </form>

                <div class="sentout_container">
                    密码已重置成功，请使用新密码登入。
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>