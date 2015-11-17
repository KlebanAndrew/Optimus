<?php

class Model_admin extends CI_Model {

	function __construct() {
        parent::__construct();
    }

    function show_one_user($user_id) {
        $query = "SELECT * FROM users WHERE id = ".$user_id;
		$res = $this->db->query($query);
        $row = $res->result_array();
        return $row;
    }	

    function show_users_z_massiva2($str) {
		$str = substr($str,1); // видаляєм першу кому		
		$str = substr($str,0,-1); // видаляєм останню кому
		$query = "SELECT id, name FROM users WHERE id IN (".$str.")";
		$res = $this->db->query($query);
		$row = $res->result_array();
		for($i=0; $i<count($row); $i++) {
			if($this->session->userdata('user_id') == $row[$i]['id']) {
				echo $row[$i]['name'].' <a href="#" title="Видалити себе з завдання" onclick="return del_zavd(this);">[x]</a>, ';				
			} else {
				echo $row[$i]['name'].', ';			
			}

		}		
    }	
	

// універсальна ф-ція відправки листа
	public function send_email($flag) {
		if($flag == 1) { $message="<b>Статус:</b> План затверджено ! <br /><b>Дата, час:</b> ".date("d.m.Y H:i"); }
		if($flag == 2) { $message="<b>Статус:</b> План повернено на доопрацювання ! <br /><b>Причина:</b> ".$this->input->post('comment')."<br /><b>Дата, час:</b> ".date("d.m.Y H:i"); }	
		if($flag == 3) { $message="<b>Статус:</b> Звіт затверджено ! <br /><b>Дата, час:</b> ".date("d.m.Y H:i"); }
		
		$user_id = $this->input->post('user_id');
		$adresat = $this->db->get_where('users', array('id' => $user_id))->row();
		
		$this->load->library('email');
		$config['mailtype'] = 'html';				// відправка в html
		$this->email->initialize($config); 
		$this->email->to($adresat->email); 			// 'Yuriy.Chopey@oe.if.ua'
		$this->email->from('info@optimus.com');
		$this->email->subject('Optimus');
		$this->email->message($message);
		$this->email->send();
	}

}
