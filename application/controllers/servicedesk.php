<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Servicedesk extends CI_Controller {

    public function __construct() {
        parent::__construct();

    }

    public function index(){

        $this->load->view('servicedesk/index');
    }
    public function import_closed_tasks(){
        $this->load->model('model_service_desk');
        $user_name = $this->session->userdata('user_login');
        $date_begin = $this->session->userdata('date_begin');
        $date_end = $this->session->userdata('date_end');
        //$task_extreme_end_time = microtime(TRUE);
        $task_extreme_begin_time = strtotime($date_begin)*1000;
       // $task_extreme_begin_time = 1435045571000;//test value
        $user_sd_id = $this->model_service_desk->get_user_id($user_name);
        $user_sd_tasks = $this->model_service_desk->get_sd_close_tasks($user_sd_id, $task_extreme_begin_time);
        $result = array();
        foreach ($user_sd_tasks as $task){
           array_push($result,$this->model_service_desk->import_sd_task($date_begin, $date_end, 3, $task));
        }
        //var_dump($result);
        redirect('/main/index/', 'refresh');
    }
    public function import_open_tasks(){
        $this->load->model('model_service_desk');
        $user_name = $this->session->userdata('user_login');
        $date_begin = $this->session->userdata('next_date_begin');
        $date_end = $this->session->userdata('next_date_end');
        $user_sd_id = $this->model_service_desk->get_user_id($user_name);
        $user_sd_tasks = $this->model_service_desk->get_sd_open_tasks($user_sd_id);
        $result = array();
        foreach ($user_sd_tasks as $task){
            array_push($result,$this->model_service_desk->import_sd_task($date_begin, $date_end, 2, $task));
        }
        //var_dump($result);
        redirect('/main/plan_next/', 'refresh');
    }
}
?>