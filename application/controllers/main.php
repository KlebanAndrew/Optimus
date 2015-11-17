<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->date_begin = $this->session->userdata('date_begin');
		$this->date_end = $this->session->userdata('date_end');
		$this->next_date_begin = $this->session->userdata('next_date_begin');
		$this->next_date_end = $this->session->userdata('next_date_end');
	}
    /*
         ������� ����������� � active directory. ����� true ��� false
    */
    public  function get_authentificate($login, $password){
        $username = $login;  // authentication login
        $password = $password;  // authentication password
/*
// jSON URL which should be requested
        $json_url = 'http://10.93.1.55/SAPServices/?task=Login&user='.$username.'&password='.$password.'&domain=ifobl';

// Initializing curl
        $ch = curl_init( $json_url );

// Configuring curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => true
        );
// Setting curl options
        curl_setopt_array( $ch, $options );
        */
        //test post
        $data = array("domain" => "ifobl", "user" => $username , "password" => $password);
        $data_string = json_encode($data);
        $json_url = 'http://10.93.1.55/SAPServices/?task=Login';
        $ch = curl_init( $json_url );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
// Getting results
        $result = curl_exec($ch); // Getting jSON result string
        $result = json_decode($result, true);
        if($result['authentic']){
            return true; //if success
        }
        else{
            return false;//if not success
        }
    }
    /* ������� ����� URL ���������. �������� �������� ������� ���. ��������� �����������(��������� ���). ��������� �������� ���

    */
    function _remap($method) {
        $login=$this->input->post('user_login'); //���������� ����� � ������ POST
        $password=$this->input->post('user_pass');
        $pars = $this->uri->segment_array(); // $pars[1]-����������; $pars[2]-�����; $pars[3]-���������
        if($method=="exit_user") {//��������� ���
            $this->session->unset_userdata('user_id');		
            $this->session->unset_userdata('user_login');            
			$this->session->unset_userdata('user_name');
			$this->session->unset_userdata('permission');
			$this->session->unset_userdata('date_begin');
			$this->session->unset_userdata('date_end');
            $this->session->unset_userdata('main_user_id');//�������� ��� ���������
            $this->session->unset_userdata('main_user_name');//�������� ��� ���������
            $this->session->unset_userdata('main_user_login');//�������� ��� ���������
            $this->session->unset_userdata('zast_user');
			$this->login('�� ������ !');
        } else {
            if($this->session->userdata('user_login'))  {//�������� �� ���� �������������
				if(@$pars[2]=='' and @$pars[3]=='') { $this->index(); } else { $this->$pars[2](@$pars[3]); }//���� �� ������ ����� � ��������� �� ��������� �� index()
            } else {                                                                                           //���� ��������� � ����� � ��������� ��������
                if(!empty($login) and !empty($password)) {//������� �����������
                    $auth = $this->get_authentificate($login, $password);
                    //var_dump($auth);
                   // exit;
                   // $password = md5($password);
					//$login_in = $this->db->get_where('users', array('login' => $login, 'pass' => $password ));
					//$login_in = $this->db->get_where('users', array('id' => 14 ));
                    $login_in = $this->db->get_where('users', array('login' => $login));
                   // if($login_in->result() and $auth) {//��������� ���
                    if($auth and $login_in->result()) {//��������� ���
                        $this->load->model('model_admin');//������ ������ � ��
                        $this->load->model('model_profile');//������ ������ � ��������������
						$user = $login_in->row();
						$this->user_id = $user->id; // 53; //$user->id;					
					    $this->session->set_userdata('user_id', $user->id); // 53; // $user->id;					
                        $this->session->set_userdata('user_login', $user->login);
						$this->session->set_userdata('user_name', $user->name);
						$this->session->set_userdata('permission', $user->perm);
                        $this->session->set_userdata('main_user_id', $user->id);//�������� ��� ���������
                        $this->session->set_userdata('main_user_name', $user->name);//�������� ��� ���������
                        $this->session->set_userdata('main_user_login', $user->login);//�������� ��� ���������
                        $this->session->set_userdata('zast_user' ,$this->model_profile->get_zast_user($user->id));//����� ��� ��� ��������� �����
						$this->get_date();
                        $this->$pars[2](@$pars[3]);
                        redirect('/main/index/', 'refresh');
					} else {//����������� ��� �������
                        $this->login('������ �������� ���� ��� ������ !');
                    }
				} else {
					$this->login('');
                }
            }
        }
    }
    /* ����� ������� ������� �����������*/
    function login($message) {
        $this->load->helper('form');
        $data['message']=$message;
		$this->load->view('login/login', $data);
	}
    /* ������� ���������� ���� � ������ �������� ����� � ����*/
    function get_date() {
		$date = date('Y-m-d');//���������� ����
        /*date_begin �������� ��������� �����
        date_end ������� ��������� �����
        next_date_begin, next_date_end ��������� ��� ���������� �����
        */
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
		/*
		$this->session->set_userdata('date_begin', '2014-03-17');
		$this->session->set_userdata('date_end', '2014-03-21');
		$this->session->set_userdata('next_date_begin', '2014-03-24');
		$this->session->set_userdata('next_date_end', '2014-03-28');		
		*/
		$this->date_begin = $this->session->userdata('date_begin');
		$this->date_end = $this->session->userdata('date_end');
		$this->next_date_begin = $this->session->userdata('next_date_begin');
		$this->next_date_end = $this->session->userdata('next_date_end');
	}
    /*������� ��������� �������� �������� ��� ��������� ���� ����������*/
    protected function get_plan_zavd($date_begin, $date_end) {
		/*
		$query1 = "
		SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, chas_fakt, richniy_plan_detalize.id AS uniq, IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title FROM richniy_plan
			LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
			d_v  >= '".$date_begin."' AND 
			d_do <= '".$date_end."' AND 
			richniy_plan_detalize.id_user = ".$this->user_id."
			WHERE richniy_plan.users LIKE '%,".$this->user_id.",%' AND 
			plan_vid <= '".$date_begin."' AND
			plan_do >= '".$date_end."'";
		*/
		$query1 = "			
		 SELECT richniy_plan.id, detalize, d_v, d_do, chas_plan, chas_fakt, data_fakt, result_detail,
		        richniy_plan_detalize.mitky, richniy_plan_detalize.id AS uniq,
		        IFNULL (CONCAT(nazva, ':', text_detail), nazva) AS title
		 FROM richniy_plan
            LEFT JOIN richniy_plan_detalize ON richniy_plan.id = richniy_plan_detalize.id_pl_zavd AND
            d_do >= '".$date_begin."' AND 
            d_v  <= '".$date_end."' AND 
            richniy_plan_detalize.id_user = ".$this->user_id."
            WHERE richniy_plan.users LIKE '%,".$this->user_id.",%' AND 
            plan_do  >= '".$date_begin."' AND
            plan_vid <= '".$date_end."'";
		$res_planovi = $this->db->query($query1);
		return $res_planovi->result_array();
	}
    /*����� ��������� ���� ��������(������ �������� �� �������)*/
	public function index() {

		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);

        //����� � �� �������� �������
 		$res_potocni = $this->db->query("SELECT * FROM zavdannya
 		                                 WHERE vlasnyk = '$this->user_id'
 		                                    AND vud= '2' AND date_zapl_zaversh >= '".$this->date_begin."'
 		                                    AND  date_begin <= '".$this->date_end."'
 		                                 ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();
        //����� � �� ������������ �������
 		$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya
 		                                      WHERE vlasnyk = '$this->user_id'
 		                                        AND vud= '3'
 		                                        AND date_zapl_zaversh >= '".$this->date_begin."'
 		                                        AND  date_begin <= '".$this->date_end."'
 		                                      ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// ��� ���������� �������� �� ���
		$data['d_do'] = $this->date_end;
		
		$status = $this->db->get_where('status', array('user_id' => $this->user_id, 'begin' => $this->date_begin, 'end' => $this->date_end));
		$data['status'] = $status->row();
		
		// ���������� ����� ��� �������. ����������� � ���� �� ��������� ���� ������� ������
		$data['product_posadu'] = $this->db->get_where('users', array('id' => $this->user_id ))->row('product_posadu');

		$this->load->view('front/index', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /* ����� ��������� ���� ��������(������ �������� �� ��������� �������)*/
	public function plan_next() {
		$this->date_begin = $this->session->userdata('next_date_begin');
		$this->date_end = $this->session->userdata('next_date_end');
	
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);
        //����� � �� �������� �������
 		$res_potocni = $this->db->query("SELECT * FROM zavdannya
 		                                 WHERE vlasnyk = '$this->user_id'
 		                                    AND vud= '2'
 		                                    AND date_zapl_zaversh >= '".$this->date_begin."'
 		                                    AND  date_begin <= '".$this->date_end."'
 		                                 ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();
        //����� � �� ������������ �������
 		$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya
 		                                      WHERE vlasnyk = '$this->user_id'
 		                                        AND vud= '3'
 		                                        AND date_zapl_zaversh >= '".$this->date_begin."'
 		                                        AND  date_begin <= '".$this->date_end."'
 		                                      ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// ��� ���������� �������� �� ���
		$data['d_do'] = $this->date_end;
		
		$status = $this->db->get_where('status', array('user_id' => $this->user_id, 'begin' => $this->date_begin, 'end' => $this->date_end));
		$data['status'] = $status->row();
		
		$this->load->view('front/plan_next', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}


    /*����� ������� ������� ��������� ������� ��������� ����� */
	public function create_zavd() {
		$this->load->helper('form');
		$data['d_v'] = $this->session->userdata('date_begin');
		$data['d_do'] = $this->session->userdata('date_end');
		$data['next_week'] = 0; // ���� ���������� �����
		// ���� ���������� ����� 1,2,3,4 ������ ��������� ��������
		$get_status = $this->db->query("SELECT * FROM (`status`)
		                                WHERE `user_id` = '$this->user_id'
		                                    AND `begin` = '$this->date_begin'
		                                    AND `end` = '$this->date_end'
		                                    AND `flag` IN (1,2,3,4)");
		if($get_status->result()) {
			$data['lock_potochni'] = 1;
		} else {
			$data['lock_potochni'] = 0;
		}
		$this->load->view('front/create_zavd', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /*����� ������� ������� ��������� ������� ���������� �����*/
	public function create_zavd_plan() {
		$this->load->helper('form');
		$data['d_v'] = $this->session->userdata('next_date_begin');
		$data['d_do'] = $this->session->userdata('next_date_end');
		$data['next_week'] = 1; // ���� ���������� �����
		$data['lock_potochni'] = 0; // �� ������ ������
		$this->load->view('front/create_zavd', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /*������� �������� � ���� ����� ������ � ��������� ���������*/
	public function create_zavd_true() {
		$this->load->model('model_main');
		$this->model_main->create_zavd_true();
		//$this->index();
		if($this->input->post('next_week') == "1") {
			header("Location: ".site_url("main/plan_next"));
		} else {
			header("Location: ".site_url("main/index"));
		}
	}
    /*������� ����������� �������� ��  id ����� ��������*/
	public function edit_zavd($id) {
		$zavdannya = $this->db->get_where('zavdannya', array('id' => $id))->row();
		if(!$zavdannya) { echo "�������� �������� !"; return; }
		if($zavdannya->vlasnyk != $this->session->userdata('user_id')) { echo "�� �� ���� �������� !"; return; }
		if($zavdannya->mitky == 1 or $zavdannya->mitky == 2 or $zavdannya->mitky == 3 or $zavdannya->mitky == 4) { echo "�������� ����������� !"; return; }
		$data['zavdannya'] = $zavdannya;
		$this->load->helper('form');
		$this->load->view('front/edit_zavd', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /*����������� ������� ������ ����� (�������� �� ����������� ����� ������� ������ �����)*/
	public function edit_plan_zavd($id) {
		$richniy_plan_detalize = $this->db->get_where('richniy_plan_detalize', array('id' => $id))->row();
		if(!$richniy_plan_detalize) { echo "�������� �������� !"; return; }
		if($richniy_plan_detalize->id_user != $this->session->userdata('user_id')) { echo "�� �� ���� �������� !"; return; }
		$data['richniy_plan_detalize'] = $richniy_plan_detalize;
		$this->load->helper('form');
		$this->load->view('front/edit_plan_zavd', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    //���� ������� ��� ��������� �������� � ���� ����� richniy_plan_detalize
    //=============================================================================================
    public function delete_plan_zavd($id) {
        $this->db->query("DELETE FROM richniy_plan_detalize
                          WHERE id = ".$id."
                            AND id_user = ".$this->session->userdata('user_id')." LIMIT 1");
        redirect($_SERVER['HTTP_REFERER']);
        //header("Location: ".site_url("main/index"));

    }
//======================================================================================================
    /*����������� �������(������� ���������� update ������� � �� (������� zavdannya))*/
	public function edit_zavd_true() {
		$this->load->model('model_main');
		$this->model_main->edit_zavd_true();	
		// ������� ��� ���������
		if(date('Y-m-d', strtotime($this->input->post('date_begin'))) >= $this->next_date_begin) {
			header("Location: ".site_url("main/plan_next"));		
		} else {
			header("Location: ".site_url("main/index"));
		}
	}
    /*����������� �������(������� ���������� update ������� � �� (������� richniy_plan_detalize))*/
	public function edit_plan_zavd_true() {
		$this->load->model('model_main');
		$this->model_main->edit_plan_zavd_true();	
		// ������� ��� ���������
		if(date('Y-m-d', strtotime($this->input->post('d_v'))) >= $this->next_date_begin) {
			header("Location: ".site_url("main/plan_next"));		
		} else {
			header("Location: ".site_url("main/index"));
		}
	}
    /*�������� �������(��������)*/
	public function pereglad_zavd($id) {
		$this->db->select('*');
		$this->db->from('zavdannya');
		$this->db->join('users', 'users.id = zavdannya.vlasnyk', 'left');
		$this->db->where('zavdannya.id', $id);
		$zavdannya = $this->db->get();
		$zavdannya = $zavdannya->row();
		if(!$zavdannya) { echo "�������� �������� !"; return; }
		if($zavdannya->vlasnyk != $this->session->userdata('user_id')) { echo "�� �� ���� �������� !"; return; }		
		$data['zavdannya'] = $zavdannya;
		$data['repeat'] = $this->db->get_where('zavdannya', array('id_end_povtor' => $id));
		$this->load->view('front/pereglad_zavd', $data);
	}
    /*�������� ��������(������� ��������� ��������� ��� ������������� �������� � ������� zavdannya)*/
	public function delete_zavd($id) {
		$this->db->query("DELETE FROM zavdannya
		                  WHERE id = ".$id."
		                    AND vlasnyk = ".$this->session->userdata('user_id')." LIMIT 1");
		header("Location: ".site_url("main/index"));
	}
    /*������� ���������� ��������� ������� �� (���������� ��������� �������� (� ������ �����)) id*/
	public function detalize($id) {
		$richniy_plan = $this->db->get_where('richniy_plan', array('id' => $id));
		$data['title'] = $richniy_plan->row('nazva');
		//$data['main'] = $this->db->get_where('richniy_plan_detalize', array('id_pl_zavd' => $id));
		//$data['main'] = $this->db->from('richniy_plan_detalize')->where('id_pl_zavd = '.$id.' AND id_user = '.$this->user_id)->order_by('id', 'ASC')->get();
		//����� ����� ��� id_user=�������� ����. ����� �����������
        $data['main'] = $this->db->from('richniy_plan_detalize')->where('id_pl_zavd = '.$id)->order_by('id', 'ASC')->get();
		$data['id_pl_zavd'] = $richniy_plan->row('id');
		$users = $richniy_plan->row('users');
		$users = substr($users,1,-1);
		$res = $this->db->query("SELECT * FROM users WHERE id IN (".$users.")");
		$data['users'] = $res->result_array();
        $data['user_id'] = $this->session->userdata('user_id');
		$this->load->helper('form');
		$this->load->view('front/detalize', $data);
       //============================================= debug

		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /*������� ����������  ������� � (���������� ��������� �������� (� ������ �����)) id*/
	public function detalize_add() {
		$id_pl_zavd=$this->input->post('id_pl_zavd');	
		$id_user=$this->session->userdata('user_id');
		//$date=$this->input->post('date');
		$date_begin = date('Y-m-d', strtotime($this->input->post('date_begin')));
        $date_end = date('Y-m-d', strtotime($this->input->post('date_end')));// dd.mm.yy ��������� � Y-m-d
		mysql_query("SET NAMES 'utf8'");
 		//$res_dates = $this->db->query("SELECT DATE_ADD('$date', INTERVAL (2-DAYOFWEEK('$date')) DAY) AS d_begin, DATE_ADD('$date', INTERVAL (6-DAYOFWEEK('$date')) DAY) AS d_end");
      //  $res_dates = $this->db->query("SELECT DATE_ADD('$date', INTERVAL (2-DAYOFWEEK('$date')) DAY) AS d_begin, '$date' AS d_end"); //����� ����� � �� � ������� ����� ��� ������ ����������
		//$dates = $res_dates->row();
		// �������� �� � ��� ����� ����� (��� �������� ���������)
		$query = $this->db->get_where('richniy_plan_detalize', array('id_pl_zavd' => $id_pl_zavd, 'id_user' => $id_user, 'd_v' => $date_begin, 'd_do' => $date_end));
		$zavdannya = $query->row();
		if(!$zavdannya) { 
			echo "��������� !"; 
			$data = array(
				'id_pl_zavd' => $id_pl_zavd,
				'id_user' => $id_user,
				'text_detail' => $this->input->post('text_detail'),
				'result_detail' => $this->input->post('result_detail'),
				'd_v' => $date_begin,
				'd_do' => $date_end,
				'chas_plan' => $this->input->post('chas_plan'),
                'mitky' => 0
			);
			$this->db->insert('richniy_plan_detalize', $data);
		} else { 
			echo '��� ����� ��� ���������� !'; 
		}	
			
	}
	/*������� ��������� (�����������) ������� ������ �����*/
	public function detalize_update() {
        //var_dump($_POST);
        $id_zavd = $this->input->post('id_zavd');
        $id_pl_zavd=$this->input->post('id_pl_zavd');
        $date_begin = $this->input->post('date_begin');
        $date_end = $this->input->post('date_end');
        mysql_query("SET NAMES 'utf8'");

        $query = $this->db->get_where('richniy_plan_detalize', array('id_pl_zavd' => $id_pl_zavd, 'id' => $id_zavd));
        $zavdannya = $query->row();
        if($zavdannya) {
            $data = array(
                'id_pl_zavd' => $id_pl_zavd,
                'text_detail' => $this->input->post('text_detail'),
                'result_detail' => $this->input->post('result_detail'),
                'd_v' => $date_begin,
                'd_do' => $date_end,
                'chas_plan' => $this->input->post('chas_plan')
            );
            $this->db->where('id', $id_zavd);
            $this->db->update('richniy_plan_detalize', $data);
            echo "���� ��������� !";
        } else {
            echo '������ �������� �� ����';
        }

    }
// ���������
	public function report() {
		$data['planovi_zavd']=$this->get_plan_zavd($this->date_begin, $this->date_end);
		
 		$res_potocni = $this->db->query("SELECT *
 		FROM zavdannya
 		WHERE vlasnyk = '$this->user_id'
 		    AND vud= '2'
 		    AND date_zapl_zaversh >= '".$this->date_begin."'
 		    AND  date_begin <= '".$this->date_end."'
 		ORDER BY id ");
		$data['potocni_zavd']=$res_potocni->result_array();

 		$res_pozachergovi = $this->db->query("SELECT *
 		FROM zavdannya
 		WHERE vlasnyk = '$this->user_id'
 		    AND vud= '3'
 		    AND date_zapl_zaversh >= '".$this->date_begin."'
 		    AND  date_begin <= '".$this->date_end."'
 		ORDER BY id ");
		$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
	
		$data['title_dates'] = date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end));
		$data['d_v'] = $this->date_begin;		// ��� ���������� �������� �� ���
		$data['d_do'] = $this->date_end;
		$this->load->view('front/report', $data);
		//$this->output->enable_profiler(TRUE);	// ���������
	}
    /*������� �������� �������� ������� (��������� �������� �� ����������� ����������� ��� ���� �����������
        �������� ������� ������� 2 ���������: ���� 0 - �������� ������� �����������, ���� ����� �������� �����������,
    ����� ����� - ���� 6 - ���������� ���� ��� �������� ��������, �������� ��������� ������)*/
	public function close_old_task(){ //�������� ������������ ���� 2 � ������������ ���� zavd_zaversh = 2(��� �������� ����������)
        $id = $this->input->post('id_zavd');
        if($id) {
            $data = array(
                'mitky' => 2,
                'zavd_zaversh' => 2
            );
            $this->db->where('id', $id);
            $this->db->update('zavdannya', $data);
            echo "���� ��������� !";
        }
        echo $id.' - �������� �������';
    }
    /*�������  ������������ ���������� ��������� ��� �������. */
	public function reporting() { //������ ��������, ������� ������ �� � �������� ����� ������ � ��


 		//$res = $this->db->query("SELECT * FROM zavdannya WHERE vlasnyk = '$this->user_id' AND vud = 2 AND mitky IN (0, 1) AND date_zapl_zaversh >= '".$this->date_begin."' AND  date_begin <= '".$this->date_end."'");		
 		$res = $this->db->query(        "SELECT *
 		    FROM zavdannya
 		    WHERE vlasnyk = '$this->user_id'
 		        AND vud = 2
 		        AND mitky IN (0, 1)
 		        AND date_zapl_zaversh >= '".$this->input->post('d_v')."'
 		        AND date_begin <= '".$this->input->post('d_do')."'");

        if($res->result()) {
            $data['zavd'] = $res->result_array();
			$data['message'] = " � ��� � ������������ �������� ! (��������� ����)";
            $data['j_stroka_id_pot'] = $this->input->post('j_stroka_id_pot');
            $data['j_stroka_id_poz'] = $this->input->post('j_stroka_id_poz');
			$this->load->view('front/message', $data);
		} else {

//  ������ � $_POST[data_fakt] �� $_POST['planovi_data_fakt'] �������
            if($this->input->post('data_fakt')){
			foreach($_POST['data_fakt'] as $k=>$v) {//����� ���� �� ������ data_fakt... ����� ���� �����?
				//echo $k.' = '.$v.'<br>';
				//$this->db->query("UPDATE zavdannya SET data_fakt = '".$v."', chas_fakt = '".$_POST['chas_fakt'][$k]."', mitky = 3 WHERE id = ".$k." LIMIT 1");
				//$this->db->query("UPDATE zavdannya SET data_fakt = '".date('Y-m-d', strtotime($v))."', chas_fakt = '".$_POST['chas_fakt'][$k]."', mitky = 3 WHERE id = ".$k." LIMIT 1");

                if(@$_POST['zavd_zaversh'][$k] == "on") { $zavd_zaversh = 1; } else { $zavd_zaversh = 0; }
				$this->db->query("UPDATE zavdannya
				    SET data_fakt = '".date('Y-m-d', strtotime($v))."',
                        chas_fakt = '".$_POST['chas_fakt'][$k]."',
                        mitky = 3,
                        zavd_zaversh = '".$zavd_zaversh."'
				    WHERE id = ".$k." LIMIT 1");
			}
            }
			// ������ �������� �������
			if($this->input->post('planovi_chas_fakt')) {
				foreach($_POST['planovi_chas_fakt'] as $k=>$v) {
					//$this->db->query("UPDATE richniy_plan_detalize SET chas_fakt = '".$_POST['planovi_chas_fakt'][$k]."', data_fakt = '".$_POST['planovi_data_fakt'][$k]."' WHERE id = ".$k." LIMIT 1");
                    if(@$_POST['plan_zavd_zaversh'][$k] == "on") { $plan_zavd_zaversh = 1; } else { $plan_zavd_zaversh = 0; }
                    $this->db->query("UPDATE richniy_plan_detalize
					    SET chas_fakt = '".$_POST['planovi_chas_fakt'][$k]."',
					        data_fakt = '".date('Y-m-d', strtotime($_POST['planovi_data_fakt'][$k]))."',
					        task_end = '".$plan_zavd_zaversh."'
					    WHERE id = ".$k." LIMIT 1");
				}
			}

			$data['message'] = "��� �������� !";
			// $this->update_status($this->user_id, $this->date_begin, $this->date_end, '�� ����������� �����', 3);
			// ���� ������ � �����
			$this->update_status($this->user_id, $this->input->post('d_v'), $this->input->post('d_do'), '��� ������ �� �������� ������������', 3);
			$this->send_email();
			header("Location: ".site_url("main/index"));
		}
		//$this->output->enable_profiler(TRUE);	// ���������
	}
	
// ��������� �� ��������� �������
	public function report_date() {
        //var_dump($_POST);
		$get_status = $this->db->get_where('status', array('user_id' => $this->user_id, 'begin' => $this->input->post('date_vid'), 'end' => $this->input->post('date_do')))->row('flag');
		if($get_status != 4) {
			$j_stroka_pot = $this->input->post('j_stroka_pot');
			$j_stroka_poz = $this->input->post('j_stroka_poz');
			$date_vid = $this->input->post('date_vid');
			$date_do = $this->input->post('date_do');
			$potocni = str_replace("[", "", $j_stroka_pot);
			$potocni = str_replace("]", "", $potocni);
			$pozachergovi = str_replace("[", "", $j_stroka_poz);
			$pozachergovi = str_replace("]", "", $pozachergovi);

			$data['planovi_zavd']=$this->get_plan_zavd($date_vid, $date_do);
			if($potocni != '') {
				$res_potocni = $this->db->query("SELECT * FROM zavdannya WHERE id IN (".$potocni.")");
				$data['potocni_zavd']=$res_potocni->result_array();
			} else {
				$data['potocni_zavd']='';		
			}
			if($pozachergovi != '') {
				$res_pozachergovi = $this->db->query("SELECT * FROM zavdannya WHERE id IN (".$pozachergovi.")");
				$data['pozachergovi_zavd']=$res_pozachergovi->result_array();
			} else {
				$data['pozachergovi_zavd']='';
			}
            $data['j_stroka_pot'] = $j_stroka_pot ;
            $data['j_stroka_poz'] = $j_stroka_poz ;
			$data['title_dates'] = date('d.m.Y', strtotime($date_vid)).' - '.date('d.m.Y', strtotime($date_do));
			$data['d_v'] = $date_vid;
			$data['d_do'] = $date_do;
			
			$this->load->view('front/report', $data);
			//$this->output->enable_profiler(TRUE);	// ���������
		} else {
			$data['message'] = "��� ��� ����������� !!!";
			$this->load->view('front/message', $data);		
		}
	}	


	// ����������
	public function faq() {
		$this->load->view('front/faq');
	}
    //������������
    public function settings(){
        $this->load->model('model_profile');
        $this->db->select('id, name, tab_nomer')->
            from('users')->
                where('id !=',$this->session->userdata('main_user_id'))->
                where('deleted', 0)->
            order_by('name', 'ASC');
        $data['users'] = $this->db->get()->result_array();
        $data['user'] = $this->db->get_where('users', array('id' => $this->session->userdata('main_user_id')))->row();
        $main_user_id = $this->session->userdata('main_user_id');
        $data['zast_user'] = $this->model_profile->get_my_zust($main_user_id);
        $this->load->view('front/settings', $data);
    }

// �����
	public function edit_user() {
		$data['user'] = $this->db->get_where('users', array('id' => $this->user_id ))->row();
		$this->load->view('front/edit_user', $data);
	}

	function user_update() {
		$data = array('product_posadu' => $this->input->post('product_posadu'));
		$this->db->where('id', $this->user_id);
		$this->db->update('users', $data);
		header("Location: ".site_url("main/index"));
	}



// �������� ������� � ����������� admin
    protected function update_status($user_id, $date_begin, $date_end, $comment, $flag) {
        $approve_time = date('Y-m-d G:i:s');
        $report_time = date('Y-m-d G:i:s');
		$data = array(
			'user_id' => $user_id,
			'begin' => $date_begin,
			'end' => $date_end,
			'comment' => $comment,
			'flag' => $flag
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

// г���� ����-------------------------------------------------------------------
	public function year_plan() {
		$this->load->model('model_admin');
        $data['plan'] = $this->db->from('richniy_plan')->like('users', ','.$this->user_id.',')->order_by('id', 'ASC')->get();
		$data['users'] = $this->db->where('id !=', 1)->from('users')->order_by('name', 'ASC')->get();
        $data['flag'] = 'my'; // flag
        $data['vlasnyk'] = $this->session->userdata('user_id');
        $plan_vlasnyk = $this->db->query("SELECT * FROM richniy_plan WHERE vlasnyk=".$this->session->userdata('user_id')."");
        $data['plan_vlasnyk']=$plan_vlasnyk->result_array();
		$this->load->view('front/year_plan', $data);

		//$this->output->enable_profiler(TRUE);	// ���������
	}

	public function year_plan_all() {
		$this->load->model('model_admin');
		$data['plan'] = $this->db->get('richniy_plan');
		$data['users'] = $this->db->from('users')->order_by('name', 'ASC')->get();
        $data['vlasnyk'] = $this->session->userdata('user_id');
		$data['flag'] = 'all'; // flag
		$this->load->view('front/year_plan', $data);

		//$this->output->enable_profiler(TRUE);	// ���������
	}

	public function ajax_add_to_richniy_plan() {
		mysql_query("SET NAMES 'utf8'");
		$users=$this->input->post('users');
		$str='';
		for($i=0; $i<count($users); $i++) {
			$str.=$users[$i].',';
		}
		if($users == '') { $str = $this->session->userdata('user_id').','; } // ���� ����� ���� �� ��������, ������� ���� id
		$str=','.$str;
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'nom_proces' => $this->input->post('nom_proces'),
			'users' => $str,
			'detalize' => 1, // $this->input->post('detalize'),
			'plan_vid' => date('Y-m-d', strtotime($this->input->post('plan_vid'))),
			'plan_do' =>  date('Y-m-d', strtotime($this->input->post('plan_do'))),
            'vlasnyk' => $this->input->post('vlasnyk')
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
		if($users == '') { $str = $this->session->userdata('user_id').','; } // ���� ����� ���� �� ��������, ������� ���� id
		$str=','.$str;
		$data = array(
			'nazva' => $this->input->post('nazva'),
			'nom_proces' => $this->input->post('nom_proces'),
			'users' => $str,
			'detalize' => 1, // $this->input->post('detalize'),
			'plan_vid' => date('Y-m-d', strtotime($this->input->post('plan_vid'))),
			'plan_do' =>  date('Y-m-d', strtotime($this->input->post('plan_do')))
		);
		$this->db->where('id', $id);
		$this->db->update('richniy_plan', $data);
	}

	public function ajax_select_pidlegli() {
		if($this->input->post('user') == 'none') {
			$user_pidlegli = $this->db->get_where('derevo', array('idUserParent' => $this->user_id));
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
				echo '<option value="'.$this->user_id.'">'.$this->session->userdata('user_name').'</option>';
			}
		}
		if($this->input->post('user') == 'all') {
			$users = $this->db->where('id !=', 1)->from('users')->order_by('name', 'ASC')->get();
			foreach ($users->result() as $user) { echo '<option value="'.$user->id.'">'.$user->name.'</option>'; }
		}
	}

	public function ajax_del_from_richniy_plan() {
		$user_id = $this->session->userdata('user_id');
		$id_zavd = $this->input->post('id_zavd');
		$zavd = $this->db->get_where('richniy_plan', array('id' => $id_zavd))->row();

		// �������� ������ �� ���� � �����
		$pieces = explode(",", $zavd->users);
		// �������� ������ � ������� ��������
		array_shift($pieces);
		array_pop($pieces);
		// print_r($pieces);
		// ��������� ���� id
		$key = array_search($user_id, $pieces);
		unset($pieces[$key]);
		// print_r($pieces);
		$str='';
		for($i=0; $i<=count($pieces); $i++) {
			if(isset($pieces[$i])) {
				$str.=$pieces[$i].',';
			}
		}
		$str=','.$str;
		echo $str;

		// ������ � ����
		$data = array('users' => $str);
		$this->db->where('id', $id_zavd);
		$this->db->update('richniy_plan', $data);
	}

// ����������� �-��� �������� �����
	public function send_email() {
		$user_id=$this->session->userdata('user_id');
		$adresat = $this->db->query("SELECT rr.name, rr.email, users.name AS my_name, users.email AS my_email
		    FROM derevo
		        LEFT JOIN users ON derevo.idUser = users.id
		        LEFT JOIN users AS rr ON derevo.idUserParent = rr.id
		    WHERE users.id = ".$user_id)->row();
		$this->load->library('email');
		$config['mailtype'] = 'html';				// �������� � html
		$this->email->initialize($config);
		$this->email->to($adresat->email); 			// 'Yuriy.Chopey@oe.if.ua'
		$this->email->from('info@optimus.com');
		$this->email->subject('��� �� ������������ �� '.$adresat->my_name);
		$this->email->message('��������� ��� ��� ��������� '.$adresat->my_name.' ('.$adresat->my_email.') �� '.date("d.m.Y"));
		$this->email->send();
	}

    public function add_service_desk_tasks(){
        $this->load->model('model_service_desk');
        $this->load->model('model_main');
        $date_begin=$this->input->post('date_begin');
        $date_end=$this->input->post('date_end');
        $user_id = $this->session->userdata('user_id');
        $res =$this->model_service_desk->import_tasks($this->date_begin,$this->date_end, 3);
       // echo '<pre>';
        //print_r($res);
       // echo '</pre>';
        $import = $this->model_main->import_task($this->date_begin,$this->date_end,0,$res);
        echo $import;

    }

	
}

