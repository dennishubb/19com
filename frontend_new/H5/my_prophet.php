<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 预测历史</title>
<?php
include("style_script.php");
?>
</head>
<script>
    $(function(){
        updateLeaguest('prophet');
    });
</script>
<body>
<?php
include("header.php");
?>
    <div class="main_container">

        <div class="body_container grey_bg">
            <div class="subpage_title">
                <a class="back" href="#" onclick="window.history.back()">返回</a>
                <div>预测历史</div>
            </div>

            <div class="profile_bg profile_subpage">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" onchange="updateLeaguest('prophet');" id="profile_prophet_category">
                                    <option value="2">篮球</option>
                                    <option value="1">足球</option>
                                    <option value="4">电竞</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_prophet_leaguest">
                                </select>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" id="profile_prophet_year">
                                    <?php
                                    $date = (int) date('Y');
                                    $numYears = 3;
                                    for ($i=$date; $i >= $date - $numYears; $i--) {
                                        echo "<option value=\"$i\" ".($i==$date?'selected="selected"':'').">$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_prophet_month">
                                    <option value="1" <?php echo (date('n') == 1 ? 'selected="selected"' : ''); ?>>1月</option>
                                    <option value="2" <?php echo (date('n') == 2 ? 'selected="selected"' : ''); ?>>2月</option>
                                    <option value="3" <?php echo (date('n') == 3 ? 'selected="selected"' : ''); ?>>3月</option>
                                    <option value="4" <?php echo (date('n') == 4 ? 'selected="selected"' : ''); ?>>4月</option>
                                    <option value="5" <?php echo (date('n') == 5 ? 'selected="selected"' : ''); ?>>5月</option>
                                    <option value="6" <?php echo (date('n') == 6 ? 'selected="selected"' : ''); ?>>6月</option>
                                    <option value="7" <?php echo (date('n') == 7 ? 'selected="selected"' : ''); ?>>7月</option>
                                    <option value="8" <?php echo (date('n') == 8 ? 'selected="selected"' : ''); ?>>8月</option>
                                    <option value="9" <?php echo (date('n') == 9 ? 'selected="selected"' : ''); ?>>9月</option>
                                    <option value="10" <?php echo (date('n') == 10 ? 'selected="selected"' : ''); ?>>10月</option>
                                    <option value="11" <?php echo (date('n') == 11 ? 'selected="selected"' : ''); ?>>11月</option>
                                    <option value="12" <?php echo (date('n') == 12 ? 'selected="selected"' : ''); ?>>12月</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <button class="button_style_dark w-100" onclick="getMyProphet();">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table style="width: 700px;">
                        <thead>
                            <tr>
                                <td width="12.5%">预测时间</td>
                                <td width="12.5%">比赛时间</td>
                                <td width="12.5%">让球</td>
                                <td width="12.5%">大小</td>
                                <td width="12.5%">独赢</td>
                                <td width="12.5%">总得战数</td>
                                <td width="12.5%">状态</td>
                            </tr>
                        </thead>
                        <tbody id="my_prophet_result">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
        include("footer.php");
    ?>
</body>
<script type="text/javascript">
    getMyProphet();
</script>
</html>