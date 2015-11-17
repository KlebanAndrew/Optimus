<?php
/**
 * Created by PhpStorm.
 * User: us9467
 * Date: 21.07.15
 * Time: 15:37
 */
include_once 'PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load("c:/WebServers/home/10.93.10.48/www/optimus_test/export_excel/report.xls");
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();
$range = range('A','Z');
array_push($range, 'AA','AB','AC','AD','AE','AF','AG');


if(isset($result_array)){//формування таблиці даних для обробки
    //var_dump($weeks_date);
    if(isset($weeks_date)){//чи прийшли дані
        $n=4;
        foreach($weeks_date as $mx){//формування шапки таблиці
            $begin = substr($mx['begin'],5);
            $begin = date("d.m", strtotime($mx['begin']));
            $end = date("d.m.y", strtotime($mx['end']));

            $aSheet->setCellValue($range[$n].'3', $begin.' - '.$end );
           // echo '<th colspan = "2">'.$mx['begin'].' до '.$mx['end'].'</th>';
            $n=$n+2;
        }
    }
    $count = 2*count($weeks_date);
    $j=5;
    foreach ($result_array as $key=>$data){//розпакування масиву даних по користувачу
        foreach($data as $weeks_key=>$weeks_data){
            $i=1;
            $aSheet->setCellValue($range[$i].$j,mb_convert_encoding($names[$key]['description'],'utf-8','windows-1251'));
            $aSheet->setCellValue($range[$i+1].$j, mb_convert_encoding($names[$key]['name'],'utf-8','windows-1251'));
            $aSheet->setCellValue($range[$i+2].$j, mb_convert_encoding($names[$key]['posada'],'utf-8','windows-1251'));
            //echo  '<td>'.$names[$key]['id'].' / '.$names[$key]['name'].'</td>';
            $approve_sum = 0;
            $report_sum = 0;
            foreach($weeks_data as $week_key=>$week_data){//розпакування масиву даних конкретного користувача по тижневих періодах
                $aSheet->setCellValue($range[$i+3].$j, mb_convert_encoding($week_data['approve_result'],'utf-8','windows-1251'));
                $aSheet->setCellValue($range[$i+4].$j, mb_convert_encoding($week_data['report_result'],'utf-8','windows-1251'));
                //echo    '<td>'.$week_data['approve_result'].'</td><td>'.$week_data['report_result'].'</td>';
                $approve_sum = $approve_sum + $week_data['approve_result'];
                $report_sum = $report_sum + $week_data['report_result'];

                $i=$i+2;
            }
            $aSheet->setCellValue('AE'.$j,$approve_sum);
            $aSheet->setCellValue('AF'.$j,$report_sum);
            $result = ($approve_sum + $report_sum)/$count;
            $aSheet->setCellValue('AG'.$j,$result);
           //echo '<td>'.number_format($approve_sum/count($weeks_date), 2).'</td><td>'.number_format($report_sum/count($weeks_date), 2).'</td><td>'.count($weeks_date).'</td></tr>';
        }
        $j=$j+1;
    }
    //$c = count($weeks_date)*2 + 1;//підсумок колонок
   // echo '<tr><td colspan="'.$c.'"></td> </tr>';
}
//создаем объект класса-писателя
include("PHPExcel/Writer/Excel5.php");
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//выводим заголовки
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="report.xls"');
header('Cache-Control: max-age=0');
//выводим в браузер таблицу с бланком
$objWriter->save('php://output');

?>