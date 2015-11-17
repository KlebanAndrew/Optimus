<?php
class Model_report extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    /*функція вибірки періодів для конкретного юзера
    *   @Param
     *  $user_id - id юзера для якого проводиться вибірка
     * $date_begin,  $date_end - початок і кінець періоду для вибірки
    */

    function get_user_period_status($user_id, $date_begin, $date_end){
        //Query #1 вибірка крайнього тижня періоду
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id',$user_id);
        $this->db->where('deleted',1);
        $deleted = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('status');
        $this->db->where('user_id',$user_id);
        $this->db->where('begin <',$date_begin);
        $this->db->where('end >=',$date_begin);
        $res1 = $this->db->get()->result_array();

        //Query #2 - віибірка всіх тижнів які входять в період повністю

        $this->db->select('*');
        $this->db->from('status');
        $this->db->where('user_id',$user_id);
        $this->db->where('begin >=',$date_begin);
        $this->db->where('end <=',$date_end);
        $res2 = $this->db->get()->result_array();

        //Query #3  вибірка крайнього тижня періоду

        $this->db->select('*');
        $this->db->from('status');
        $this->db->where('user_id',$user_id);
        $this->db->where('begin <=',$date_end);
        $this->db->where('end >',$date_end);
        $res3 = $this->db->get()->result_array();

        //All

        $res = array_merge($res1, $res2,$res3);
        if($res and !$deleted){
            return $res;
        }
        else{
            return false;
        }



    }
    /*функція вибірки періодів для створення еталонної шапки(вибірка всіх існуючих тижнів періоду)
        *   @Param
         *
         * $date_begin,  $date_end - початок і кінець періоду для вибірки
        */
    function get_period_weeks($date_begin, $date_end){
        $this->db->distinct();
        $this->db->select('begin, end');
        $this->db->from('status');
        $this->db->where('begin <',$date_begin);
        $this->db->where('end >=',$date_begin);
        $res1 = $this->db->get()->result_array();

        //Query #2 - віибірка всіх тижнів які входять в період повністю

        $this->db->distinct();
        $this->db->select('begin, end');
        $this->db->from('status');
        $this->db->where('begin >=',$date_begin);
        $this->db->where('end <=',$date_end);
        $res2 = $this->db->get()->result_array();

        //Query #3  вибірка крайнього тижня періоду

        $this->db->distinct();
        $this->db->select('begin, end');
        $this->db->from('status');
        $this->db->where('begin <=',$date_end);
        $this->db->where('end >',$date_end);
        $res3 = $this->db->get()->result_array();

        //All
        $res = array_merge($res1, $res2,$res3);
        if($res){
            function cb($a, $b) {
                return strtotime($a['begin']) - strtotime($b['begin']);
            }
            usort($res, 'cb');
            return $res;
        }
        else{
            return false;
        }

    }

}
?>