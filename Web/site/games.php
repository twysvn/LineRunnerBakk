<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

$graphlabels = [];
$graphdata = [[],[],[],[]];
$graphdatawos0 = [[],[],[],[]];
$graphdataws0 = [[],[],[],[]];
$graphdatamet = [[],[],[],[]];

for ($i=0; $i < 100; $i++) {
    $graphlabels[$i] = $i/100;
    $graphdata[0][$i] = 0;
    $graphdata[1][$i] = 0;
    $graphdata[2][$i] = 0;
    $graphdata[3][$i] = 0;
    $graphdatamet[0][$i] = 0;
    $graphdatamet[1][$i] = 0;
    $graphdatamet[2][$i] = 0;
    $graphdatamet[3][$i] = 0;
    $graphdatawos0[0][$i] = 0;
    $graphdatawos0[1][$i] = 0;
    $graphdatawos0[2][$i] = 0;
    $graphdatawos0[3][$i] = 0;
    $graphdataws0[0][$i] = 0;
    $graphdataws0[1][$i] = 0;
    $graphdataws0[2][$i] = 0;
    $graphdataws0[3][$i] = 0;
}

for ($i=0; $i < 4; $i++) {
    $stmt = $conn->prepare("SELECT g.difficulty, (COUNT( g.id ) * 100 / (Select Count(*) From `game` WHERE type = $i))
                            FROM  `game` AS g
                            WHERE type = $i
                            GROUP BY g.difficulty
                            ORDER BY g.difficulty ASC");
    $stmt->bind_result($graphdiff, $grapham);
    $stmt->execute();

    while ($stmt->fetch()) {
        $graphdata[$i][round($graphdiff * 100, 0)] = round($grapham, 2);
    }
    $stmt->close();
}

for ($i=0; $i < 4; $i++) {
    $stmt = $conn->prepare("SELECT g.difficulty, (COUNT( g.id ) * 100 / (Select Count(*) From `game` WHERE type = $i))
                            FROM  `game` AS g
                            WHERE type = $i AND g.difficulty_standard_deviation > 0
                            GROUP BY g.difficulty
                            ORDER BY g.difficulty ASC");
    $stmt->bind_result($graphdiffwos0, $grapham);
    $stmt->execute();

    while ($stmt->fetch()) {
        $graphdatawos0[$i][round($graphdiffwos0 * 100, 0)] = round($grapham, 2);
    }
    $stmt->close();
}

for ($i=0; $i < 4; $i++) {
    $stmt = $conn->prepare("SELECT g.difficulty, (COUNT( g.id ) * 100 / (Select Count(*) From `game` WHERE type = $i))
                            FROM  `game` AS g
                            WHERE type = $i AND g.difficulty_standard_deviation = 0
                            GROUP BY g.difficulty
                            ORDER BY g.difficulty ASC");
    $stmt->bind_result($graphdiffws0, $grapham);
    $stmt->execute();

    while ($stmt->fetch()) {
        $graphdataws0[$i][round($graphdiffws0 * 100, 0)] = round($grapham, 2);
    }
    $stmt->close();
}

for ($i=0; $i < 4; $i++) {
    $stmt = $conn->prepare("SELECT g.difficulty_metric, (COUNT( g.id ) * 100 / (Select Count(*) From `game` WHERE type = $i))
                            FROM  `game` AS g
                            WHERE type = $i
                            GROUP BY g.difficulty_metric
                            ORDER BY g.difficulty_metric ASC");
    $stmt->bind_result($graphdiffmet, $grapham);
    $stmt->execute();

    while ($stmt->fetch()) {
        $graphdatamet[$i][round($graphdiffmet * 100, 0)] = round($grapham, 2);
    }
    $stmt->close();
}

$graphsigmalabels = [];
$graphsigmadata = [[],[],[],[]];
for ($i=0; $i < 50; $i++) {
    $graphsigmalabels[$i] = $i/100;
    $graphsigmadata[0][$i] = null;
    $graphsigmadata[1][$i] = null;
    $graphsigmadata[2][$i] = null;
    $graphsigmadata[3][$i] = null;
}

for ($i=0; $i < 4; $i++) {

    $stmt = $conn->prepare("SELECT g.difficulty_standard_deviation, (COUNT( g.id ) * 100 / (Select Count(*) From `game` WHERE type = $i))
                            FROM  `game` AS g
                            WHERE type = $i
                            GROUP BY g.difficulty_standard_deviation
                            ORDER BY g.difficulty_standard_deviation ASC");
    $stmt->bind_result($graphsigma, $grapham);
    $stmt->execute();

    while ($stmt->fetch()) {
        $graphsigmadata[$i][round($graphsigma * 100, 0)] = round($grapham, 2);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Games</title>
            <link rel="stylesheet" href="style.css">
            <script src="../site/script/sbecky.js" charset="utf-8"></script>
            <script src="Chart.min.js" charset="utf-8"></script>
            <script src="chart.js" charset="utf-8"></script>
        </head>
    </head>
    <body>
        <h1>Compare game properties</h1>
        <form style="display:block;overflow:hidden;width:100%;" class="" action="getdata.php" method="post" sb-form="getdata" sb-ajax="getdata.php" sb-bind="getdata">
            <div class="" sb-placeholder="getdata" style="height: 1094px;line-height: 1094px;width: 100%;text-align:center;color:gray;background: #f9f9f9;">
                Loading...
            </div>
            <div class="" sb-error="getdata" style="height: 1094px;line-height: 1094px;width: 100%;text-align:center;color:red;background: #f9f9f9;">
                Server not responding (maybe choose a better hoster?).
            </div>
        </form>

        <div class="charts">
            <div>
                <h1>Game Difficulty Probability</h1>
                <canvas id="diffprob"></canvas>
            </div>
            <div>
                <h1>Log Game Difficulty Probability</h1>
                <canvas id="diffproblog"></canvas>
            </div>
            <div>
                <h1>Game Difficulty Standard Deviation Probability</h1>
                <canvas id="diffsigma"></canvas>
            </div>
            <div>
                <h1>Log Game Difficulty Standard Deviation Probability</h1>
                <canvas id="diffsigmalog"></canvas>
            </div>
            <div>
                <h1>Game Difficulty Metric Probability</h1>
                <canvas id="diffmetprob"></canvas>
            </div>
            <div>
                <h1>Log Game Difficulty Metric Probability</h1>
                <canvas id="diffmetproblog"></canvas>
            </div>
            <div>
                <h1>Game Difficulty Probability sigma > 0</h1>
                <canvas id="diffprobwos0"></canvas>
            </div>
            <div>
                <h1>Log Game Difficulty Probability sigma > 0</h1>
                <canvas id="diffproblogwos0"></canvas>
            </div>
            <div>
                <h1>Game Difficulty Probability with only sigma = 0</h1>
                <canvas id="diffprobws0"></canvas>
            </div>
            <div>
                <h1>Log Game Difficulty Probability with only sigma = 0</h1>
                <canvas id="diffproblogws0"></canvas>
            </div>
        </div>

        <?php
        $stmt = $conn->prepare("SELECT COUNT(id) FROM game
                                WHERE type = 1 ");
        $stmt->bind_result($len);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(id) FROM game
                                WHERE `interesting` = 1
                                AND type = 1 ");
        $stmt->bind_result($sel);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
         ?>
        <h1>Games (<?= $sel ?>/<?= $len ?>)</h1>

        <p>
            Sort table by <a href="javascript:void(0)" onclick="sort(6, this)" style="margin-bottom:1rem;display:inline-block;">random</a>
        </p>
        <table>
            <thead>
                <tr>
                    <th><div>p_flat</div></th>
                    <th><div>p_hole</div></th>
                    <th><div>p_obstacle</div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(11, this)">speed</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(10, this)">force</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(9, this)">gravity</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(8, this)">obst_height</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(7, this)">block_length</a></div></th>
                    <th><div>seed</div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(2, this)">score</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(0, this)">difficulty</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(1, this)">sigma</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(12, this)">difficulty_metric</a></div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(3, this)">survivors</a></div></th>
                    <th><div>max_players</div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(4, this)" class="selected">timestamp</a></div></th>
                    <th><div>graph</div></th>
                    <th><div><a href="javascript:void(0)" onclick="sort(5, this)">Interesting</a></div></th>
                    <th><div>export</div></th>
                </tr>
            </thead>

            <!-- <tbody sb-ajax="getplayed.php" sb-trigger="poll" onlynew="true" poll-interval="10000" sb-bind="poll"></tbody> -->
            <tbody sb-ajax="getplayed.php" items = "20" sb-bind="table" sb-trigger="scroll-end"></tbody>

        </table>
        <a href="javascript:void(0)" sb-button="table" class="loadmore">Load more</a>

        <table style="display:none;">
            <tbody>
                <tr sb-error="table">
                    <td colspan="19"><i>Error</i></td>
                </tr>
                <tr sb-placeholder="table" height="117px">
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                    <td class="fade" style="text-align:center;"><i>...</i></td>
                </tr>
            </tbody>
        </table>

        <style media="screen">
            @keyframes fadeIn {
                0% { opacity: 1; }
                50% { opacity: 0; }
                100% { opacity: 1; }
            }

            .fade {
                animation: fadeIn 0.5s infinite ease-in-out;
            }
        </style>

        <script type="text/javascript">

            sbecky_ready(function () {
                // sbecky_onresponse('table', function () {
                //     var poll = sbecky_get('poll')[0]
                //     var table = sbecky_get('table')[0]
                //     poll.setAttribute('last', table.children[0].children[14].innerHTML);
                //     sbecky_load('poll');
                // })

                sbecky_onresponse_error('getdata', function () {
                    setTimeout(function () {
                        sbecky_load('getdata');
                    }, 5000);
                })
            })

            function sort(w, a) {
                var tr = a.parentNode.parentNode.parentNode;
                for (var i in tr.children) {
                    if (tr.children.hasOwnProperty(i)) {
                        var th = tr.children[i]
                        var div = th.children[0]
                        if (div.children.length > 0) {
                            var ia = div.children[0]
                            ia.classList.remove('selected')
                        }
                    }
                }
                a.classList.add('selected')

                var table = sbecky_get("table")[0];
                table.setAttribute('order', w);
                table.innerHTML = "";
                // sbecky_load(table);
            }
            function copytoclipboard(value) {
                var tempInput = document.createElement("textarea");
                tempInput.style = "position: absolute; left: -1000px; top: -1000px";
                tempInput.value = value;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                return "copied!";
            }

            function setupprobgraph(name, axistype, labels, data1, data2, data3, data4, label1, label2, revrse = false, spanGaps = false) {
                var diffprob = document.getElementById(name).getContext('2d');
                new Chart(diffprob, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: (function () {
                            var arr = [{
                                label: 'default',
                                data: data1,
                                backgroundColor: [
                                    'rgba(255, 80, 90, 0.6)'
                                ],
                                pointRadius: 0,
                                lineTension:0
                            },{
                                label: '!every_second_flat',
                                data: data2,
                                backgroundColor: [
                                    'rgba(80, 255, 90, 0.6)'
                                ],
                                pointRadius: 0,
                                lineTension:0
                            },{
                                label: '!smart_level_gen',
                                data: data3,
                                backgroundColor: [
                                    'rgba(90, 80, 255, 0.6)'
                                ],
                                pointRadius: 0,
                                lineTension:0
                            },{
                                label: '!every_second_flat & !smart_level_gen',
                                data: data4,
                                backgroundColor: [
                                    'rgba(80, 80, 80, 0.6)'
                                ],
                                pointRadius: 0,
                                lineTension:0
                            }]
                            if (revrse) {
                                return arr.reverse();
                            }
                            return arr;
                        })()
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                type: axistype,
                                ticks: {
                                    beginAtZero:true,
                                    fontSize: 14,
                                    callback: function(value, index, values) {
                                        const remain = value / (Math.pow(10, Math.floor(Chart.helpers.log10(value))));

                                        if (axistype == 'linear' || remain === 1 || remain === 2 || remain === 5 || index === 0 || index === values.length - 1 ) {
                                            return Number(value.toString()) + '%';
                                        } else {
                                            return '';
                                        }
                                    },
                                    autoSkip: true
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: label1
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontSize: 14
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: label2
                                }
                            }]
                        },
                        tooltips: {
                            enabled: true,
                            mode: 'single',
                            callbacks: {
                                label: function(tooltipItems, data) {
                                    return tooltipItems.yLabel + ' %';
                                }
                            }
                        },
                        spanGaps: spanGaps
                    }
                });
            }

            setupprobgraph("diffprob",
                            "linear",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdata[1]) ?>,
                            <?= json_encode($graphdata[0]) ?>,
                            <?= json_encode($graphdata[2]) ?>,
                            <?= json_encode($graphdata[3]) ?>,
                            'percentage',
                            'game difficulty');
            setupprobgraph("diffproblog",
                            "logarithmic",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdata[1]) ?>,
                            <?= json_encode($graphdata[0]) ?>,
                            <?= json_encode($graphdata[2]) ?>,
                            <?= json_encode($graphdata[3]) ?>,
                            'percentage',
                            'game difficulty');
            setupprobgraph("diffsigma",
                            "linear",
                            <?= json_encode($graphsigmalabels) ?>,
                            <?= json_encode($graphsigmadata[1]) ?>,
                            <?= json_encode($graphsigmadata[0]) ?>,
                            <?= json_encode($graphsigmadata[2]) ?>,
                            <?= json_encode($graphsigmadata[3]) ?>,
                            'percentage',
                            'game difficulty standard deviation', true, true);
            setupprobgraph("diffsigmalog",
                            "logarithmic",
                            <?= json_encode($graphsigmalabels) ?>,
                            <?= json_encode($graphsigmadata[1]) ?>,
                            <?= json_encode($graphsigmadata[0]) ?>,
                            <?= json_encode($graphsigmadata[2]) ?>,
                            <?= json_encode($graphsigmadata[3]) ?>,
                            'percentage',
                            'game difficulty standard deviation', true, true);
            setupprobgraph("diffmetprob",
                            "linear",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdatamet[1]) ?>,
                            <?= json_encode($graphdatamet[0]) ?>,
                            <?= json_encode($graphdatamet[2]) ?>,
                            <?= json_encode($graphdatamet[3]) ?>,
                            'percentage',
                            'game difficulty');
            setupprobgraph("diffmetproblog",
                            "logarithmic",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdatamet[1]) ?>,
                            <?= json_encode($graphdatamet[0]) ?>,
                            <?= json_encode($graphdatamet[2]) ?>,
                            <?= json_encode($graphdatamet[3]) ?>,
                            'percentage',
                            'game difficulty');

            setupprobgraph("diffprobwos0",
                            "linear",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdatawos0[1]) ?>,
                            <?= json_encode($graphdatawos0[0]) ?>,
                            <?= json_encode($graphdatawos0[2]) ?>,
                            <?= json_encode($graphdatawos0[3]) ?>,
                            'percentage',
                            'game difficulty');
            setupprobgraph("diffproblogwos0",
                            "logarithmic",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdatawos0[1]) ?>,
                            <?= json_encode($graphdatawos0[0]) ?>,
                            <?= json_encode($graphdatawos0[2]) ?>,
                            <?= json_encode($graphdatawos0[3]) ?>,
                            'percentage',
                            'game difficulty');

            setupprobgraph("diffprobws0",
                            "linear",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdataws0[1]) ?>,
                            <?= json_encode($graphdataws0[0]) ?>,
                            <?= json_encode($graphdataws0[2]) ?>,
                            <?= json_encode($graphdataws0[3]) ?>,
                            'percentage',
                            'game difficulty');
            setupprobgraph("diffproblogws0",
                            "logarithmic",
                            <?= json_encode($graphlabels) ?>,
                            <?= json_encode($graphdataws0[1]) ?>,
                            <?= json_encode($graphdataws0[0]) ?>,
                            <?= json_encode($graphdataws0[2]) ?>,
                            <?= json_encode($graphdataws0[3]) ?>,
                            'percentage',
                            'game difficulty');

        </script>
    </body>
</html>
