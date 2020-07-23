<?php
include 'database.php';
$connect = mysqli_connect($hostname, $username, $password, $dbName);
$output = '';
if (isset($_POST["query"])) {
	$search = mysqli_real_escape_string($connect, $_POST["query"]);
	$query = "
	SELECT * FROM `regions`, `dataindicator`
	where `regions`.`id_region`=`dataindicator`.`id_region` and `dataindicator`.`id_region`<>999
	and `name_region` LIKE '%" . $search . "%'
	group by name_region  
	ORDER BY name_region ASC
	";
} else {
	$query = "
	SELECT * FROM `regions`, `dataindicator`
	where `regions`.`id_region`=`dataindicator`.`id_region` and `dataindicator`.`id_region`<>999
	group by name_region  
	ORDER BY name_region ASC";
}
$result = mysqli_query($connect, $query);
if (mysqli_num_rows($result) > 0) {
	$region = $_POST["listselectregion"];
	// echo "<p>".$region."</p>";
	$regionarr = explode(",", $region);
	$dataLength = count($regionarr);
	echo "<ul id=\"sortable\" class=\"list-unstyled\">";
	while ($region_list = mysqli_fetch_array($result)) {
		if ($_POST["select_reg"]!=="true") {
			echo "<li class=\"ui-state-default\"><label class=\"list-region\">
				<input type=\"checkbox\" onclick=\"selectregion(this.value)\" 
				id=\"{$region_list['id_region']}\"value=\"{$region_list['id_region']}\"";
			if ($dataLength > 0) {
				for ($i = 0; $i < $dataLength; $i++) {
					if ($region_list['id_region'] == $regionarr[$i]) {
						echo "checked=\"checked\"";
					}
				}
			}
			echo "/>
            <span class=\"pseudocheckbox\" >{$region_list['name_region']}</span>";
			echo "</label></li>";
		} else {
			if ($dataLength > 0) {
				for ($i = 0; $i < $dataLength; $i++) {
					if ($region_list['id_region'] == $regionarr[$i]) {
						echo "<li class=\"ui-state-default\"><label class=\"list-region\">
							<input type=\"checkbox\" onclick=\"selectregion(this.value)\"
							id=\"{$region_list['id_region']}\"value=\"{$region_list['id_region']}\"";
						echo "checked=\"checked\"/><span class=\"pseudocheckbox\" >
							{$region_list['name_region']}</span></label></li>";
					}
				}
			}
		}
	}

	echo "</ul>";
} else {
	echo 'Ничего не найдено';
}
