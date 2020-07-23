async function load_data_for_chart() {
    var indname = $('#indicator-list').val();
    baseyear = $('#baseyear').val();
    yeardata = $('#year').val();
    checkbox = document.getElementById('soil_check').checked;
    dataquery = { 'indname': indname, 'yeardata': yeardata, 'listselectregion': "all" };
    const response = await fetch('chart_map_ajax_data.php', {
        method: 'post',
        body: JSON.stringify({ indname: indname, yeardata: yeardata, baseyear: baseyear, type: "map", 'soil': checkbox }),
        headers: {
            'content-type': 'application/json'
        }
    });
    console.log(dataquery);
    const data = await response.json();
    return data

}

function mapredrawing(datachart) {
    count_int = document.getElementById("count_int").value;
    as = Math.round(((datachart['max'] - datachart['min']) / count_int) * 100) / 100;
    var data = [];
    alldata = [];
    res = 0;
    colormap = [];
    for (i = 0; i < count_int; i++) {
		data[i] = []
		if (datachart['error']==false){
        left_interval_bound = datachart['min'] + res - 0.01;
        res = res + as;
        colormap.push(document.getElementById(i).value)
        $('#' + i + 'left').val(left_interval_bound.toFixed(2));
        // document.getElementById(i + 'left').value = String(interval_bound);
        if ((Number(count_int) - 1) == i) {
            right_interval_bound = (datachart['max']);
            document.getElementById(i + 'right').value = right_interval_bound.toFixed(2);
        } else {
            right_interval_bound = left_interval_bound + as - 0.01;
            document.getElementById(i + 'right').value = right_interval_bound.toFixed(2);
        }
		}
        console.log(right_interval_bound);
        name = String(left_interval_bound.toFixed(2)) + ' - ' + String(right_interval_bound.toFixed(2));
        for (j = 0; j < datachart['alldata'].length; j++) {
			if (datachart['error']==false){
            if (left_interval_bound <= datachart['alldata'][j][1] &&
                datachart['alldata'][j][1] <= right_interval_bound + 0.01) {
                data[i].push({
                    "hc-key": datachart['alldata'][j][0],
                    "value": datachart['alldata'][j][1]
                })
            }
			}
            if ((datachart['alldata'][j][1] === null) && (i == 0)) {
                data[i].push({
                    "hc-key": datachart['alldata'][j][0],
                    "value": datachart['alldata'][j][1]
                })
            }

        }
        alldata.push({ "name": name, "data": data[i] });

    }
    console.log(alldata)
    return alldata;
}

$(async function() {
    // $('.float').numberMask({
    //     type: 'float',
    //     afterPoint: 2,
    //     defaultValueInput: "0,00",
    //     decimalMark: ','
    // });
    $('#list_interval').on('change', '.float', function(e) {
            var data = [];
            alldata = [];
            colormap = [];

            count_int = document.getElementById("count_int").value;
            for (i = 0; i < Number(count_int); i++) {
                data[i] = [];
                colormap.push(document.getElementById(i).value)
                right_interval_bound = Number(document.getElementById(i + "right").value);
                left_interval_bound = Number(document.getElementById(i + "left").value);
                name = String(left_interval_bound.toFixed(2)) + ' - ' + String(right_interval_bound.toFixed(2));
                for (j = 0; j < datachart['alldata'].length; j++) {
                    if (left_interval_bound <= datachart['alldata'][j][1] &&
                        datachart['alldata'][j][1] <= right_interval_bound + 0.01) {
                        data[i].push({
                            "hc-key": datachart['alldata'][j][0],
                            "value": datachart['alldata'][j][1]
                        })
                    }
                    if ((datachart['alldata'][j][1] === null) && (i == 0)) {
                        data[i].push({
                            "hc-key": datachart['alldata'][j][0],
                            "value": datachart['alldata'][j][1]
                        })
                    }

                }
                alldata.push({ "name": name, "data": data[i] });
            }
            chart.update({
                colors: colormap,
                series: alldata,
            }, true, true);
            console.log(count_int);
        }

    ).numberMask({
        type: 'float',
        afterPoint: 2,
        defaultValueInput: '',
        decimalMark: '.'
    });
    datachart = await load_data_for_chart();
    alldata = mapredrawing(datachart);
    console.log(alldata);
    indname = $('#indicator-list').name;
    // $('#list_interval').on('change', '.float', function(e) {
    //     colormap = [];
    //     count_int = document.getElementById("count_int").value;
    //     for (i = 0; i < count_int; i++) {
    //         colormap.push(document.getElementById(i).value)
    //     }
    //     chart.update({
    //         colors: colormap,
    //     }, true, true);
    //     console.log(count_int);
    // });
    $('#list_interval').on('change', '.inp_my_color', function(e) {
        colormap = [];
        count_int = document.getElementById("count_int").value;
        for (i = 0; i < count_int; i++) {
            colormap.push(document.getElementById(i).value)
        }
        chart.update({
            colors: colormap,
        }, true, true);
        console.log(count_int);
    });

    // $('#list_interval').on('change', '.float',);
    // $(".inp_my_color").change(function() {
    //     colormap = [];
    //     count_int = document.getElementById("count_int").value;
    //     for (i = 0; i < count_int; i++) {
    //         colormap.push(document.getElementById(i).value)
    //     }
    //     chart.update({
    //         colors: colormap,
    //     }, true, true);
    //     console.log(count_int);
    // });
    $("#count_int").change(function() {
        colormap = [];
        alldata = mapredrawing(datachart);
        count_int = document.getElementById("count_int").value;
        for (i = 0; i < Number(count_int); i++) {
            colormap.push(document.getElementById(i).value)
        }
        chart.update({
            colors: colormap,
            series: alldata,
        }, true, true);
        console.log($('#indicator-list').val());
    });


    var $button = $('#map-button');
    $button.click(async function() {
        chart.showLoading();
        datachart = await load_data_for_chart();
        alldata = mapredrawing(datachart);
        console.log(datachart);
        chart.hideLoading();
        chart.update({
            colors: colormap,
            title: {
                text: datachart['namechart']
            },
			subtitle: {
                    text: datachart['year']+' г.'
                },
            series: alldata,
        }, true, true);
    });


    // create the chart
    var chart = new Highcharts.mapChart({
        chart: {
            // style: {
            //     fontFamily: 'Latin Modern',
            //     lineWidth: 1,
            //     lineColor: '#000',
            //     fontWeight: "lighter",
            //     Color: "#150d0d",
            //     fill: "#983838"
            // },
            renderTo: 'container',
            map: 'countries/ru/custom/ru-all-disputed'
        },
        plotOptions: {
            series: {
                allAreas: false,
                // mapData: Highcharts.maps['countries/ar/ar-all'],
                //joinBy: 'hc-key',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        colors: colormap,
        credits: {
            enabled: false,
        },

        title: {
                text: datachart['namechart']
            },
			subtitle: {
                    text: datachart['year']+' г.'
                },

        mapNavigation: {
            enabled: true,
            buttonOptions: {
                verticalAlign: 'bottom'
            }
        },


        series: alldata,
        // states: {
        //     hover: {
        //         color: ['#BADA55', ]
        //     }
        // },
        // dataLabels: {
        //     enabled: true,
        //     format: '{point.name}'
        // }

    });
});