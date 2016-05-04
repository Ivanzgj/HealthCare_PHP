<html>
<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <title>HealthCare后台数据统计</title>
    <script src="js/echarts.js" type="text/javascript"></script>
    <style>
        .chart {
            width:50%;
            height:400px;
            margin-top: 40px;
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
 * Date: 2016/5/1
 * Time: 13:24
 */
require_once "database.php";
if (!isset($db)) {
    $db = new Database();
}
$users = $db->query()->table(USER_TABLE)
    ->field("age")
    ->field("sex")
    ->select();

$sex_s = Array();
$age_s = Array();
for ($i = 0; $i < count($users); $i++) {

    $sex = $users[$i]["sex"];
    $age = $users[$i]["age"];

    $sex_s[$i] = $sex;
    $age_s[$i] = $age;
}

$boy = 0;
$girl = 0;
$alien = 0;
$children = 0;
$youth = 0;
$adult = 0;
$eldest = 0;
for ($i=0;$i<count($sex_s);$i++) {
    if ($sex_s[$i] == 0) {
        $boy++;
    } else if ($sex_s[$i] == 1) {
        $girl++;
    } else {
        $alien++;
    }
    if ($age_s[$i] > 0) {
        if ($age_s[$i] <= 12) {
            $children++;
        } else if ($age_s[$i] <= 25) {
            $youth++;
        } else if ($age_s[$i] <= 50) {
            $adult++;
        } else {
            $eldest++;
        }
    }
}
?>

<h2>HealthCare后台数据统计系统</h2>
<div id="sex" class="chart"></div>
<div id="age" class="chart"></div>

<script type="text/javascript">
    var count = 0;
    count = <?php echo count($sex_s);?>;
    // 基于准备好的dom，初始化echarts实例
    var sexChart = echarts.init(document.getElementById('sex'));
    if (count > 1) {
        var option = {
            title: {
                text: "用户性别分布图",
                x: "center"
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['男','女','外星人']
            },
            series : [
                {
                    name: '用户性别分布图',
                    type: 'pie',
                    radius: '80%',
                    data:[
                        {value:<?php echo $boy?>, name:'男'},
                        {value:<?php echo $girl?>, name:'女'},
                        {value:<?php echo $alien?>, name:'外星人'}
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        sexChart.setOption(option);
    } else {
        document.getElementById('sex').style.display = "none";
    }

    count = 0;
    count = <?php echo count($sex_s);?>;
    // 基于准备好的dom，初始化echarts实例
    var ageChart = echarts.init(document.getElementById('age'));
    if (count > 1) {
        option = {
            title: {
                text: "用户年龄分布图",
                x: "center"
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['12岁以下儿童','12~25岁青少年','25~50岁成年人','50岁以上老年人']
            },
            series : [
                {
                    name: '用户性别分布图',
                    type: 'pie',
                    radius: '80%',
                    data:[
                        {value:<?php echo $children?>, name:'12岁以下儿童'},
                        {value:<?php echo $youth?>, name:'12~25岁青少年'},
                        {value:<?php echo $adult?>, name:'25~50岁成年人'},
                        {value:<?php echo $eldest?>, name:'50岁以上老年人'}
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        ageChart.setOption(option);
    } else {
        document.getElementById('age').style.display = "none";
    }
</script>

</div>
</body>
</html>