<?php
class Model_profile extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_zast_user($user_id){ //параметр id поточного користувача, пошук відбувається чи є поточний користувач чиїмсь секретарем
        $query = "SELECT secretary.user_id, secretary.secretar_id, users.name, users.tab_nomer FROM optimus.secretary
join optimus.users on secretary.user_id = users.id where secretary.secretar_id=".$user_id;
        $zast_users = $this->db->query($query);
        $zast_users = $zast_users->result_array();
        return $zast_users;
    }
    public function get_my_zust($user_id){ //параметр id поточного користувача, пошук відбувається чи є поточний користувач чиїмсь секретарем
        $query = "SELECT secretary.id, secretary.user_id, secretary.secretar_id, users.name, users.tab_nomer FROM optimus.secretary
join optimus.users on secretary.secretar_id = users.id where secretary.user_id=".$user_id;
        $zast_users = $this->db->query($query);
        $zast_users = $zast_users->result_array();
        return $zast_users;
    }
    /*$user_id - id замість кого затвердждуємо плани. $secretar_id - id хто може затверджувати зимість $user*/
    public function add_new_secretary($user_id, $secretar_id){//створення нового секретаря
        $data = array(
            'user_id' => $user_id,
            'secretar_id' => $secretar_id
        );
        $dublicate = $this->db->get_where('secretary', $data);
        if($dublicate->result()){
            return false;
        }else{
            $this->db->insert('secretary', $data);
            return $this->db->insert_id();
        }
    }

    /*$user_id - id замість кого затвердждуємо плани. $secretar_id - id хто може затверджувати зимість $user*/
    public function delete_secretary($id){// видалення секретаря
        $this->db->where('id', $id);
        $this->db->delete('secretary');
        if($this->db->affected_rows()>0)
            return $id;
        else{
            return false;
        }
    }
    /*$user_id - id замість кого затвердждуємо плани. $secretar_id - id хто може затверджувати зимість $user
    $id - номер запису в бд*/
    public function edit_secretary($id, $user_id, $secretar_id){
        $data = array(
            'user_id' => $user_id,
            'secretar_id' => $secretar_id
        );
        $this->db->where('id', $id);
        $this->db->update('secretary', $data);
        if($this->db->affected_rows()>0)
            return $id;
        else{
            return false;
        }
    }
    public function check_user_id($id){
        $user = $this->db->get_where('users', array('id' => $id));
        if($user->result()){
            return true;
        }else{
            return false;
        }
    }
}