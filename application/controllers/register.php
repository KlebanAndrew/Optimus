<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {
	
	public function index() {
		$this->load->helper('form');
		$this->load->view('register/register');
	}
	
	public function do_registered() {
		$data = array(
			'login' => $this->input->post('login_w'),
			'pass' => md5($this->input->post('pass_w')),			
			'name' => htmlspecialchars($this->input->post('user'), ENT_QUOTES), // екрануєм лапки
			'posada' => $this->input->post('posada'),
			'description' => $this->input->post('description'),
			'tab_nomer' => $this->input->post('tab_no'),
			'nomer_debitora' => $this->input->post('deb_no'),
			'tel' => $this->session->userdata('tel'),
			'email' => $this->session->userdata('email'),
		);
		$this->db->insert('users', $data); 		
		$this->load->view('register/register_complete');
	}

}