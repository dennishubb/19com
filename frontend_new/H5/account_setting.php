<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 账号设置</title>
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
                <a class="back" href="javascript:void(0);" onclick="history.go(-1);">返回</a>
                <div>账号设置</div>
            </div>

            <div class="account_setting_container">
                <form id="account_setting">
                    <label class="block_content" style="display: none;"><input name="id" type="text" placeholder="会员ID" value="<?php echo $userInfo['user']['id']?>" readonly></label>
                    <label class="block_content"><input name="phone" type="text" placeholder="手机号" value="<?php echo $userExtraInfo['user']['phone']?>"></label>
                    <label class="block_content"><input name="username" type="text" placeholder="姓名" value="<?php echo $userExtraInfo['user']['name']?>"></label>
                    <label class="block_content"><input name="email" type="text" placeholder="邮箱 (忘记密码用，必填)" value="<?php echo $userExtraInfo['user']['email']?>"></label>
                    <label class="block_content"><input name="address" type="text" placeholder="地址" value="<?php echo $userExtraInfo['user']['address']?>"></label>
                    <div class="block_content block_content_row">
                        <select name="dob-year">
                            <?php
                            $date = (int) date('Y');
                            $numYears = 100;
                            for ($i=$date; $i >= $date - $numYears; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                        </select>

                        <select name="dob-month">
                            <option value="01">1月</option>
                            <option value="02">2月</option>
                            <option value="03">3月</option>
                            <option value="04">4月</option>
                            <option value="05">5月</option>
                            <option value="06">6月</option>
                            <option value="07">7月</option>
                            <option value="08">8月</option>
                            <option value="09">9月</option>
                            <option value="10">10月</option>
                            <option value="11">11月</option>
                            <option value="12">12月</option>
                        </select>

                        <select name="dob-day">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>
                    </div>
                    <label class="block_content"><input name="weibo" type="text" placeholder="微博" value="<?php echo $userExtraInfo['user']['weibo']?>"></label>

                    <div class="form_footer_button_container">
                        <button type="reset" class="w-40 button_style_dark" id="reset_account_setting">清除</button>
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