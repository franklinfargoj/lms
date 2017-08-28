<?php

/**
 * Master_model Class
 *
 * @author Ashok Jadhav
 *
 */
class Ticker_model extends CI_Model{
	
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

	#####################################
	/* FAQ Function*/
	#####################################

	/**
	 * add_record
	 * @author Ashok Jadhav
	 * @access public
	 * @param $data
	 * @return int
	 */
	public function add_record($data){
		return $this->insert(Tbl_Ticker,$data);
	}

	/**
	 * edit_record
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id,$data
	 * @return int
	 */
	public function edit_record($id,$data){
		$where['id'] = $id;
		return $this->update($where,Tbl_Ticker,$data);
	}

	/**
	 * delete_record
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return int
	 */
	public function delete_record($id){
		$where[] = $id;
		$data['is_deleted'] = 1;
		return $this->soft_delete($where,Tbl_Ticker,$data);
	}

	/**
	 * view_record
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_record($id = null,$order_by = array()){
		$select = array('id','title','description_text','created_by','status');
		$where['is_deleted'] = 0;
		if(!empty($id)){
			$where['id'] = $id;
		}
		$join = array();
		return $this->view($select,$where,Tbl_Ticker,$join,$order_by,$limit = 0);
	}

	#####################################
	/* Private Function*/
	#####################################
	public function insert($table,$data){
		$this->db->insert($table,$data);
		return $this->db->insert_id();
	}

	public function update($where,$table,$data){
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}

	public function view($select,$where,$table,$join = array(),$order_by = array(),$limit=''){

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
			/*pe($order_by);
			exit;*/
		}else{
			$this->db->order_by($table.'.id','DESC');
		}
		if(!empty($limit)){
			$this->db->limit($limit);
		}
		$query = $this->db->get();
		//pe($this->db->last_query())
		return $query->result_array();
	}

	public function soft_delete($where,$table,$data){
		$this->db->where_in('id',$where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}
	

	


	
}
