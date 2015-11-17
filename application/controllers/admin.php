<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->date_begin = $this->session->userdata('date_begin');
		$this->date_end = $this->session->userdata('date_end');
		$this->next_date_begin = $this->session->userdata('next_date_begin');
		$this->next_date_end = $this->session->userdata('next_date_end');
	}

    function _remap($method) {
		if($this->session->userdata('permission') == 1) {
			$pars = $this->uri->segment_array(); // $pars[1]-контроллер; $pars[2]-метод; $pars[3]-параметри
			//$this->$pars[2](@$pars[3]);
			if(@$pars[2]=='' and @$pars[3]=='') { $this->index(); } else { $this->$pars[2](@$pars[3]);
				//$this->output->enable_profiler(TRUE);	// профайлер
			}
		} else {
			echo "No permission!";
		}
	}
    //відображення адмін сторінки
	public function index() {
		$query = $this->db->select('id,name')->order_by('name', 'ASC')->get('users');
		$data['allUsers'] = $query->result();
		$this->load->view('admin/index', $data);
	}
	public function ajaxUsers(){

		if($this->input->post('ajaxParentUserId')){
			$userParentId = $this->input->post('ajaxParentUserId');
			//$query = "SELECT u.id,u.name,d.* FROM users u JOIN derevo d ON d.idUser = u.id AND d.idUserParent!=$userParentId AND d.idUser!=$userParentId";
			$query = "SELECT * FROM users u
			          WHERE u.id NOT IN
			            (SELECT idUser FROM derevo d
			             WHERE d.idUserParent=$userParentId)
			                AND u.id!=$userParentId
			             ORDER BY name";
			$res = $this->db->query($query);
			$res = $res->result();
            //var_dump($res);
			foreach($res as $row){
				echo "<option value=".$row->id.">".$row->name."</option>\n";
			}
			die();
		}		
	}
    //витяг дерева працівників з бд
	public function addDerevoUsers(){
		$idUser = (int)$this->input->post('Users');
		$idUserParent = (int)$this->input->post('parentUsers');
		if($idUser and $idUserParent){
			$data = array(
				'idUser' => $idUser,
				'idUserParent' =>$idUserParent
			);
			$this->db->insert('derevo', $data); 
		}
		$adress = $_SERVER['HTTP_REFERER'];
		header("Location: $adress");	
	}
	//тестова функція видалення працівника з структури дерева
	public function delete_from_derevo($id) {
		$this->db->query("DELETE FROM derevo WHERE id = ".$id." LIMIT 1");
		$this->index();
	}	
	

// Річний план-------------------------------------------------------------------	
	public function year_plan() {
		$this->load->model('model_admin');
		$data['plan'] = $this->db->get('richniy_plan');
		$data['users'] = $this->db->get('users');
		$this->load->view('admin/year_plan', $data);
	}
	
	public function ajax_add_to_richniy_plan() {
		mysql_query("SET NAMES 'utf8'");
		$users=$this->input->post('users');
		$str='';
		for($i=0; $i<count($users); $i++) {
			$str.=$users[$i].',';
		}
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


// Перегляд планів------------------------------------------------------------------
    protected function get_plan_zavd($date_begin, $date_end, $user_id) {
		$query1 = "			
		 SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, chas_fakt, data_fakt, result_detail,
		        richniy_plan_detalize.mitky, richniy_plan_detalize.id AS uniq,
		        richniy_plan_detalize.prymitky, richniy_plan_detalize.task_end, IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title
		 FROM richniy_plan
            LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
            d_do >= '".$date_begin."' AND 
            d_v  <= '".$date_end."' AND 
            richniy_plan_detalize.id_user = ".$user_id."
            WHERE richniy_plan.users LIKE '%,".$user_id.",%' AND 
            plan_do  >= '".$date_begin."' AND
            plan_vid <= '".$date_end."'";
		$res_planovi = $this->db->query($query1);
		return $res_planovi->result_array();
	}	

	public function plans() {

		$data['user_id'] = $user_id = $this->session->userdata('user_id');//id юзера під яким хочемо працювати
		if($user_id == 2) { $user_id = 8; $data['user_id'] = 1; } // для себе (41 для дерева)
		 // для костишина
		/*
		$query = "SELECT u.id, name, COUNT(z.id) AS 'kilk', z.mitky FROM derevo d
				LEFT JOIN users u ON d.idUser = u.id
				LEFT JOIN zavdannya z ON u.id = z.vlasnyk AND (z.mitky = 1 or z.mitky = 3)
				WHERE d.idUserParent = $user_id
				GROUP BY u.id, name";
		*/

        //вибірка з бази даних статусів незавершених тижневих планів за весь час(до поточного тижня)
        $status = $this->db->query(" SELECT s.user_id, count(s.user_id) as kilk
                                         FROM optimus.status s left join permissions p
                                         on s.user_id = p.perm_user_id
                                         where (s.flag = 1 or s.flag = 3) and p.user_id = $user_id
                                         group by s.user_id ");
        $data['status'] = $status->result_array();//надсилання інформації на сторінку формування інтерфейсу керівника
        ///////////////////////////////////////////////////////////////////
		$res1 = $this->db->query("SELECT u.id, name, COUNT(z.id) AS 'kilk', z.mitky
            FROM derevo d
                LEFT JOIN users u ON d.idUser = u.id
                LEFT JOIN zavdannya z
                ON u.id = z.vlasnyk
                    AND (z.mitky = 1 OR z.mitky = 3)
                    AND z.date_zapl_zaversh >= '".$this->date_begin."'
                    AND  z.date_begin <= '".$this->date_end."'
            WHERE d.idUserParent = $user_id
                GROUP BY u.id, name");
		$data['users1'] = $res1->result_array();
		$res2 = $this->db->query("SELECT u.id, name, COUNT(z.id) AS 'kilk', z.mitky
            FROM derevo d
                LEFT JOIN users u ON d.idUser = u.id
                LEFT JOIN zavdannya z
                ON u.id = z.vlasnyk
                    AND (z.mitky = 1 OR z.mitky = 3)
                    AND z.date_zapl_zaversh >= '".$this->next_date_begin."'
                    AND z.date_begin <= '".$this->next_date_end."'
            WHERE d.idUserParent = $user_id
                GROUP BY u.id, name");
		$data['users2'] = $res2->result_array();
		$this->load->view('admin/plans', $data);	
	}
// тестова (перегляд дерева підпорядкованих)	
	public function plans_demo($user_id = 8) {	// test
		if(!$user_id) { $user_id = 8; }			// test
		$data['user_id'] = $user_id; 			// 41, 3, 8	// test
		$res1 = $this->db->query("SELECT u.id, name, COUNT(z.id) AS 'kilk', z.mitky
            FROM derevo d
                LEFT JOIN users u ON d.idUser = u.id
                LEFT JOIN zavdannya z
                ON u.id = z.vlasnyk
                    AND (z.mitky = 1 OR z.mitky = 3)
                    AND z.date_zapl_zaversh >= '".$this->date_begin."'
                    AND  z.date_begin <= '".$this->date_end."'
            WHERE d.idUserParent = $user_id
                GROUP BY u.id, name");
		$data['users1'] = $res1->result_array();
		$res2 = $this->db->query("SELECT u.id, name, COUNT(z.id) AS 'kilk', z.mitky
            FROM derevo d
                LEFT JOIN users u ON d.idUser = u.id
                LEFT JOIN zavdannya z
                ON u.id = z.vlasnyk
                    AND (z.mitky = 1 OR z.mitky = 3)
                    AND z.date_zapl_zaversh >= '".$this->next_date_begin."'
                    AND z.date_begin <= '".$this->next_date_end."'
            WHERE d.idUserParent = $user_id
                GROUP BY u.id, name");
		$data['users2'] = $res2->result_array();

		$this->load->view('admin/plans', $data);
		//$this->output->enable_profiler(TRUE);	// профайлер
	}
	
// тестова (перегляд всіх хто заповнив плани)	
	public function plans_hto() {	// test
		echo $this->date_begin.' - '.$this->date_end.'<br>';
		$query ="SELECT users.id, users.name FROM zavdannya 
					LEFT JOIN users ON zavdannya.vlasnyk = users.id
					WHERE zavdannya.date_zapl_zaversh >= '$this->date_begin' AND  zavdannya.date_begin <= '$this->date_end'
				GROUP BY id";
		$res1 = $this->db->query($query);
		foreach ($res1->result() as $row) {
			echo '<a href="'.site_url("admin/show_user_plan/".$row->id).'">'.$row->name.'</a><br />';
		}
		echo $this->next_date_begin.' - '.$this->next_date_end.'<br>';
		$query ="SELECT users.id, users.name FROM zavdannya 
					LEFT JOIN users ON zavdannya.vlasnyk = users.id
					WHERE zavdannya.date_zapl_zaversh >= '$this->next_date_begin' AND  zavdannya.date_begin <= '$this->next_date_end'
				GROUP BY id";
		$res2 = $this->db->query($query);
		foreach ($res2->result() as $row) {
			echo '<a href="'.site_url("admin/show_user_plan/".$row->id).'">'.$row->name.'</a><br />';
		}		
		echo '<hr>';
		echo '<table border="1">
				<tr>
					<td>id</td>
					<td>begin</td>
					<td>end</td>
					<td>comment</td>
					<td>flag</td>
					<td>name</td>
					<td>u_id</td>					
				</tr>';
		$res3 = $this->db->query("SELECT status.id, status.begin, status.end, status.comment, status.flag,
		                                 users.name, users.id AS u_id
		                          FROM STATUS LEFT JOIN users
		                               ON status.user_id=users.id
		                          ORDER BY u_id");
		foreach ($res3->result() as $row) {
			echo '<tr>
					<td>'.$row->id.'</td>
					<td>'.$row->begin.'</td>
					<td>'.$row->end.'</td>
					<td>&nbsp;'.$row->comment.'</td>
					<td>'.$row->flag.'</td>
					<td><a href="'.site_url("admin/show_user_plan/".$row->u_id).'">'.$row->name.'</a></td>
					<td><a href="'.site_url("admin/show_user_plan/".$row->u_id).'">'.$row->u_id.'</a></td>					
				</tr>';
		}	
		echo '</table>';
		
		$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	public function show_status_plan($user_id) {	
		$data['status'] = $this->db->order_by('begin', 'DESC')->get_where('status', array('user_id' => $user_id));
		$data['user_data'] = $this->db->get_where('users', array('id' => $user_id))->row();
		$this->load->view('admin/show_status_plan', $data);
	}
	
// дерево зліва --- функція відображення планів поточного і наступного тижнів
	public function show_user_plan($user_id) {
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud = 2
                AND date_zapl_zaversh >= '".$this->date_begin."'
                AND  date_begin <= '".$this->date_end."'
            ORDER BY id");
        $data['potocni_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud = 3
                AND date_zapl_zaversh >= '".$this->date_begin."'
                AND  date_begin <= '".$this->date_end."'
            ORDER BY id");
        $data['pozachergovi_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud = 2
                AND date_zapl_zaversh >= '".$this->next_date_begin."'
                AND  date_begin <= '".$this->next_date_end."'
            ORDER BY id");
        $data['next_potocni_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud = 3
                AND date_zapl_zaversh >= '".$this->next_date_begin."'
                AND  date_begin <= '".$this->next_date_end."'
            ORDER BY id");
        $data['next_pozachergovi_zavd'] = $res->result_array();
		$user = $this->db->get_where('users', array('id' => $user_id));
		$data['user_data'] = $user->row();
		$this->load->view('admin/show_user_plan', $data);
	}
//  функція відображення планів поточного  тижня (з деталізацією + вибором дати) + кнопки навігації і затвердження
	public function show_user_plan_na_zatverd($user_id) {
		$this->load->helper('form');
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end, $user_id);
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=2
                AND date_zapl_zaversh >= '".$this->date_begin."'
                AND  date_begin <= '".$this->date_end."'
            ORDER BY id");
        $data['potocni_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=3
                AND date_zapl_zaversh >= '".$this->date_begin."'
                AND  date_begin <= '".$this->date_end."'
            ORDER BY id");
        $data['pozachergovi_zavd'] = $res->result_array();
		$user = $this->db->get_where('users', array('id' => $user_id));
		$data['user_data'] = $user->row();
		$data['period'] = "now"; // флаг періода (потрібний при затвердженні)
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));	
		$status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $this->date_begin, 'end' => $this->date_end))->row();
		$data['status'] = $status;	// для відображення повідомлення статусів та виводу кнопок


        //опрацювання обмеження редагування планів завдань
        $query ='SELECT * FROM permissions
		    WHERE user_id='.$this->session->userdata('user_id').'
		        and perm_user_id='.$user_id;
        $res2 = $this->db->query($query);
        if($res2->result_array()){
            $data['check'] = 0;
        }
        else {
            $data['check'] = 1;
        }

        //============================================================
        $this->load->view('admin/show_user_plan_na_zatverd', $data);

	}	
// функція відображення планів наступного тижня	(з деталізацією + вибором дати) + кнопки навігації і затвердження
	public function show_user_plan_na_zatverd_next($user_id) {
		$this->load->helper('form');
		$data['planovi_zavd']=$this->get_plan_zavd($this->next_date_begin, $this->next_date_end, $user_id);
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=2
                AND date_zapl_zaversh >= '".$this->next_date_begin."'
                AND  date_begin <= '".$this->next_date_end."'
            ORDER BY id");
        $data['potocni_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=3
                AND date_zapl_zaversh >= '".$this->next_date_begin."'
                AND  date_begin <= '".$this->next_date_end."'
            ORDER BY id");
        $data['pozachergovi_zavd'] = $res->result_array();
		$user = $this->db->get_where('users', array('id' => $user_id));
		$data['user_data'] = $user->row();
		$data['period'] = "next"; // флаг періода (потрібний при затвердженні)
		$data['title_dates'] = date('d.m.Y', strtotime($this->next_date_begin)).' - '.date('d.m.Y', strtotime($this->next_date_end));	
		$data['status'] = '';	// для відображення повідомлення статусів та виводу кнопок
        $status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $this->next_date_begin, 'end' => $this->next_date_end))->row();
        $data['status'] = $status;
        $query ='SELECT * FROM permissions
					WHERE user_id='.$this->session->userdata('user_id').'
					and perm_user_id='.$user_id;
        $res2 = $this->db->query($query);
        if($res2->result_array()){
            $data['check'] = 0;
        }
        else {
            $data['check'] = 1;
        }
		$this->load->view('admin/show_user_plan_na_zatverd_next', $data);		
	}
// любий (клік по календарику) функція відображення планів будь якого тижня	(з деталізацією + вибором дати) + кнопки навігації і затвердження
	public function show_user_plan_na_zatverd_date() {
		$this->load->helper('form');
		$d_vid = $this->input->post('period');
		$user_id = $this->input->post('user_id');
 		$res_dates = $this->db->query("SELECT DATE_ADD('$d_vid', INTERVAL (2-DAYOFWEEK('$d_vid')) DAY) AS d_begin,
 		                                      DATE_ADD('$d_vid', INTERVAL (6-DAYOFWEEK('$d_vid')) DAY) AS d_end");
		$dates = $res_dates->row();
		$data['title_dates'] = date('d.m.Y', strtotime($dates->d_begin)).' - '.date('d.m.Y', strtotime($dates->d_end));
		$data['planovi_zavd']=$this->get_plan_zavd($dates->d_begin, $dates->d_end, $user_id);		
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=2
                AND date_zapl_zaversh >= '".$dates->d_begin."'
                AND  date_begin <= '".$dates->d_end."'
            ORDER BY id");
        $data['potocni_zavd'] = $res->result_array();
		$res = $this->db->query("SELECT *
            FROM zavdannya
            WHERE vlasnyk = '$user_id'
                AND vud=3
                AND date_zapl_zaversh >= '".$dates->d_begin."'
                AND  date_begin <= '".$dates->d_end."'
            ORDER BY id");
        $data['pozachergovi_zavd'] = $res->result_array();
		$user = $this->db->get_where('users', array('id' => $user_id));
		$data['user_data'] = $user->row();
		$data['period'] = "select"; 		// флаг періода (потрібний при затвердженні), якщо він є 
		$data['d_begin'] = $dates->d_begin;	// то вказані період початку
		$data['d_end'] = $dates->d_end;		// і кінця (для таблиці status hidden поля)
		$status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $dates->d_begin, 'end' => $dates->d_end))->row();
		$data['status'] = $status;	// для відображення повідомлення статусів та виводу кнопок

        $query ='SELECT * FROM permissions
					WHERE user_id='.$this->session->userdata('user_id').'
					and perm_user_id='.$user_id;
        $res2 = $this->db->query($query);
        if($res2->result_array()){
            $data['check'] = 0;
        }
        else {
            $data['check'] = 1;
        }
		$this->load->view('admin/show_user_plan_na_zatverd_date', $data);
		//$this->output->enable_profiler(TRUE);	// профайлер
	}
	
// Затвердження планів-------------------------------------------------------------------
	public function delete_zavd($str) {
		$pieces = explode("-", $str);
		$id = $pieces[0];
		$user_id = $pieces[1];
		$this->db->query("DELETE FROM zavdannya WHERE id = ".$id." LIMIT 1");
		header("Location: ".site_url("admin/show_user_plan_na_zatverd/".$user_id));
	}
        //обробник кнопки "Затвердити план"
	public function zatverduty_plan() {
		$this->update_zavdannya(2); // апдейт завдань
		// апдейт статуса
		if($this->input->post('period') == "now") {
			$this->update_status($this->input->post('user_id'), $this->date_begin, $this->date_end, '', 2);
		}
		if($this->input->post('period') == "next") {
			$this->update_status($this->input->post('user_id'), $this->next_date_begin, $this->next_date_end, '', 2);
		}
		if($this->input->post('period') == "select") {
			$this->update_status($this->input->post('user_id'), $this->input->post('d_begin'), $this->input->post('d_end'), '', 2);
		}
		$this->load->model('model_admin');
		$this->model_admin->send_email(1); // План затверджено !
		$this->plans();
		//$this->output->enable_profiler(TRUE);	// профайлер
	}
	
	public function ne_zatverduty_plan() { // В попередніх версіях статус ставився не 0 а 1

		$this->update_zavdannya(0); // апдейт завдань
		// апдейт статуса
		if($this->input->post('period') == "now") {
			$this->update_status($this->input->post('user_id'), $this->date_begin, $this->date_end, $this->input->post('comment'), 0);
		}
		if($this->input->post('period') == "next") {
			$this->update_status($this->input->post('user_id'), $this->next_date_begin, $this->next_date_end, $this->input->post('comment'), 0);
		}
		if($this->input->post('period') == "select") {
			$this->update_status($this->input->post('user_id'), $this->input->post('d_begin'), $this->input->post('d_end'), $this->input->post('comment'), 0);
		}
		$this->load->model('model_admin');
		$this->model_admin->send_email(2); // План відправлено на доопрацювання !
		$this->plans();	
	}
//обробник кнопки "Затвердити факт виконання"
	public function zatverduty_fakt() {
        $id_per_plan_zavd = json_decode($this->input->post('id_per_plan_zavd'));
        $id_per_potoch_zavd =json_decode($this->input->post('id_per_potoch_zavd'));
        $next_date_end = $this->session->userdata('next_date_end');
        $next_date_begin = $this->session->userdata('next_date_begin');

        $this->update_zavdannya(4); // апдейт завдань (ставиться мітка 4)

        if(isset($id_per_plan_zavd)){//функція перенесення планових(по річному плану) завдань на наступний тиждень
        foreach ($id_per_plan_zavd as $plan) {
            echo "ID plan zavd : ".$plan."<br>";
            $this->db->query("UPDATE richniy_plan_detalize
                              SET mitky = 5
                              WHERE id = ".$plan);

            $this->db->query("INSERT INTO richniy_plan_detalize (
                                            id_pl_zavd, id_user, text_detail,
                                            result_detail, d_v, d_do, chas_plan, chas_fakt,
                                            data_fakt, prymitky, mitky)
                              SELECT id_pl_zavd, id_user, text_detail, result_detail,
                                     '".$next_date_begin."' as d_v, '".$next_date_end."' as d_do, chas_plan,
                                     NULL as chas_fakt, NULL as data_fakt,
                                     prymitky, 0 as mitky
                              FROM   richniy_plan_detalize
                              where id = ".$plan);

        }
        }

        if(isset($id_per_potoch_zavd)){//функція перенесення планових(поточних) завдань на наступний тиждень
        foreach ($id_per_potoch_zavd as $pot) {
            echo "ID potoch zavd : ".$pot."<br>";
            $this->db->query("UPDATE zavdannya
                              SET mitky = 5
                              WHERE id = ".$pot);

            $this->db->query("INSERT INTO zavdannya(
                                       nazva, vud, strateg, rezult, date_begin,
                                       date_zapl_zaversh, zapl_chas, data_fakt,
                                       chas_fakt, vlasnyk, vykonavets,
                                       mitky, prymitky, date_end_povtor,
                                       id_end_povtor, zavd_zaversh)
                                SELECT nazva, vud, strateg, rezult,
                                       '".$next_date_begin."' as date_begin,
                                       '".$next_date_end."' as date_zapl_zaversh, zapl_chas, NULL as data_fakt,
                                       NULL as chas_fakt, vlasnyk, vykonavets,
                                       0 as mitky, prymitky, '0000-00-00' as date_end_povtor,
                                       NULL as id_end_povtor, 0 as zavd_zaversh
                                FROM zavdannya
                                WHERE id = ".$pot);
        }
        }


		// апдейт статуса
		if($this->input->post('period') == "now") {
			$this->update_status($this->input->post('user_id'), $this->date_begin, $this->date_end, '', 4);
		}
		// перестраховка - факт можна затвердити тільки в поточному тижні (в вюшці забрана кнопка)
		if($this->input->post('period') == "next") {
			$this->update_status($this->input->post('user_id'), $this->next_date_begin, $this->next_date_end, '', 4);
		}
		if($this->input->post('period') == "select") {
			$this->update_status($this->input->post('user_id'), $this->input->post('d_begin'), $this->input->post('d_end'), '', 4);
		}
		$this->load->model('model_admin');
		$this->model_admin->send_email(3); // Звіт затверджено !
		$this->plans();	
	}
	//допоміжна функція для апдейту міток в таблиці zavdannya
    protected function update_zavdannya($mitky) {
		$j_stroka_id_pot = json_decode($this->input->post('j_stroka_id_pot')); //print_r($j_stroka_id_pot);
		$j_stroka_id_poz = json_decode($this->input->post('j_stroka_id_poz')); //print_r($j_stroka_id_poz);
        $j_stroka_id_plan = json_decode($this->input->post('j_stroka_id_plan'));//print_r($j_stroka_id_plan);
		// зливаємо 2 масива
		$id_shki = array_merge($j_stroka_id_pot, $j_stroka_id_poz);				//print_r($id_shki);
		// апдейт завдань
		if($id_shki) {
			$this->db->where_in('id', $id_shki);
			$this->db->update('zavdannya', array('mitky' => $mitky));
		}
        if($j_stroka_id_plan) {
            $this->db->where_in('id', $j_stroka_id_plan);
            $this->db->update('richniy_plan_detalize', array('mitky' => $mitky));
        }
	}	
	//допоміжна функція апдейту періоду
    protected function update_status($user_id, $date_begin, $date_end, $comment, $flag) {
		$data = array(
			'user_id' => $user_id,
			'begin' => $date_begin,
			'end' => $date_end,
			'comment' => $comment,
			'flag' => $flag
		);
		$get_status = $this->db->get_where('status', array('user_id' => $user_id, 'begin' => $date_begin, 'end' => $date_end));
		if($get_status->result()) {
			$id = $get_status->row('id');
			$this->db->where('id', $id);
			$this->db->update('status', $data);	
		} else {
			$this->db->insert('status', $data);
		}
	}
	
	
}

