<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

// клік по фільтру	
	public function ajax_form() {
		$user_id=$this->session->userdata('user_id');
        /*/
        $date = date('Y-m-d', strtotime($this->input->post('date')));
        $query = "SELECT
				DATE_ADD('$date', INTERVAL (2-DAYOFWEEK('$date')) DAY) AS date_begin,
				DATE_ADD('$date', INTERVAL (6-DAYOFWEEK('$date')) DAY) AS date_end";
        $res_dates = $this->db->query($query);
        $dates = $res_dates->row();
        $actually_date_begin = $dates->date_begin;
        $actually_date_end = $dates->date_end;

        $this->session->set_userdata('date_begin',$actually_date_begin);
        $this->session->set_userdata('date_end',$actually_date_end);
        */
		//$d_vid = $this->input->post('date');
		$d_vid = date('Y-m-d', strtotime($this->input->post('date')));	// dd.mm.yy приводимо в Y-m-d
		$query1 = "
		SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, chas_fakt, data_fakt,
		       result_detail, richniy_plan_detalize.mitky, richniy_plan_detalize.id AS uniq,
		       IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title
		FROM richniy_plan
			LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
            d_v >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) AND 
            d_do <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY) AND richniy_plan_detalize.id_user = $user_id
        WHERE richniy_plan.users LIKE '%,".$user_id.",%'
        AND plan_do >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) 
        AND plan_vid <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY)";
		$res_planovi = $this->db->query($query1);
		$data['planovi_zavd']=$res_planovi->result_array();

		
        $query2 = "SELECT *
        FROM zavdannya
        WHERE vlasnyk = '$user_id'
            AND vud=2
            AND date_zapl_zaversh >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY)
            AND date_begin <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY)
        ORDER BY id ";
 		$res_potocni = $this->db->query($query2);
		$data['potocni_zavd']=$res_potocni->result_array();

        $query3 = "SELECT *
        FROM zavdannya
        WHERE vlasnyk = '$user_id'
            AND vud=3
            AND date_zapl_zaversh >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY)
            AND date_begin <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY)
        ORDER BY id ";
 		$res_pozachergovi = $this->db->query($query3);
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
		
 		$res_dates = $this->db->query("SELECT DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) AS d_begin, DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY) AS d_end");
		$dates = $res_dates->row();
		$data['d_v'] = $dates->d_begin;		// для фільтрації планових по даті
		$data['d_do'] = $dates->d_end;		
		$this->load->view('front/ajax_pl', $data);

	}

// затвердження плану
	public function ajax_zatv_pl2() {

		$j_stroka_id_pot = json_decode($this->input->post('j_stroka_id_pot')); //print_r($j_stroka_id_pot); id поточних планових завдань
		$j_stroka_id_poz = json_decode($this->input->post('j_stroka_id_poz')); //print_r($j_stroka_id_poz); id поточних позачергових завдань
		$date_do = $this->input->post('date_do');
        $date_vid = $this->input->post('date_vid');
        $period = $this->input->post('period');
		// зливаємо 2 масива
		$id_shki = array_merge($j_stroka_id_pot, $j_stroka_id_poz);				//print_r($id_shki);
	
		if($j_stroka_id_pot) {
            //var_dump ($id_shki);
			$data = array('mitky' => 1);
			$this->db->where_in('id', $id_shki);
			$this->db->update('zavdannya', $data);
			if($period == 'now') {
				$this->update_status($this->session->userdata('user_id'), $this->session->userdata('date_begin'), $this->session->userdata('date_end'), 'План поданий на узгодження', 1);
			}
			if($period == 'next') {
				$this->update_status($this->session->userdata('user_id'), $this->session->userdata('next_date_begin'), $this->session->userdata('next_date_end'), 'План поданий на узгодження', 1);
			}
            if($period == 'custom'){
                $this->update_status($this->session->userdata('user_id'), $date_vid,  $date_do, 'План поданий на узгодження', 1);
            }
			echo 'ok';
			$this->send_email();
		} else { 	// якщо нема планових, не відправляємо на затвердж.
			echo 'error';
			exit();
		}
	}
	
	
// підтягування тексту примітки при звітуванні
	public function get_prymitky() {
		if($this->input->post('is_planovi') == 0) {
			echo $this->db->get_where('zavdannya', array('id' => $this->input->post('id_zavd')))->row('prymitky');
		}
		if($this->input->post('is_planovi') == 1) {
			echo $this->db->get_where('richniy_plan_detalize', array('id' => $this->input->post('id_zavd')))->row('prymitky');
		}
	}
	
// збереження тексту примітки при звітуванні
	public function save_prymitky() {
		mysql_query("SET NAMES 'utf8'");
		$data = array('prymitky' => $this->input->post('prymitka'));
		$this->db->where('id', $this->input->post('id_zavd'));
		if($this->input->post('is_planovi') == 0) {
			$this->db->update('zavdannya', $data);
		}
		if($this->input->post('is_planovi') == 1) {
			$this->db->update('richniy_plan_detalize', $data);
		}
	}
	
	
    protected function update_status($user_id, $date_begin, $date_end, $comment, $flag) {
        $approve_time = date('Y-m-d G:i:s');
        $report_time = date('Y-m-d G:i:s');
        $data = array(
			'user_id' => $user_id,
			'begin' => $date_begin,
			'end' => $date_end,
			'comment' => $comment,
			'flag' => $flag,
		);
        if($flag == 1){
            $data['approve_time'] = $approve_time;
        }
        if($flag == 3){
            $data['report_time'] = $report_time;
        }
		$get_status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $date_begin, 'end' => $date_end));
		if($get_status->result()) {
			$id = $get_status->row('id');
			$this->db->where('id', $id);
			$this->db->update('status', $data);	
		} else {
			$this->db->insert('status', $data);
		}
	}	

	
// універсальна ф-ція відправки листа
	public function send_email() {
		$user_id=$this->session->userdata('user_id');
		$adresat = $this->db->query("SELECT rr.name, rr.email, users.name AS my_name, users.email AS my_email
		    FROM derevo
		        LEFT JOIN users ON derevo.idUser = users.id
		        LEFT JOIN users AS rr ON derevo.idUserParent = rr.id
		    WHERE users.id = ".$user_id)->row();
		$this->load->library('email');
		$config['mailtype'] = 'html';				// відправка в html
		$this->email->initialize($config); 
		$this->email->to($adresat->email); 			// 'Yuriy.Chopey@oe.if.ua'
		$this->email->from('info@optimus.com');
		$this->email->subject('План на затвердження від '.$adresat->my_name);
		$this->email->message('Затвердіть план '.$adresat->my_name.' ('.$adresat->my_email.') від '.date("d.m.Y"));
		$this->email->send();
	}

//функція зміни користувача(для замісників)
    public function change_user()
    {
        $user_id=$this->session->userdata('user_id');//поточний юзер
        $id = $this->input->post('id');
        if($id == $this->session->userdata('main_user_id')){$cheked = 1;}else{$cheked = 0;}
        $count ='';
        //тут буде перевірка на правильність отриманого id
        $legal_id = $this->session->userdata('zast_user');
        foreach ($legal_id as $l_id){
            if($id == $l_id['user_id']){
                $cheked = 1;
            }
        }

        //id правильне, міняємо юзера
            if(isset($id) and $cheked != 0){
                $login_in = $this->db->get_where('users', array('id' => $id));
                $user = $login_in->row();
                $this->session->set_userdata('user_id', $user->id); // 53; // $user->id;
                $this->session->set_userdata('user_login', $user->login);
                $this->session->set_userdata('user_name', $user->name);
                $this->session->set_userdata('permission', $user->perm);
                echo " змінено";
            }
        else{
            echo $count."Не змінено";
        }
    }

    //функція добавлення секретаря в настройках(settings.php)
    public function set_secretar(){
        $this->load->model('model_profile');
        $user_id = $this->session->userdata('main_user_id');
        $id_secretar = $this->input->post('id_secretar');
       // $id_secretar = json_decode($id_secretar, true);
        $check_user_id = $this->model_profile->check_user_id($id_secretar);
        if($check_user_id){
            $res = $this->model_profile->add_new_secretary($user_id, $id_secretar);

//перевірка чи виконався запис
            if($res){//видаэм ынформацыю для нового запиту на сторынку
                $this->db->select('name, tab_nomer')->from('users')->where('id',$id_secretar)->order_by('name', 'ASC');
                $data = $this->db->get()->result_array();
                $a = iconv("windows-1251", "utf-8", "Секретар добавлений");
                $name = iconv("windows-1251", "utf-8", $data[0]['name']);
                $res_array = array('msg'=> $a,
                                   'id_sec'=> $id_secretar,
                                   'name'=>$name,
                                   'tab_nomer'=>$data[0]['tab_nomer'],
                                   'id' => $res
                );

            }else{//видаєм інформацію про дубляж
                $a = iconv("windows-1251", "utf-8", "Такий секретар вже існує");
                $res_array = array('msg'=> $a,'html'=> '');
            }
        }
        else {
            $a = iconv("windows-1251", "utf-8", "Такого користувача не існує");
            $res_array = array('msg'=> $a,'html'=> '');
        }
        echo  json_encode( $res_array);


    }

    public function del_secretar(){
        $this->load->model('model_profile');
        $id = $this->input->post('id');
        $res = $this->model_profile->delete_secretary($id);
        if($res){
            $a = iconv("windows-1251", "utf-8", "Секретар видалений");
            $res_array = array('msg'=>$a, 'id'=>$id);
        }else{
            $a = iconv("windows-1251", "utf-8", "Помилка");
            $res_array = array('msg'=>$a, 'id'=>0);
        }
        echo  json_encode( $res_array);
    }
}



