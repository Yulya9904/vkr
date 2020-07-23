<? session_start(); ?>
<?php
function arr_region_in_str($regionlist)
{
	$text = '';
	// $regionarr = explode(",", $regionlist);
	// $dataLength = count($regionarr);
	if ($regionlist <> "") {
		$text .= "and `regions`.id_region in (";
		$text .= $regionlist;
		// for ($i = 0; $i < $dataLength; $i++) {
		// 	if ($dataLength - 1 <> $i) {
		// 		$text .= $regionarr[$i] . ',';
		// 	} else {
		// 		$text .= $regionarr[$i];
		// 	}
		// }
		$text .= ') ';
	} else $text = '';
	return $text;
}


function get_data_indicator($text, $baseyear, $year, $idindicator)
{
	include 'database.php';
	$mysqli = new mysqli($hostname, $username, $password, $dbName);
	$query = "SELECT dataindicator.`id_region`,year,`name_region`,`data`,`id_ind`,`name_map`
		FROM `dataindicator` 
		INNER JOIN `regions` ON `regions`.`id_region`= `dataindicator`.`id_region`
		WHERE year BETWEEN " . $baseyear . " and " . $year . " " . $text . "
		and `dataindicator`.`id_ind` in (" . $idindicator . ") 
		ORDER BY `dataindicator`.`id_ind`, dataindicator.`id_region`, year ASC";
	$result = $mysqli->query($query);
	// print($query);
	return $result;
	$mysqli->close();
}


function list_regions($text)
{
	include 'database.php';
	$mysqli = new mysqli($hostname, $username, $password, $dbName);
	if ($text == '') {
		$text = "";
	} else {
		$text = "WHERE id_region in (" . $text . ") and id_region<>999";
	}
	$query_list_regions = "SELECT `id_region`, `name_region`,`name_map` FROM `regions`
		" . $text . "  ORDER BY `name_region` ASC"; // ВРП 
	$list_regions = $mysqli->query($query_list_regions);
	return $list_regions;
	$mysqli->close();
}


//GRP at comparable prices
function GRP($text, $baseyear, $year)
{
	include 'database.php';
	$mysqli = new mysqli($hostname, $username, $password, $dbName);
	$text = arr_region_in_str($text);
	$query_GRP = "SELECT dataindicator.`id_region`,`data` FROM `dataindicator`
		INNER JOIN `regions` ON `regions`.`id_region`= `dataindicator`.`id_region`
		WHERE year=" . $baseyear . " " . $text . "
		and `id_ind`=1  and `regions`.`id_region`<>999 ORDER BY dataindicator.`id_region` ASC"; // ВРП
	$list_regions = $mysqli->query($query_GRP); // список регионов с данными по ВРП в базовом году
	$result = get_data_indicator($text, $baseyear, $year, '2'); //индекс физического объема ВРП
	while ($record = $result->fetch_row()) {
		$all_data_index_GRP[$record[0]][$record[1]] = $record[3];
	}
	while ($region_baseyear = $list_regions->fetch_row()) {
		for ($i = $baseyear; $i <= $year; $i++) {
			if ($i == $baseyear and $region_baseyear[1] <> '') {
				$data_regions_GRP[$region_baseyear[0]][$i] = (float) $region_baseyear[1];
				// print($data_regions_GRP[$region_baseyear[0]][$i]);
				// echo "<br>";
				// print "@";;
			} elseif (
				$i <> $baseyear
				and $data_regions_GRP[$region_baseyear[0]][$i - 1] <> null
				and $all_data_index_GRP[$region_baseyear[0]][$i] <> ''
			) {
				$GRP = $data_regions_GRP[$region_baseyear[0]][$i - 1] * $all_data_index_GRP[$region_baseyear[0]][$i] / 100;
				$data_regions_GRP[$region_baseyear[0]][$i] = $GRP;
				// print($data_regions_GRP[$region_baseyear[0]][$i - 1]);
				// echo "<br>";
				// print($all_data_index_GRP[$region_baseyear[0]][$i]);
				// echo "<br>";
				// print($data_regions_GRP[$region_baseyear[0]][$i]);
				// echo "<br>";
			} else {
				$data_regions_GRP[$region_baseyear[0]][$i] = null;
			}
		}
	}
	return $data_regions_GRP;
	$mysqli->close();
}

//population incomes in comparable prices
function population_incomes($text, $baseyear, $year)
{
	$list_regions = list_regions($text);
	$text = arr_region_in_str($text);
	$result = get_data_indicator($text, $baseyear, $year, '3,4');
	while ($record = $result->fetch_row()) {
		switch ($record[4]) {
			case 3:
				$all_data_incomes[$record[0]][$record[1]] = $record[3]; //  среднедушевые денежные доходы в текущих ценах ценах	
			case 4:
				$all_data_price_index[$record[0]][$record[1]] = $record[3]; //consumer price index
		}
	}
	while ($region = $list_regions->fetch_row()) {
		for ($i = $baseyear; $i <= $year; $i++) {
			if ($i == $baseyear and $all_data_price_index[$region[0]][$i] <> null) {
				if ($all_data_incomes[$region[0]][$i] <> '') {
					$all_data_price_index[$region[0]][$i] = 1;
				} else {
					$all_data_price_index[$region[0]][$i] = 1;
					$all_data_incomes[$region[0]][$i] = null;
				}
			} elseif (
				$i <> $baseyear
				and $all_data_price_index[$region[0]][$i - 1] <> null
				and $all_data_price_index[$region[0]][$i] <> null
			) {
				if ($all_data_incomes[$region[0]][$i] <> is_null($all_data_incomes[$region[0]][$i])) {
					$all_data_price_index[$region[0]][$i] = $all_data_price_index[$region[0]][$i - 1] * $all_data_price_index[$region[0]][$i] / 100;
					$all_data_incomes[$region[0]][$i] = $all_data_incomes[$region[0]][$i] / $all_data_price_index[$region[0]][$i];
				} else {
					$all_data_incomes[$region[0]][$i] = null;
				}
			} else {
				$all_data_price_index[$region[0]][$i] = null;
			}
		}
	}
	return ($all_data_incomes);
}


function distinction_S_Se($text, $baseyear, $year, $check_soil)
{
	$Se = Se($text, $baseyear, $year, $check_soil);
	$S = S($text, $baseyear, $year);
	$list_regions = list_regions($text);
	while ($region = $list_regions->fetch_row()) {
		for ($i = $baseyear; $i <= $year; $i++) {
			if (!is_null($S[$region[0]][$i]) and !is_null($Se[$region[0]][$i])){
			if ($Se[$region[0]][$i] > $S[$region[0]][$i]) {
				$distinct[$region[0]][$i] = ($Se[$region[0]][$i] - $S[$region[0]][$i]) / $Se[$region[0]][$i] * 100;
			} else {
				$distinct[$region[0]][$i] = ($S[$region[0]][$i] - $Se[$region[0]][$i]) / $S[$region[0]][$i] * 100;
			}
		}else{
			$distinct[$region[0]][$i] =null;
		}
		}
	}
	return $distinct;
}


function cost_of_living_index($text, $baseyear, $year)
{ //social and economic well-being
	include 'database.php';
	$id_indicator_list = '6';
	$list_regions = list_regions($text);
	if ($text <> '') {
		$text .= ',999';
		$text = arr_region_in_str($text);
	}
	$result = get_data_indicator($text, $baseyear, $year, $id_indicator_list);
	//print_r($result);
	while ($record = $result->fetch_row()) {
		$all_cost_of_consumer_set[$record[0]][$record[1]] = $record[3]; //sanitary chemical water samples
	}
	while ($region = $list_regions->fetch_row()) {
		for ($i = $baseyear; $i <= $year; $i++) {
			if (
				$all_cost_of_consumer_set[999][$i] <> '' and
				$all_cost_of_consumer_set[$region[0]][$i] <> ''
			) {
				//индекс, обратный относительной стоимости жизни в регионе (отношение стоимости набора в стране к его стоимости  в регионе
				$CI_CIi = $all_cost_of_consumer_set[999][$i] / $all_cost_of_consumer_set[$region[0]][$i];
				$all_data_cost[$region[0]][$i] = $CI_CIi;
			} else {
				$all_data_cost[$region[0]][$i] = null;
			}
		}
	}
	return $all_data_cost;
}

function GRP_popilation($text, $baseyear, $year)
{ //social and economic well-being
	include 'database.php';
	$id_indicator_list = '5';
	$GPR = GRP($text, $baseyear, $year);
	$list_regions = list_regions($text);
	if ($text <> '') {
		$text = arr_region_in_str($text);
	}
	$result = get_data_indicator($text, $baseyear, $year, $id_indicator_list);
	//print_r($result);
	while ($record = $result->fetch_row()) {
		$all_population[$record[0]][$record[1]] = $record[3]; //microbiological water samples
	}
	while ($region = $list_regions->fetch_row()) {
		for ($i = $baseyear; $i <= $year; $i++) {
			if (
				$GPR[$region[0]][$i] <> '' and
				$all_population[$region[0]][$i] <> ''
			) {
				$all_data_Se['nulldata'] = false;
				//среднедушевой номинальный ВРП в i-м регионе
				$Yi_Ni = ($GPR[$region[0]][$i] * 1000000) / ($all_population[$region[0]][$i] * 1000);
				$all_data_GRP_popilation[$region[0]][$i] = $Yi_Ni;
			} else {
				$all_data_GRP_popilation[$region[0]][$i] = null;
			}
		}
	}
	return $all_data_GRP_popilation;
}
// $text = "28,29,30";
// GRP_popilation($text, 2008, 2017);

function call_calculation_func($indname, $text, $baseyear, $yeardata, $check_soil)
{
	switch ($indname) {
		case 14:
			$alldataregion = GRP($text, $baseyear, $yeardata);
			break;
		case 15:
			$alldataregion = population_incomes($text, $baseyear, $yeardata);
			break;
		case 16:
			$alldataregion = Ei($text, $baseyear, $yeardata, $check_soil);
			break;
		case 17:
			$alldataregion = S($text, $baseyear, $yeardata);
			break;
		case 18:
			$alldataregion = Se($text, $baseyear, $yeardata, $check_soil);
			break;
		case 19:
			$alldataregion = distinction_S_Se($text, $baseyear, $yeardata, $check_soil);
			break;
		case 20:
			$alldataregion = GRP_popilation($text, $baseyear, $yeardata);
			break;
		case 21:
			$alldataregion = cost_of_living_index($text, $baseyear, $yeardata);
			break;
		default:
			$text = arr_region_in_str($text);
			$result = get_data_indicator($text, $baseyear, $yeardata, $indname);
			while ($record = $result->fetch_row()) {
				$alldataregion[$record[0]][$record[1]] = round((float) $record[3], 2);
			}
	}
	return $alldataregion;
}
?>