<?php
include 'lib.php';
?>
<?php
include 'header.php';
?>
<?
if (isset($_POST['submit'])) {
	include 'database.php';
	$link = mysqli_connect($hostname, $username, $password, $dbName);
	if (!$link) {
		echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
	} else {
		$nd = 0;
		$res = mysqli_query($link, "SELECT * FROM `regions`, `dataindicator`where `regions`.`id_region`=`dataindicator`.`id_region` group by name_region  ORDER BY name_region ASC");
		while ($list = mysqli_fetch_assoc($res)) {
			$n = "{$list['id_region']}";
			$list_region[$nd] = $_POST[$n];
			$nd = $nd + 1;
		}
	}
	//asd($list_region);
}
?>
<!-- Page Content -->
<div class="container">
	<h3 class="my-4">Вспомогательные показатели </h3>
	<hr>
	<!-- Marketing Icons Section -->
	<div class="row">
		<div class="col-lg-3 mb-4">
			<div class="card h-100">
				<!-- <h5 class="card-header">Укажите параметры</h5> -->
				<div class="card-body">
					<h6>Выберите показатель:<span class="form-required red-star">*</span>
						<h6>
							<div class="divlist">
								<select id="indicator-list" onChange="selectindicator()" class="form-control custom-select" required>
									<?php
									include 'database.php';
									$link = mysqli_connect($hostname, $username, $password, $dbName);
									if (!$link) {
										echo "&nbsp;Ошибка: невозможно установить соединение " . PHP_EOL;
									} else {
										$sql = "SELECT id_ind,name_ind FROM `indicator` where id_ind between 1 and 15 or id_ind in (20,21) ORDER BY name_ind ASC";
										$zap = mysqli_query($link, $sql) or die(mysqli_error($link));
										$result = mysqli_query($link, $sql);
										$nd = 0;

										while ($list_type = mysqli_fetch_assoc($result)) {
											$id = "{$list_type['id_ind']}";
											echo "<option value=\"{$list_type['id_ind']}\" name=\"{$list_type['name_ind']}\">{$list_type['name_ind']}</option>";
											echo "{$list_type['id_ind']}";
										}
									}
									?>
								</select>
							</div>
							<hr>

							<h6 id="h6baseyear">Начало периода (год)<span class="form-required red-star">*</span>
							</h6>
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
							<hr>
							<h6>Конец периода (год)<span class="form-required red-star">*</span></h6>
							<div class="divlist">
								<div id="yearend">

									<select id="year" class="form-control custom-select" required>
										<!--  -->
									</select>
								</div>
							</div>
							<hr>
							<h6>Список регионов:<span class="form-required red-star">*</span>
								<div class="divlist">
									<? include "list_region.php" ?>

									<div style="text-align: center;"><button class="btn btn-primary" id="polar-button">Построить график
										</button>
									</div>

									<div style="text-align: center;"><button class="btn btn-primary" id="line-export-button">Экспорт графика
										</button>
									</div>

									<div id="exp">
									</div>
								</div>
								<!-- <div class="card-footer">
								
								<form method="post" action="export.php">
									<input class="btn btn-primary" type="submit" id="submit" value="Экспорт данных">
								</form>
							</div> -->
				</div>
			</div>
			<div class="col-lg-9">
				<div class="card h-100">
					<!-- <h2>Карта регионов России</h2> -->
					<div class="card-body">
						<div id="line-diagram" class="polar-diagram"></div>
						<div id="error">
							<div id="okno">
								<b>Выберите хотя бы один регион из списка!</b>
								<a href="#" class="close">Закрыть</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="./line-diagram/line-script.js"></script>
	<script src="./js/form_map_chart.js"></script>
<script>selectbaseyear(); selectindicator();</script>

	<br />
	<?php
	include 'footer.php';
	?>
	</body>

	</html>