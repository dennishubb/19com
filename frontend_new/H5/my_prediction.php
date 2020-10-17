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
                <a class="back" href="#" onclick="window.history.back()">返回</a>
                <div>我的预测与主推</div>
            </div>

            <div class="profile_bg profile_subpage">
                <div class="my_prediction_container">
                    <form>
                        <div class="my_prediction_list">
                            <div class="item">
                                <div class="container">
                                    <div class="close"></div>
                                    <div class="title_block">
                                        <div class="title">皇家马德里 <span>VS</span> 皇家社会</div>
                                        <div class="subtitle">西班牙足球甲级联赛 <span>06-09 19:00</span></div>
                                    </div>
                                    
                                    <div class="content_block">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>让球</td>
                                                    <td>
                                                        <div>多伦多猛龙<span>+1.5</span><img src="img/star_full.svg" class="star"></div>
                                                    </td>
                                                    <td rowspan="3"><div class="edit_btn edit-match-button"><img src="img/prediction/edit_icon.png"><br>修<br>改</div></td>
                                                </tr>
                                                <tr>
                                                    <td>大小</td>
                                                    <td>
                                                        <div>多伦多猛龙<span>大212.5</span><img src="img/star_full.svg" class="star"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>独赢</td>
                                                    <td>
                                                        <div><span>主</span><img src="img/star.svg" class="star"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="container">
                                    <div class="close"></div>
                                    <div class="title_block">
                                        <div class="title">皇家马德里 <span>VS</span> 皇家社会</div>
                                        <div class="subtitle">西班牙足球甲级联赛 <span>06-09 19:00</span></div>
                                    </div>
                                    
                                    <div class="content_block">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>让球</td>
                                                    <td>
                                                        <div>多伦多猛龙<span>+1.5</span><img src="img/star_full.svg" class="star"></div>
                                                    </td>
                                                    <td rowspan="3"><div class="edit_btn edit-match-button"><img src="img/prediction/edit_icon.png"><br>修<br>改</div></td>
                                                </tr>
                                                <tr>
                                                    <td>大小</td>
                                                    <td>
                                                        <div>多伦多猛龙<span>大212.5</span><img src="img/star_full.svg" class="star"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>独赢</td>
                                                    <td>
                                                        <div><span>主</span><img src="img/star.svg" class="star"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.my_prediction_list -->

                        <div class="form_footer_button_container">
                            <button class="w-40 active" id="submit_prediction_btn">送出预测</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
</html>