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
            //pe($lead_data);exit;

            $this->db->db_debug = FALSE;
            $this->db->insert($this->_tbl_db_leads, $lead_data);
            $errors = $this->db->error();
            if($errors['code']){
                $response['status'] = 'error';
                $response['code'] = $errors['code'];
                return $response;
            }else{
                return $this->db->insert_id();
            }
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
        $this->db->select('id,category_id,map_with');
        $this->db->from('db_master_products');
        $this->db->where($whereArray);
        $resultArray = $this->db->get()->result_array();
        if (count($resultArray) > 0) {
            return $resultArray[0];
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
        $this->db->where('db_master_products.status','active');
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
        $this->db->select('db_leads.*,db_master_products.title as product_title,db_master_products.map_with as mapping');
        $this->db->from('db_leads');
        $this->db->join('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $this->db->join('db_master_products','db_master_products.id = db_leads.product_id ','left');
        //$this->db->where('db_lead_assign1.lead_id',NULL);
        $this->db->where("(db_lead_assign.lead_id IS NULL OR db_lead_assign.is_deleted=1)", NULL, FALSE);
       // $this->db->or_where('db_lead_assign.is_deleted',1);
        $this->db->where('db_leads.branch_id',$login_user['branch_id']);
        $this->db->order_by('db_leads.created_on','desc');
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
            return $this->lists($table,$select,$where,$join,$group_by,$order_by);
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
    public function get_bm_assigned_leads($table,$select,$where,$join)
    {
        return $this->counts($table,$select,$where,$join,$group_by=array());
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

    public function update_reminder_data($where,$data,$table){
        return $this->update($where,$table,$data);
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
    public function lists($table,$select,$where,$join,$group_by,$order_by,$limit=''){


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
        if(!empty($limit)){
            $this->db->limit($limit);
        }
//        pe($this->db);die;
        $query = $this->db->get();
//       pe($this->db->last_query());die;
        if($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
    }



    public function getDataTable($table,$select,$where,$join,$group_by,$order_by,$limit=''){

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
        if(!empty($limit)){
            $this->db->limit($limit);
        }
//        /*pe($this->db);die;*/
        $query = $this->db->get();

        if($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
    }



    public function fetchProductData($table,$select,$whereIn,$join,$group_by,$order_by,$limit=''){

        $this->db->select($select,TRUE);
        $this->db->from($table);
        if(!empty($whereIn)){
            $this->db->where_in('id',$whereIn);
        }

        $query = $this->db->get();
        if($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
    }


    public function listMasters($action,$table,$select,$where,$join,$group_by,$order_by,$limit=''){

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
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        //pe($this->db);die;
        $query = $this->db->get();
//       pe($this->db->last_query());die;
        if($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
            //echo 'm in if';

        }
        //pe($query->result_array());exit;

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

    public function delete($where,$table)
    {

        $this->db->where($where);
        $this->db->delete($table);
    }

    public function get_generated_lead_bm_zm($where_generated_Array){

        $result = array();
        if(!empty($where_generated_Array)){
            //for branch manager
            if(array_key_exists('branch_id',$where_generated_Array)){
                $where_generated_Array['created_by_branch_id']=$where_generated_Array['branch_id'];
                unset ($where_generated_Array['branch_id']);
                $this->db->select('created_by, COUNT(created_by) as total , created_by_name');
                $this->db->group_by('created_by');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
                return $result;
            }
            //for gm
            if(array_key_exists('created_by_zone_id !=',$where_generated_Array)){
                $this->db->select('created_by_zone_id, COUNT(zone_id) as total');
                $this->db->group_by('created_by_zone_id');
                $this->db->order_by('total','desc');
                $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
                return $result;
            }
            //for zonal manager
            $this->db->select('created_by_branch_id, COUNT(branch_id) as total');
            $this->db->group_by('created_by_branch_id');
            $this->db->order_by('total','desc');
            $result = $this->db->get_where(Tbl_Leads,$where_generated_Array)->result_array();
        }
        return $result;

    }


    public function getEntireZoneDetails(){
        $this->db->select('code,name');
        $result = $this->db->get(Tbl_zone)->result_array();
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
            if(!empty($result)){
                return $result[0]->default_assign;
            }
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
        if($lead_status == 'walkin'){
            $this->db->select('db_leads.*,db_branch.name as generated_by,DATEDIFF(CURDATE( ),db_leads.created_on) as elapsed_day,db_master_products.title as product_title,db_master_products.map_with');
            $this->db->from('db_leads');
            $this->db->join('db_branch','db_branch.code = db_leads.created_by_branch_id ','');
        }else{
            $this->db->select('db_leads.*,DATEDIFF(CURDATE( ),db_leads.created_on) as elapsed_day,db_master_products.title as product_title,db_master_products.map_with');
            $this->db->from('db_leads');
        }
        $this->db->join('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $this->db->join('db_master_products','db_master_products.id = db_leads.product_id ','left');

       // $this->db->where('db_lead_assign.lead_id',NULL);
 $this->db->where("(db_lead_assign.lead_id IS NULL OR db_lead_assign.is_deleted=1)", NULL, FALSE);

        $this->db->order_by('db_leads.created_on','desc');
        if(!empty($lead_status)){
            $this->db->where('db_leads.lead_source',$lead_status);
        }
        if(!empty($branch_id)){
            $this->db->where('db_leads.branch_id',$branch_id);
        }
        $result = $this->db->get();
        pe($this->db->last_query());
        exit;
        

        return $result->result_array();

    }

    public function get_employee_dump($select,$where,$group_by,$table,$view=''){
            $this->db->select($select,TRUE);
            $this->db->from($table);
            if(!empty($where)){
                $this->db->where($where);
            }
            $this->db->group_by($group_by);
            if($view != ''){
              if($view == 'EM'){
                  $this->db->order_by('employee_name','ASC');
              }
              if($view == 'BM'){
                  $this->db->order_by('branch_name','ASC');
              }
              if($this->session->userdata('admin_type') == 'ZM' || $this->session->userdata('admin_type') == 'GM'){
                $this->db->order_by('zone_name','ASC');
              }
            }

            $Q = $this->db->get();
            return $Q->result();
    }

    public function get_all_branch_detail(){
        $this->db->select('z.id AS z_id,z.code AS zone_code,z.name AS zone_name,s.id AS s_id,s.code AS state_code,s.name AS state_name,d.id AS d_id,d.code AS dist_code,d.name AS dist_name,b.id AS b_id,b.code AS branch_code,b.name AS branch_name');
        $this->db->from(Tbl_district . ' AS d');
        $this->db->join(Tbl_branch . ' AS b', 'b.district_code= d.code');
        $this->db->join(Tbl_state . ' AS s', 's.code = d.state_code');
        $this->db->join(Tbl_zone . ' AS z', 'z.code = s.zone_code');
        $result = $this->db->get()->result_array();
        if(!empty($result)){
            return $result;
        }
        return false;
    }

    public function lead_previous_status($lead_id){
        $status_array = array();
        $previous_status = $this->db->select('DISTINCT(status) as status')->from(Tbl_LeadAssign)->where(array('is_updated' => 0,'lead_id' =>$lead_id))->get()->result_array();
        foreach ($previous_status as $status){
            $status_array[] = $status['status'];
        }
        return $status_array;
    }

    public function get_status($select,$where){

        $this->db->select($select,TRUE);
        $this->db->from('db_lead_assign');
        $this->db->where($where);
        $query = $this->db->get();
//		pe($this->db->last_query());die;
        return $query->result_array();
    }

    public function update_routed_lead($where,$table,$data,$order_by,$limit=''){
        $this->db->where($where);
        $this->db->order_by($order_by);
        $this->db->limit($limit);
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
   public function is_exsits($whereEx){
         $this->db->select('id');
         $this->db->from('db_leads');
         $this->db->where($whereEx);
         $resultArray = $this->db->get()->result_array();
         if (count($resultArray) > 0) {
             return $resultArray[0]['id'];
         }
         return false;
     }


    /**
     * branch_manager_id
     * Retrieves branch manager id
     * @author Franklin Fargoj
     * @access public
     * @param $branch_id
     * @return value
     */
    public  function branch_manager_id($branch_id){
        $this->db->select('hrms_id');
        $this->db->from('employee_dump');
        $this->db->where('branch_id',$branch_id);
        $this->db->like('designation','BRANCH MANAGER');
        $result = $this->db->get()->result_array();
        return $result[0]['hrms_id'];
    }

    /**
     * lead_details
     * Gets the detail of lead
     * @author Franklin Fargoj
     * @access public
     * @param $lead_id
     * @return value
     */
    public function lead_details($lead_id){

        $this->db->select('Ld.id,Ld.lead_identification,Ld.lead_ticket_range,Ld.opened_account_no,Ld.created_by_branch_id,Ld.customer_name,Ld.contact_no,Ld.remark,La.employee_name,La.status,La.is_deleted,La.is_updated,La.followup_date,db_master_products.title,rs.reminder_text,rs.is_cancelled,La.reason_for_drop,La.desc_for_drop')
                ->from('db_leads AS Ld')
                ->join('db_lead_assign AS La', 'La.lead_id = Ld.id', 'left')
                ->join('db_master_products', 'db_master_products.id = Ld.product_id', 'left')
                ->join('db_reminder_scheduler AS rs','rs.lead_id=Ld.id','left')
                ->order_by("La.id", "desc")
                ->order_by("rs.id", "desc")
                ->limit(1,0)
                ->where('Ld.id',$lead_id);
        $result = $this->db->get()->result_array();
        return $result;
    }



    public function actual_amt($table,$select,$where,$join,$group_by,$order_by,$limit=''){

        $this->db->select($select,TRUE);
        //$this->db->from($table);
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
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        /*pe($this->db);die;*/
        $query = $this->db->get();
        /* pe($query->result_array());die;*/
        if($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function is_cbs_exsits($whereEx){
        $this->db->select('id');
        $this->db->from('db_response_from_cbs');
        $this->db->where($whereEx);
        $resultArray = $this->db->get()->result_array();
        if (count($resultArray) > 0) {
            return $resultArray[0]['id'];
        }
        return false;
    }

    public function all_employee_dump($limit,$start,$col,$dir)
    {
        $query = $this->db
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->limit($limit,$start)
            ->order_by($col,$dir)
            ->get('employee_dump');

        if($query->num_rows()>0)
        {
            return $query->result();
        }
        else
        {
            return null;
        }
    }

    public function all_employee_dump_count()
    {
        $query = $this->db
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->get('employee_dump');

        return $query->num_rows();
    }

    public function employee_dump_search($limit,$start,$search,$col,$dir,$key)
    {
        $query = $this
            ->db
            ->like($key,$search)
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->limit($limit,$start)
            ->order_by($col,$dir)
            ->get('employee_dump');


        if($query->num_rows()>0)
        {
            return $query->result();
        }
        else
        {
            return null;
        }
    }

    function employee_dump_search_count($search,$key)
    {
        $query = $this
            ->db
            ->like($key,$search)
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->get('employee_dump');

        return $query->num_rows();
    }

    public function employee_dump_search_for_all($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->db
            ->like('name',$search['name'])
            ->like('email_id',$search['email_id'])
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->limit($limit,$start)
            ->order_by($col,$dir)
            ->get('employee_dump');


        if($query->num_rows()>0)
        {
            return $query->result();
        }
        else
        {
            return null;
        }
    }

    function employee_dump_search_count_for_all($search)
    {
        $query = $this
            ->db
            ->like('name',$search['name'])
            ->like('email_id',$search['email_id'])
            ->like('designation', 'ZONAL MANAGER')
//            ->where('email_status', 'active')
            ->get('employee_dump');

        return $query->num_rows();
    }

    public function get_product_cat_name($product_category_id){
        $this->db->select('title');
        $this->db->from('db_master_product_category');
        $this->db->where('id',$product_category_id);
        $result = $this->db->get()->result();
        return $result;
    }
}
