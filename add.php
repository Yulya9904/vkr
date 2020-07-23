<?php
include 'lib.php';
?>
<style>
  /* CSS */
  .alert-arrow {
    border: 1px solid #60c060;
    color: #54a754;
  }

  .alert-arrow .alert-icon {
    position: relative;
    width: 3rem;
    background-color: #60c060;
  }

  .alert-arrow .alert-icon::after {
    content: "";
    position: absolute;
    width: 0;
    height: 0;
    border-top: .75rem solid transparent;
    border-bottom: .75rem solid transparent;
    border-left: .75rem solid #60c060;
    right: -.75rem;
    top: 50%;
    transform: translateY(-50%);
  }

  .alert-arrow .close {
    font-size: 1rem;
    color: #cacaca;
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<?php
require_once 'Classes/PHPExcel.php';
require_once 'database.php';
$link = mysqli_connect($hostname, $username, $password, $dbName);
$dbh = new PDO("mysql:host=$hostname;dbname=$dbName", $username, $password);
$uploaddir = './media/';
$inputFileName = $uploaddir . basename($_FILES['file']['name']);
copy($_FILES['file']['tmp_name'], $inputFileName);
$id_ind = htmlspecialchars(stripslashes($_POST['select-numerator']));

if ($link) {
  include_once 'Classes/PHPExcel/IOFactory.php';

  $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
  foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
  {
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $colNumber = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $years = [];
    for ($col = 1; $col <= $colNumber - 1; $col++) {
      $years[] = $worksheet->getCellByColumnAndRow($col, 1);
    }
    for ($row = 2; $row <= $highestRow; ++$row) {
      $name_region = array();
      $cell1 = $worksheet->getCellByColumnAndRow(0, $row);
      $select_sub = $dbh->query("SELECT id_region FROM regions WHERE name_region='" . $cell1 . "'");
      $select_sub->setFetchMode(PDO::FETCH_ASSOC);
      while ($select_name_region = $select_sub->fetch()) {
        $name_region[] = $select_name_region;
      }
      // echo "имя:";
      // print_r($name_region);
      if (is_array($name_region) || is_object($name_region)) {
        foreach ($name_region as $value) {
          for ($col = 1; $col <= $colNumber - 1; $col++) {
            $id_region = $value['id_region'];
            $data = $worksheet->getCellByColumnAndRow($col, $row);
            $cell_year = $worksheet->getCellByColumnAndRow($col, 1);

            // print "<p></p> $g";
            // print strlen( $trimdata);
            //print($trimdata);
            $sql = "SELECT count(id_ind_data) FROM `dataindicator` WHERE id_region='$id_region' and id_ind='$id_ind' and year='$cell_year'";
            $zap = mysqli_query($link, $sql) or die(mysqli_error($link));
            $empty = mysqli_fetch_row($zap);
            $pos = strripos($data, "-");
            if ($pos === false) {
              if ((integer)$empty[0] === 1) {
                // $data=(float)$data;
                $sql = "update dataindicator set data=$data WHERE id_region='$id_region' and id_ind='$id_ind' and year='$cell_year'";
                $zap = mysqli_query($link, $sql) or die(mysqli_error($link));
              }
              if ((integer)$empty[0] === 0){
                $add_data = $dbh->prepare("INSERT INTO dataindicator(id_region,id_ind,data,year) 
                VALUES(:sub,:type,:value,:year)");
                $add_data->bindParam(":sub",  $id_region, PDO::PARAM_INT);
                $add_data->bindParam(":type", $id_ind, PDO::PARAM_INT);
                $add_data->bindParam(":value", $data, PDO::PARAM_STR);
                $add_data->bindParam(":year", $cell_year, PDO::PARAM_STR);
                $add_data->execute();
              }
            } else {
              if (!$empty[0] == 0) {
                $sql1 = "DELETE FROM dataindicator WHERE id_region='$id_region' and id_ind='$id_ind' and year='$cell_year'";
                $zap = mysqli_query($link, $sql1) or die(mysqli_error($link));
              }
            }
          }
        }
      }
    }
  }
  // print "<script>window.location.href = 'output.php';</script>";
} else {
  $massage = 'div class="alert alert-icon alert-danger" role="alert">
    <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i> Ошибка соединения
    </div>';
}
?>
<!-- Bootstrap 4 -->
<div class="container">
  <br>
  <div class="col-lg-6" style='margin:0 auto;'>
    <div class="card h-100">
      <div class="card-body" style='overflow:hidden;'>
        <div class="alert alert-arrow d-flex rounded p-0" role="alert">
          <div class="alert-icon d-flex justify-content-center align-items-center text-white flex-grow-0 flex-shrink-0">
            <i class="fa fa-check"></i>
          </div>
          <div class="alert-message d-flex align-items-center py-2 pl-4 pr-3">
            <h4> Данные успешно загружены!</h4>
          </div>
        </div>
        <a class="btn btn-success" href='ind_adm.php' style="width:100%">Перейти к проверке показателей</a>


      </div>
    </div>

    <br />
  </div>