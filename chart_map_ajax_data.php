<?php
include './calculation.php';
include './database.php';
$mysqli = new mysqli($hostname, $username, $password, $dbName);
$data = json_decode(file_get_contents("php://input"), true);
//$text = '';
$text = $data['listselectregion'];
//$regionlist = "28,29,30";
// $regionarr = explode(",", $regionlist);
// $dataLength = count($regionarr);
// if ($dataLength > 0) {
// 	$text .= "and `dataindicator`.id_region in (";
// 	for ($i = 0; $i < $dataLength; $i++) {
// 		if ($dataLength - 1 <> $i) {
// 			$text .= $regionarr[$i] . ',';
// 		} else {
// 			$text .= $regionarr[$i];
// 		}
// 	}
// 	$text .= ') ';
// }
if ($data['type'] == "polar-chart") {
	// $text = "28,29,30";
	// $data['indname'] = 14;
	// $data['baseyear'] = 2008;
	// $data['yeardata'] = 2013;
	// $data['soil']=false;
	$list_regions = list_regions($text);
	$S = S($text, $data['baseyear'], $data['yeardata']);
	$Se = Se($text, $data['baseyear'], $data['yeardata'], $data['soil']);
	$S_chart = array();
	$Se_chart = array();
	$listnameregion = array();
	$count_reg = 0;
	if (!$S['nulldata']) {
		while ($region = $list_regions->fetch_row()) {
			$listnameregion[] = $region[1];
			if (!is_null($S[$region[0]][$data['yeardata']])) {
				$S_chart[] = round($S[$region[0]][$data['yeardata']],2);
			} else {
				$S_chart[] = null;
			}
			if (!is_null($Se[$region[0]][$data['yeardata']])) {
				$Se_chart[] = round($Se[$region[0]][$data['yeardata']],2);
			} else {
				$Se_chart[] = null;
			}
			$count_reg++;
		}
	} else {
		$listnameregion[] = "Выберите хотя бы один регион";
		$S_chart[] = null;
		$Se_chart[] = null;
	}
	$alldata = [
		'listnameregion' => $listnameregion,
		'S' => $S_chart,
		'Se' => $Se_chart,
		'year' => $data['yeardata'],
		'count_reg' => $count_reg,
		// 'name_ind' => $name_ind,
		// 'unit_ind' => $unit_ind
	];
	//print_r($alldata);
	//$alldata->alldata = json_encode($all);
	echo json_encode($alldata);
	// 	$query = "SELECT `name_region`,`id_ind`,`data` FROM `dataindicator`,`regions` WHERE 
	// `regions`.`id_region`= `dataindicator`.`id_region` " . $text . " and year=" . $data['yeardata'] . " 
	// and (`id_ind`=1 or `id_ind`=3)";
	// 	// and `dataindicator`.id_region BETWEEN 27 and 33
	// 	$result = $mysqli->query($query);

	// 	while ($record = $result->fetch_row()) {
	// 		$all[] =  array($record[0], $record[1], (float) $record[2]);
	// 	}
	// 	echo json_encode($all);
}


if ($data['type'] == "line-chart") {
	// $data['indname'] = 14;
	// $data['baseyear'] = 2008;
	// $data['yeardata'] = 2013;
	$querydataindicators = "SELECT `name_ind`,`unit_ind`
		FROM `indicator`WHERE `indicator`.`id_ind`=" . $data['indname'];
	$dataindicators = $mysqli->query($querydataindicators);
	while ($dataindicator = $dataindicators->fetch_row()) {
		$name_ind = $dataindicator[0];
		$unit_ind = $dataindicator[1];
	}
	$allyears = array();
	for ($i = $data['baseyear']; $i <= $data['yeardata']; $i++) {
		$allyears[] = $i;
	}
    if($unit_ind<>''){
	    $namechart=''.$name_ind.', '.$unit_ind;
	} else {
		$namechart=$name_ind;
	}
	$alldataregion = array();
	$list_regions = list_regions($text);
	$alldataregion = call_calculation_func($data['indname'], $text, $data['baseyear'], $data['yeardata'],$data['soil']);
	$all = array();
	$count_reg = 0;
	while ($region = $list_regions->fetch_row()) {
		$data_of_one_region = array();
		$data_of_one_region['name'] = $region[1];
		for ($i = $data['baseyear']; $i <= $data['yeardata']; $i++) {
			if (!is_null($alldataregion[$region[0]][$i])) {
				$data_of_one_region['data'][] = round($alldataregion[$region[0]][$i],2);
			} else {
				$data_of_one_region['data'][] = null;
			}
		}
		array_push($all, $data_of_one_region);
		$count_reg++;
	}
	$alldata = [
		'alldata' => $all,
		'years' => $allyears,
		'name_ind' => $name_ind,
		'unit_ind' => $unit_ind,
		'count_reg' => $count_reg,
		'namechart' => $namechart,
	];
	//print_r($alldata);
	//$alldata->alldata = json_encode($all);
	echo json_encode($alldata);
}


if ($data['type'] == "map") {
	// 	$data['indname'] = 6;
	// $data['baseyear'] = 2008;
	// $data['yeardata'] = 2013;
	$querydataindicators = "SELECT `name_ind`,`unit_ind`
		FROM `indicator`WHERE `indicator`.`id_ind`=" . $data['indname'];
	$dataindicators = $mysqli->query($querydataindicators);
	while ($dataindicator = $dataindicators->fetch_row()) {
		$name_ind = $dataindicator[0];
		$unit_ind = $dataindicator[1];
	}
	 if($unit_ind<>''){
	    $namechart=''.$name_ind.', '.$unit_ind;
	} else {
		$namechart=$name_ind;
	}
	$alldataregion = array();
	$list_regions = list_regions($text);
	$alldataregion = call_calculation_func($data['indname'], $text, $data['baseyear'], $data['yeardata'],$data['soil']);
	$all = array();
	$data_of_one_region = array();
	$min = $alldataregion[1][$data['yeardata']];
	$max = $alldataregion[1][$data['yeardata']];
	$error=false;
	$count_reg=0;
	$count_reg_null=0;
	while ($region = $list_regions->fetch_row()) {
		$count_reg+=1;
		if (!is_null($alldataregion[$region[0]][$data['yeardata']])) {
			array_push($data_of_one_region, array($region[2], round($alldataregion[$region[0]][$data['yeardata']],2)));
			if ($min > $alldataregion[$region[0]][$data['yeardata']]) {
				$min = (float)$alldataregion[$region[0]][$data['yeardata']];
			};
			if ($max < $alldataregion[$region[0]][$data['yeardata']]) {
				$max = (float)$alldataregion[$region[0]][$data['yeardata']];
			}
		} else {
			array_push($data_of_one_region, array($region[2], null));
			$count_reg_null+=1;
			
		}
	}
	if ($count_reg==$count_reg_null){
				$error=true;
				$data['yeardata']="Недостаточно данных для расчета показателя за ".$data['yeardata'];
				}
			else {
				$error=false ;	
			}
	$alldata = [
		'alldata' => $data_of_one_region,
		'year' => $data['yeardata'],
		'name_ind' => $name_ind,
		'unit_ind' => $unit_ind,
		'max' => $max,
		'min' => $min,
		'namechart' => $namechart,
		'error' => $error,
	];
	//print_r($alldata);
	//$alldata->alldata = json_encode($all);
	echo json_encode($alldata);
}


$mysqli->close();
