<?php
$chart_data = json_decode($_POST["S_Se"]);
require_once('Classes/PHPExcel.php');
$document = new \PHPExcel();
$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе


$columnPosition = 0; // Начальный столбец
$startLine = 1; // Начальная строка


// Получаем активный лист
//$sheet->setTitle("Показатели благосостояния");


$sheet->setCellValueByColumnAndRow(0, 2, "Регион");
// Вставляем заголовок в "A2" 
$sheet->setCellValueByColumnAndRow($columnPosition, $startLine, 'Уровень социального благополучия выбранных регионов РФ, ' . $chart_data->{'year'} . ' г.');
// Выравниваем по центру

$sheet->getStyleByColumnAndRow($columnPosition, $startLine)->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
);
// Объединяем ячейки "A2:C2"
$document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, $startLine, $columnPosition + 2, $startLine);
// Перекидываем указатель на следующую строку
$startLine++;

$currentColumn = 1;


$sheet->setCellValueByColumnAndRow(1, 2, "Уровень социального благополучия (без учета экологической составляющей)");
$sheet->setCellValueByColumnAndRow(2, 2, "Уровень социального благополучия (с учетом экологической составляющей)");
$sheet->getColumnDimension("A")->setAutoSize(true);
$sheet->getColumnDimension("B")->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(30);

//Заполняем таблицу
for ($i = 0; $i < $chart_data->{'count_reg'}; $i++) {
    $startLine++; // Перекидываем указатель на следующую строку
    $sheet->setCellValueByColumnAndRow(0, $startLine, $chart_data->{'listnameregion'}[$i]);
    $sheet->setCellValueByColumnAndRow(1, $startLine, $chart_data->{'S'}[$i]);
    $sheet->setCellValueByColumnAndRow(2, $startLine, $chart_data->{'Se'}[$i]);
}
$max_col = $sheet->getHighestColumn();
$max_row = $sheet->getHighestRow();

$sheet->getStyle('B3:' . $max_col . '' . $max_row . '')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

$sheet->mergeCells('A1:' . $max_col . '1');
$sheet->getColumnDimension('A')->setAutoSize(true);

$sheet->getStyle('B2:' . $max_col . '' . $max_row . '')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => TRUE
    ),
    'font' => array(
        'bold' => true
    )
);

$sheet->getStyle("A1:" . $max_col . "2")->applyFromArray($style);

$border_thin = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$sheet->getStyle("A2:" . $max_col . "" . $max_row . "")->applyFromArray($border_thin);
$sheet->getStyle('A1:' . $max_col . '' . $max_row . '')->getFont()->setName('Times New Roman');


$reg = $chart_data->{'count_reg'} + 2;
$dataSeriesLabels = array(
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$2', NULL, 1),    //	S
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$2', NULL, 1),    //	Se
);


$col = $chart_data->{'count_reg'} + 2;
$xAxisTickValues = array(
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$3:$A$' . $col, NULL, 4),
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$3:$A$' . $col, NULL, 4),
);

$dataSeriesValues = array(
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$3:$B$' . $col, NULL, 4),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$3:$C$' . $col, NULL, 4),
);

$series = new PHPExcel_Chart_DataSeries(
    \PHPExcel_Chart_DataSeries::TYPE_RADARCHART,
    NULL,
    range(0, count($dataSeriesValues) - 1),
    $dataSeriesLabels,
    $xAxisTickValues,
    $dataSeriesValues,
    NULL,
    NULL, // добавили 1 параметр из конструктора (plot direction)
    \PHPExcel_Chart_DataSeries::STYLE_MARKER //изменили маркер				// plotStyle
);

$layout = new PHPExcel_Chart_Layout();
$plotArea = new PHPExcel_Chart_PlotArea($layout, array($series));

$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);

$title = new PHPExcel_Chart_Title('Уровень социального благополучия выбранных регионов РФ, ' . $chart_data->{'year'} . ' г.');
//$yAxisLabel = new PHPExcel_Chart_Title('' . $unit . '');
//$yAxisLabel = new PHPExcel_Chart_Title('Test Stacked Line Chart');
$chart = new PHPExcel_Chart(
    'chart1',   // name
    $title,     // title
    $legend,    // legend
    $plotArea,    // plotArea
    true,            // plotVisibleOnly
    0,                // displayBlanksAs
    NULL,            // xAxisLabel
    NULL  // yAxisLabel
);
$row = $sheet->getHighestRow();
$chart->setTopLeftPosition('E2');
$chart->setBottomRightPosition('O22');

$sheet->addChart($chart);
$currentColumn = 0;
// $result = mysqli_query($link, "SELECT distinct(`dataindicator`.`id_region`),`name_region` FROM `regions`,`dataindicator` where `regions`.`id_region`=`dataindicator`.`id_region` ORDER BY `name_region` ASC");
// $sheet->getStyle("A2:C6")->applyFromArray($border);

// header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
// header("Cache-Control: no-cache, must-revalidate");
// header("Pragma: no-cache");
// header("Content-type: application/vnd.ms-excel");
// header("Content-Disposition: attachment; filename=123.xlsx");

// $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
// $objWriter->setIncludeCharts(TRUE);
// $objWriter->save('php://output');
if (file_exists('media/')) {
    foreach (glob('media/*') as $file) {
        unlink($file);
    }
}
$objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$pathfile = "media/Сравнение показателей S и SE" . date('m.d.Y') . ".xlsx";
$objWriter->save($pathfile);
//header('Location: http://localhost/ redirec2t.php ');

print "<script>window.open('$pathfile', '_self');</script>";

// //print "<script>window.location.href ='index.php';</script>"; 
