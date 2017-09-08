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
            return $this->db->insert_id();
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

    public function unassigned_leads($lead_status = '',$id = ''){
        $login_user = get_session();
        $this->db->select('db_leads.*,db_master_products.title as product_title');
        $this->db->from('db_leads');
        $this->db->join('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $this->db->join('db_master_products','db_master_products.id = db_leads.product_id ','left');
        $this->db->where('db_lead_assign.lead_id',NULL);
        $this->db->where('db_leads.branch_id',$login_user['branch_id']);
        if(!empty($lead_status)){
            $this->db->where('db_leads.lead_source',$lead_status);
        }
        if(!empty($id)){
            $this->db->where('db_leads.id',$id);
        }
        $result = $this->db->get();
        return $result->result_array();
        
    }

    public function unassigned_status_count($select,$table,$join,$where,$group_by){
        $this->db->select($select);
        $this->db->from($table);
        $this->db->join($join[0],$join[1],$join[2]);
        $this->db->where($where);
        $this->db->group_by('db_leads.lead_source');
        $result = $this->db->get()->result_array();
        return $result;
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
//            return $this->db->where($where)->count_all_results($table);
            return $this->counts($table,$select,$where,$join,$group_by);
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
    public function get_enum($table,$field){
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
    public function get_assigned_leads($where_assigned_Array = array())
    {
        $result = array();
        if (!empty($where_assigned_Array)) {
            $assigned_leads = $this->db->where($where_assigned_Array)->count_all_results(Tbl_LeadAssign);
            return $assigned_leads;
        } return $result;
    }

    /**
     * update_lead_status
     * Update Lead Status
     * @author Ashok Jadhav
     * @access public
     * @param $where,$data
     * @return array
     */
    public function update_lead_data($where,$data,$table){
        return $this->update($where,$table,$data);
    }

    public function insert_lead_data($data,$table){
        return $this->insert($table,$data);
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
    public function lists($table,$select,$where,$join,$group_by,$order_by){

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
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update($where,$table,$data){
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

    public function get_generated_lead_bm_zm($where_generated_Array){
        $result = array();
        if(!empty($where_generated_Array)){
            //for branch manager
            if(array_key_exists('branch_id',$where_generated_Array)){
                $this->db->select('created_by, COUNT(created_by) as total , created_by_name');
                $this->db->group_by('created_by');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
                return $result;
            }
            //for gm
            if(array_key_exists('zone_id !=',$where_generated_Array)){
                $this->db->select('zone_id, COUNT(zone_id) as total');
                $this->db->group_by('zone_id');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
                return $result;
            }
            //for zonal manager
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
            if(array_key_exists('employee_id',$where_converted_Array)){
                $this->db->select('COUNT(created_by) as total');
                $result = $this->db->get_where(Tbl_LeadAssign,$where_converted_Array)->result_array();
                if(!empty($result)){
                    return $result[0]['total'];
                }
                $total = 0;
                return $total;

            }
            //for gm
            if(array_key_exists('zone_id !=',$where_converted_Array)){
                $this->db->select('COUNT(zone_id) as total');
                $result = $this->db->get_where(Tbl_LeadAssign,$where_converted_Array)->result_array();
                return $result;
            }
            //for zonal
            $this->db->select('COUNT(branch_id) as total');
            $result = $this->db->get_where(Tbl_LeadAssign,$where_converted_Array)->result_array();
            if(!empty($result)){
                return $result[0]['total'];
            }
            $total = 0;
            return $total;
        }
        return $result;

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
    public function get_product_assign_to($prod_id){
        if($prod_id != ''){
            $this->db->select('default_assign');
            $this->db->from(Tbl_Products);
            $this->db->where('id',$prod_id);
            $result =  $this->db->get()->result();
            return $result[0]->default_assign;
        }
        return false;
    }
    public function insert_assign($data=array()){
        if(!empty($data)){
            $this->db->insert(Tbl_LeadAssign,$data);
            return true;
        }
        return false;
    }

    public function get_uploaded_leads_logs($whereArray = array()){
        return $result = $this->db->get_where(Tbl_Log,$whereArray)->result_array();

    }

    public function check_mapping($whereArray = array()){
            if(!empty($whereArray)){
                $this->db->select('other_processing_center_id');
                $result = $this->db->get_where(Tbl_processing_center,$whereArray)->result_array();
                if(count($result) == 1){
                    return $result[0]['other_processing_center_id'];
                }
            }
        return array();
    }

    public function get_pending_leads($whereArray,$join){
        $this->db->select(Tbl_Leads.'.id',Tbl_Leads.'.created_on',Tbl_LeadAssign.'.lead_id');
        $this->db->from(Tbl_Leads);
        $this->db->join($join['table'],$join['on_condition'],$join['type']);
        $this->db->where($whereArray);
        $pending_leads = $this->db->get()->result_array();
        if(!empty($pending_leads)){
            return $pending_leads;
        }return array();

    }

    public function unassigned_leads_api($lead_status = '',$branch_id = ''){
        $this->db->select('db_leads.*,db_master_products.title as product_title');
        $this->db->from('db_leads');
        $this->db->join('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $this->db->join('db_master_products','db_master_products.id = db_leads.product_id ','left');
        $this->db->where('db_lead_assign.lead_id',NULL);
        if(!empty($lead_status)){
            $this->db->where('db_leads.lead_source',$lead_status);
        }
        if(!empty($branch_id)){
            $this->db->where('db_leads.branch_id',$branch_id);
        }
        $result = $this->db->get();
        return $result->result_array();

    }

}