async function load_data_for_chart() {
    var indname = $('#indicator-list').val();
    baseyear = $('#baseyear').val();
    yeardata = $('#year').val();
    var id_ind = [16, 17, 18, 19];
    console.log(indname);
    console.log(id_ind.includes(indname));
    if (id_ind.includes(Number(indname))) {
        checkbox = document.getElementById('soil_check').checked;
        console.log(indname);
    } else { checkbox = false; }
    var lslen = localStorage.length;
    var listselectregion = '';
    if (lslen > 0) {
        for (var i = 0; i < lslen; i++) {
            var key = localStorage.key(i);
            listselectregion = listselectregion + localStorage.getItem(key) + ',';
        }
    }
    listselectregion = listselectregion.substring(0, listselectregion.length - 1)
    dataquery = { 'indname': indname, 'yeardata': yeardata, 'listselectregion': listselectregion, 'soil': checkbox };
    const response = await fetch('chart_map_ajax_data.php', {
        method: 'post',
        body: JSON.stringify({
            indname: indname,
            yeardata: yeardata,
            baseyear: baseyear,
            listselectregion: listselectregion,
            soil: checkbox,
            type: "line-chart"
        }),
        headers: {
            'content-type': 'application/json'
        }
    });
	console.log(dataquery);
    const data = await response.json();
    return data

}

$(async function() {
    // the button handler
    var lslen = localStorage.length;
    // if (lslen > 0) {
    var datachart = await load_data_for_chart();
    console.log(datachart);
    console.log(datachart['alldata']);
    indname = $('#indicator-list').name;
    // } else {
    //     datachart = [];
    //     datachart['name_ind'] = "";
    //     datachart['unit_ind'] = "";
    //     datachart['years'] = [null];
    //     datachart['years'] = [null];
    //     datachart['alldata'] = [{ name: "Нет выбранных регионов", data: [null] }]
    //         // window.location.href = "#error"
    // }


    $('#line-export-button').click(async function() {
        var lslen = localStorage.length;
        if (lslen > 0) {
            var data_export = await load_data_for_chart();
            const data1 = { username: 'example' };
            console.log(data_export);
            $.ajax({
                url: "exportline.php",
                method: "post",
                data: {
                    alldata: JSON.stringify(data_export),
                },
                success: function(data) {
                    $('#exp').html(data);
                }
            });
            window.location.href = "#"
        } else {
            window.location.href = "#error"
        }

    });
    var $button = $('#polar-button');

    $button.click(async function() {
        var lslen = localStorage.length;
        if (lslen > 0) {
            chart.showLoading();
            var datachart = await load_data_for_chart();
            console.log(datachart);
            chart.hideLoading();
            chart.update({
                yAxis: {
                    title: {
                        text: datachart['namechart']
                    }
                },
                tooltip: {
					valueSuffix: ' '+datachart['unit_ind']
				},
                title: {
                    text: datachart['name_ind']
                },
                subtitle: {
                    text: datachart['unit_ind']
                },
                xAxis: {
                    categories: datachart['years'],
                    labels: {
                        style: {
                            fontFamily: 'Verdana, sans-serif'
                        }
                    },
                    tickmarkPlacement: 'on'
                },
                series: datachart['alldata']
            }, true, true);
            window.location.href = "#"
        } else {
            window.location.href = "#error"
        }
    });


    // create the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'line-diagram',
            // type: "spline"
        },
        //redraw: colorPoints,
        credits: {
            enabled: false
        },
        colors: ['#FF0000', '#00FF7F', '#FF4500', '#1E90FF', '#8A2BE2', '#778899', '#DC143C', '#00FF00', '#00FFFF'],
        title: {
            text: datachart['name_ind']
        },
        subtitle: {
            text: datachart['unit_ind']
        },

        yAxis: {
            title: {
                text: datachart['namechart']
            }
        },
        tooltip: {
            valueSuffix: ' '+datachart['unit_ind']
        },
        xAxis: {
            categories: datachart['years'],
        },
        series: datachart['alldata']
    });
});