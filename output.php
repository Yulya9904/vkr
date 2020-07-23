<?php
include 'lib.php';
?>

<?php
include 'header.php';
?>
<link rel="stylesheet" href="css/bootstrap4.css">
<link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="css/responsive.bootstrap4.min.css">
<!-- https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css

https://cdn.datatables.net/responsive/2.2.5/css/responsive.bootstrap4.min.css -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>

<div class="container">
	<br>
	<div class="col-lg-12">
		<div class="card h-100">
			<!-- <h5 class="card-header">Укажите параметры</h5> -->
			<div class="card-body">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label class="form-label"><h6>Выберите показатель:<span class="form-required red-star">*</span></h6></label>
						<select id="indicator-list" onChange="selectindicator()"  name="select-numerator" class="form-control custom-select" required>
							<?php
							include 'database.php';
							$link = mysqli_connect($hostname, $username, $password, $dbName);
							if (!$link) {
								echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
							} else {
								$sql = "SELECT id_ind,name_ind FROM `indicator` ORDER BY name_ind ASC";
								$zap = mysqli_query($link, $sql) or die(mysqli_error($link));
								$result = mysqli_query($link, $sql);
								$nd = 0;

								while ($list_type = mysqli_fetch_assoc($result)) {
									$id = "{$list_type['id_ind']}";
									echo "<option value=\"{$list_type['id_ind']}\"";
									if ($_POST['select-numerator'] == "{$list_type['id_ind']}") {
										echo "selected";
									}
									echo ">{$list_type['name_ind']}</option>";
									echo "{$list_type['id_ind']}";
								}
							}
							?>
						</select>
						<h6><div id="soil">
                            <label class="list-region">
                              <input type="checkbox" id="soil_check" />
                              <span class="pseudocheckbox">С учетом состояния почвы</span>
                            </label>
                          </div></h6>
					</div>
					
					<div class="form-group col-md-3">
						<label class="form-label"><h6 id="h6baseyear">Начало периода (год)<span class="form-required red-star">*</span></h6></label>
						<div class="divlist">
							<select id="baseyear" onchange=selectbaseyear() class="form-control custom-select" required>
								<?php
								include 'database.php';
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
					</div>
					<div class="form-group col-md-3">
						<label class="form-label"><h6>Конец периода (год)<span class="form-required red-star">*</span></h6></label>
						<div class="divlist">
							<div id="yearend">
								<select id="year" class="form-control custom-select" required>
								</select>
							</div>
						</div>
						</select>
					</div>
					
				</div>
				
				  <div class="col-md-5" style="float: right;">
				<div class="form-row">
					
					<div class="form-group col-md-8">
						<button class="btn btn-primary" id="table-button">Получить данные</button>
					</div>
					<div class="form-group col-md-4">
						<button class="btn btn-primary" id="table-export-button" >Экспорт</button>
					</div>
					
					<div id="exp">
				</div>
				</div>
			</div>
			</div>
		</div>
		<br />
		<div class="card h-100">
			<div class="card-body" style='overflow:hidden;'>
				<div id="title"></div>
				<table id="table" class="table  table-bordered reg" style="width:100%">
             
				</table>
			</div>


		</div>
	</div>
	<script src="./table.js"></script>
	<script src="./js/form_map_chart.js"></script>
<script>selectbaseyear(); selectindicator();</script>
	<br />
</div>
<?php
include 'footer.php';
?>