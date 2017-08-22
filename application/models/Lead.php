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

	public function add_leads($lead_data = array())
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
     * get_leads  
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
     * lead_status  
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
     * update_lead_status
     * Update Lead Status
     * @author Ashok Jadhav
     * @access public
     * @param $where,$data
     * @return array
     */
    public function update_lead_status($where,$data){
        return $this->update($where,Tbl_LeadAssign,$data);
    }

    /**
     * add_reminder
     * Add Reminder scheduler data.
     * @author Ashok Jadhav
     * @access public
     * @param $data
     * @return int
     */
    public function add_reminder($data){
        $where = array('lead_id' => $data['lead_id'],'remind_to' => $data['remind_to'],'is_cancelled'=> 'No');
        $this->update($where,Tbl_Reminder,array('is_cancelled'=> 'Yes'));
        /*echo $this->db->last_query();
        exit;*/
        return $this->insert(Tbl_Reminder,$data);
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
	

}