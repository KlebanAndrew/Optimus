<?php

class Model_main extends CI_Model {

	function __construct() {
        parent::__construct();
    }
    function import_task($date_begin, $date_end, $task_type, $import_array){
        //var_dump($import_array);
        $import_array[0]['TITLE'] = iconv('UTF-8', 'cp1251',$import_array[0]['TITLE']);
        $import_array[0]['DESCRIPTION'] = iconv('UTF-8', 'cp1251',$import_array[0]['DESCRIPTION']);
        $data = array(
            'nazva' => $import_array[0]['TITLE'],
            'vud' => $task_type,
            'strateg' => 0,
            'rezult' => $import_array[0]['DESCRIPTION'],
            'date_begin' => $date_begin,					// dd.mm.yy приводимо в Y-m-d
            'date_zapl_zaversh' => $date_end,	// dd.mm.yy приводимо в Y-m-d
            'zapl_chas' => 0,
            'vlasnyk' => $this->session->userdata('user_id'),
            'vykonavets' => $this->session->userdata('user_id'),
            'mitky' => 0,
            'date_end_povtor' => ''
        );
        $res = $this->db->insert('zavdannya', $data);
        if($this->db->affected_rows()>0)
            return true;
        else{
            return false;
        }
    }

    function create_zavd_true() {
		$strateg=$this->input->post('strateg');
		if($strateg == "on") { $strateg=1; }	
		if($this->input->post('date_end_povtor')) {
			$date_end_povtor = date('Y-m-d', strtotime($this->input->post('date_end_povtor')));			// dd.mm.yy приводимо в Y-m-d
		} else {
			$date_end_povtor = '0000-00-00';
		}
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'vud' => $this->input->post('vud'),			
			'strateg' => $strateg,
			'rezult' => $this->input->post('rezult'),
			'date_begin' => date('Y-m-d', strtotime($this->input->post('date_begin'))),					// dd.mm.yy приводимо в Y-m-d
			'date_zapl_zaversh' => date('Y-m-d', strtotime($this->input->post('date_zapl_zaversh'))),	// dd.mm.yy приводимо в Y-m-d
			'zapl_chas' => str_replace(",", ".", $this->input->post('zapl_chas')),
			'vlasnyk' => $this->session->userdata('user_id'),
			'vykonavets' => $this->session->userdata('user_id'),
			'mitky' => 0,
			'date_end_povtor' => $date_end_povtor
		);
		$res = $this->db->insert('zavdannya', $data); 
		// повторення завдання
		if($this->input->post('date_end_povtor')) {
			$res = mysql_insert_id();	// повертаємо id завдання
			if($this->input->post('povtor_result')=="1") {
				$this->db->query("CALL optimus.zavdPlan($res, 1)");
			} else {
				$this->db->query("CALL optimus.zavdPlan($res, 0)");			
			}
		}
    }
	
    function edit_zavd_true() {
		$strateg=$this->input->post('strateg');
		if($strateg == "on") { $strateg=1; }	
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'vud' => $this->input->post('vud'),			
			'strateg' => $strateg,
			'rezult' => $this->input->post('rezult'),
			'date_begin' => date('Y-m-d', strtotime($this->input->post('date_begin'))),					// dd.mm.yy приводимо в Y-m-d
			'date_zapl_zaversh' => date('Y-m-d', strtotime($this->input->post('date_zapl_zaversh'))),	// dd.mm.yy приводимо в Y-m-d
			'zapl_chas' => str_replace(",", ".", $this->input->post('zapl_chas')),
			'prymitky' => $this->input->post('prymitky'),
			//'vlasnyk' => $this->session->userdata('user_id'),
			//'vykonavets' => $this->session->userdata('user_id'),
		);		
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('zavdannya', $data);		
	}	

	function edit_plan_zavd_true() {
		
		$data = array(
			'text_detail' => $this->input->post('text_detail'),
			'result_detail' => $this->input->post('result_detail'),			
			'd_v' => date('Y-m-d', strtotime($this->input->post('d_v'))),					// dd.mm.yy приводимо в Y-m-d
			'd_do' => date('Y-m-d', strtotime($this->input->post('d_do'))),	// dd.mm.yy приводимо в Y-m-d
			'chas_plan' => str_replace(",", ".", $this->input->post('chas_plan')),
			//'prymitky' => $this->input->post('prymitky'),
			//'vlasnyk' => $this->session->userdata('user_id'),
			//'vykonavets' => $this->session->userdata('user_id'),
		);		
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('richniy_plan_detalize', $data);		
	}	
	
    function show_one_user($id) {
        $query = "SELECT * FROM users WHERE id = ".$id;
		$res = $this->db->query($query);
        $row = $res->result_array();
        return $row[0]['name'];
    }	
	
    function show_user() {
        $query = "SELECT * FROM users ORDER BY id";
        $res = $this->db->query($query);
		$row = $res->result_array();
		return $row;
    }	
	
	
    function show_control_dates($id_zavd) {
        $query = "SELECT * FROM control_dates WHERE id_zavd=".$id_zavd." ORDER BY id";
        $res = $this->db->query($query);
		$row = $res->result_array();
		return $row;
    }
	
	
}
