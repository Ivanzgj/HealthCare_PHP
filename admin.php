<html>
<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <title>HealthCare后台管理系统</title>
    <script src="js/admin.js" type="text/javascript"></script>
    <style>
        body {
            font-family: "Microsoft YaHei UI", sans-serif;
            padding: 0 20px;
        }
        .datatb {
            table-layout: fixed;
        }
        .datatd {
            vertical-align: middle;
            padding: 10px 10px;
            border-bottom: 1px solid dimgray;
        }
        table th {
            padding: 0 10px;
        }
        table img {
            width: 30px;
            height: 30px;
        }
        table input {
            display: inline;
        }
        a {font-size:16px}
        a:link {color: cyan; text-decoration:none;}
        a:active:{color: cyan; }
        a:visited {color:cyan;text-decoration:none;}
        a:hover {color: red; text-decoration:underline;}
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
 * Date: 2016/4/30
 * Time: 13:52
 */
if (!isset($uid)) {
    $uid = "";
}
$table = "";
$page = 0;
if (!isset($curPage)) {
    $curPage = 0;
}
define("DATA_PER_PAGE", 10);
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if (isset($_GET["uid"])) {
        $uid = $_GET["uid"];
    }

    require_once "database.php";
    if (!isset($db)) {
        $db = new Database();
    }
    $query = $db->query()->table(USER_TABLE)
        ->field("uid")
        ->field("name")
        ->field("age")
        ->field("sex")
        ->field("birth")
        ->field("constellation")
        ->field("introduction")
        ->field("email")
        ->field("address")
        ->field("measure_today_times")
        ->field("measure_total_times")
        ->field("measure_total_assessment")
        ->order("cast(uid as signed) DESC");
    if (!empty($uid)) {
        $query = $query->where("uid")->eq($REQ['uid'], "string");
    }
    $users = $query->select();

    if (count($users) == 0) {
        $table = "";
    } else {
        $table = "<div><table class='datatb' cellspacing='0' cellpadding='0'>".
            "<tr><th style='height:50px; background-color: darkcyan;'>uid</th>".
            "<th style='height:50px; background-color: cyan;'>avatar</th>".
            "<th style='height:50px; background-color: darkcyan;'>name</th>".
            "<th style='height:50px; background-color: cyan;'>sex</th>".
            "<th style='height:50px; background-color: darkcyan;'>age</th>".
            "<th style='height:50px; background-color: cyan;'>birth</th>".
            "<th style='height:50px; background-color: darkcyan;'>constellation</th>".
            "<th style='height:50px; background-color: cyan;'>email</th>".
            "<th style='height:50px; background-color: darkcyan;'>address</th>".
            "<th style='height:50px; background-color: cyan;'>introduction</th>".
            "<th style='height:50px; background-color: darkcyan;'>measure_today_times</th>".
            "<th style='height:50px; background-color: cyan;'>measure_total_times</th>".
            "<th style='height:50px; background-color: darkcyan;'>measure_total_assessment</th>".
            "<th style='height:50px; background-color: cyan;'>operation</th></tr>";

        if (isset($_GET["page"])) {
            $curPage = $_GET["page"];
        }
        $count = count($users);
        $page = ceil($count / DATA_PER_PAGE);
        for ($i = $curPage * DATA_PER_PAGE; $i < ($curPage+1)*DATA_PER_PAGE; $i++) {
            if ($i >= $count) {
                break;
            }
            $id = $users[$i]["uid"];
            $name = $users[$i]["name"];
            $sex = $users[$i]["sex"];
            $age = $users[$i]["age"];
            $birth = $users[$i]["birth"];
            $constellation = $users[$i]["constellation"];
            $email = $users[$i]["email"];
            $address = $users[$i]["address"];
            $introduction = $users[$i]["introduction"];
            $measure_today_times = $users[$i]["measure_today_times"];
            $measure_total_times = $users[$i]["measure_total_times"];
            $measure_total_assessment = $users[$i]["measure_total_assessment"];

            $table .= "<tr><td style='vertical-align: middle; background-color: #888;' align='middle' class='datatd'>"
                . "<a href='data.php?uid=$id&time=&submit=搜索' target='_blank'>"
                . $id
                . "</a></td><td style='background-color: #999;' align='middle' class='datatd'>"
                . "<img alt='NONE' src='upload/$id.png'>"
                . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $name . "</td><td style='background-color: #999;' align='middle' class='datatd'>"
                . $sex . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $age . "</td><td style='background-color: #999;' align='middle' class='datatd'>"
                . $birth . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $constellation . "</td><td style='background-color: #999;' align='middle' class='datatd'>"
                . $email . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $address . "</td><td style='background-color: #999;' align='middle' class='datatd'>"
                . $introduction . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $measure_today_times . "</td><td style='background-color: #999;' align='middle' class='datatd'>"
                . $measure_total_times . "</td><td style='background-color: #888;' align='middle' class='datatd'>"
                . $measure_total_assessment . "</td><td style='background-color: #999; padding:0 0' align='middle' class='datatd'>"
                . "<input type='button' name='删除' value='删除' onclick='del($id)'>"
                . "<input type='button' name='重置' value='重置' onclick='reset($id)'>" . "</td></tr>";
        }
        $table .= "</table></div>";
    }
}
?>

<h2>HealthCare后台管理系统</h2>

<div>
    <form style="float: left" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <p style="display: inline;">uid</p>
        <input style="position: relative; left: 10px;" type="text" name="uid" title="uid" value="<?php echo $uid;?>">
        <input style="position: relative; left: 20px;" type="submit" name="submit" value="搜索"/>
    </form>

    <table style="float: right; background-color: cadetblue; width: 300px">
        <tr>
            <td align="middle"><a href="data.php">Measurement ></a></td>
            <td align="middle"><a href="chart.php">Statistic ></a></td>
        </tr>
    </table>
</div>

<?php
echo $table;

$startPage = 0;
if ($curPage > 3) {
    $startPage = $curPage - 3;
}
$endPage = $curPage + 3;
if ($endPage >= $page) {
    $endPage = $page - 1;
}
echo "<table><tr>";
for ($i=$startPage;$i<=$endPage;$i++) {
    echo "<td><a onclick='next($i)' style='margin: 0 10px'>$i</a></td>";
}
echo "</tr></table>";

?>

</div>
</body>
</html>