<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

   /**
     * construct
     * constructor method
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
//          is_logged_in();     //check login
          $this->load->model('Lead','master');
          
	}

	/**
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	 * @access public
     * @param none
     * @return void
     *
     */
	public function index()
	{
        //Get session data
        $login_user = get_session();
        $middle = '';
        //Create Breadcumb
        /*$arrData['breadcrumb'] = $this->make_bread->output();*/
        $arrData['breadcrumb'] = '';


        //Get session data
        $leads = array();

        if(isset($login_user['designation_name']) && !empty($login_user['designation_name'])){
            switch ($login_user['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to get_leads function.
                    $action = 'count';
                    $select = array();
                    $join = array();

                    //For Generated Leads Count
                    $table = Tbl_Leads;

                        //Month till date
                        $where = array(Tbl_Leads.'.created_by' => $login_user['hrms_id'],'MONTH('.Tbl_Leads.'.created_on)' => date('m'));
                        $leads['generated_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                        //Year till date
                        $where  = array(Tbl_Leads.'.created_by' => $login_user['hrms_id'],'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                        $leads['generated_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For converted leads Count
                    $table = Tbl_LeadAssign;

                        //Month till date
                        $where = array(Tbl_LeadAssign.'.employee_id' => $login_user['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'MONTH('.Tbl_LeadAssign.'.created_on)' => date('m'));
                        $leads['converted_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());


                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $login_user['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['converted_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For assigned leads Count
                    $table = Tbl_LeadAssign;

                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $login_user['hrms_id'],Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['assigned_leads'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    $arrData['leads'] = $leads;

                    //Middle view
                    $middle = "dashboard";
                    break;
                case 'BM':
                    $branch_id = $login_user['branch_id'];
                    $arrData['leads'] = $this->bm_view($branch_id);
                    $middle = "Leads/view/bm_view";
                    break;
                case 'ZM':
                    $zone_id = $login_user['zone_id'];
                    $arrData['leads'] = $this->zm_view($zone_id);
                    $middle = "Leads/view/zm_view";
                    break;
                case 'GM':
                    $arrData['leads'] = $this->gm_view();
                    $middle = "Leads/view/gm_view";
                    break;
            }

        }
        return load_view($middle,$arrData);
	}

    /**
     * bm_view
     * loads the branch manager view
     * @author Gourav Thatoi
     */
    private function bm_view($branch_id){

        //for generated lead
        $where_month_Array = array('branch_id' => $branch_id,
            'MONTH(created_on)' => date('m'));
        $where_year_Array = array('branch_id' => $branch_id,
                    'YEAR(created_on)' => date('Y'));
        $generated['monthly_generated_leads'] = $this->master->get_generated_lead_bm_zm($where_month_Array);
        $generated['yearly_generated_leads'] = $this->master->get_generated_lead_bm_zm($where_year_Array);
        $generated_key_value = array();
        $generated_key_value_year = array();
        $final = array();
        foreach ($generated['monthly_generated_leads'] as $k => $v) {
            $generated_key_value[$v['created_by']] = $v['total'];
        }
        foreach ($generated['yearly_generated_leads'] as $k => $v) {
            $generated_key_value_year[$v['created_by']] = $v['total'];
        }


        //for converted lead
        $final = array();
        $login_user = get_session();
        $result = get_details($login_user['hrms_id']);
        foreach ($result['list'] as $key =>$val){
            if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => 0,
                    'total_generated_ytd' => 0);
            }else {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                    'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
            }
            $final[$val->DESCR10] = $push_generated;
        }
        foreach ($final as $id => $value) {

            $where_month_Array = array('employee_id' => $value['created_by'],'MONTH(created_on)' => date('m'),'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $where_year_Array = array('employee_id' => $value['created_by'],'YEAR(created_on)' => date('Y'),'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            $converted_yearly = $this->master->get_converted_lead_bm_zm($where_year_Array);
            if (empty($converted)) {
                $converted = 0;
            }
            if (empty($converted_yearly)) {
                $converted_yearly = 0;
            }
            $final[$value['created_by']]['total_converted_mtd'] = $converted;
            $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
        }
        return $final;
    }

    /**
     * zm_view
     * loads the zonal manager view
     * @author Gourav Thatoi
     */
    private function zm_view($zone_id){

        $where_month_Array = array('zone_id' => $zone_id,'MONTH(created_on)' => date('m'));
        $where_year_Array = array('zone_id' => $zone_id,'YEAR(created_on)' => date('Y'));
        $generated['monthly_generated_leads'] = $this->master->get_generated_lead_bm_zm($where_month_Array);
        $generated['yearly_generated_leads'] = $this->master->get_generated_lead_bm_zm($where_year_Array);
        /*pe($generated);
        exit;*/
        $generated_key_value = array();
        $generated_key_value_year = array();
        $final = array();
        foreach ($generated['monthly_generated_leads'] as $k => $v) {
            $generated_key_value[$v['branch_id']] = $v['total'];
        }
        foreach ($generated['yearly_generated_leads'] as $k => $v) {
            $generated_key_value_year[$v['branch_id']] = $v['total'];
        }
        $final = array();
        $login_user = get_session();
        $result = get_details($login_user['hrms_id']);
        foreach ($result['list'] as $key => $val) {
            if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => 0,
                    'total_generated_ytd' => 0);
            } else {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                    'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
            }
            $final[$val->DESCR10] = $push_generated;
        }
        //for converted
        foreach ($final as $id => $value) {

            $where_month_Array = array('branch_id' => $value['created_by'],
                'MONTH(created_on)' => date('m'),
                'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $where_year_Array = array('branch_id' => $value['created_by'],
                'YEAR(created_on)' => date('Y'),
                'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            $converted_yearly = $this->master->get_converted_lead_bm_zm($where_year_Array);
            if (empty($converted)) {
                $converted = 0;
            }
            if (empty($converted_yearly)) {
                $converted_yearly = 0;
            }
            $final[$value['created_by']]['total_converted_mtd'] = $converted;
            $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
        }
        return $final;
    }

    /**
     * zm_view
     * loads the zonal manager view
     * @author Gourav Thatoi
     */
    private function gm_view(){

        $where_generated_Array = array('zone_id !=' => NULL,
            'MONTH(created_on)' => date('m'));
        $where_year_Array = array('zone_id !=' => NULL,
            'YEAR(created_on)' => date('Y'));
        $generated['generated_leads'] = $this->master->get_generated_lead_bm_zm($where_generated_Array);
        $generated['yearly_generated_leads'] = $this->master->get_generated_lead_bm_zm($where_year_Array);
        $generated_key_value = array();
        $generated_key_value_year = array();
        $final = array();
        foreach ($generated['generated_leads'] as $k => $v) {
            $generated_key_value[$v['zone_id']] = $v['total'];
        }
        foreach ($generated['yearly_generated_leads'] as $k => $v) {
            $generated_key_value_year[$v['zone_id']] = $v['total'];
        }
        $login_user = get_session();
        $result = get_details($login_user['hrms_id']);
        foreach ($result['list'] as $key => $val) {
            if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => 0,
                    'total_generated_ytd' => 0);
            } else {
                $push_generated = array(
                    'created_by' => $val->DESCR10,
                    'created_by_name' => $val->DESCR30,
                    'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                    'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
            }
            $final[$val->DESCR10] = $push_generated;
        }
        //for converted
        foreach ($final as $id => $value) {

            $where_month_Array = array('zone_id' => $value['created_by'],
                'MONTH(created_on)' => date('m'),
                'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $where_year_Array = array('zone_id' => $value['created_by'],
                'YEAR(created_on)' => date('Y'),
                'status' => 'converted','is_updated' => 1,'is_deleted' => 0);
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            $converted_yearly = $this->master->get_converted_lead_bm_zm($where_year_Array);
            if (empty($converted)) {
                $converted = 0;
            }
            if (empty($converted_yearly)) {
                $converted_yearly = 0;
            }
            $final[$value['created_by']]['total_converted_mtd'] = $converted;
            $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
        }
        return $final;
    }


    /**
     * leads_performance
     * loads the performance of employee
     * @author Gourav Thatoi
     */
    public function leads_performance($id=''){
        $source = $this->config->item('lead_source');
        $login_user = get_session();;
        $branch_id = decode_id($id);
        $zone_id = decode_id($id);
        $created_by = decode_id($id);
        if($this->session->userdata('admin_type')=='EM')
        $created_by = $login_user['hrms_id'];
        $action = 'count';
        $table = Tbl_Leads .' as l';
        $result = array();
        $join[] = array('table' => Tbl_LeadAssign .' as la', 'on_condition' => 'l.id = la.lead_id', 'type' => '');
        $select = array();
        $middle = "Leads/view/performance";
        if ($this->session->userdata('admin_type') == 'ZM') {
            $result['title'] = 'Lead Performance';
            $result['branch_id'] = $branch_id;
            $this->make_bread->add('Lead Performance', '', 0);
            foreach ($source as $key => $lead_source){
                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $branch_id,'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'),'l.lead_source' => $lead_source);
                $result['lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $branch_id, 'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source);
                $result['month_lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $branch_id,'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $branch_id,'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['month_lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
            }
        }

        if ($this->session->userdata('admin_type') == 'GM') {
            $result['title'] = 'Lead Performance';
            $result['zone_id'] = $zone_id;
            $this->make_bread->add('Lead Performance', '', 0);
            foreach ($source as $key => $lead_source){
                $where = array('la.zone_id' => $zone_id,'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'), 'l.lead_source' => $lead_source);
                $result['lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $zone_id, 'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source);
                $result['month_lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $zone_id, 'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $zone_id,'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['month_lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
            }
        }

        if($this->session->userdata('admin_type') =='BM' || $this->session->userdata('admin_type') =='EM'){
            if($this->session->userdata('admin_type') =='EM'){
                $result['title'] = 'My Lead Performance';
                $result['employee_id'] = $created_by;
                $this->make_bread->add('My Lead Performance', '', 0);
            }else{
                $result['title'] = 'Lead Performance';
                $result['employee_id'] = $created_by;
                $this->make_bread->add('Lead Performance', '', 0);
            }
            foreach ($source as $key => $lead_source){
                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $login_user['branch_id'],'la.employee_id' => $created_by, 'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'),'l.lead_source' => $lead_source);
                $result['lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $login_user['branch_id'],'la.employee_id' => $created_by, 'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source);
                $result['month_lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $login_user['branch_id'],'la.employee_id' => $created_by, 'la.is_deleted' => 0,'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array('la.zone_id' => $login_user['zone_id'],'la.branch_id' => $login_user['branch_id'],'la.employee_id' => $created_by, 'la.is_deleted' => 0,'la.is_updated' => 1, 'MONTH(la.created_on)' => date('m'),'l.lead_source' => $lead_source,'la.status' => 'Converted');
                $result['month_lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            }
        }
        $result['breadcrumb'] = $this->make_bread->output();
        load_view($middle,$result);

    }

    /**
     * leads_status
     * loads the status of employee
     * @author Gourav Thatoi
     */
   public function leads_status_old($id='',$lead_source=''){
        $result = array();
        $status = $this->config->item('lead_status');
        $designation_type = $this->session->userdata('admin_type');
        $join[] = array('table' => Tbl_Leads, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        
        if(!empty($designation_type) && $designation_type == 'ZM'){
            $this->make_bread->add('Generated Leads', '', 0);
            //$this->make_bread->add($branch_id, '', 0);  //Put Branch name Here

            $table = Tbl_LeadAssign;
            $action = 'count';
            $id=$this->uri->segment(3);
            $branch_id = decode_id($id);
            $result['branch_id'] = $branch_id;

            if(!empty($status)){
                foreach ($status as $key => $value) {
                    $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id,'status' => $key,'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

                    $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
                }
            }
        }

        if(!empty($designation_type) && $designation_type == 'GM'){
            $this->make_bread->add('Generated Leads', '', 0);
            //$this->make_bread->add($branch_id, '', 0);  //Put Branch name Here

            $table = Tbl_LeadAssign;
            $action = 'count';
            $id=$this->uri->segment(3);
            $zone_id = decode_id($id);
            $result['zone_id'] = $zone_id;

            if(!empty($status)){
                foreach ($status as $key => $value) {
                    $whereArray = array(Tbl_Leads.'.zone_id' => $zone_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

                    $whereArray = array(Tbl_Leads.'.zone_id' => $zone_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
                }
            }
        }
        
        if(!empty($designation_type) && ($designation_type == 'BM' || $designation_type == 'EM')){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $employee_id = decode_id($id);
            $result['employee_id'] = $employee_id;
            if($designation_type == 'EM'){
                $this->make_bread->add('My Generated Leads', '', 0);
                //$this->make_bread->add($employee_id, '', 0);  //Put Employee name Here
                $login_user = get_session();
                $employee_id = $login_user['hrms_id'];
                $result['employee_name'] = $login_user['full_name'];
            }else{
                $this->make_bread->add('Generated Leads', '', 0);
                //$this->make_bread->add($branch_id, '', 0);  //Put Branch name Here
            }
            if(!empty($status)){
                foreach ($status as $key => $value) {
                    if(!empty($lead_source)){
                        $whereArray = array(Tbl_LeadAssign.'.employee_id'=>$employee_id,'status'=>$key, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    }else{
                        $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>$key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    }
                    $result[$key]['Year'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

                    $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>$key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'),Tbl_LeadAssign.'.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $result[$key]['Month'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');        
                }
            }
        }
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = "Leads/view/status";
        load_view($middle,$result);

   }

   public function leads_status($type,$id = null,$lead_source = null){
        $result = array();
        $status = $this->config->item('lead_status');
        $login_user = get_session();
        $designation_type = $login_user['designation_name'];
        $result['type'] =  $type;
        //Building common parameters
        $action = 'count';
        $table = Tbl_Leads.' as l';
        $where = array('la.is_deleted' => 0,'la.is_updated' => 1);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la', 'on_condition' => 'l.id = la.lead_id', 'type' => '');

        //User Level conditions
        if(!empty($designation_type) && $designation_type == 'GM'){
            $zone_id = decode_id($id);
            $result['zone_id']  = $zone_id;
            if($type == 'generated'){
                $this->make_bread->add('Generated Leads', '', 0);
                $where['l.zone_id'] = $zone_id;
            }else{
                $this->make_bread->add('Lead Performace', 'dashboard/leads_performance/'.$id, 0);
                $this->make_bread->add(ucwords($lead_source), '', 0);
                $where['la.zone_id'] = $zone_id;
            }
        }
        if(!empty($designation_type) && $designation_type == 'ZM'){
            $branch_id = decode_id($id);
            $result['branch_id']  = $branch_id;
            if($type == 'generated'){
                $this->make_bread->add('Generated Leads', '', 0);
                $where['l.branch_id'] = $branch_id;
                $where['l.zone_id'] = $login_user['zone_id'];
            }else{
                $this->make_bread->add('Lead Performace', 'dashboard/leads_performance/'.$id, 0);
                $this->make_bread->add(ucwords($lead_source), '', 0);
                $where['la.branch_id'] = $branch_id;
                $where['la.zone_id'] = $login_user['zone_id'];
            }
        }
        if(!empty($designation_type) && $designation_type == 'BM'){
            $employee_id = decode_id($id);
            $result['employee_id']  = $employee_id;
            if($type == 'generated'){
                $this->make_bread->add('Generated Leads', '', 0);
                $where['l.created_by'] = $employee_id;
                $where['l.branch_id'] = $login_user['branch_id'];
                $where['l.zone_id'] = $login_user['zone_id'];
            }else{
                $this->make_bread->add('Lead Performace', 'dashboard/leads_performance/'.$id, 0);
                $this->make_bread->add(ucwords($lead_source), '', 0);
                $where['la.employee_id'] = $employee_id;
                $where['la.branch_id'] = $login_user['branch_id'];
                $where['la.zone_id'] = $login_user['zone_id'];
            }
        }
        if(!empty($designation_type) && $designation_type == 'EM'){
            $employee_id = $login_user['hrms_id'];
            $result['employee_id']  = $employee_id;
            if($type == 'generated'){
                $this->make_bread->add('My Generated Leads', '', 0);
                $where['l.created_by'] = $employee_id;
            }else{
                $this->make_bread->add('My Lead Performace', 'dashboard/leads_performance/'.$id, 0);
                $this->make_bread->add(ucwords($lead_source), '', 0);
                $where['la.employee_id'] = $employee_id;
            }
        }

        if(!empty($lead_source)){
           $result['lead_source'] =  $lead_source;
           $where['l.lead_source'] = $lead_source; 
        }

        //Genearted Count and Assigned Count
        switch ($type) {
            case 'generated':
                    $year_where['YEAR(l.created_on)'] = date('Y');
                    $month_where['MONTH(l.created_on)'] = date('m');
                    if(!empty($status)){
                        foreach ($status as $key => $value) {
                            $where['status'] = $key;
                            
                            //This Year Generated
                            $year_where = array_merge($year_where,$where);
                            
                            //This Month Generated
                            $month_where = array_merge($month_where,$where);
                            
                            $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $month_where, $join, '', '');
                            //pe($this->db->last_query());
                            $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $year_where, $join, '', '');
                        }
                    } 
                break;
            case 'assigned':
                    $year_where['YEAR(la.created_on)'] = date('Y');
                    $month_where['MONTH(la.created_on)'] = date('m');
                    if(!empty($status)){
                        foreach ($status as $key => $value) {
                            $where['status'] = $key;

                            //This Year Assigned
                            $year_where = array_merge($year_where,$where);

                            //This Month Assigned
                            $month_where = array_merge($month_where,$where);

                            $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $month_where, $join, '', '');
                            //pe($this->db->last_query());
                            $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $year_where, $join, '', '');
                        }
                    }
                break; 
        }
        //pe($this->db->last_query());
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = "Leads/view/status";
        load_view($middle,$result);
    }


    public function emi_calculator(){
        $this->make_bread->add('EMI Calculator', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = '/emi_calculator';
        load_view($middle,$result);
    }

    public function home_excel(){
        $login_user = get_session();
        $designation_type = $login_user['designation_name'];
        switch ($designation_type){
            case 'BM':
                $header_value = array('Sr.No','Employee Name','Generated Leads(This Month)','Generated Leads(This Year)',
                    'Converted Leads(This Month)','Converted Leads(This Year)');
                $id = $login_user['branch_id'];
                $data = $this->bm_view($id);
                export_excel($header_value,$data);
                break;
            case 'ZM':
                $header_value = array('Sr.No','Branch Name','Generated Leads(This Month)',
                    'Converted Leads(This Month)');
                $id = $login_user['zone_id'];
                $data = $this->zm_view($id);
                export_excel($header_value,$data);
                break;
        }
    }

    public function fd_calculator(){
        $this->make_bread->add('FD Calculator', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = '/fd_calculator';
        load_view($middle,$result);
    }
    public function rd_calculator(){
        $this->make_bread->add('RD Calculator', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = '/rd_calculator';
        load_view($middle,$result);
    }
}
