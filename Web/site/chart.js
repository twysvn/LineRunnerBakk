
function setupScatter(id, data, label, max, min) {
    var ctx = document.getElementById(id).getContext('2d');
    return Chart.Scatter(ctx, {
        data: {
            datasets: [{
                label: label,
                data: data,
                backgroundColor: [
                    'rgba(255, 80, 90, 0.6)'
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[0],
                        max: max[0]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label.split("/")[0]
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[1],
                        max: max[1]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label.split("/")[1]
                    }
                }]
            }
        }
    });
}

function setupBubble(id, data, label, max, min) {
    var ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bubble',
        data: {
          labels: label,
          datasets: data,
          backgroundColor: [
              'rgba(255, 80, 90, 0.4)'
          ]
        },
        options: {
            legend: false,
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[0],
                        max: max[0]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label.split("/")[0]
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[1],
                        max: max[1]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label.split("/")[1]
                    }
                }]
            }
        }
    });
}

function setupLine(id, data, label, label1, label2, max, min) {
    var ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
          labels: label,
          datasets: data,
          backgroundColor: [
              'rgba(255, 80, 90, 0.6)'
          ]
        },
        options: {
            legend: false,
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[0],
                        max: max[0]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label1
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[1],
                        max: max[1]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label2
                    }
                }]
            }
        }
    });
}

function setupBar(id, data, label, label1, label2, max, min, stacked = false) {
    var ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
          labels: label,
          datasets: data
        },
        options: {
            legend: false,
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[0],
                        max: max[0]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label1
                    },
                    stacked: stacked
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 14,
                        min: min[1],
                        max: max[1]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: label2
                    },
                    stacked: stacked
                }]
            }
        }
    });
}

function updateChart(chart, data) {

    // data = data[0];
    //
    // var label = [];
    // var rdata = [];
    // for (var key in data) {
    //     if (data.hasOwnProperty(key)) {
    //         // if (!data[key]['x']) {
    //         //     label.push(key);
    //         // }
    //         if (rdata[key]) {
    //             rdata.push(data[key]);
    //         }else{
    //             rdata[key] = data[key];
    //         }
    //     }
    // }
    //
    // var rrdata = [];
    //
    // for (var i = 1; i < 6; i++) {
    //     if (!rdata[i]) {
    //         rdata[i] = null;
    //     }
    // }
    //
    // for (var i in rdata) {
    //     if (rdata.hasOwnProperty(i)) {
    //         rrdata.push(rdata[i]);
    //     }
    // }

    // window[name].data.labels = [1,2,3,4,5];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = data;
    });
    chart.update();
}

Colors = {};
Colors.names = {
    beekeeper: "#f6e58d",
    pinkglamour: "#ff7979",
    junebud: "#badc58",
    exodusfruit: "#686de0",
    pureapple: "#6ab04c",
    carminepink: "#eb4d4b",
    heliotrope: "#e056fd",
    deepkoamaru: "#30336b",
    spicednectarine: "#ffbe76",
    greenlandgreen: "#22a6b3",
    wizardgrey: "#535c68",
    blurple: "#4834d4",
    aqua: "#00ffff",
    beige: "#f5f5dc",
    black: "#000000",
    blue: "#0000ff",
    brown: "#a52a2a",
    darkblue: "#00008b",
    darkcyan: "#008b8b",
    darkgreen: "#006400",
    darkkhaki: "#bdb76b",
    darkmagenta: "#8b008b",
    darkolivegreen: "#556b2f",
    darkorange: "#ff8c00",
    darkorchid: "#9932cc",
    darkred: "#8b0000",
    darksalmon: "#e9967a",
    darkviolet: "#9400d3",
    fuchsia: "#ff00ff",
    gold: "#ffd700",
    green: "#008000",
    indigo: "#4b0082",
    lightblue: "#add8e6",
    lightgreen: "#90ee90",
    lime: "#00ff00",
    magenta: "#ff00ff",
    maroon: "#800000",
    navy: "#000080",
    olive: "#808000",
    orange: "#ffa500",
    pink: "#ffc0cb",
    purple: "#800080",
    red: "#ff0000"
};
Colors.counter = 0;

Colors.random = function(c) {
    var result;
    var count = 0;
    for (var prop in this.names)
        if (Math.random() < 1/++count)
           result = prop;
    return result;
};
Colors.next = function() {
    var count = 0;
    for (var prop in this.names)
        if (count++ >= Colors.counter) {
            Colors.counter = count
            return this.names[prop]
        }
};
Colors.reset = function() {
    Colors.counter = 0
};
