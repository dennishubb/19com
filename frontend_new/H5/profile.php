<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/config/shared_function.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common/function.php');
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>19资讯 - 我的首页</title>
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
        $euid = rawurldecode($_COOKIE['euid']) ;
        $param = array('euid'=>$euid);
        $userInfo = httpPost(CURL_API_URL . '/service/user.php?action=getuserinfo',$param);
        $userInfoExtra = httpPost(CURL_API_URL . '/service/user.php?action=getextrainfo',$param);
        $userInfo = json_decode($userInfo, true);
        $userInfoExtra = json_decode($userInfoExtra, true);
        ?>

        <div class="body_container">
            <div class="profile_header">
                <img src="<?php echo IMAGE_URL . $userInfo['user']['image'] ?>">
            </div>

            <div class="profile_name_container">
                <div><?php echo $userInfoExtra['user']['name'] ?></div>
                <div class="level"><?php echo $userInfo['user']['level']?></div>

                <div class="account_manage_container">
                    <button class="active" onclick="window.location = 'account_setting.php'">账号设置</button>
                    <button onclick="window.location = 'my_comments.php'">留言收藏</button>
                </div>
            </div>

            <div class="point_exchange_container session_block">
                <div>
                    <div>总累积战数</div>
                    <div class="point"><?php echo intval($userInfo['user']['total_points'])?></div>
                    <div class="weekly">本周 <?php echo intval($userInfo['user']['weekly_points'])?></div>
                </div>
                <div>
                    <div>现有战数</div>
                    <div class="point"><?php echo intval($userInfo['user']['points'])?></div>
                </div>
                <div>
                    <button onclick="window.location='/exchange.php'">兑换</button>
                </div>
            </div>

            <div class="profile_session">
                <div class="session_block_title">战绩</div>
            </div>

            <div class="profile_bg">
                <div class="profile_submenu_container">
                    <div><button class="active" data-id="overview">总览</button></div>
                    <div onclick="getWinchanges();"><button data-id="winchanges">胜率</button></div>
                    <div onclick="getProphet();"><button data-id="prophet">预言家资格</button></div>
                </div>
            </div>

            <div class="profile_bg profile_subpage" id="profile_overview">
                <div class="profile_chart">
                    <canvas id="chartjs-radar-canvas"></canvas>
                </div>

                <div class="profile_graph">
                    <select class="w-60" id="leaguest_list" onchange="getLeaguestData();">
                    </select>

                    <canvas id="chartjs-bar-canvas"></canvas>
                </div>

                <div class="profile_session">
                    <div class="session_block_title">预测历史 <a href="/my_prophet.php">更多</a></div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td>预测时间</td>
                                <td>比赛时间</td>
                                <td>让球</td>
                                <td>大小</td>
                                <td>独赢</td>
                                <td>总得战数</td>
                                <td>状态</td>
                            </tr>
                        </thead>
                        <tbody id="history_list">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="profile_bg profile_subpage" style="display: none;" id="profile_winchanges">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select id="profile_winchanges_category" class="w-100" onchange="updateLeaguest('winchanges');">
                                    <option value="2" selected="selected">篮球</option>
                                    <option value="1">足球</option>
                                    <option value="4">电竞</option>
                                </select>
                            </div>
                            <div class="content_block">
                                <select class="w-100" id="profile_winchanges_leaguest">

                                </select>
                            </div>
                        </div>

                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" id="profile_winchanges_year">
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
                                <select class="w-100" id="profile_winchanges_month">
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
                                <button class="button_style_dark w-100" onclick="getWinchanges();">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td width="25%">项目</td>
                                <td width="25%">胜场</td>
                                <td width="25%">败场</td>
                                <td width="25%">胜率 [排名]</td>
                            </tr>
                        </thead>
                        <tbody id="profile_winchanges_result">
                        </tbody>
                    </table>
                    <div class="notice">战绩于每日下午五点计算</div>
                </div>
            </div>

            <div class="profile_bg profile_subpage" style="display: none;" id="profile_prophet">
                <div class="profile_subpage_filter_container">
                    <div class="profile_subpage_filter_content">
                        <div class="content_block_row">
                            <div class="content_block">
                                <select class="w-100" onchange="updateLeaguest('prophet');" id="profile_prophet_category">
                                    <option value="2" selected="selected">篮球</option>
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
                            <div class="content_block w-100">
                                <select class="w-100" id="profile_prophet_time">
                                    <?php echo '<option value="'.date('Y/m', time()).'" selected="selected">'.date('Y/m', time()).'</option>'; ?>
                                    <?php echo '<option value="'.date('Y/m', strtotime('-1 month')).'">'.date('Y/m', strtotime('-1 month')).'</option>'; ?>
                                    <?php echo '<option value="'.date('Y/m', strtotime('-2 month')).'">'.date('Y/m', strtotime('-2 month')).'</option>'; ?>
                                </select>
                            </div>
                            <div class="content_block w-50">
                                <button class="button_style_dark w-100" onclick="getProphet();">确认</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table_container layout">
                    <table>
                        <thead>
                            <tr>
                                <td width="25%">项目</td>
                                <td width="25%">累积战绩</td>
                                <td width="25%">神级预言家</td>
                                <td width="25%">达到标准</td>
                            </tr>
                        </thead>
                        <tbody id="profile_prophet_result">

                        </tbody>
                    </table>
                    <div class="notice">下期评选日:<?php echo date('m/2', strtotime('+1 month')); ?>, 评选期间:<?php echo date('m/01', time()).'~'.date('m/d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day')); ?></div>
                </div>
            </div>

        </div>
    </div>

    <?php
        include("footer.php");
    ?>
    <script>
        var num = 0;
        $(function(){
            loadLeaguest(2);
            loadLeaguest(1);
            loadLeaguest(4);
            updateLeaguest('winchanges');
            updateLeaguest('prophet');
            getLeaguestData();

            var year = new Date().getFullYear();
            var month = parseInt(new Date().getMonth())+1;
            getHistyoryList(0,0,year,month);
        });

        function loadLeaguest(cvalue){
            var url = api_domain+'/service/match.php';
            $.ajax({
                method: "GET",
                url: url,
                data:{action:'get_leagues',category_id:cvalue},
                async: false,
                success: function(data)
                {
                    if(data != null || data != '') {
                        for (const [key, value] of Object.entries(data)) {
                            $("#leaguest_list").append('<option value='+cvalue+'_'+data[key]['id']+' '+(num==0?'selected="selected"':'')+'>' + data[key]['name_zh'] + '</option>');
                            num++;
                        }
                    }
                },
                error: function (request, status, error) {
                }
            });
        }

        function getLeaguestData(){
            var leaguestValue = document.getElementById('leaguest_list').value ;
            var cid = leaguestValue.split("_")[0];
            var lid = leaguestValue.split("_")[1];
            var euid = Cookies.get('euid');
            var url = api_domain+'/service/user.php';
            $.ajax({
                method: "POST",
                url: url,
                data:{action:'get_prediction_stats',category_id:cid,league_id:lid,euid:euid},
                async: false,
                success: function(data)
                {
                    if(data != null || data != '') {
                        setTimeout(function() {
                            var dataGenerate = [data['prediction_total_count'],data['prediction_count'],data['voucher'],data['win_rate'],data['total_win_rate']];
                            console.log(dataGenerate);
                            generateChat(dataGenerate);
                            var dataGenerate = [data.win_rate,0,data.top_ten_count];
                            console.log(dataGenerate);
                            generateChatBar(dataGenerate);
                        }, 1000);
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });

            // var time = year + '/' + month;
            // $.ajax({
            //     method: "POST",
            //     url: url,
            //     data:{action:'get_prediction_rate',category_id:cid,league_id:lid,euid:euid,time:time} ,
            //     success: function(data)
            //     {
            //         console.log(data);
            //         if(data != null || data != '') {
            //             // var dataGenerate = [data.win_rate,data['handicap']['rate'],data['total']['rate']];
            //             // generateChatBar(dataGenerate);
            //         }
            //     },
            //     error: function (request, status, error) {
            //         alert(request.responseText);
            //     }
            // });

        }

        function generateChat(value){
            var data = {
                labels: ["战数", "预言次数", "神級兌換券", "参与率", "总胜率"], // Radar Chart Label
                datasets: [{ data: value, // Radar Chart Data
                    borderColor: "#ff4a60",
                    borderWidth: 1,
                    backgroundColor: "rgba(255, 108, 126, 0.5)",
                    pointBorderWidth: 5,
                    pointBorderColor: "#ff6c7e"
                }]
            };

            var options = {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                scale: {
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        stepSize: 0,
                        display: false,
                        maxTicksLimit: 1,
                    },
                    gridLines: {
                        color: "#858585"
                    },
                    angleLines: {
                        color: '#858585'
                    }
                }
            };

            var ctx = document.getElementById("chartjs-radar-canvas");
            var myRadarChart = new Chart(ctx, {
                type: 'radar',
                data: data,
                options: options,
            });
        }

        function generateChatBar(value){
            var dataBar = {
                labels: ["单月胜率", "主推月胜率", "神准预言家"], // Radar Chart Label
                datasets: [{ data: value, // Radar Chart Data
                    backgroundColor: ["#ee243c", "#fcbc0a", "#969696", "#0113ff", "#00f75d", "#f700ee", "#00eef7"]
                }]
            };

            var optionsBar = {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        display: false,
                        ticks: {
                            min: 0,
                            max: (dataBar.datasets[0].data.max() + 30)
                        },
                        gridLines: {
                            display: false
                        }
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            color: "rgba(0, 0, 0, 0)",
                            display: false
                        }
                    }]
                },
                animation: {
                    duration: 1,
                    onComplete: function () {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                        ctx.textAlign = 'center';
                        ctx.fillStyle = "rgba(0, 0, 0, 1)";
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index];
                                if(index < 2){
                                    data = data + "%";
                                }
                                ctx.fillText(data, bar._model.x, bar._model.y - 5);
                            });
                        });
                    }
                }
            };

            var ctxBar = document.getElementById("chartjs-bar-canvas").getContext('2d');
            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: dataBar,
                options: optionsBar,
            });
        }

        function getHistyoryList(cid,lid,year,month){
            var url = api_domain+'/service/prediction.php';
            var euid = Cookies.get('euid');
            $("#history_list").empty();
            $.ajax({
                method: "POST",
                url: url,
                data:{action:'get_prediction_history',category_id:cid,league_id:lid,year:year,month:month,euid:euid} ,
                success: function(data)
                {
                    if(data != null || data != '') {
                        var listData = data['list'];
                        for (const [key, value] of Object.entries(listData)) {
                            var match_at = value['match_at'].split(" ");
                            var created_at = value['created_at'].split(" ");
                            // var handicap = value['handicap'].split(" ");
                            $('#history_list').append('<tr>' +
                                '<td>'+created_at[0]+' <div>'+created_at[1]+'</div></td>' +
                                '<td>'+match_at[0]+'<div>'+match_at[1]+'</div></td>' +
                                '<td>'+value['handicap']+'</td>' +
                                '<td>'+value['over_under']+'</td>' +
                                '<td>'+value['single']+'</td>' +
                                '<td>'+value['win_amount']+'</td>' +
                                '<td>'+value['status']+'</td>' +
                                '</tr>');
                        }
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        }


function getWinchanges(){
    var url = api_domain+'/service/user.php';
    var cid = document.getElementById('profile_winchanges_category').value ;
    var lid = document.getElementById('profile_winchanges_leaguest').value ;
    var time = document.getElementById('profile_winchanges_year').value + '/'+ document.getElementById('profile_winchanges_month').value;
    var euid = decodeURIComponent(getCookie('euid'));
    $("#profile_winchanges_result").empty();
    $.ajax({
        method: "POST",
        url: url,
        data:{action:'get_prediction_rate',category_id:cid,league_id:lid,time:time,euid:euid} ,
        success: function(data)
        {
            var html = '<tr><td>让分</td><td>'+ data.handicap.win_count + '</td> <td>'+ data.handicap.lose_count +'</td> <td>'+ data.handicap.rate +'% ['+ data.handicap.rank +']</td></tr><tr><td>大小</td><td>'+ data.over_under.win_count + '</td> <td>'+ data.over_under.lose_count +'</td> <td>'+ data.over_under.rate +'% ['+ data.over_under.rank +']</td></tr><tr><td>独赢</td><td>'+ data.single.win_count + '</td> <td>'+ data.single.lose_count +'</td> <td>'+ data.single.rate +'% ['+ data.single.rank +']</td></tr><tr><td>总胜场</td><td>'+ data.total.win_count + '</td> <td>'+ data.total.lose_count +'</td> <td>'+ data.total.rate +'% ['+ data.total.rank +']</td></tr>';
            $('#profile_winchanges_result').html(html);
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

function getProphet(){
    var url = api_domain+'/service/user.php';
    var cid = document.getElementById('profile_prophet_category').value ;
    var lid = document.getElementById('profile_prophet_leaguest').value ;
    var time = document.getElementById('profile_prophet_time').value;
    var euid = decodeURIComponent(getCookie('euid'));
    $("#profile_prophet_result").empty();
    $.ajax({
        method: "POST",
        url: url,
        data:{action:'get_prediction_qualification',category_id:cid,league_id:lid,time:time,euid:euid} ,
        success: function(data)
        {
            var html = template.render($("#prediction_table_tpl").html(), {"data": data});
            $("#profile_prophet_result").html(html);
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}
    </script>

    <script>
        $(function(){
            var v = [0,0,0,0,0];
            var v2 = [0,0,0];
            generateChat(v);
            generateChatBar(v2);
            $(".profile_submenu_container button").click(function(e){
                e.preventDefault();

                var thisData = $(this).data("id");
                $(".profile_submenu_container button").removeClass("active");
                $(this).addClass("active");
                $(".profile_subpage").stop().slideUp(300);
                $("#profile_"+thisData).stop().slideDown(300);
            })
        })
    </script>

  <script type="text/html" id="prediction_table_tpl">
    <tr>
        <td>本赛季胜率</td>
        <td>{{data.season_rate}}%</td>
        <td>{{data.top_ten_season_rate}}%</td>
        <td>
            {{if (data.top_ten_season_rate=='-')}}
            ㄨ
            {{else}}
            {{data.season_rate>=data.top_ten_season_rate?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
    <tr>
        <td>单月最低预测次数</td>
        <td>{{data.total_count}}</td>
        <td>{{data.top_ten_prediction_count}}</td>
        <td>
            {{if (data.top_ten_prediction_count=='-')}}
            ㄨ
            {{else}}
            {{data.total_count>=data.top_ten_prediction_count?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
    <tr>
        <td>单月总胜率</td>
        <td>{{data.rate}}%</td>
        <td>{{data.top_ten_rate}}%</td>
        <td>
            {{if (data.top_ten_rate=='-')}}
            ㄨ
            {{else}}
            {{data.rate>=data.top_ten_rate?'〇':'ㄨ'}}
            {{/if}}
        </td>
    </tr>
  </script>
</body>
</html>