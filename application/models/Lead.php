<?php if (!defined('BASEPATH'))    exit('No direct access to script is allowed');

/**
 *
 * This is Lead Model
 * 
 * @author	Gourav Thatoi
 * @subpackage	Model
 */
class Lead  extends CI_Model
{
	protected $_tbl_db_leads;

	public function __construct()
	{
		parent::__construct();
		$this->_tbl_db_leads = 'db_leads';

	}

	public function insert($lead_data = array())
	{
		if (!empty($lead_data)) {
			$this->db->insert($this->_tbl_db_leads, $lead_data);
			return true;
		}
		return false;
	}

	public function get_all_category($whereArray = array())
	{
		$resultObj = $this->db->get_where('db_master_product_category', $whereArray);
		if ($resultObj->num_rows() > 0) {
			$result_array = $resultObj->result_array();
			return $result_array;
		}
	}

	public function get_all_products($whereArray = array())
	{
		$resultObj = $this->db->get_where('db_master_products', $whereArray);
		if ($resultObj->num_rows() > 0) {
			$result_array = $resultObj->result_array();
			return $result_array;
		}
	}
	
	public function fetch_product_id($whereArray = array() ){
        $this->db->select('id,category_id');
        $this->db->from('db_master_products');
        $this->db->where($whereArray);
        $resultArray = $this->db->get()->result_array();
        if (count($resultArray) > 0) {
            $result = array('product_id'=>$resultArray[0]['id'],'category_id'=>$resultArray[0]['category_id']);
            return $result;
        }
        return false;
	}

    public function fetch_product_category_id($whereArray = array() ){
        $this->db->select('id');
        $this->db->from('db_master_product_category');
        $this->db->where($whereArray);
        $resultArray = $this->db->get()->result_array();
        if (count($resultArray) > 0) {

            return $resultArray[0]['id'];
            
        }
        return false;
	}

    public function all_products_under_category($cat_id){
        $this->db->select('db_master_products.title');
        $this->db->from('db_master_products');
        $this->db->where('db_master_product_category.id',$cat_id);
        $this->db->join('db_master_product_category', 'db_master_product_category.id = db_master_products.category_id');
        $query = $this->db->get()->result_array();
        if (count($query) > 0) {
            $query = array_map('current', $query);
            return $query;
        }
        return false;
    }
	public function insert_uploaded_data($table,$data,$escape = NULL)
	{
		return $this->db->insert_batch($table,$data,$escape);
	}

	public function uploaded_log($table,$data)
	{
		return $this->db->insert($table,$data);
	}

    public function unassigned_leads(){
        $this->db->select('db_leads.*,db_master_products.title product_title');
        $this->db->from('db_leads');
        $this->db->join('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $this->db->join('db_master_products','db_master_products.id = db_leads.product_id ','left');
        $this->db->where('db_lead_assign.lead_id',NULL);
        $result = $this->db->get();
        
        if($result->num_rows() > 0){
            return $result->result_array();
        } 
        return false;
    }

    /**
     * Get Leads count and list based on inputs
     * @author Ashok Jadhav
     * @access public
     * @param $action,$table,$select,$where,$join,$order_by
     * @param $action - It can be count or list (fetch count or records)
     * @return array
     */
    public function get_leads($action,$table,$select,$where,$join,$group_by,$order_by)
    {
        if($action == 'count'){
            return $this->db->where($where)->count_all_results($table);
        }elseif($action == 'list'){
            return $this->lists($table,$select,$where,$join,$group_by,$order_by = array());
        }
    }

    /**
     * Get all possible lead status available
     * @author Ashok Jadhav
     * @access public
     * @param $table,$field
     * @return array
     */
    public function lead_status($table,$field){
        $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        $enums = array();
        foreach ($enum as $key => $value) {
            $enums[$value] = $value;
        }
        return $enums;
    }

    /**
     * Get all possible lead status available
     * @author Ashok Jadhav
     * @access public
     * @param $table,$field
     * @return array
     */
    public function update_lead_status($where,$data){
        return $this->update($where,Tbl_LeadAssign,$data);
    }

    

    /*Private Function*/

    /**
     * Dynamic List Function
     * @author Ashok Jadhav
     * @access public
     * @param $table,$select,$where,$join,$order_by
     * @return array
     */
    private function lists($table,$select,$where,$join,$group_by,$order_by){

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
        if(!empty($order_by)){
            $this->db->order_by($order_by);
        }else{
            $this->db->order_by($table.'.id','DESC');
        }
        $query = $this->db->get();
        //pe($this->db->last_query())
        return $query->result_array();
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

    public function get_generated_lead_bm_zm($where_generated_Array){
        $result = array();
        if(!empty($where_generated_Array)){
            if(array_key_exists('branch_id',$where_generated_Array)){
                $this->db->select('created_by, COUNT(created_by) as total');
                $this->db->group_by('created_by');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
                return $result;
            }
            $this->db->select('branch_id, COUNT(branch_id) as total');
            $this->db->group_by('branch_id');
            $this->db->order_by('total','desc');
            $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
        }
        return $result;

    }
    public function get_converted_lead_bm_zm($where_converted_Array){
        $result = array();
        if(!empty($where_converted_Array)){
            if(array_key_exists('branch_id',$where_converted_Array)){
                $this->db->select('created_by, COUNT(created_by) as total');
                $this->db->group_by('created_by');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_LeadAssign,$where_converted_Array)->result_array();
                return $result;
            }
            $this->db->select('branch_id, COUNT(branch_id) as total');
            $this->db->group_by('branch_id');
            $this->db->order_by('total','desc');
            $result = $this->db->get_where(Tbl_LeadAssign,$where_converted_Array)->result_array();
        }
        return $result;

    }


}