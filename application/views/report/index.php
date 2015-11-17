<?php $this->load->view('front/header'); ?>
    <script src="<? echo base_url() ?>application/views/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>

    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>

    <link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.14.custom.css">
    <link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-timepicker-addon.css">
    <script type="text/javascript" src="<? echo base_url() ?>application/views/js/DatePickerScript.js"></script>

    <!-- Підказки -->
    <script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
    <link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip_index.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>

    <script>

</script>
    <div id="container">
        <div id="body">
            <div class="login_name"></div>
            <h1>Звіт для статистики</h1>
            <form action="<? echo site_url("report/get_user_data") ?>" method="post" id="export">
                <div class="perDiv">
                    <table class="perTab">
                        <tr>
                            <td>дата початку</td>
                            <td><input type="text" class="form-control DatePicker" name="date_begin" value=" "  /></td>
                        </tr>
                        <tr>
                            <td>дата кінця</td>
                            <td><input type="text" name="date_end" value=" " class="form-control DatePicker" /></td>
                        </tr>
                    </table>
                    <div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
                        <input type="submit" class="but orange" style="float:none;" value="Зберегти" />
                    </div>
                </div>
            </form>
        </div>
        <div style="margin-top: 25px;">
        <?php
        //error_reporting(E_ALL ^ E_WARNING);
        echo '<table class="adminlist">
        <th rowspan="2">User_id\Name</th>';
            if(isset($result_array)){//формування таблиці даних для обробки
               //var_dump($weeks_date);
                if(isset($weeks_date)){//чи прийшли дані
                   foreach($weeks_date as $mx){//формування шапки таблиці
                        echo '<th colspan = "2">'.$mx['begin'].' до '.$mx['end'].'</th>';
                   }
                    echo '<th rowspan="2">Коф. затв.</th><th rowspan="2">Коф. Звіт.</th><th rowspan="2">Сум. тиж.</th>';
                    echo '<tr>';
                    foreach($weeks_date as $mx){//формування шапки таблиці
                        echo '<td >Затверджено</td><td>Відзвітовано</td>';
                   }
                    echo '</tr>';
               }
                        foreach ($result_array as $key=>$data){//розпакування масиву даних по користувачу
                                foreach($data as $weeks_key=>$weeks_data){
                                    echo '<tr>';
                                    echo  '<td>'.$names[$key]['id'].' / '.$names[$key]['name'].'</td>';
                                    $approve_sum = 0;
                                    $report_sum = 0;
                                    foreach($weeks_data as $week_key=>$week_data){//розпакування масиву даних конкретного користувача по тижневих періодах

                                        echo    //'<td>'.$week_data['extreme_date_approve'].'  '.$week_data['extreme_date_report'].'</td>'.
                                                //'<td>'.$week_data['date_approve'].'  '.$week_data['date_report'].'</td>'.
                                                '<td>'.$week_data['approve_result'].'</td><td>'.$week_data['report_result'].'</td>';
                                        $approve_sum = $approve_sum + $week_data['approve_result'];
                                        $report_sum = $report_sum + $week_data['report_result'];
                                        //unset($week_key);
                                    }

                                echo '<td>'.number_format($approve_sum/count($weeks_date), 2).'</td><td>'.number_format($report_sum/count($weeks_date), 2).'</td><td>'.count($weeks_date).'</td></tr>';
                                }
                            }
                $c = count($weeks_date)*2 + 1;//підсумок колонок
                       echo '<tr><td colspan="'.$c.'"></td> </tr>';
                    }
        echo '</table>';
        ?>
        </div>
    </div>

<?php $this->load->view('front/footer'); ?>