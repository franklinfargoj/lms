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
          is_logged_in();     //check login
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
        $input = get_session();
        $middle = '';
        //Create Breadcumb
        /*$arrData['breadcrumb'] = $this->make_bread->output();*/
        $arrData['breadcrumb'] = '';


        //Get session data
        $input = get_session();
        $leads = array();

        if(isset($input['designation_name']) && !empty($input['designation_name'])){
            switch ($input['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to get_leads function.
                    $action = 'count';
                    $select = array();
                    $join = array();

                    //For Generated Leads Count
                    $table = Tbl_Leads;

                        //Month till date
                        $where = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'MONTH('.Tbl_Leads.'.created_on)' => date('m'));
                        $leads['generated_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                        //Year till date
                        $where  = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                        $leads['generated_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For converted leads Count
                    $table = Tbl_LeadAssign;

                        //Month till date
                        $where = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'MONTH('.Tbl_LeadAssign.'.created_on)' => date('m'));
                        $leads['converted_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());


                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['converted_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For assigned leads Count
                    $table = Tbl_LeadAssign;

                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['assigned_leads'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    $arrData['leads'] = $leads;

                    //Middle view
                    $middle = "dashboard";
                    break;
                case 'BM':
                    $branch_id = $input['branch_id'];
                    $arrData['leads'] = $this->bm_view($branch_id);
                    $middle = "Leads/view/bm_view";
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
    public function bm_view($branch_id){

        //for generated lead
        $where_generated_Array = array('branch_id' => $branch_id,
            'created_by !=' => 0,
            'MONTH(created_on)' => date('m'));
        $leads['generated_leads'] = $this->master->get_generated_lead_bm_zm($where_generated_Array);
        //for converted lead
        $where_converted_Array = array('branch_id' => $branch_id,
            'MONTH(created_on)' => date('m'),
            'created_by !=' => 0,
            'status' => 'converted');
        $leads['converted_leads'] = $this->master->get_converted_lead_bm_zm($where_converted_Array);
        if(!empty($leads['converted_leads']))
            $leads['all_converted_created_by'] = array_column($leads['converted_leads'],'created_by');

        return $leads;
    }

    /**
     * leads_performance
     * loads the performance of employee
     * @author Gourav Thatoi
     */
    public function leads_performance($id){
        $branch_id = decode_id($id);
        $created_by = decode_id($id);
        $action = 'count';
        $table = Tbl_Leads;
        $result = array();
        $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        $select = array();
        $middle = "Leads/view/bm_performance";
        $this->make_bread->add('lead performance', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();

        if ($this->session->userdata('admin_type') == 'ZM') {
            //Walk-in
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, '', '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['month_lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, '', '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Walk-in',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Walk-in',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Third Party
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['month_lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Bank Website
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['month_lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Analytics
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['month_lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

        }if($this->session->userdata('admin_type') =='BM'){
            //Walk-in
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['month_lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => 'Walk-in',Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => 'Walk-in',Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Third Party
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['month_lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Bank Website
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['month_lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Analytics
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['month_lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
        }
        load_view($middle,$result);

    }

   public function leads_status(){
        $result = array();
        $session_vals = get_session();
        $designation_type = $session_vals['designation_name'];
        if(!empty($designation_type) && $designation_type == 'BM'){
            $table = Tbl_LeadAssign;
            $branch_id = $session_vals['designation_name'];
            $action = 'count';
            $whereArray = array('branch_id' => $branch_id, 'status' => 'Not Contacted');
            $result['not_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Interested/Follow up');
            $result['follow_up'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Converted');
            $result['converted'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Account Opened');
            $result['account_opened'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Documents Collected');
            $result['documents_collected'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Drop/Not Interested');
            $result['drop_not_interested'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Cannot be contacted');
            $result['can_not_be_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');

            $whereArray = array('branch_id' => $branch_id, 'status' => 'Closed');
            $result['closed'] = $this->master->get_leads($action, $table, '', $whereArray, '', '', '');
        }
        if(!empty($designation_type) && $designation_type == 'EM'){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $employee_id = $session_vals['hrms_id'];
            $whereArray = array('employee_id'=>$employee_id,'status'=>'Not Contacted');
            $result['not_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Interested/Follow up');
            $result['follow_up'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Converted');
            $result['converted'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Account Opened');
            $result['account_opened'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Documents Collected');
            $result['documents_collected'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Drop/Not Interested');
            $result['drop_not_interested'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Cannot be contacted');
            $result['can_not_be_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');

            $whereArray = array('employee_id'=>$employee_id,'status'=>'Closed');
            $result['closed'] = $this->master->get_leads($action,$table,'',$whereArray,'','','');
        }
       $middle = "Leads/view/bm_performance";
       load_view($middle,$result);

   }
    
}
