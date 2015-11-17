<?php
class Model_service_desk extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * @return PDO
     */
    private function connect(){
    $host = "10.93.1.70";
    $dbname = "ServiceDesk";
    $user = "us9467";
    $pass = "9467";
    $DBH = new PDO("sqlsrv:Server=$host;Database=$dbname;", $user, $pass);
    return $DBH;
}

    /**
     * @param $user_sd_id
     * @param null $date_begin
     * @return array
     */
    function get_sd_close_tasks($user_sd_id, $date_begin = NULL){
    header("Content-Type: text/html; charset=utf-8");

    $query = "SELECT  [WorkOrderStates].[WORKORDERID]
      ,[WorkOrderStates].[OWNERID]
      ,[WorkOrderStates].[STATUSID],
      [WorkOrder].[CREATEDTIME],
      [WorkOrder].[DUEBYTIME],
      [WorkOrder].[TITLE],
      [WorkOrder].[DESCRIPTION],
      [WorkOrderHistory].[OPERATIONTIME]

  FROM [ServiceDesk].[dbo].[WorkOrderStates] JOIN [ServiceDesk].[dbo].[WorkOrder]
       ON [WorkOrderStates].[WORKORDERID] = [WorkOrder].[WORKORDERID] JOIN [ServiceDesk].[dbo].[WorkOrderHistory]
       ON [ServiceDesk].[dbo].[WorkOrderHistory].WORKORDERID = [WorkOrder].[WORKORDERID]
  WHERE [WorkOrderStates].[OWNERID] = '".$user_sd_id."' AND STATUSID = 3 AND OPERATION = 'CLOSE'
        AND [WorkOrderHistory].[OPERATIONTIME] > '".$date_begin."'";
    $res = $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    //$res = $this->dbConnect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}
    function get_sd_open_tasks($user_sd_id, $date_begin = NULL){
        header("Content-Type: text/html; charset=utf-8");
        $query = "SELECT DISTINCT [WorkOrderStates].[WORKORDERID]
      ,[WorkOrderStates].[OWNERID]
      ,[WorkOrderStates].[STATUSID],
      [WorkOrder].[CREATEDTIME],
      [WorkOrder].[DUEBYTIME],
      [WorkOrder].[TITLE],
      [WorkOrder].[DESCRIPTION]

  FROM [ServiceDesk].[dbo].[WorkOrderStates] JOIN [ServiceDesk].[dbo].[WorkOrder]
       ON [WorkOrderStates].[WORKORDERID] = [WorkOrder].[WORKORDERID] JOIN [ServiceDesk].[dbo].[WorkOrderHistory]
       ON [ServiceDesk].[dbo].[WorkOrderHistory].WORKORDERID = [WorkOrder].[WORKORDERID]
  WHERE [WorkOrderStates].[OWNERID] = '".$user_sd_id."' AND STATUSID = 1";
        $res = $this->connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        //$res = $this->dbConnect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    /**
     * @param $user_name
     * @return mixed
     */
    function get_user_id($user_name){
        header("Content-Type: text/html; charset=utf-8");
        $query = "SELECT user_id FROM AaaLogin WHERE name ='".$user_name."'";
        $res = $this->connect()->query($query)->fetchAll(PDO::FETCH_COLUMN);
        //$res = $this->dbConnect()->query($query)->fetchAll(PDO::FETCH_COLUMN);
        return $res[0];
    }

    /**
     * @param $date_begin
     * @param $date_end
     * @param $task_type
     * @param $import_array
     * @return bool
     */
    function import_sd_task($date_begin, $date_end, $task_type, $import_array){
        //var_dump($import_array);
        $import_array['TITLE'] = iconv('UTF-8', 'cp1251',$import_array['TITLE']);
        $import_array['DESCRIPTION'] = iconv('UTF-8', 'cp1251',$import_array['DESCRIPTION']);
        $import_array['WORKORDERID'] = iconv('UTF-8', 'cp1251',$import_array['WORKORDERID']);

        $data = array(
            'nazva' => iconv('UTF-8', 'cp1251',"№").$import_array['WORKORDERID'].' : '.$import_array['TITLE'],
            'vud' => $task_type,
            'strateg' => 0,
            'rezult' => $import_array['DESCRIPTION'],
            'date_begin' => $date_begin,					// dd.mm.yy приводимо в Y-m-d
            'date_zapl_zaversh' => $date_end,	// dd.mm.yy приводимо в Y-m-d
            'zapl_chas' => 0,
            'vlasnyk' => $this->session->userdata('user_id'),
            'vykonavets' => $this->session->userdata('user_id'),
            'mitky' => 0,
            'date_end_povtor' => '',
            'sd_task_id' => $import_array['WORKORDERID']
        );
        if($task_type == 3 and isset($import_array['OPERATIONTIME'])){
            $data['data_fakt'] = Date('Y-m-d', round($import_array['OPERATIONTIME']/1000));
        }

        $this->db->select('*');
        $this->db->from('zavdannya');
        $this->db->where('sd_task_id',$import_array['WORKORDERID']);
        $this->db->where('date_begin',$date_begin );
        $this->db->where('date_zapl_zaversh',$date_end );
       $cheked = $this->db->get()->result_array();
        if($cheked){
            return 'Запис вже існує';
        }else{
            $res = $this->db->insert('zavdannya', $data);
            if($this->db->affected_rows()>0)
                return true;
            else{
                return false;
            }
        }


    }
}
?>