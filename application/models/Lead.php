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
     * get_generated_lead
     * Gives generated lead of month and year
     * @author Gourav Thatoi
     * @access public
     * @param $where_month_Array,$where_year_Array
     * @return array
     */
    public function get_generated_lead($where_month_Array = array(), $where_year_Array = array())
    {
        $result = array();
        if (!empty($where_month_Array)) {
            $generated_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_Leads);
            $result['generated_mtd'] = $generated_lead_month;
        }
        if (!empty($where_year_Array)) {
            $generated_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_Leads);
            $result['generated_ytd'] = $generated_lead_year;
        }
        return $result;
    }

    /**
     * get_converted_lead
     * Gives converted lead of month and year
     * @author Gourav Thatoi
     * @access public
     * @param $where_month_Array,$where_year_Array
     * @return array
     */
    public function get_converted_lead($where_month_Array = array(), $where_year_Array = array())
    {
        $result = array();
        if (!empty($where_month_Array)) {
            $converted_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_LeadAssign);
            $result['converted_mtd'] = $converted_lead_month;
        }
        if (!empty($where_year_Array)) {
            $converted_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_LeadAssign);
            $result['converted_ytd'] = $converted_lead_year;
        }
        return $result;
    }

    /**
     * get_assigned_leads
     * Gives assigned leads of year
     * @author Gourav Thatoi
     * @access public
     * @param $where_assigned_Array
     * @return array
     */
    public function get_assigned_leads($where_assigned_Array = array()){
        $result = array();
        if (!empty($where_assigned_Array)) {
                $assigned_leads = $this->db->where($where_assigned_Array)->count_all_results(Tbl_LeadAssign);
                $result['assigned_leads']= $assigned_leads;
            
        }
        return $result;
    }
	

}