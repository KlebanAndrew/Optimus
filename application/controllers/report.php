<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();

    }

    public function index(){
        $this->load->view('report/index');
    }

    public function get_user_data(){//��������� ������ �������� ������
        $this->load->model('model_report');
        $this->load->model('model_main');
        $date_begin = date('Y-m-d', strtotime($this->input->post('date_begin')));
        $date_end = date('Y-m-d', strtotime($this->input->post('date_end')));
        $users = $this->model_main->show_user();
        $weeks_date = $this->model_report->get_period_weeks($date_begin, $date_end);
        foreach ($users as $us){//��������� ������ ������� ������� �����������
            $result =$this->model_report->get_user_period_status($us['id'], $date_begin, $date_end);
            if($result){
                $res[$us['id']] = $result;
                $assocarray[$us['id']] = array('id' => $us['id'], 'name' => $us['name'],'posada' => $us['posada'], 'description' => $us['description']);//��������� ���� ��� - id ��� ���������� ��� �����������
                $result_array[$us['id']] = $this->get_result($res[$us['id']], $weeks_date);//���������� ������ �������� �������� ��� ������� �����������
            }
        }
        //$user_id = $this->input->post('user_id');
       // print_r("<pre>");
       // print_r($res);
       // print_r("</pre>");exit;
        $data = array(
            //'user_data' => $res,
            //'users' => $users,
            'names' => $assocarray,//���������� ��� ����� ������������
            'result_array' => $result_array,//��� ���������� ��� �������� ��� ������� �����������
            'weeks_date' => $weeks_date //���������� ��� ������� ������
        );

       // print_r("<pre>");print_r($data);print_r("</pre>");
        //exit;
        //$this->load->view('report/index',$data);
        $this->load->ext_view('export_excel/approve_report',$data);//ext_view - load view from another folder
    }
    /*������� �������� �������� ������������ �����
    * @param
     * $array - ����� ������ �������� ������� ��� �����������
     * @result
     * $array (array(
     *       'result' => $approve_result, -- ����� ���������� �������� ��������  �������� �����
     *      )
     *  array[index] - �� index �� id ����������� � ������� users
     *  result[index] - �� index �� �������� �����(��������� � 1) ����������� � ������� users
     *
     *
    BEGIN OF get_result FUNCTION*/
    private function get_result($array, $date_array){
        $i =0;
        foreach ($date_array as $ar){
            foreach($array as $ar_key=>$ar_value){
                if($ar_value['begin'] == $ar['begin']){
                    $key = $ar_key;
                    $cheker = true;
                    break;
                }else{
                    $cheker = false;
                }
                //var_dump($ar_value); echo '<br>';
            }
            if ($cheker){
                $cur_date = date('Y-m-d', strtotime($array[$key]['begin']));
                $cur_last_date = strtotime($cur_date);
                $cur_last_date = date("Y-m-d",strtotime("+7 day",$cur_last_date));

                $cur_date =$this->date_to_timestamp($cur_date);
                $cur_last_date =$this->date_to_timestamp($cur_last_date);

                $result[$i]['date_begin'] = $array[$key]['begin'];
                $result[$i]['date_end'] = $array[$key]['end'];
                if($cur_date > $array[$key]['approve_time']){
                    $result[$i]['approve_result'] = 1;
                    $result[$i]['date_approve'] = $array[$key]['approve_time'];
                    $result[$i]['extreme_date_approve'] = $cur_date;
                }
                else{
                    $result[$i]['approve_result'] = 0;
                    $result[$i]['date_approve'] = $array[$key]['approve_time'];
                    $result[$i]['extreme_date_approve'] = $cur_date;
                }
                if($cur_last_date > $array[$key]['report_time']){
                    $result[$i]['report_result'] = 1;
                    $result[$i]['date_report'] = $array[$key]['report_time'];
                    $result[$i]['extreme_date_report'] = $cur_last_date;
                }
                else{
                    $result[$i]['report_result'] = 0;
                    $result[$i]['date_report'] = $array[$key]['report_time'];
                    $result[$i]['extreme_date_report'] = $cur_last_date;
                }
            }else{
                $result[$i]['approve_result'] = '�';
                $result[$i]['report_result'] = '�';
                $result[$i]['date_approve'] = '';
                $result[$i]['date_report'] = '';
                $result[$i]['extreme_date_approve'] = '';
                $result[$i]['extreme_date_report'] = '';
            }
            $i = $i+1;

            //unset($key);
        }

        $data = array(
            'result' => $result,
            //'report_result' => $report_result,
        );
        return $data;
    }
    /*END OF get_result FUNCTION*/

    /*������� ���������� �� ���� ������� ������ ��� ������������
    *@param
    *$date - ����
     * @result
     * timestamp
     * ������� "2015-07-06" ==> "2015-07-06 08:30:00"
     * ���� ������� ����� �������� ��������� ��� ��������� ������
    */
    private function date_to_timestamp($date){//������� ���������� ������ �� ����
        list($year, $month, $day) = explode('-', $date);
        $cur_date = mktime(8, 30, 0, $month, $day, $year);
        return date("Y-m-d H:i:s", $cur_date);
    }
}
?>