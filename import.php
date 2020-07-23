<?php
include('./class/User.php');
$user = new User();
$user->adminLoginStatus();
include('lib.php');
include('include/menus.php');
?>
<div class="container">
  <br>
  <div class="row">
    <div class="col-lg-10" style='margin:0 auto;'>
      <div class="card h-100">
        <h4 class="card-header">Импорт показателей</h4>
        <div class="card-body">
          <form enctype="multipart/form-data" id="dataloading" action="add.php" method="POST">
            <div class="modal-body">
              <div class="border border-info" style="margin-bottom: 10px;">
                <h6 align='center' style="margin-top: 20px;">Правила загрузки показателей</h6>
                <div style="padding-left:25px;margin-bottom: 20px;">
                  <span>Для загрузки статистических данных в информационную систему воспользуйтесь следующим <a target="_blank" href="\images\example.xlsx">шаблоном</a>, используя следующие правила:</span><br>
                  <ul style='list-style-type: square;padding-left:35px'>
                    <li>пустые значения заносить как &laquo;&ndash;&raquo;;</li>
                    <li>нулевые значения заносить как  &laquo;0&raquo;;</li>
                    <li>данные необходимо загружать в формате до трех значащих знаков после запятой;</li>
                    <li>название регионов и первая ячейка листа должны строго соответствовать шаблону;</li>
                    <li>год должен быть введен без дополнительных обозначений.</li>

                  </ul>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Выберите показатель:<span class="form-required red-star">*</span></label>
                <select name="select-numerator" class="form-control custom-select" required>
                  <?php
                  include 'database.php';
                  $link = mysqli_connect($hostname, $username, $password, $dbName);
                  if (!$link) {
                    echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
                  } else {
                    $sql = "SELECT id_ind,name_ind FROM `indicator` where id_ind between 1 and 13 ORDER BY name_ind  ASC";
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
              </div>
              <br>
              <label class="form-label">Файл:<span class="form-required red-star">*</span></label>
              <!-- <input type="hidden" name="MAX_FILE_SIZE" value="30000"> -->
              <br>
              <input type="file" name="file" id="file" placeholder="Выберите файл" required>
              <br><br>
              <hr>
              <!-- <div class="modal-footer"> -->
              <button type='submit' class="btn btn-primary">Загрузить</button>
              <!-- </div> -->
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<br><br>
<?php include('include/footer.php'); ?>
<!-- <script>window.open('$pathfile', '_self');</script> -->