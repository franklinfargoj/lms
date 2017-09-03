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
		return $this->insert(Tbl_Category,$data);
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
		return $this->update($where,Tbl_Category,$data);
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
		return $this->soft_delete($where,Tbl_Category,$data);
	}

	/**
	 * view_product_category
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_product_category($id = null,$order_by = array()){
		$select = array('id','title','created_by','status');
		$where['is_deleted'] = 0;
		if(!empty($id)){
			$where['id'] = $id;
		}
		$join = array();
		return $this->view($select,$where,Tbl_Category,$join,$order_by);
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
		return $this->insert(Tbl_Products,$data);
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
		return $this->update($where,Tbl_Products,$data);
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
		return $this->soft_delete($where,Tbl_Products,$data);
	}

	/**
	 * view_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_product($id = null,$where = array(),$order_by = array()){
		$select = array(Tbl_Products.'.id',Tbl_Products.'.title',Tbl_Products.'.default_assign',Tbl_Products.'.created_by',Tbl_Products.'.status',Tbl_Products.'.turn_around_time',Tbl_Category.'.title AS category','category_id');
		$where[Tbl_Products.'.is_deleted'] = 0;
		if(!empty($id)){
			$where[Tbl_Products.'.id'] = $id;
		}
		$join = array('table' => Tbl_Category,'on_condition' => Tbl_Products.'.category_id = '.Tbl_Category.'.id','type' => '');
		return $this->view($select,$where,Tbl_Products,$join,$order_by);
	}

	#####################################
	/* Product Guide */
	#####################################

	/**
	 * add_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $data
	 * @return int
	 */
	public function add_product_guide($data){
		return $this->insert(Tbl_ProductDetails,$data);
	}

	/**
	 * edit_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id,$data
	 * @return int
	 */
	public function edit_product_guide($id,$data){
		$where['id'] = $id;
		return $this->update($where,Tbl_ProductDetails,$data);
	}

	/**
	 * delete_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return int
	 */
	public function delete_product_guide($id){
		$where[] = $id;
		$data['is_deleted'] = 1;
		return $this->soft_delete($where,Tbl_ProductDetails,$data);
	}

	/**
	 * view_product
	 * @author Ashok Jadhav
	 * @access public
	 * @param $id
	 * @return array
	 */
	public function view_product_guide($productId,$id = null,$order_by = array()){
		$select = array(Tbl_ProductDetails.'.id',Tbl_ProductDetails.'.title',Tbl_ProductDetails.'.description_text',Tbl_ProductDetails.'.created_by',Tbl_Products.'.title AS product_name','product_id');
		$where[Tbl_ProductDetails.'.product_id'] = $productId;
		$where[Tbl_ProductDetails.'.is_deleted'] = 0;
		if(!empty($id)){
			$where[Tbl_ProductDetails.'.id'] = $id;
		}
		$join = array('table' => Tbl_Products,'on_condition' => Tbl_Products.'.id = '.Tbl_ProductDetails.'.product_id','type' => 'right');
		return $this->view($select,$where,Tbl_ProductDetails , $join,$order_by);
	}


	#####################################
	/* Private Function*/
	#####################################
	private function insert($table,$data){
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

	private function update($where,$table,$data){
		$this->db->where($where);
		$this->db->update($table,$data);
		$errors = $this->db->error();
		if($errors['code']){
			$response['status'] = 'error';
			$response['code'] = $errors['code'];
		}else{
			$response['status'] = 'success';
			$response['affected_rows'] = $this->db->affected_rows();
		}
		return $response;
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
			/*pe($order_by);
			exit;*/
		}else{
			$this->db->order_by($table.'.id','DESC');
		}
		$query = $this->db->get();
		//pe($this->db->last_query())
		return $query->result_array();
	}

	private function soft_delete($where,$table,$data){
		$this->db->where_in('id',$where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}

	function get_enum_values( $table, $field )
	{
	    $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
	    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
	    $enum = explode("','", $matches[1]);
	    $enums = array();
	    $enums[''] = 'Select';
	    foreach ($enum as $key => $value) {
	    	$enums[$value] = $value;
	    }
	    return $enums;
	}
}
