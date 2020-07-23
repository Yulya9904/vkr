function selectregion(id) {
    var checkbox = document.getElementById(id);
    if (checkbox.checked) {
        // localStorage.removeItem(id);
        localStorage.setItem(id, id);
    } else {
        localStorage.removeItem(id);
    }
}

function selectindicator() {
    var select = Number(document.getElementById("indicator-list").value);
    console.log(select);
	var id_ind = [14,15, 17, 18, 19, 20];
    // console.log(id_ind.includes(select));
    if (!id_ind.includes(select)) {
      //if (select in [16, 17, 18, 19]) {
      $('#h6baseyear').html('Начало периода (год)<span class="form-required red-star">*</span>');
    } else {
      $("#h6baseyear").html('Начало периода (базовый год):<span class="form-required red-star">*</span>');
    }
    var id_ind = [16, 17, 18, 19];
    var element=document.getElementById('soil');
	if(element){
    if (!id_ind.includes(select)) {
        //if (select in [16, 17, 18, 19]) {
        document.getElementById('soil').style.display = 'none';
    } else {
        document.getElementById('soil').style.display = 'block';
    }
	}
}



function listregion() {
    var lslen = localStorage.length;
    var listselectregion = [];
    if (lslen > 0) {
        for (var i = 0; i < lslen; i++) {
            var key = localStorage.key(i);
            listselectregion.push([
                localStorage.getItem(key)
            ]);
            console.log(localStorage.getItem(key));
        }
    }
    return (listselectregion);
}


function selectbaseyear() {
    var baseyear = document.getElementById("baseyear");
    var yearend = $('#baseyear option:last').val();

    baseyear = Number(baseyear.value);
    // alert(baseyear);
    // alert(Number(yearend) + Number(baseyear));
    year.innerHTML = "";
    for (i = baseyear; i <= yearend; i++) {
        o = new Option(i, i, false, true);
        year.append(o);
    };
}

$(document).ready(function() {
    load_data();

    function load_data(query) {
        var lslen = localStorage.length;
        var listselectregion = '';
        if (lslen > 0) {
            for (var i = 0; i < lslen; i++) {
                var key = localStorage.key(i);
                listselectregion = listselectregion + localStorage.getItem(key) + ',';
            }
        }
        checkbox = document.getElementById('select_reg').checked;
        console.log(checkbox);
        listselectregion = listselectregion.substring(0, listselectregion.length - 1)
        $.ajax({
            url: "fetch.php",
            method: "post",
            data: {
                query: query,
                listselectregion: listselectregion,
                select_reg: checkbox,
            },
            success: function(data) {
                $('#result').html(data);
            }
        });
    }
    // $('#polar-export-button').click(function() {
    //     load_data();
    // })
    $('#search_text').keyup(function() {
        var search = $(this).val();
        if (search != '') {
            load_data(search);
        } else {
            load_data();
        }
    });
    $("#select_reg").on("click", function() {
        load_data();
        $('#search_text').val("")
    })

    $("#all_reg").on("click", function() {
        if ($(this).is(":checked")) {
            $('#sortable input:checkbox').prop('checked', true);
            // $('#select_reg input:checkbox').prop('checked', false);
            $('#sortable input:checkbox:checked').each(function() {
                selectregion($(this).val());
            });
            load_data();
        } else {
            localStorage.clear();
            load_data();
        }
    })
});