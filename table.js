async function load_data_for_chart() {
    var indname = $('#indicator-list').val();
        id_ind = [16, 17, 18, 19];
    baseyear = $('#baseyear').val();
    yeardata = $('#year').val();
	
    if (id_ind.includes(Number(indname))) {
        checkbox = document.getElementById('soil_check').checked;
        console.log(indname);
    } else { checkbox = false; }
    dataquery = {
        'indname': indname,
        'yeardata': yeardata,
        'soil': checkbox
    };
    const response = await fetch('chart_map_ajax_data.php', {
        method: 'post',
        body: JSON.stringify({
            indname: indname,
            yeardata: yeardata,
            baseyear: baseyear,
            soil: checkbox,
            type: "line-chart"
        }),
        headers: {
            'content-type': 'application/json'
        }
    });
    //console.log(response.json());
    const data = await response.json();
    return data;

}

function createtable(data_table) {
    lang = {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
            "first": "Первая",
            "previous": "Предыдущая",
            "next": "Следующая",
            "last": "Последняя"
        },
        "aria": {
            "sortAscending": ": активировать для сортировки столбца по возрастанию",
            "sortDescending": ": активировать для сортировки столбца по убыванию"
        }
    };
    $("#title").html('<h4>' + data_table['name_ind'] + ', ' + data_table['unit_ind'] + '</h4>');
    $('#table').empty(); // empty in case the columns change
    // ... skipped ...
    headtable = [{
        data: 'reg',
        title: "Регионы"
    }]
    for (i = 0; i < data_table['years'].length; i++) {
        headtable.push({
            data: '' + data_table['years'][i] + '',
            title: '' + data_table['years'][i] + ''
        })
    }
    alldata = []
    for (i = 0; i < data_table['alldata'].length; i++) {
        bodytable = [];
        bodytable['reg'] = data_table['alldata'][i]['name'];
        for (j = 0; j < data_table['years'].length; j++) {
            // if (data_table['alldata'][i]['data'][j] !== null) {
            bodytable['' + data_table['years'][j] + ''] = data_table['alldata'][i]['data'][j];
            // }
        }
        alldata.push(bodytable);
    }

    console.log(alldata);
    $('#table').DataTable({
        columns: headtable,
        data: alldata,
        language: lang,
        scrollX: true,
        //     data: data_table['years']
    });
    // $("#table").html('<tr>');
    // headtable = '<th>&nbsp;&nbsp;Регионы &nbsp;&nbsp;';
    // for (i = 0; i < data_table['years'].length; i++) {
    //     headtable = headtable + '</th><th>&nbsp;&nbsp;' + data_table['years'][i] + '&nbsp;&nbsp;';
    // }
    // $("#table").append('<thead><tr>' + headtable + '</th></tr></thead>')
    // alldatatable = '';
    // for (i = 0; i < data_table['alldata'].length; i++) {
    //     strtable = '<td>' + data_table['alldata'][i]['name'];
    //     for (j = 0; j < data_table['years'].length; j++) {
    //         if (data_table['alldata'][i]['data'][j] == null) {
    //             strtable = strtable + '</td><td>&nbsp;';
    //         } else {
    //             strtable = strtable + '</td><td>&nbsp;&nbsp;' + data_table['alldata'][i]['data'][j] + '&nbsp;&nbsp;';
    //         }
    //     }
    //     alldatatable = alldatatable + '<tr>' + strtable + '</td></tr>';
    // }
    // $("#table").append('<tbody>' + alldatatable + '</tbody>')
}

$(async function() {

    // the button handler

    var data_table = await load_data_for_chart();
    console.log(data_table);
    createtable(data_table);
    // $('#table').DataTable({ language: lang });
    $button = $('#table-export-button');
    $('#table-export-button').click(async function() {
        var data_export = await load_data_for_chart();
        const data1 = { username: 'example' };
        console.log(data_export);
        $.ajax({
            url: "exporttable.php",
            method: "post",
            data: {
                alldata: JSON.stringify(data_export),
            },
            success: function(data) {
                $('#exp').html(data);
            }
        });
    });

    $('#table-button').click(async function() {
        // chart.showLoading();
        var data_table = await load_data_for_chart();
        console.log(data_table);
        $('#table').DataTable().destroy();
        createtable(data_table);
    });


});

selectindicator();

function selectindicator() {
    var select = Number(document.getElementById("indicator-list").value);
    console.log(select);
    var id_ind = [16, 17, 18, 19];
    console.log(id_ind.includes(select));
    if (!id_ind.includes(select)) {
        //if (select in [16, 17, 18, 19]) {

        document.getElementById('soil').style.display = 'none';
    } else {
        document.getElementById('soil').style.display = 'block';
    }
}