<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->date_begin = $this->session->userdata('date_begin');
		$this->date_end = $this->session->userdata('date_end');
		$this->next_date_begin = $this->session->userdata('next_date_begin');
		$this->next_date_end = $this->session->userdata('next_date_end');
	}
	
    function _remap($method) {
        $login=$this->input->post('user_login');
        $password=$this->input->post('user_pass');
        $pars = $this->uri->segment_array(); // $pars[1]-контроллер; $pars[2]-метод; $pars[3]-параметри
        if($method=="exit_user") {
            $this->session->unset_userdata('user_id');		
            $this->session->unset_userdata('user_login');            
			$this->session->unset_userdata('user_name');
			$this->session->unset_userdata('permission');
			$this->session->unset_userdata('date_begin');
			$this->session->unset_userdata('date_end');
			$this->login('Ви вийшли !');
        } else {
            if($this->session->userdata('user_login'))  {
				if(@$pars[2]=='' and @$pars[3]=='') { $this->index(); } else { $this->$pars[2](@$pars[3]); }
            } else {
                if(!empty($login) and !empty($password)) {
                    $password = md5($password);				
					$login_in = $this->db->get_where('users', array('login' => $login, 'pass' => $password ));
                    if($login_in->result()) {
						$user = $login_in->row();					
					    $this->session->set_userdata('user_id', $user->id);					
                        $this->session->set_userdata('user_login', $user->login);
						$this->session->set_userdata('user_name', $user->name);
						$this->session->set_userdata('permission', $user->perm);
						$this->get_date();
                        $this->$pars[2](@$pars[3]);
					} else {
                        $this->login('Невірно введений логін або пароль !');
                    }
				} else {
					$this->login('');
                }
            }
        }
    }	
	
    function login($message) {
        $this->load->helper('form');
        $data['message']=$message;
		$this->load->view('login/login', $data);
	}
	
    function get_date() {
		$date = date('Y-m-d');
		$query = "SELECT 
				DATE_ADD('$date', INTERVAL (2-DAYOFWEEK('$date')) DAY) AS date_begin, 
				DATE_ADD('$date', INTERVAL (6-DAYOFWEEK('$date')) DAY) AS date_end,
				DATE_ADD('$date', INTERVAL (9-DAYOFWEEK('$date')) DAY) AS next_date_begin, 
				DATE_ADD('$date', INTERVAL (13-DAYOFWEEK('$date')) DAY) AS next_date_end";
		$res_dates = $this->db->query($query);
		$dates = $res_dates->row();
		$this->session->set_userdata('date_begin', $dates->date_begin);
		$this->session->set_userdata('date_end', $dates->date_end);
		$this->session->set_userdata('next_date_begin', $dates->next_date_begin);
		$this->session->set_userdata('next_date_end', $dates->next_date_end);
		$this->date_begin = $this->session->userdata('date_begin');
		$this->date_end = $this->session->userdata('date_end');
		$this->next_date_begin = $this->session->userdata('next_date_begin');
		$this->next_date_end = $this->session->userdata('next_date_end');
	}
	
    protected function get_plan_zavd($date_begin, $date_end) {
		$user_id=$this->session->userdata('user_id');
		$query1 = "
		SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title FROM richniy_plan
			LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
			d_v  >= '".$date_begin."' AND 
			d_do <= '".$date_end."' AND 
			richniy_plan_detalize.id_user = $user_id
			WHERE richniy_plan.users LIKE '%,".$user_id.",%' AND 
			plan_vid <= '".$date_begin."' AND
			plan_do >= '".$date_end."'";
		$res_planovi = $this->db->query($query1);
		return $res_planovi->result_array();
	}	

	public function index() {
		$user_id=$this->session->userdata('user_id');
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);
		
 		$res_potocni = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '2' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();

 		$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '3' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// для фільтрації планових по даті
		$data['d_do'] = $this->date_end;
		
		$status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $this->date_begin, 'end' => $this->date_end));
		$data['status'] = $status->row();		
		
		$this->load->view('front/index', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}

	public function plan_next() {
		$this->date_begin = $this->session->userdata('next_date_begin');
		$this->date_end = $this->session->userdata('next_date_end');
	
		$user_id=$this->session->userdata('user_id');
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);
		
 		$res_potocni = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '2' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();

 		$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '3' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// для фільтрації планових по даті
		$data['d_do'] = $this->date_end;
		
		$status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $this->date_begin, 'end' => $this->date_end));
		$data['status'] = $status->row();		
		
		$this->load->view('front/plan_next', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}	
	
	
	
	public function create_zavd() {
		$this->load->helper('form');
		$data['d_v'] = $this->session->userdata('date_begin');
		$data['d_do'] = $this->session->userdata('date_end');
		$data['next_week'] = 0; // флаг наступного тижня
		$this->load->view('front/create_zavd', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	public function create_zavd_plan() {
		$this->load->helper('form');
		$data['d_v'] = $this->session->userdata('next_date_begin');
		$data['d_do'] = $this->session->userdata('next_date_end');
		$data['next_week'] = 1; // флаг наступного тижня
		$this->load->view('front/create_zavd', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}

	public function create_zavd_true() {
		$this->load->model('model_main');
		$this->model_main->create_zavd_true();
		$this->index();
	}
	
	public function edit_zavd($id) {
		$query = $this->db->get_where('zavdannya', array('id' => $id));
		$zavdannya = $query->row();
		if(!$zavdannya) { echo "Неіснуюче завдання !"; return; }
		if($zavdannya->vlasnyk != $this->session->userdata('user_id')) { echo "Це не ваше завдання !"; return; }
		//if($zavdannya->mitky == 1 or $zavdannya->mitky == 2) { echo "Завдання заблоковане !"; return; }
		$data['zavdannya'] = $zavdannya;
		$this->load->helper('form');
		$this->load->view('front/edit_zavd', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	public function edit_zavd_true() {
		$this->load->model('model_main');
		$this->model_main->edit_zavd_true();	
		$this->index();
	}
	
	public function pereglad_zavd($id) {
		$this->db->select('*');
		$this->db->from('zavdannya');
		$this->db->join('users', 'users.id = zavdannya.vlasnyk', 'left');
		$this->db->where('zavdannya.id', $id);
		$zavdannya = $this->db->get();
		$zavdannya = $zavdannya->row();
		if(!$zavdannya) { echo "Неіснуюче завдання !"; return; }
		if($zavdannya->vlasnyk != $this->session->userdata('user_id')) { echo "Це не ваше завдання !"; return; }		
		$data['zavdannya'] = $zavdannya;
		$data['repeat'] = $this->db->get_where('zavdannya', array('id_end_povtor' => $id));
		$this->load->view('front/pereglad_zavd', $data);
	}
	
	public function delete_zavd($id) {
		$this->db->query("DELETE FROM zavdannya WHERE id = ".$id." LIMIT 1");
		$this->index();
	}
	
	public function detalize($id) {
		$user_id=$this->session->userdata('user_id');
		$richniy_plan = $this->db->get_where('richniy_plan', array('id' => $id));
		$data['title'] = $richniy_plan->row('nazva');
		//$data['main'] = $this->db->get_where('richniy_plan_detalize', array('id_pl_zavd' => $id));
		$data['main'] = $this->db->from('richniy_plan_detalize')->where('id_pl_zavd = '.$id.' AND id_user = '.$user_id)->order_by('id', 'ASC')->get();
		$data['id_pl_zavd'] = $richniy_plan->row('id');
		$users = $richniy_plan->row('users');
		$users = substr($users,1,-1);
		$res = $this->db->query("SELECT * FROM users WHERE id IN (".$users.")");
		$data['users'] = $res->result_array();
		$this->load->helper('form');
		$this->load->view('front/detalize', $data);
		//$this->output->enable_profiler(TRUE);	// профайлер
	}

	public function detalize_add() {
		$id_pl_zavd=$this->input->post('id_pl_zavd');	
		$id_user=$this->session->userdata('user_id');
		$date=$this->input->post('date');
		mysql_query("SET NAMES 'utf8'");
 		$res_dates = $this->db->query("SELECT DATE_ADD('$date', INTERVAL (2-DAYOFWEEK('$date')) DAY) AS d_begin, DATE_ADD('$date', INTERVAL (6-DAYOFWEEK('$date')) DAY) AS d_end");
		$dates = $res_dates->row();
		// дивимось чи є вже такий період (для усунення повторень)
		$query = $this->db->get_where('richniy_plan_detalize', array('id_pl_zavd' => $id_pl_zavd, 'id_user' => $id_user, 'd_v' => $dates->d_begin, 'd_do' => $dates->d_end));
		$zavdannya = $query->row();
		if(!$zavdannya) { 
			echo "Збережено !"; 
			$data = array(
				'id_pl_zavd' => $id_pl_zavd,
				'id_user' => $id_user,
				'text_detail' => $this->input->post('text_detail'),
				'd_v' => $dates->d_begin,
				'd_do' => $dates->d_end,
				'chas_plan' => $this->input->post('chas_plan')
			);		
			$this->db->insert('richniy_plan_detalize', $data);
		} else { 
			echo 'Цей період вже заповнений !'; 
		}	
			
	}
	
	
// Звітування	
	public function report() {
		$user_id=$this->session->userdata('user_id');
		$d_vid = date('Y-m-d');
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);
		
 		$res_potocni = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '2' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();

 		$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud= '3' AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."' ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// для фільтрації планових по даті
		$data['d_do'] = $this->date_end;
		$this->load->view('front/report', $data);
	}	
	
	public function reporting() {
		$user_id=$this->session->userdata('user_id');
		$d_vid = date('Y-m-d');
		//$res = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '".$this->session->userdata('user_id')."' AND mitky IN (0, 1) AND date_zapl_zaversh >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) AND date_begin <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY)");
 		$res = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$user_id' AND vud = 2 AND mitky IN (0, 1) AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."'");		
		if($res->result()) {
			$data['message'] = "У вас є незатверджені завдання ! (затвердіть план)";
		} else {
			foreach($_POST['data_fakt'] as $k=>$v) {
				//echo $k.' = '.$v.'<br>';
				$this->db->query("UPDATE zavdannya SET data_fakt = '".$v."', chas_fakt = '".$_POST['chas_fakt'][$k]."', mitky = 3 WHERE id = ".$k." LIMIT 1");
			}
			$data['message'] = "Звіт прийнято !";	
			//$this->db->query("UPDATE status SET flag = 3 WHERE user_id = '$user_id' AND end >= DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) AND begin <= DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY) LIMIT 1");
			$this->db->query("UPDATE status SET flag = 3 WHERE user_id = '$user_id' AND end >= '".$this->date_begin."' AND begin <= '".$this->date_end."'");
		}
		$this->load->view('front/message', $data);
		$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	
	
	
	
// Дублюючі функції з контроллера admin	
// Річний план-------------------------------------------------------------------	
	public function year_plan() {
		$user_id=$this->session->userdata('user_id');
		$this->load->model('model_admin');
		$data['plan'] = $this->db->from('richniy_plan')->like('users', ','.$user_id.',')->order_by('id', 'ASC')->get();
		$data['users'] = $this->db->from('users')->order_by('name', 'ASC')->get();
		$this->load->view('front/year_plan', $data);
		//$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	public function year_plan_all() {
		$this->load->model('model_admin');
		$data['plan'] = $this->db->get('richniy_plan');
		$data['users'] = $this->db->from('users')->order_by('name', 'ASC')->get();
		$this->load->view('front/year_plan', $data);
		//$this->output->enable_profiler(TRUE);	// профайлер
	}	
	
	
	public function ajax_add_to_richniy_plan() {
		mysql_query("SET NAMES 'utf8'");
		$users=$this->input->post('users');
		$str='';
		for($i=0; $i<count($users); $i++) {
			$str.=$users[$i].',';
		}
		if($users == '') { $str = $this->session->userdata('user_id').','; } // якщо ніодин юзер не вибраний, ставимо свою id
		$str=','.$str;
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'nom_proces' => $this->input->post('nom_proces'),
			'users' => $str,
			'detalize' => $this->input->post('detalize'),
			'plan_vid' => $this->input->post('plan_vid'),
			'plan_do' => $this->input->post('plan_do')
		);		
		$this->db->insert('richniy_plan', $data);
	}	
	
	public function ajax_edit_richniy_plan($id) {
		mysql_query("SET NAMES 'utf8'");
		$users=$this->input->post('users');
		$str='';
		for($i=0; $i<count($users); $i++) {
			$str.=$users[$i].',';
		}
		if($users == '') { $str = $this->session->userdata('user_id').','; } // якщо ніодин юзер не вибраний, ставимо свою id
		$str=','.$str;
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'nom_proces' => $this->input->post('nom_proces'),
			'users' => $str,
			'detalize' => $this->input->post('detalize'),
			'plan_vid' => $this->input->post('plan_vid'),
			'plan_do' => $this->input->post('plan_do')
		);
		$this->db->where('id', $id);
		$this->db->update('richniy_plan', $data);		
	}		


	public function ajax_select_pidlegli() {
		if($this->input->post('user') == 'none') {
			$user_id = $this->session->userdata('user_id');
			$user_pidlegli = $this->db->get_where('derevo', array('idUserParent' => $user_id));
			$arr_id= array();
			foreach ($user_pidlegli->result_array() as $row) {
				$arr_id[] = $row['idUser'];
			}
			//print_r($arr_id);		
			if($arr_id) {
				$user_names = $this->db->where_in('id', $arr_id)->get('users');
				//print_r($user_names);	
				foreach ($user_names->result_array() as $row) {
					//echo $row['name'];
					echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			} else {
				echo '<option value="'.$user_id.'">'.$this->session->userdata('user_name').'</option>';
			}
		}
		if($this->input->post('user') == 'all') {
			$users = $this->db->from('users')->order_by('name', 'ASC')->get();
			foreach ($users->result() as $user) { echo '<option value="'.$user->id.'">'.$user->name.'</option>'; }
		}
		
	}		

	
}

