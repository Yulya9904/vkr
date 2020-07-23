<?php
include 'lib.php';
?>
<?php
include 'header.php';
include 'database.php';
?>

<body>
  <!-- Page Content -->
  <div class="container">
    <h3 class="my-4">Распределение показателя по регионам</h3>
    <hr>
    <!-- Marketing Icons Section -->
    <div class="row">
      <div class="col-lg-3 mb-4">
        <div class="card h-100">
          <!-- <h4 class="card-header">Card Title</h4> -->
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#description">Построение</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#characteristics">Настройка</a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade show active" id="description">
                <div class="divlist" style='margin-top:10px;'>
                  <h6>Выберите показатель:<span class="form-required red-star">*</span>
                    <h6>
                      <div class="divlist">
                        <select id="indicator-list" onChange="selectindicator()" class="form-control custom-select" required>
                          <?php
                          $link = mysqli_connect($hostname, $username, $password, $dbName);
                          if (!$link) {
                            echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
                          } else {
                            $sql = "SELECT id_ind,name_ind FROM `indicator` where id_ind between 15 and 21 ORDER BY id_ind DESC";
                            $zap = mysqli_query($link, $sql) or die(mysqli_error($link));
                            $result = mysqli_query($link, $sql);
                            $nd = 0;

                            while ($list_type = mysqli_fetch_assoc($result)) {
                              $id = "{$list_type['id_ind']}";
                              echo "<option value=\"{$list_type['id_ind']}\">{$list_type['name_ind']}</option>";
                              echo "{$list_type['id_ind']}";
                            }
                          }
                          ?>
                        </select>
                        <div id="all">
                          <div id="soil">
                            <label class="list-region">
                              <input type="checkbox" id="soil_check" />
                              <span class="pseudocheckbox">С учетом состояния почвы</span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div id='basy'>
                        <h6 id="h6baseyear">Базовый год:<span class="form-required red-star">*</span>
                          <h6>
                            <div class="divlist">
                              <select id="baseyear" class="form-control custom-select" required>
                                <?php
                                $link = mysqli_connect($hostname, $username, $password, $dbName);
                                if (!$link) {
                                  echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
                                } else {
                                  $sql = "SELECT distinct(year)  FROM `dataindicator`  ORDER BY year ASC";
                                  $zap = mysqli_query($link, $sql) or die(mysqli_error($link));
                                  $result = mysqli_query($link, $sql);
                                  $nd = 0;

                                  while ($year = mysqli_fetch_assoc($result)) {
                                    echo "<option value=\"{$year['year']}\"";
                                    echo ">{$year['year']}</option>";
                                    echo "{$year['year']}";
                                  }
                                }
                                ?>
                              </select>
                            </div>
                            <hr>
                      </div>
                      <h6 id='headyearend'>Год:<span class="form-required red-star">*</span>
                      </h6>
                      <div class="divlist">
                        <select id="year" class="form-control custom-select" required>
                          <?php
                          $link = mysqli_connect($hostname, $username, $password, $dbName);
                          if (!$link) {
                            echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
                          } else {
                            $sql = "SELECT distinct(year)  FROM `dataindicator`  ORDER BY year ASC";
                            $zap = mysqli_query($link, $sql) or die(mysqli_error($link));
                            $result = mysqli_query($link, $sql);
                            $nd = 0;

                            while ($year = mysqli_fetch_assoc($result)) {
                              echo "<option value=\"{$year['year']}\"";
                              if ($_POST['select-numerator'] == "{$list_type['id_ind']}") {
                                echo "selected";
                              }
                              echo ">{$year['year']}</option>";
                              echo "{$year['year']}";
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <!-- <hr> -->
                      <button class="btn btn-primary" id="map-button">Построить карту
                      </button>
                </div>
              </div>

              <div class="tab-pane fade" id="characteristics">
                <!-- <div class="alert alert-primary" role="alert" style='top: 5px;padding: 7px;'>
                  <span class="mb-0" style='font-size:12px;'>Настройку интервалов необходимо осуществлять с нижних интервалов, т.к.
                    возможно установить значение не больше следующего и не меньше предудущего!
                  </span>
                </div> -->
                <div class="divlist" style='margin-top:10px;'>
                  <h6>Количество интервалов</h6>
                  <div class="divlist">

                    <select id="count_int" onchange=count_interval() class="form-control custom-select" required>
                      <?php
                      for ($i = 2; $i < 8; $i++) {
                        echo "<option value=\"{$i}\">{$i}</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <!-- <div class="form-group" id='list_interval'> -->
                  <div class="divlist" style='margin-top:5px;'>
                    <div class="form-row" id='list_interval'>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div>

            <!-- </div>
        </div> -->
          </div>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="card h-100">
          <!-- <h2>Карта регионов России</h2> -->
          <div class="card-body" style="padding:10px;">
            <div id="container"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../map/mapscript.js"></script>
  <br />
  <?php
  include 'footer.php';
  ?>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="./js/all.js"></script>
<!-- <script src="./js/form_map_chart.js"></script> -->
<script>
  selectindicator();

  function selectindicator() {
    var select = Number(document.getElementById("indicator-list").value);
    console.log(select);
    var id_ind = [16, 18, 19];
    console.log(id_ind.includes(select));
    if (!id_ind.includes(select)) {
      //if (select in [16, 17, 18, 19]) {

      document.getElementById('soil').style.display = 'none';
    } else {
      document.getElementById('soil').style.display = 'block';
    }
    var id_ind = [15, 17, 18, 19, 20];
    // console.log(id_ind.includes(select));
    if (!id_ind.includes(select)) {
      //if (select in [16, 17, 18, 19]) {
      //$('#headyearend').html('Год:<span class="form-required red-star">*</span>');

      document.getElementById('basy').style.display = 'none';
    } else {
      //$("#headyearend").html('Конец периода (год):<span class="form-required red-star">*</span>');
      document.getElementById('basy').style.display = 'block';
    }
  }
  count_interval();

  function openForm() {
    document.getElementById("myForm").style.display = "block";
  }

  function closeForm() {
    document.getElementById("myForm").style.display = "none";
  }

  function changecolor(id) {
    // console.log(id);
    $('#' + id + 'label').css('background-color', "" + document.getElementById(id).value);
  }

  function changevalinterval(id) {
    // document.getElementById(id).value=123;
    valint = Number(document.getElementById(id).value);
    idinp = Number(id[0]);
    i = idinp + 1;
    right = Number(document.getElementById(i + "right").value);
    left = Number(document.getElementById(idinp + "left").value);
    // alert(valint)
    // alert(left)
    // alert(right)
    if (left < valint && valint < right) {
      document.getElementById(id).value = valint;
      valint = valint + 0.01;
      document.getElementById(i + "left").value = valint.toFixed(2);
      // alert(right)
      // alert(valint)
    } else {
      valint = Number(document.getElementById(i + "left").value) - 0.01
      document.getElementById(id).value = valint.toFixed(2);
    }
  }

  function count_interval() {
    $("#list_interval").html('');
    var count_int = document.getElementById("count_int").value;
    for (i = 0; i < count_int; i++) {
      color = 255 - i * 25; //&mdash;
      //   $("#list_interval").append('<div class="form-group col-md-5">\
      //     <input id="' + i + 'left' + '" type="text" onchange=mapredrawing() class="form-control">\
      // </div>\
      //   <label class="labelinterval">&ndash;</label>\
      // <div class="form-group col-md-5">\
      //   <input id="' + i + 'right' + '" type="text" onchange=mapredrawing() class="form-control">\
      // </div>\
      // <div class="form-group col-md-1">\
      //   <label class="label_my_color" id="' + i + 'label' + '">\
      //     <input id="' + i + '" onchange=changecolor(this.id) class="inp_my_color" type="color" />\
      //   </label>\
      // </div>');
      $("#list_interval").append('<div class="input-group">\
                      <p></p>\
                      <input  id="' + i + 'left' + '" type="text" class="form-control float" readonly style="padding:4px">\
                      <p> &nbsp;&ndash;&nbsp; </p>\
                      <input id="' + i + 'right' + '" type="text" onchange=changevalinterval(this.id) class="form-control float" style="padding:4px">\
                      &nbsp;\
                      <label class="label_my_color" id="' + i + 'label' + '">\
                      <input id="' + i + '" onchange=changecolor(this.id) class="inp_my_color" type="color" />\
                      </label>\
                    </div>');
      color = '#' + (0x1000000 + (0 << 16) + (color << 8) + color).toString(16).substring(1);
      $('#' + i + 'label').css('background-color', "" + color);
      document.getElementById(i).value = color;
    }
    inp = count_int - 1;
    document.getElementById(inp + "right").setAttribute('disabled', 'disabled');
  }
</script>