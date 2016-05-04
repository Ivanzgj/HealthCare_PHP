<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <title>用户测量数据查询</title>
    <script src="js/echarts.js"></script>
    <style>
        body {
            font-family: "Microsoft YaHei UI", sans-serif;
            padding: 0 20px;
        }
        .datatb {
            table-layout: fixed;
            width:100%;/*定宽*/
            margin:20px auto;/* margin-left 与 margin-right 设置为 auto */
        }
        .datatd {
            word-break: break-all;
            word-wrap: break-word;
            vertical-align: top;
            padding: 15px 15px;
            border-bottom: 1px solid dimgray;
            width: 25%;
        }
        .chart {
            width:50%;
            height:400px;
            margin-top: 20px;
            float: left;
        }
    </style>
</head>
<body>
<div>

<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/27
 * Time: 13:42
 */
$uid = $time = $table = "";
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if (empty($_GET["uid"])) {
        $uid = "";
        $time = "";
    } else {
        $uid = $_GET["uid"];
        $time = $_GET["time"];
        require_once "database.php";
        if (!isset($db)) {
            $db = new Database();
        }
        $query = $db->query()->table(VIBRATION_TABLE)
            ->field("time")
            ->field("data")
            ->where("uid")->eq($uid, "string")
            ->order("time DESC");
        if (!empty($time)) {
            $query = $query->where("time")->eq($time, "string");
        }
        $acc = $query->select();

        $query = $db->query()->table(SRC_TABLE)
            ->field("time")
            ->field("status")
            ->field("date")
            ->where("uid")->eq($uid, "string")
            ->order("date DESC");
        if (!empty($time)) {
            $query = $query->where("date")->eq($time, "string");
        }
        $src = $query->select();

        if (count($acc) == 0) {
            $table = "";
        } else {
            $table = "<div><table class='datatb' cellspacing='0' cellpadding='0'>".
                "<tr><th style='height:50px; background-color: cyan;'>time</th>".
                "<th style='height:50px; background-color: darkcyan;'>acc_data</th>".
                "<th style='height:50px; background-color: cyan;'>src_time</th>".
                "<th style='height:50px; background-color: darkcyan;'>src_status</th></tr>";

            for ($i = 0; $i < count($acc); $i++) {
                $t = $acc[$i]["time"];
                $acc_data = $acc[$i]["data"];
                $src_time = $src[$i]["time"];
                $src_status = $src[$i]["status"];

                $table .= "<tr><td style='vertical-align: middle; background-color: #999;' align='middle' class='datatd'>"
                    . $t . "</td><td style='background-color: #888;' class='datatd'>"
                    . $acc_data . "</td><td style='background-color: #999;' class='datatd'>"
                    . $src_time . "</td><td style='background-color: #888;' class='datatd'>"
                    . $src_status . "</td></tr>";
            }
            $table .= "</table></div>";
        }
    }
}
?>

<h2>用户测量数据查询系统</h2>
<form style="border-bottom: 1px solid dimgray; padding-bottom: 20px" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <p style="display: inline;">uid</p>
    <input style="position: relative; left: 10px;" type="text" name="uid" title="uid" value="<?php echo $uid;?>">
    <p style="display: inline; position:relative; left: 20px;">time</p>
    <input style="position: relative; left: 30px;" type="text" name="time" title="time" value="<?php echo $time;?>">
    <input style="position: relative; left: 50px;" type="submit" name="submit" value="搜索"/>
</form>

<div id="acc" class="chart"></div>
<div id="src" class="chart"></div>

<script type="text/javascript">
    var accCount = 0;
    accCount = <?php echo count($acc);?>;
    // 基于准备好的dom，初始化echarts实例
    var accChart = echarts.init(document.getElementById('acc'));
    if (accCount != 0) {
        var str = "<?php echo $acc[0]["data"];?>";
        var data = str.split("|");
        var x = [];
        for (var i=0;i<data.length;i++) {
            x[i] = i;
        }
        var date = "<?php echo $acc[0]["time"];?>";
        date = date.substr(0, 4)+"年"+date.substr(4, 2)+"月"+date.substr(6, 2)+"日"+date.substr(8, 2)+":"
                +date.substr(10, 2)+":"+date.substr(12, 2);
        // 指定图表的配置项和数据
        var option = {
            title: {
                text: 'acc_data'
            },
            tooltip: {},
            legend: {
                data: [date]
            },
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    axisLine: {onZero: true},
                    data: x
                }
            ],
            yAxis : [
                {
                    name : '加速度(m/s^2)',
                    type : 'value',
                    max : 35
                }
            ],
            series: [{
                name: date,
                type: 'line',
                data: data,
                areaStyle: {normal: {}}
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        accChart.setOption(option);
    } else {
        document.getElementById('main').style.display = "none";
    }

    var srcCount = 0;
    srcCount = <?php echo count($src);?>;
    // 基于准备好的dom，初始化echarts实例
    var srcChart = echarts.init(document.getElementById('src'));
    if (srcCount != 0) {
        data = "<?php echo $src[0]["status"];?>".split("|");
        x = "<?php echo $src[0]["time"];?>".split("|");
        date = "<?php echo $src[0]["date"];?>";
        date = date.substr(0, 4)+"年"+date.substr(4, 2)+"月"+date.substr(6, 2)+"日"+date.substr(8, 2)+":"
            +date.substr(10, 2)+":"+date.substr(12, 2);
        // 指定图表的配置项和数据
        option = {
            title: {
                text: 'src_data'
            },
            tooltip: {},
            legend: {
                data: [date]
            },
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    axisLine: {onZero: true},
                    data: x
                }
            ],
            yAxis : [
                {
                    name : '屏幕控制(off/on/in)',
                    type : 'value'
                }
            ],
            series: [{
                name: date,
                type: 'line',
                data: data,
                areaStyle: {normal: {}}
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        srcChart.setOption(option);
    } else {
        document.getElementById('src').style.display = "none";
    }
</script>

<?php
echo $table;
?>

</div>
</body>
</html>