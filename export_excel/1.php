<?php
/*
	header("Content-type: text/html;charset=utf-8");
	set_time_limit(0);
	error_reporting(E_ALL);
	date_default_timezone_set('Europe/London');
*/

include('../script/connection.php');

include_once 'PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load("plan.xls");
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$userId = $_POST['user_id'];
//$period = $_POST['period'];
$period = date('Y-m-d', strtotime($_POST['period']));  // dd.mm.yy приводимо в Y-m-d
// дати
$resDate = mysql_query("SELECT DATE_ADD('$period', INTERVAL (2-DAYOFWEEK('$period')) DAY) AS d_begin, DATE_ADD('$period', INTERVAL (6-DAYOFWEEK('$period')) DAY) AS d_end");
$date = mysql_fetch_assoc($resDate);
$date = date('d.m.Y', strtotime($date['d_begin'])).'-'.date('d.m.Y', strtotime($date['d_end']));
$aSheet->setCellValue('A2', mb_convert_encoding("Період: ",'utf-8','windows-1251').$date);
// юзер
$res_user = mysql_query("SELECT REPLACE(u.name,'&#039;','`') AS nameconv, u.posada, u.product_posadu, u2.name AS kerivnyk FROM users u LEFT JOIN derevo ON idUser = u.id LEFT JOIN users u2 ON u2.id = derevo.idUserParent WHERE u.id = '$userId'");
$user = mysql_fetch_assoc($res_user);
$aSheet->setCellValue('D2', mb_convert_encoding("ПІБ: ",'utf-8','windows-1251').$user['nameconv']);
$aSheet->setCellValue('F2', mb_convert_encoding("Посада: ",'utf-8','windows-1251').$user['posada']);
$aSheet->setCellValue('A4', mb_convert_encoding("Продукт посади: ",'utf-8','windows-1251').$user['product_posadu']);

$aSheet->setCellValue('A35', mb_convert_encoding("Працівник: ",'utf-8','windows-1251').$user['nameconv'].mb_convert_encoding(" підпис_____________________",'utf-8','windows-1251'));
$aSheet->setCellValue('E35', mb_convert_encoding("Погоджую: ",'utf-8','windows-1251').$user['kerivnyk'].mb_convert_encoding(" підпис_____________________",'utf-8','windows-1251'));
$aSheet->setCellValue('A55', mb_convert_encoding("Працівник: ",'utf-8','windows-1251').$user['nameconv'].mb_convert_encoding(" підпис_____________________",'utf-8','windows-1251'));

$aSheet->setCellValue('E55', mb_convert_encoding("Керівник: ",'utf-8','windows-1251').$user['kerivnyk'].mb_convert_encoding(" підпис_____________________",'utf-8','windows-1251'));


// поточні завдання
	$iter=19;
	$num=1;
	$sql = "SELECT * FROM zavdannya WHERE vlasnyk = '$userId' AND vud=2 AND date_zapl_zaversh >= DATE_ADD('$period', INTERVAL (2-DAYOFWEEK('$period')) DAY) AND date_begin <= DATE_ADD('$period', INTERVAL (6-DAYOFWEEK('$period')) DAY) ORDER BY id";
	$res=mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$aSheet->setCellValue('B'.$iter, $num);
		// автовисота стовбця
		$pieces = explode("\n", $row['rezult']);
		if(count($pieces)>=2) { $aSheet->getRowDimension($iter)->setRowHeight(-1); }
		$aSheet->setCellValue('C'.$iter, $row['nazva']);
		$aSheet->setCellValue('D'.$iter, $row['rezult']);
		$aSheet->setCellValue('E'.$iter, $row['zapl_chas']);
		$aSheet->setCellValue('F'.$iter, $row['date_zapl_zaversh']);	
		$aSheet->setCellValue('G'.$iter, $row['chas_fakt']);	
		$aSheet->setCellValue('H'.$iter, $row['data_fakt']);	
		$aSheet->setCellValue('J'.$iter, $row['prymitky']);	
		$iter++;
		$num++;
	}

// позачергові завдання
	$iter=40;
	$num=1;
	$sql = "SELECT * FROM zavdannya WHERE vlasnyk = '$userId' AND vud=3 AND date_zapl_zaversh >= DATE_ADD('$period', INTERVAL (2-DAYOFWEEK('$period')) DAY) AND date_begin <= DATE_ADD('$period', INTERVAL (6-DAYOFWEEK('$period')) DAY) ORDER BY id";
	$res=mysql_query($sql);
	while ($row = mysql_fetch_array($res)) {
		$aSheet->setCellValue('B'.$iter, $num);
		$aSheet->setCellValue('C'.$iter, $row['nazva']);
		// автовисота стовбця
		$pieces = explode("\n", $row['rezult']);
		if(count($pieces)>=2) { $aSheet->getRowDimension($iter)->setRowHeight(-1); }
		$aSheet->setCellValue('D'.$iter, $row['rezult']);		
		$aSheet->setCellValue('E'.$iter, $row['zapl_chas']);
		$aSheet->setCellValue('F'.$iter, $row['date_zapl_zaversh']);	
		$aSheet->setCellValue('G'.$iter, $row['prymitky']);	
		$iter++;
		$num++;
	}

// планові завдання
	$iter=10;
	$num=1;
	$sql = "
		SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, chas_fakt, result_detail, richniy_plan_detalize.id AS uniq, IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title
        FROM richniy_plan
          LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
            d_v >= DATE_ADD('$period', INTERVAL (2-DAYOFWEEK('$period')) DAY) AND 
            d_do <= DATE_ADD('$period', INTERVAL (6-DAYOFWEEK('$period')) DAY) AND richniy_plan_detalize.id_user = $userId
        WHERE richniy_plan.users LIKE '%,".$userId.",%'
        AND plan_do >= DATE_ADD('$period', INTERVAL (2-DAYOFWEEK('$period')) DAY) 
        AND plan_vid <= DATE_ADD('$period', INTERVAL (6-DAYOFWEEK('$period')) DAY)";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)) {
		// якщо більше 8 завдань - додаємо строки
		$countRows = mysql_num_rows($res);
		$countAddRows = $countRows-8;
		if($countAddRows){
			//$objPHPExcel->getActiveSheet()->insertNewRowBefore(17, $countAddRows);  
		}
		while ($row = mysql_fetch_array($res)) {
			$aSheet->setCellValue('B'.$iter, $num);
			$aSheet->setCellValue('C'.$iter, $row['title']);
			// автовисота стовбця
			$pieces = explode("\n", $row['result_detail']);
			if(count($pieces)>=2) { $aSheet->getRowDimension($iter)->setRowHeight(-1); }
			$aSheet->setCellValue('D'.$iter, $row['result_detail']);  
			$aSheet->setCellValue('E'.$iter, $row['chas_plan']);
			$aSheet->setCellValue('F'.$iter, $row['d_do']); 
			$aSheet->setCellValue('G'.$iter, $row['chas_fakt']); 	
			$aSheet->setCellValue('H'.$iter, $row['d_do']); 
			$aSheet->setCellValue('J'.$iter, $row['prymitky']); 
			$iter++;
			$num++;
		}
	}	
		
		

//создаем объект класса-писателя
include("PHPExcel/Writer/Excel5.php");
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//выводим заголовки
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="print.xls"');
header('Cache-Control: max-age=0');
//выводим в браузер таблицу с бланком
$objWriter->save('php://output');	

?>