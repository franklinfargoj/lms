<?php

/**
 * Master_model Class
 *
 * @author Ashok Jadhav
 *
 */
class Notification_model extends CI_Model{
	
	/**
	 * construct
	 *
	 * initializes
	 * @author Ashok Jadhav
	 * @access private
	 * @param none
	 * @return void
	 *
	 */
	function __construct()
	{
		// Initialization of class
		parent::__construct();
	}


	/**
	 * get_admin_details
	 * Get login admin details
	 * @author Ashok Jadhav
	 * @access public
	 * @param $where
	 * @return array
	 */
	public function get_notifications($action,$select,$where,$table,$join = array(),$order_by = ''){
		if($action == 'list'){
			return $this->view($select,$where,$table,$join,$order_by);
		}else{
			return $this->counts($table,$select,$where,$join = array(),$group_by = array());
		}
	}


	public function update($where,$table,$data){
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}

	public function get_notifications_count(){
    	$input = get_session();
        $table = Tbl_Notification.' as n';
        $select= array('n.id');
        $where  = array('n.notification_to' => $input['hrms_id'],'n.is_read' => 0);
        $notifications = $this->get_notifications('count',$select,$where,$table,$join = array(),$order_by = '');
        return $notifications;
    }


	private function view($select,$where,$table,$join = array(),$order_by = array()){

		$this->db->select($select,TRUE);
		$this->db->from($table);
		if(!empty($join)){
			$this->db->join($join['table'],$join['on_condition'],$join['type']);
		}
		if(!empty($where)){
			$this->db->where($where);
		}
		if(!empty($order_by)){
			$this->db->order_by($order_by);
		}else{
			$this->db->order_by($table.'.id','DESC');
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	private function counts($table,$select,$where,$join,$group_by){
        $this->db->select($select,TRUE);
        $this->db->from($table);
        if(!empty($join)){
            foreach ($join as $key => $value) {
                $this->db->join($value['table'],$value['on_condition'],$value['type']);
            }
        }
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }
        return $this->db->count_all_results();


    }

    public function insert($table,$data){
		$response = array();
		$this->db->db_debug = FALSE; //enable debugging for queries
		$this->db->insert($table,$data);
		$errors = $this->db->error();
		if($errors['code']){
			$response['status'] = 'error';
			$response['code'] = $errors['code'];
		}else{
			$response['status'] = 'success';
			$response['insert_id'] = $this->db->insert_id();
		}
		return $response;
	}
}
