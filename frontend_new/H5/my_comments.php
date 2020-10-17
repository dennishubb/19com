<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 留言收藏</title>
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
                <a class="back" href="javascript:void(0);" onclick="history.go(-1);">返回</a>
                <div>留言收藏</div>
            </div>

            <div class="profile_bg profile_subpage">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" id="my_comment_year">
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
                                <select class="w-100" id="my_comment_month">
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
                        </div>

                        <div class="content_block_row">
                            <div class="content_block w-100" style="display: none;">
                                <select class="w-100">
                                    <option>批量操作</option>
                                </select>
                            </div>
                            <div class="content_block w-50">
                                <button class="button_style_dark w-100" onclick="getComment()">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td width="25%">收藏时间</td>
                                <td width="37.5%">留言内容</td>
                                <td width="37.5%">文章</td>
                            </tr>
                        </thead>
                        <tbody id="my_comment_result">
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
function getComment(){
    var year = document.getElementById('my_comment_year').value ;
    var month =document.getElementById('my_comment_month').value;
    var euid = decodeURIComponent(Cookies.get('euid'));
    $('#my_comment_result').empty();
    var url = api_domain + '/service/message.php';
    $.ajax({
        method: "POST",
        url: url,
        data:{action:'get_collected_message',year:year,month:month,sorting:1,euid:euid} ,
        success: function(data)
        {
            console.log(data);
            if(data != null || data != '') {
                var listData = data['list'];
                for (const [key, value] of Object.entries(listData)) {
                    console.log('key: '+ key + " data: " + value);
                    var created_at = value['created_at'].split(" ");
                    $('#my_comment_result').append('<tr>\n' +
                        '<td>'+ created_at[0] +' <div>'+ created_at[1] +'</div></td>\n' +
                        '<td><div class="long_text">'+value['message']+'</div></td>\n' +
                        '<td><div class="long_text">'+ value['article_title']+'</div></td>\n' +
                        '</tr>');
                }
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}
getComment();
</script>
</html>