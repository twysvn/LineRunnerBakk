<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Charts</title>
        <link rel="stylesheet" href="style.css">
        <script src="../site/script/sbecky.js" charset="utf-8"></script>
        <script src="Chart.min.js" charset="utf-8"></script>
        <script src="chart.js" charset="utf-8"></script>

        <script src="data/speed.js" charset="utf-8"></script>
        <script src="data/end_position.js" charset="utf-8"></script>
        <script src="data/difficulty_variation.js" charset="utf-8"></script>
        <script src="data/seed.js" charset="utf-8"></script>
        <script src="data/simulationspeed.js" charset="utf-8"></script>
        <script src="data/block_lenght.js" charset="utf-8"></script>
    </head>
    <body>

        <h1>Extra Charts</h1>
        <div class="charts">
            <div>
                <h1>End Position Variation Difficulty</h1>
                <canvas id="endposchartdiff"></canvas>
            </div>
            <div>
                <h1>End Position Variation Difficulty Standard Deviation</h1>
                <canvas id="endposchartsigma"></canvas>
            </div>
            <div>
                <h1>End Position Variation Difficulty Derivation</h1>
                <canvas id="endposchartdiffdiff"></canvas>
            </div>
            <div>
                <h1>End Position Variation Sigma Derivation</h1>
                <canvas id="endposchartsigmadiff"></canvas>
            </div>
            <div>
                <h1>Speed Variation game Difficulty</h1>
                <canvas id="speedchartdifffull"></canvas>
            </div>
            <div>
                <h1>Speed Variation game Difficulty Standard Deviation</h1>
                <canvas id="speedchartsigmafull"></canvas>
            </div>
            <div>
                <h1>Speed Variation game Difficulty</h1>
                <canvas id="speedchartdiff"></canvas>
            </div>
            <div>
                <h1>Speed Variation game Difficulty Standard Deviation</h1>
                <canvas id="speedchartsigma"></canvas>
            </div>
            <div>
                <h1>Difficulty Variation same game</h1>
                <canvas id="diffchartdiff"></canvas>
            </div>
            <div>
                <h1>Sigma Variation same game</h1>
                <canvas id="diffchartsigma"></canvas>
            </div>
            <div>
                <h1>Seed Variation Difficulty</h1>
                <canvas id="seedchartdiff"></canvas>
            </div>
            <div>
                <h1>Seed Variation Sigma</h1>
                <canvas id="seedchartsigma"></canvas>
            </div>
            <div>
                <h1>Simulation Speed Variation Difficulty Per Game</h1>
                <canvas id="simspeedchartdiffpergame"></canvas>
            </div>
            <div>
                <h1>Simulation Speed Variation Sigma Per Game</h1>
                <canvas id="simspeedchartsigmapergame"></canvas>
            </div>
            <div>
                <h1>Simulation Speed Variation Difficulty</h1>
                <canvas id="simspeedchartdiff"></canvas>
            </div>
            <div>
                <h1>Simulation Speed Variation Sigma</h1>
                <canvas id="simspeedchartsigma"></canvas>
            </div>
            <div>
                <h1>Block Length Variation Difficulty</h1>
                <canvas id="blocklenchartdiff"></canvas>
            </div>
            <div>
                <h1>Block Length Variation Sigma</h1>
                <canvas id="blocklenchartsigma"></canvas>
            </div>
            <div>
                <h1>Block Length Variation Difficulty Per Game</h1>
                <canvas id="blocklenchartdiffpergame"></canvas>
            </div>
            <div>
                <h1>Block Length Variation Sigma Per Game</h1>
                <canvas id="blocklenchartsigmapergame"></canvas>
            </div>
        </div>

        <script type="text/javascript">
            function extractData(arr, name) {
                Colors.reset()
                var data = []
                var i = 0;
                for (var game of arr) {
                    var gamedata = [];
                    for (var result of game) {
                        gamedata.push(result[name])
                    }
                    var clr = Colors.next()
                    data.push({
                                backgroundColor: 'rgba(0,0,0,0)',
                                borderColor: clr,
                                pointBackgroundColor: clr,
                                pointBorderColor: 'rgba(255,255,255,0)',
                                data: gamedata
                            })
                }
                return data
            }
            function extractDataAverage(arr, name, showstd = false) {
                var data = []
                var mean = []
                var stddata1 = []
                var stddata2 = []
                for (var game of arr) {
                    for (var i = 0; i < game.length; i++) {
                        if (data[i] == undefined) {
                            data[i] = []
                        }
                        data[i].push(game[i][name])
                    }
                }
                var i = 0
                for (d of data) {
                    var avg = average(d)
                    var std = standardDeviation(d)
                    stddata1[i] = avg-std/2
                    mean[i] = avg
                    stddata2[i] = avg+std/2
                    i++
                }
                if (!showstd) {
                    // for (var i in data) {
                    //     if (data.hasOwnProperty(i)) {
                    //         data[i] /= len
                    //     }
                    // }

                    return [{
                        backgroundColor: 'rgba(0,0,0,0)',
                        borderColor: 'rgba(255, 80, 90, 0.6)',
                        pointBackgroundColor: 'rgba(255, 80, 90, 0.6)',
                        data: mean
                    }]
                }else{
                    // for (var i in data) {
                    //     if (data.hasOwnProperty(i)) {
                    //         data[i] /= len
                    //     }
                    // }

                    return [{
                        backgroundColor: 'rgba(255,255,255,0)',
                        borderColor: 'rgba(255, 80, 90, 0)',
                        pointBackgroundColor: 'rgba(255, 80, 90, 0)',
                        data: stddata1
                    },{
                        backgroundColor: 'rgba(255,255,255,0)',
                        borderColor: 'rgba(255, 80, 90, 0.6)',
                        pointBackgroundColor: 'rgba(255, 80, 90, 0.6)',
                        data: mean
                    },{
                        backgroundColor: 'rgba(255, 80, 90, 0.2)',
                        borderColor: 'rgba(255, 80, 90, 0)',
                        pointBackgroundColor: 'rgba(255, 80, 90, 0)',
                        fill: '-2',
                        data: stddata2
                    }]
                }


                // return [{
                //     backgroundColor: 'rgba(0,0,0,0)',
                //     borderColor: 'rgba(255, 80, 90, 0.6)',
                //     pointBackgroundColor: 'rgba(255, 80, 90, 0.6)',
                //     data: data
                // }]
            }
            function extractDataAverageDiffstd(arr, name) {
                var data = []
                var mean = []
                var stddata1 = []
                var stddata2 = []
                var std1 = []
                var std2 = []

                for (var game of arr) {
                    for (var i = 0; i < game.length; i++) {
                        if (data[i] == undefined) {
                            data[i] = []
                        }
                        if (stddata1[i] == undefined) {
                            stddata1[i] = []
                        }
                        if (stddata2[i] == undefined) {
                            stddata2[i] = []
                        }
                        data[i].push(game[i][name])
                        stddata1[i].push(game[i]["difficulty_standard_deviation"])
                        stddata2[i].push(game[i]["difficulty_standard_deviation"])
                    }
                }
                var i = 0
                for (d of data) {
                    var avg = average(d)
                    mean[i] = avg
                    i++
                }
                var i = 0
                for (d of stddata1) {
                    var avg = average(d)
                    std1[i] = avg + mean[i]
                    i++
                }
                var i = 0
                for (d of stddata2) {
                    var avg = average(d)
                    std2[i] = - avg + mean[i]
                    i++
                }

                return [{
                    backgroundColor: 'rgba(255,255,255,0)',
                    borderColor: 'rgba(255, 80, 90, 0)',
                    pointBackgroundColor: 'rgba(255, 80, 90, 0)',
                    data: std1
                },{
                    backgroundColor: 'rgba(255,255,255,0)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    pointBackgroundColor: 'rgba(255, 80, 90, 0.6)',
                    data: mean
                },{
                    backgroundColor: 'rgba(255, 80, 90, 0.2)',
                    borderColor: 'rgba(255, 80, 90, 0)',
                    pointBackgroundColor: 'rgba(255, 80, 90, 0)',
                    fill: '-2',
                    data: std2
                }]
            }
            function extractDataDiff(arr, name) {
                var data = []
                var data0 = []
                var len = arr.length;
                for (var game of arr) {
                    for (var i = 0; i < game.length; i++) {
                        if (data[i] == undefined) {
                            data[i] = 0;
                        }
                        data[i] += game[i][name]
                    }
                }
                for (var i in data) {
                    if (data.hasOwnProperty(i)) {
                        data[i] /= len
                        data0[i] = 0
                    }
                }
                var last = 0;
                for (var i in data) {
                    if (data.hasOwnProperty(i)) {
                        var tmp = data[i]
                        data[i] = data[i] - last
                        last = tmp
                    }
                }
                return [{
                    backgroundColor: 'rgba(0,0,0,0)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    pointBackgroundColor: 'rgba(255, 80, 90, 0.6)',
                    data: data
                },{
                    backgroundColor: 'rgba(0,0,0,0)',
                    borderColor: 'rgba(0,0,0,0.3)',
                    data: data0,
                    borderWidth: 0.4,
                    pointRadius: 0
                }]
            }
            function extractDataAveragePerGame(arr, name) {
                var data = []
                var upperdata = []
                var lowerdata = []
                var averagedata = []
                var j = 0;
                var averagedata = []
                var stddata1 = []
                var stddata2 = []
                for (var game of arr) {
                    var tmp = [];
                    for (var i = 0; i < game.length; i++) {
                        tmp.push(game[i][name])
                    }

                    var std = standardDeviation(tmp);
                    var avg = average(tmp);
                    averagedata.push(avg)
                    stddata1.push(std)
                    // data[j] = avg;
                    // upperdata[j] = avg + std/2
                    // lowerdata[j] = avg + std/2
                    data[j] = std/2;
                    upperdata[j] = std/2
                    lowerdata[j] = avg - std/2
                    j++
                }
                var tmpavg = average(averagedata)
                var tmpstd = average(stddata1)
                for (var i = 0; i < averagedata.length; i++) {
                    stddata1[i] = tmpavg - tmpstd/2
                    averagedata[i] = tmpstd/2;
                    stddata2[i] = tmpstd/2
                }
                return [{
                    backgroundColor: 'rgba(255, 80, 90, 0.2)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    data: lowerdata
                },{
                    backgroundColor: 'rgba(255, 80, 90, 0.6)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    data: data
                },{
                    backgroundColor: 'rgba(255, 80, 90, 0.8)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    data: upperdata
                },{
                    type: "line",
                    backgroundColor: 'rgba(255, 80, 90, 0)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    data: stddata1,
                    pointRadius: 0,
                    borderWidth: 1,
                    pointHoverRadius: 0
                },{
                    type: "line",
                    backgroundColor: 'rgba(255, 80, 90, 0)',
                    borderColor: 'rgba(255, 80, 90, 1)',
                    data: averagedata,
                    pointRadius: 0,
                    borderWidth: 1,
                    pointHoverRadius: 0
                },{
                    type: "line",
                    backgroundColor: 'rgba(255, 80, 90, 0)',
                    borderColor: 'rgba(255, 80, 90, 0.6)',
                    data: stddata2,
                    pointRadius: 0,
                    borderWidth: 1,
                    pointHoverRadius: 0
                }]
            }
            function extractDataRow(arr, name) {
                for (var game of arr) {
                    var gamedata = [];
                    for (var result of game) {
                        gamedata.push(result[name].toFixed(1))
                    }
                    return gamedata;
                }
            }
            function extractDataCol(arr, name) {
                var res = []
                var i = 0;
                for (var game of arr) {
                    res.push(i++)
                }
                return res
            }
            function standardDeviation(values){
              var avg = average(values);

              var squareDiffs = values.map(function(value){
                var diff = value - avg;
                var sqrDiff = diff * diff;
                return sqrDiff;
              });

              var avgSquareDiff = average(squareDiffs);

              var stdDev = Math.sqrt(avgSquareDiff);
              return stdDev;
            }

            function average(data){
              var sum = data.reduce(function(sum, value){
                return sum + value;
              }, 0);

              var avg = sum / data.length;
              return avg;
            }
            // function range(start, i){return i?range(start, i-1).concat(i + start):[]}

            function range(start, end) {
                var ret = [];
                for (var i = 0; i < 15; i++) {
                    ret.push(((end-start+1)/15) * i + start)
                }
                return ret
            }

            //END POSITION
            var dataendposdiff = extractDataAverageDiffstd(end_pos_data, "difficulty")
            var dataendpossigma = extractDataAverage(end_pos_data, "difficulty_standard_deviation")
            var lbl = extractDataRow(end_pos_data, "value")

            setupLine("endposchartdiff", dataendposdiff, lbl, 'difficulty', 'end position', [1, 60], [0, 10]);
            setupLine("endposchartsigma", dataendpossigma, lbl, 'sigma', 'end position', [0.5, 60], [0, 10]);

            //END POSITION (derivation)
            var dataendposdiffdiff = extractDataDiff(end_pos_data, "difficulty")
            var dataendpossigmadiff = extractDataDiff(end_pos_data, "difficulty_standard_deviation")
            var lbl = extractDataRow(end_pos_data, "value")

            setupLine("endposchartdiffdiff", dataendposdiffdiff, lbl, 'difficulty', 'end position', [-0.2, 60], [0.2, 10]);
            setupLine("endposchartsigmadiff", dataendpossigmadiff, lbl, 'sigma', 'end position', [-0.2, 60], [0.2, 10]);


            //SPEED
            var dataspeeddifffull = extractDataAverageDiffstd(speed_data_full, "difficulty")
            var dataspeedsigmafull = extractDataAverage(speed_data_full, "difficulty_standard_deviation")
            var lblspeedfull = extractDataRow(speed_data_full, "value")

            setupLine("speedchartdifffull", dataspeeddifffull, lblspeedfull, 'difficulty avg/std', 'speed', [1, NaN], [0, NaN], true);
            setupLine("speedchartsigmafull", dataspeedsigmafull, lblspeedfull, 'sigma avg/std', 'speed', [0.5, NaN], [0, NaN], true);

            //SPEED PER GAME
            var dataspeeddiff = extractData(speed_data, "difficulty")
            var dataspeedsigma = extractData(speed_data, "difficulty_standard_deviation")
            var lblspeed = extractDataRow(speed_data, "value")

            setupLine("speedchartdiff", dataspeeddiff, lblspeed, 'difficulty', 'speed', [1, 19], [0, 5]);
            setupLine("speedchartsigma", dataspeedsigma, lblspeed, 'sigma', 'speed', [0.5, 19], [0, 5]);


            //DIFFICULTY VARIATION
            var diffdiff = extractDataAveragePerGame(difficulty_variation_data, "difficulty")
            var diffsigma = extractDataAveragePerGame(difficulty_variation_data, "difficulty_standard_deviation")
            var lbldiff = extractDataCol(difficulty_variation_data, "value")

            setupBar("diffchartdiff", diffdiff, lbldiff, 'difficulty avg/std', 'game', [1, NaN], [0, NaN], true);
            setupBar("diffchartsigma", diffsigma, lbldiff, 'sigma avg/std', 'game', [0.5, NaN], [0, NaN], true);


            //SEED
            var seeddiff = extractDataAveragePerGame(seed_data, "difficulty")
            var seedsigma = extractDataAveragePerGame(seed_data, "difficulty_standard_deviation")
            var lblseed = extractDataCol(seed_data, "value")

            setupBar("seedchartdiff", seeddiff, lblseed, 'difficulty avg/std', 'game', [1, NaN], [0, NaN], true);
            setupBar("seedchartsigma", seedsigma, lblseed, 'sigma avg/std', 'game', [0.5, NaN], [0, NaN], true);


            //SIMULATION SPEED PER GAME
            var simspeeddiffpergame = extractDataAveragePerGame(simulation_speed_data, "difficulty")
            var simspeedsigmapergame = extractDataAveragePerGame(simulation_speed_data, "difficulty_standard_deviation")
            var lblsimspeedpergame = extractDataCol(simulation_speed_data, "value")

            setupBar("simspeedchartdiffpergame", simspeeddiffpergame, lblsimspeedpergame, 'difficulty avg/std', 'game', [1, NaN], [0, NaN], true);
            setupBar("simspeedchartsigmapergame", simspeedsigmapergame, lblsimspeedpergame, 'sigma avg/std', 'game', [0.5, NaN], [0, NaN], true);


            //SIMULATION SPEED
            var simspeeddiff = extractDataAverageDiffstd(simulation_speed_data, "difficulty")
            var simspeedsigma = extractDataAverage(simulation_speed_data, "difficulty_standard_deviation")
            var lblsimspeed = extractDataRow(simulation_speed_data, "value")

            setupLine("simspeedchartdiff", simspeeddiff, lblsimspeed, 'difficulty avg/std', 'simulation speed', [1, NaN], [0, NaN], true);
            setupLine("simspeedchartsigma", simspeedsigma, lblsimspeed, 'sigma avg/std', 'simulation speed', [0.5, NaN], [0, NaN], true);


            //BLOCK LENGTH
            var blocklendiff = extractDataAverageDiffstd(block_length_data, "difficulty")
            var blocklensigma = extractDataAverage(block_length_data, "difficulty_standard_deviation")
            var lblblocklen = extractDataRow(block_length_data, "value")

            setupLine("blocklenchartdiff", blocklendiff, lblblocklen, 'difficulty avg/std', 'block length', [1, NaN], [0, NaN], true);
            setupLine("blocklenchartsigma", blocklensigma, lblblocklen, 'sigma avg/std', 'block length', [0.5, NaN], [0, NaN], true);


            //BLOCK LENGTH PER GAME

            var blocklendiffpergame = extractData(block_length_data_full, "difficulty")
            var blocklensigmapergame = extractData(block_length_data_full, "difficulty_standard_deviation")
            var lblblocklenpergame = extractDataRow(block_length_data_full, "value")

            setupLine("blocklenchartdiffpergame", blocklendiffpergame, lblblocklenpergame, 'difficulty', 'block length', [1, 19], [0, 5]);
            setupLine("blocklenchartsigmapergame", blocklensigmapergame, lblblocklenpergame, 'sigma', 'block length', [0.5, 19], [0, 5]);


        </script>

    </body>
</html>
