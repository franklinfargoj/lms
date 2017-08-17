<?php

/**
 * Master_model Class
 *
 * @author Ashok Jadhav
 *
 */
class Master_model extends CI_Model{
	
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
	/* Product Category Function*/
	#####################################

	/**
	 * add_product_category
	 * @author Ashok Jadhav
	 * @access public
	 * @param $data
	 * @return int
	 */
	public function add_product_category($data){
		return $this->insert($table = Tbl_Category,$data);
	}

	/**
	 * edit_product_category
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id,$data
	 * @return int
	 */
	public function edit_product_category($id,$data){
		$where['id'] = $id;
		return $this->update($where,$table = Tbl_Category,$data);
	}

	/**
	 * delete_product_category
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return int
	 */
	public function delete_product_category($id){
		$where[] = $id;
		$data['is_deleted'] = 1;
		return $this->soft_delete($where,$table = Tbl_Category,$data);
	}

	/**
	 * view_product_category
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_product_category($id = null){
		$select = array('id','title','created_by');
		$where['is_deleted'] = 0;
		if(!empty($id)){
			$where['id'] = $id;
		}
		return $this->view($select,$where,$table = Tbl_Category);
	}
	

	#####################################
	/* Product Function*/
	#####################################

	/**
	 * add_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $data
	 * @return int
	 */
	public function add_product($data){
		return $this->insert($table = Tbl_Products,$data);
	}

	/**
	 * edit_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id,$data
	 * @return int
	 */
	public function edit_product($id,$data){
		$where['id'] = $id;
		return $this->update($where,$table = Tbl_Products,$data);
	}

	/**
	 * delete_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return int
	 */
	public function delete_product($id){
		$where[] = $id;
		$data['is_deleted'] = 1;
		return $this->soft_delete($where,$table = Tbl_Products,$data);
	}

	/**
	 * view_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_product($id = null){
		$select = array(Tbl_Products.'.id',Tbl_Products.'.title',Tbl_Products.'.created_by',Tbl_Category.'.title AS category');
		$where[Tbl_Products.'.is_deleted'] = 0;
		if(!empty($id)){
			$where[Tbl_Products.'.id'] = $id;
		}
		$join = array('table' => Tbl_Category,'on_condition' => Tbl_Products.'.category_id = '.Tbl_Category.'.id','type' => '');
		return $this->view($select,$where,$table = Tbl_Products , $join);
	}

	/* Private Functions*/
	private function insert($table,$data){
		$this->db->insert($table,$data);
		return $this->db->insert_id();
	}

	private function update($where,$table,$data){
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
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
				$this->db->order_by($order_by);
		$query = $this->db->get();
		return $query->result_array();
	}

	private function soft_delete($where,$table,$data){
		$this->db->where_in('id',$where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}
}
