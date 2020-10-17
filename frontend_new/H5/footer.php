
<footer>
    <div class="footer">
        <div><a href="#">关于我们</a></div>
        <div><a href="#">客户服务</a></div>
        <div><a href="#">常见问题</a></div>
    </div>
    <div class="footer_social">
        <div><a href="https://weibo.com/p/1006067185249509/home?from=page_100606&mod=TAB#place"><img src="/img/logoWb@3x.png"></a></div>
        <div><a href="https://v.douyin.com/JFxkx31/"><img src="/img/logoDy@3x.png"></a></div>
        <div><a href="https://www.instagram.com/19zixun/?hl=zh-tw"><img src="/img/instagram.png"></a></div>
        <div style="display: none;"><a href="#"><img src="../img/logoTt@3x.png"></a></div>
    </div>
    <div class="footer_copyright">版权所有 &copy;2020 19资讯保留</div>
</footer>


<div class="watch_scorer_container" id="watch_scorer_show">
    <div class="block_content link">
        <select id="banner_dropdown" onchange="get_live_matches();">
            <option value="popular" selected="selected">热门赛事</option>
            <option value="football">足球</option>
            <option value="basketball">篮球</option>
            <option value="1">NBA</option>
            <option value="3">CBA</option>
            <option value="1423">英超</option>
            <option value="1461">西甲</option>
            <option value="1469">德甲</option>
            <option value="1449">意甲</option>
            <option value="1482">法甲</option>
            <option value="1877">中超</option>
            <option value="1388">欧冠杯</option>
            <option value="1827">亚冠杯</option>
            <option value="1387">欧洲杯</option>
        </select>
        <span class="selectbox fa fa-chevron-right"></span>
    </div>

    <div id="div_live_matches">

    </div>
</div>

<script type="text/javascript">
    function get_live_matches() {
        var league_id = $("#banner_dropdown").val();

        $.ajax({
            type: 'GET',
            url: api_domain + '/service/match.php',
            data: {"action":"get_live_matches","league_id":league_id},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            success: function (response, status, xhr) {
                var html = template.render($("#live_matches_tpl").html(), {"data": response, "title": $("#banner_dropdown").find("option:selected").text()});
                $("#div_live_matches").html(html);
            },
            error: function () {
            },
        });
    }

    $("#banner_dropdown").change();
</script>

<script type="text/html" id="live_matches_tpl">
    <div class="block_content group_title">{{title}}</div>
    {{each data value index}}
    <div class="block_content group_content">
        <div>{{value.match_type_name}}</div>
        <div>
            <div>{{value.home_team_name}}-主队</div>
            <div>{{value.away_team_name}}-客队</div>
        </div>
        <div>
            <div>{{value.home_score}}</div>
            <div>{{value.away_score}}</div>
        </div>
    </div>
    {{/each}}
</script>

<?php
if(isset($_COOKIE['euid'])){
    $isLogin = true ;
} else {
    $isLogin = false;
}

if(!$isLogin):
    ?>
    <div class="global_menu_container" id="global_menu_show">
        <div class="login_register_tab_container">
            <div class="login_register_tab">
                <a href="#" class="active" data-id="login"><span>登入</span><div class="tab_bg"></div></a>
                <a href="#" data-id="register"><span>注册</span></a>
            </div>
        </div>

        <div class="my_login_container" id="my_login">
            <form id="login_form">
                <label class="block_content"><input name="username" type="text" placeholder="请输入用户ID"></label>
                <label class="block_content"><input name="password" type="password" placeholder="请输入密码"></label>
                <div class="div_imagetranscrits" style="display: flex;">
                    <input name="captcha" type="text" placeholder="请输入验证码">
                    <img src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';">
                </div>
                <label class="block_content text_align_right"><input type="checkbox" id="keeplogin">保持登入</label>
                <input style="display: none" name="action" value="login">
                <div class="block_content"><button type="submit" id="submit_login_btn" class="w-100 active">立即登入</button></div>
                <div class="block_content_row">
                    <button class="w-100 button_style_grey" onclick="window.location = '/forgot_password.php'; return false;">找回密码</button>
                    <button class="w-100 button_style_grey" onclick="window.location = '/forgot_account.php'; return false;">忘记帐户</button>
                </div>
            </form>
        </div>

        <div class="my_login_container" id="my_register" style="display: none;">
            <form id="register_form">
                <label class="block_content"><input name="username" type="text" placeholder="输入用户ID"></label>
                <label class="block_content"><input name="password" type="password" placeholder="输入密码"></label>
                <label class="block_content"><input name="confirm_password" type="password" placeholder="确认密码"></label>
                <label class="block_content" style="display: flex;">
                    <input name="captcha" type="text" placeholder="请输入验证码">
                    <img src="<?php echo CURL_BACKEND_URL; ?>/service/captcha.php" onclick="this.src='<?php echo CURL_BACKEND_URL; ?>/service/captcha.php';">
                </label>
                <input style="display: none" name="action" value="register">
                <div class="block_content"><button type="submit" id="submit_register_btn" class="w-100 active">送出</button></div>
            </form>
        </div>
    </div>
<?php
else:
    ?>
    <div class="global_menu_container" id="global_menu_show">
        <div class="global_menu_categories" id="global_menu_categories">
            <div><a href="#" data-id="football" class="active">足球</a></div>
            <div><a href="#" data-id="basketball">篮球</a></div>
            <div><a href="#" data-id="complex">综合</a></div>
            <div><a href="#" data-id="profile">我的</a></div>
        </div>

        <div class="global_menu_group">
            <div class="global_menu_list football active">
                <div class="block_content"><a href="prediction.php"><img src="../img/menu/estimated.png"></a>预测</div>
                <div class="block_content"><a href="/category.php?category=1"><img src="../img/menu/news.png"></a>新闻</div>
                <!-- <div class="block_content"><a href="#"><img src="../img/menu/competition.png"></a>赛程</div>
                <div class="block_content"><a href="#"><img src="../img/menu/database.png"></a>资料库</div> -->
            </div>

            <div class="global_menu_list basketball">
                <div class="block_content"><a href="prediction.php"><img src="../img/menu/estimated.png"></a>预测</div>
                <div class="block_content"><a href="/category.php?category=2"><img src="../img/menu/news.png"></a>新闻</div>
                <!-- <div class="block_content"><a href="#"><img src="../img/menu/competition.png"></a>赛程</div>
                <div class="block_content"><a href="#"><img src="../img/menu/database.png"></a>资料库</div> -->
            </div>

            <div class="global_menu_list complex">
                <div class="block_content"><a href="/category.php?category=4"><img src="../img/menu/slots.png"></a>电竞</div>
                <div class="block_content"><a href="/category.php?category=3"><img src="../img/menu/snoker.png"></a>台球</div>
                <div class="block_content"><a href="/category.php?category=5"><img src="../img/menu/tennis.png"></a>乒乓球</div>
                <div class="block_content"><a href="/category.php?category=6"><img src="../img/menu/badminton.png"></a>羽毛球</div>
                <div class="block_content"><a href="/category.php?category=8"><img src="../img/menu/others.png"></a>其他</div>
            </div>

            <div class="global_menu_list profile">
                <div class="block_content"><a href="/profile.php"><img src="../img/menu/profile.png"></a>我的首页</div>
                <div class="block_content"><a href="/my_prophet.php"><img src="../img/menu/estimated_history.png"></a>预测历史</div>
                <div class="block_content"><a href="/promo.php"><img src="../img/menu/promo.png"></a>活动专区</div>
                <div class="block_content"><a href="/my_comments.php"><img src="../img/menu/comment.png"></a>留言收藏</div>
                <div class="block_content"><a href="/exchange.php"><img src="../img/menu/point.png"></a>积分兑换</div>
                <div class="block_content"><a href="#"><img src="../img/menu/star.png"></a>潮星天堂</div>
            </div>
        </div>
    </div>
<?php
endif;
?>
