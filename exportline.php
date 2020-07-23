<?php
$chart_data = json_decode($_POST["alldata"]);
require_once('Classes/PHPExcel.php');
$document = new \PHPExcel();
$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе


$columnPosition = 0; // Начальный столбец
$startLine = 1; // Начальная строка


// Получаем активный лист
//$sheet->setTitle("Показатели благосостояния");

$years=count($chart_data->{'years'});

// Вставляем заголовок в "A2" 
$sheet->setCellValueByColumnAndRow($columnPosition, $startLine, $chart_data->{'namechart'});
// Выравниваем по центру

$sheet->getStyleByColumnAndRow($columnPosition, $startLine)->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
);
$sheet->setCellValueByColumnAndRow(0, 2, "Регион");
// Объединяем ячейки 
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, $startLine, $columnPosition + $years, $startLine);
// Перекидываем указатель на следующую строку
$startLine++;

$currentColumn = 1;


// $sheet->getColumnDimensionByColumn("A")->setAutoSize(true);

for ($i = 0; $i < $years; $i++) {
    $sheet->setCellValueByColumnAndRow($i + 1, 2, $chart_data->{'years'}[$i]);
}
// $sheet->getColumnDimension("B")->setWidth(30);
//$sheet->getRowDimension('1')->setRowHeight(33);


//Заполняем таблицу
for ($i = 0; $i < $chart_data->{'count_reg'}; $i++) {
    $startLine++; // Перекидываем указатель на следующую строку
    $sheet->setCellValueByColumnAndRow(0, $startLine, $chart_data->{'alldata'}[$i]->{'name'});
    for ($j = 0; $j < $years; $j++) {
        $sheet->setCellValueByColumnAndRow($j + 1, $startLine, $chart_data->{'alldata'}[$i]->{'data'}[$j]);
    }
}
$max_col = $sheet->getHighestColumn();
$max_row = $sheet->getHighestRow();
foreach(range('B',$max_col) as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$sheet->getStyle('B3:' . $max_col . '' . $max_row . '')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

$sheet->mergeCells('A1:' . $max_col . '1');
$sheet->getColumnDimension('A')->setAutoSize(true);
//$sheet->getRowDimension(1)->setRowHeight(ceil(strlen($text)/width)*height);

$sheet->getStyle('B2:' . $max_col . '' . $max_row . '')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:' . $max_col . '' . $max_row . '')->getAlignment()->setWrapText(true);
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

$reg=$chart_data->{'count_reg'}+2; //кол-во строк

$dataSeriesLabels = array(); 
for ($i=3;$i<=$reg;$i++) {  
  $dataSeriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$' . $i, NULL, 1); 
} 

$col=$sheet->getHighestColumn();
$xAxisTickValues = array( 
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$2:$' . $col . '$2', NULL, 4) 
); 

$dataSeriesValues = array(); 
for ($i=3;$i<=$reg;$i++) { 
  $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$'. $i. ':$' .$col.'$'. $i, NULL, 4); 
}   

$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_LINECHART,    // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
  range(0, count($dataSeriesValues)-1),     // plotOrder
  $dataSeriesLabels,                // plotLabel
  $xAxisTickValues,               // plotCategory
  $dataSeriesValues               // plotValues
);

$plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));

$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false); 

  // $title = new PHPExcel_Chart_Title('Test Stacked Line Chart');
  $title = new PHPExcel_Chart_Title($chart_data->{'namechart'});
$chart = new PHPExcel_Chart(
  'chart1',   // name
  $title,     // title
  $legend,    // legend
  $plotArea,    // plotArea
  true,     // plotVisibleOnly
  0,        // displayBlanksAs
  NULL,     // xAxisLabel
  $yAxisLabel   // yAxisLabel
);
$row=$sheet->getHighestRow();
$chart->setTopLeftPosition('A'. ($row+3)); 
$chart->setBottomRightPosition('K'. ($row+18)); 

$sheet->addChart($chart);
$currentColumn = 0;
if (file_exists('media/')) {
  foreach (glob('media/*') as $file) {
      unlink($file);
  }
}
$objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$pathfile = "media/".$chart_data->{'name_ind'} .', ' . date('m.d.Y') . ".xlsx";
$objWriter->save($pathfile);
//header('Location: http://localhost/ redirec2t.php ');

print "<script>window.open('$pathfile', '_self');</script>";

// //print "<script>window.location.href ='index.php';</script>"; 
