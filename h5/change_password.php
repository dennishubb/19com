<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 密码编辑</title>
<?php
    include("style_script.php");
?>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">
        <?php
        $euid = rawurldecode($_COOKIE['euid']) ;//get_cookie('euid');
        $param = array(
            'euid' => $euid,
        );
        $userInfo = httpPost(CURL_API_URL . '/service/user.php?action=getuserinfo',$param);
        $userInfo = json_decode($userInfo,true);
        $userExtraInfo = httpPost(CURL_API_URL . '/service/user.php?action=getextrainfo',$param);
        $userExtraInfo = json_decode($userExtraInfo,true);
        ?>
        <div class="body_container grey_bg">
            <div class="subpage_title">
                <a class="back" href="#" onclick="window.history.back()">返回</a>
                <div>密码编辑</div>
            </div>

            <div class="account_setting_container">
                <form id="change_pw">
                    <label class="block_content" style='display:none'><input name="current_password" type="password" placeholder="旧密码" ></label>
                    <label class="block_content"><input name="new_password" type="password" placeholder="新密码" ></label>
                    <label class="block_content"><input name="new_password_confirm" type="password" placeholder="确认新密码" ></label>
                   
                    <div class="form_footer_button_container">
                        <button type="reset" class="w-40 button_style_dark" id="reset_change_pw">清除</button>
                        <button type="submit" class="w-40 active" id="submit_setting_btn">完成</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>