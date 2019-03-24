<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ratings</title>
        <link rel="stylesheet" href="style.css">
        <script src="../site/script/sbecky.js" charset="utf-8"></script>
        <script src="Chart.min.js" charset="utf-8"></script>
        <script src="chart.js" charset="utf-8"></script>
    </head>
    <body>
        <div class="overlay" sb-ajax="getratingsofgame.php" sb-trigger="none" sb-bind="overlay" style="display:none;"></div>
        <div class="ratings">
            <?php
            $stmt = $conn->prepare("SELECT COUNT(id) FROM rating");
            $stmt->bind_result($len);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
             ?>
            <h1>Ratings (<?= $len ?>)</h1>
            <table>
                <thead>
                    <tr>
                        <th><div><a href="javascript:void(0)" onclick="sort(0, this)">game</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(1, this)">game difficulty</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(2, this)">game sigma</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(3, this)">#ratings</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(4, this)" class="selected">μ(fun)</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(5, this)">σ(fun)</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(6, this)">μ(difficulty)</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(7, this)">σ(difficulty)</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(8, this)">μ(score)</a></div></th>
                        <th><div><a href="javascript:void(0)" onclick="sort(9, this)">σ(score)</a></div></th>
                        <th><div>p_hole</div></th>
                        <th><div>p_obstacle</div></th>
                        <th><div>speed</div></th>
                        <th><div>force</div></th>
                        <th><div>gravity</div></th>
                        <th><div>obst_height</div></th>
                        <th><div>block_length</div></th>
                    </tr>
                </thead>

                <tbody sb-ajax="getratings.php" items = "100" sb-bind="table"></tbody>

            </table>
            <!-- <a href="javascript:void(0)" sb-button="table" class="loadmore">Load more</a> -->

            <table style="display:none;">
                <tbody>
                    <tr sb-error="table">
                        <td colspan="10"><i>Error</i></td>
                    </tr>
                    <tr sb-placeholder="table">
                        <td colspan="10"><i style="text-align:center;">...</i></td>
                    </tr>
                </tbody>
            </table>

            <div class="charts">

                <div>
                    <h1>Game Difficulty/User Difficulty Graph</h1>
                    <canvas id="chartdiffrate"></canvas>
                </div>
                <div>
                    <h1>Game Difficulty/User Difficulty Point Graph</h1>
                    <canvas id="chartdiffratepoint"></canvas>
                </div>
                <div>
                    <h1>Game Difficulty Metric/User Difficulty</h1>
                    <canvas id="chartdiffmetrate"></canvas>
                </div>
                <div>
                    <h1>Game Difficulty/Fun Graph</h1>
                    <canvas id="chartfundiff"></canvas>
                </div>
                <div>
                    <h1>Sigma/User Difficulty</h1>
                    <canvas id="chartsigmadiff"></canvas>
                </div>
                <div>
                    <h1>User Score/Game Difficulty Metric</h1>
                    <canvas id="chartscorediff"></canvas>
                </div>
                <div>
                    <h1>User Score/User Difficulty</h1>
                    <canvas id="chartscoreuserdiff"></canvas>
                </div>
                <div>
                    <h1>Game Sigma/User Score Sigma</h1>
                    <canvas id="chartsigmasigma"></canvas>
                </div>

            </div>
            <br />

            <form style="display:block;overflow:hidden;width:100%;text-align:left;" class="" action="getplayergraphs.php" method="post" sb-form="getdata" sb-ajax="getplayergraphs.php" sb-bind="getdata">
                <div class="" sb-placeholder="getdata" style="height: 1094px;line-height: 1094px;width: 100%;text-align:center;color:gray;background: #f9f9f9;">
                    Loading...
                </div>
                <div class="" sb-error="getdata" style="height: 1094px;line-height: 1094px;width: 100%;text-align:center;color:red;background: #f9f9f9;">
                    Server not responding (maybe choose a better hoster?).
                </div>
            </form>

            <?php
            $stmt = $conn->prepare("SELECT COUNT(distinct user) FROM rating");
            $stmt->bind_result($len);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
             ?>
            <h1>Players (<?= $len ?>)</h1>
            <table>
                <thead>
                    <tr>
                        <th><div>player</div></th>
                        <th><div>played games</div></th>
                        <th><div>μ(fun)</div></th>
                        <th><div>σ(fun)</div></th>
                        <th><div>μ(difficulty)</div></th>
                        <th><div>σ(difficulty)</div></th>
                        <th><div>μ(score)</div></th>
                        <th><div>σ(score)</div></th>
                    </tr>
                </thead>

                <tbody sb-ajax="getplayers.php" items = "100" sb-bind="players"></tbody>

            </table>
        </div>


        <script type="text/javascript">

            function showgameratings(game) {
                sbecky.overlay = "loading..";
                var overlay = sbecky_get("overlay")[0];
                var ratingsdiv = document.getElementsByClassName('ratings')[0];
                overlay.setAttribute('game', game);
                if(overlay.hasAttribute('user'))overlay.removeAttribute('user');
                overlay.style.display = 'block';
                ratingsdiv.style.display = 'none';
                sbecky_load(overlay);
            }

            function showuser(id) {
                sbecky.overlay = "loading..";
                var overlay = sbecky_get("overlay")[0];
                var ratingsdiv = document.getElementsByClassName('ratings')[0];
                overlay.setAttribute('user', id);
                if(overlay.hasAttribute('game'))overlay.removeAttribute('game');
                overlay.style.display = 'block';
                ratingsdiv.style.display = 'none';
                sbecky_load(overlay);
            }

            function closeoverlay() {
                sbecky.overlay = "";
                var overlay = sbecky_get("overlay")[0];
                var ratingsdiv = document.getElementsByClassName('ratings')[0];
                overlay.style.display = 'none';
                ratingsdiv.style.display = 'block';
            }


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

                // var p = a;
                // while(!p.hasAttribute("sb-bind")) p = p.parentNode;
                // table = p;
                var table = sbecky_get("table")[0];
                table.setAttribute('order', w);
                table.innerHTML = "";
                sbecky_load(table);
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

            var ctxdiffrate = document.getElementById("chartdiffrate").getContext('2d');
            var chartdiffrate = new Chart(ctxdiffrate, {
                type: 'line',
                data: {
                    labels: [1,2,3,4,5],
                    datasets: [{
                        label: 'game difficulty/user difficulty',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 80, 90, 0.6)'
                        ],
                        pointRadius: 0,
                        lineTension:0
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                fontSize: 14
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'game difficulty'
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 14
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'user difficulty'
                            }
                        }]
                    },
                    spanGaps: true
                }
            });

            // var chartdiffrate = setupLine("chartdiffrate", [], [1,2,3,4,5], 'game difficulty', 'user difficulty', [1, 5], [0, 1]);

            var chartdiffratepoint = setupScatter("chartdiffratepoint", [], 'game difficulty/user difficulty', [1, 5], [0, 1]);
            var chartdiffmetrate = setupScatter("chartdiffmetrate", [], 'game difficulty metric/user difficulty', [1, 5], [0, 1]);
            var chartfundiff = setupScatter("chartfundiff", [], 'game difficulty/fun', [1, 5], [0, 1]);

            var chartsigmadiff = setupScatter("chartsigmadiff", [], 'sigma/user difficulty', [0.5, 5], [0, 1]);

            var chartscorediff = setupScatter("chartscorediff", [], 'score/game difficulty metric', [1, 1], [0, 0]);
            var chartscoreuserdiff = setupScatter("chartscoreuserdiff", [], 'score/user difficulty', [1, 5], [0, 1]);
            var chartsigmasigma = setupScatter("chartsigmasigma", [], 'game sigma/score sigma', [0.5, 0.5], [0, 0]);

        </script>
    </body>
</html>
