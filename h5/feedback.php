<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 意见反馈</title>
<?php
    include("style_script.php");
?>
<style type="text/css">
    form textarea {
        width: 100%;
        padding: 12px 20px;
        height: 120px;
        border: 1px solid #c7c7c7;
        border-radius: 8px;
        background: #FFF;
        outline: none;
    }
</style>
</head>

<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container grey_bg">
            <div class="subpage_title">
                <a class="back" href="#" onclick="window.history.back()">返回</a>
                <div>意见反馈</div>
            </div>

            <div class="account_setting_container">
                <form id="feedbackForm">
                    <label class="block_content">
                        <select id="feedbackType">
                            <option>帐户相关问题</option>
                            <option>站数/预测卷问题</option>
                            <option>其他问题</option>
                        </select>
                    </label><label class="block_content"><textarea id="feedback-message" type="text" placeholder="请描述问题现像，我们将尽快处理"></textarea></label>
                    <label class="block_content"><input id="feedback-email" name="email" type="text" placeholder="您的郵箱(必填)"></label><div class="div_imagetranscrits" style="display: flex;margin-bottom: 200px;">
                        <input id="feedback-captcha-input" name="captcha" type="text" placeholder="请输入验证码" data-type="required">
                        <img src="/assets/scripts/get-captcha.php" onclick="this.src='/assets/scripts/get-captcha.php';">
                    </div>

                    <input name="action" value="forget_password" style="display: none">
                    <div class="form_footer_button_container">
                        <button type="button" class="w-40 active" id="submit_fpassword_btn" onclick="submitFeedbackForm()">送出</button>
                    </div>
                </form>

                <div class="sentout_container">
                    <div class="img"><img src="img/sentout_icon.png"></div>
                    您的反馈已成功提交<br>
                    本站将会以最快的速度处理。
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
<script>
//--feedback form begins

function submitFeedbackForm() {
    var formData = {
        type: $('#feedbackType').val(),
        message: $("#feedback-message").val(),
        email: $("#feedback-email").val(),
        captcha: $("#feedback-captcha-input").val()
    }
    $.ajax({
        type: "POST",
        url:'/assets/scripts/feedback.php',
        data: formData,
        crossDomain: true,
    }).then(response => {
        response = JSON.parse(response)
        if(response.code == 1) {
            $("#feedbackForm").hide()
            $(".sentout_container").show()
        }else {
            alert(response.statusMsg)
        }

    }, error => {
        alert('AJAX ERROR - create feedback');
    });
}

//--feedback form ends
</script>
</html>