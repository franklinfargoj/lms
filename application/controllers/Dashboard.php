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
                case 'ZM':
                    $zone_id = $input['zone_id'];
                    $arrData['leads'] = $this->zm_view($zone_id);
                    $middle = "Leads/view/zm_view";
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
     * zm_view
     * loads the zonal manager view
     * @author Gourav Thatoi
     */
    public function zm_view($zone_id){

        //for generated lead
        $where_generated_Array = array('zone_id' => $zone_id,
            'created_by !=' => 0,
            'MONTH(created_on)' => date('m'));
        $leads['generated_leads'] = $this->master->get_generated_lead_bm_zm($where_generated_Array);
        //for converted lead
        $where_converted_Array = array('zone_id' => $zone_id,
            'MONTH(created_on)' => date('m'),
            'created_by !=' => 0,
            'status' => 'converted');
        $leads['converted_leads'] = $this->master->get_converted_lead_bm_zm($where_converted_Array);
        if(!empty($leads['converted_leads']))
            $leads['all_converted_branch_id'] = array_column($leads['converted_leads'],'branch_id');

        return $leads;
    }


    /**
     * leads_performance
     * loads the performance of employee
     * @author Gourav Thatoi
     */
    public function leads_performance($id=''){
        $input = get_session();;
        $branch_id = decode_id($id);
        $created_by = decode_id($id);
        if($this->session->userdata('admin_type')=='EM')
        $created_by = $input['hrms_id'];
        $action = 'count';
        $table = Tbl_Leads;
        $result = array();
        $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        $select = array();
        $middle = "Leads/view/performance";
        $this->make_bread->add('lead performance', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();

        if ($this->session->userdata('admin_type') == 'ZM') {
            //Walk-in
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['month_lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

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

        }if($this->session->userdata('admin_type') =='BM' || $this->session->userdata('admin_type') =='EM'){
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

    /**
     * leads_status
     * loads the status of employee
     * @author Gourav Thatoi
     */
   public function leads_status($id='',$name=''){
       $result = array();
       $designation_type = $this->session->userdata('admin_type');
       $this->make_bread->add('My Generated Leads', '', 0);
       $join[] = array('table' => Tbl_Leads, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
       $result['breadcrumb'] = $this->make_bread->output();
       if(!empty($designation_type) && $designation_type == 'ZM'){
           $table = Tbl_LeadAssign;
           $action = 'count';
           $id=$this->uri->segment(3);
           $branch_id = decode_id($id);
           $result['branch_id'] = $branch_id;
           $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'NC', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['not_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'NC', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_not_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'FU', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['follow_up'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'FU', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['month_follow_up'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'Converted', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['converted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'Converted', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_converted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'AO', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['account_opened'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'AO', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_account_opened'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'DC', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['documents_collected'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'DC', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_documents_collected'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'NI', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['drop_not_interested'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'NI', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_drop_not_interested'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'CBC', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['can_not_be_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'CBC', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_can_not_be_contacted'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'Closed', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['closed'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
            $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => 'Closed', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_closed'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
        }
        if(!empty($designation_type) && ($designation_type == 'BM' || $designation_type == 'EM')){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $employee_id = decode_id($id);
            $result['employee_id'] = $employee_id;
            $result['employee_name'] = $name;
            if($designation_type == 'EM'){
                $input = get_session();
                $employee_id = $input['hrms_id'];
                $result['employee_id'] = $employee_id;
                $result['employee_name'] = $input['full_name'];
            }
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'NC', 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['not_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'NC', 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_not_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'FU','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['follow_up'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'FU','MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_follow_up'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'Converted','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['converted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'Converted','MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_converted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'AO','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['account_opened'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'AO','MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_account_opened'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'DC','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['documents_collected'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'DC','MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_documents_collected'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'NI','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['drop_not_interested'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'NI','MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_drop_not_interested'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'CBC','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['can_not_be_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'CBC','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_can_not_be_contacted'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'Closed','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
            $result['closed'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
            $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>'Closed','YEAR(' . Tbl_LeadAssign . '.created_on)' => date('m'));
            $result['month_closed'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');
        }
       $middle = "Leads/view/status";
       load_view($middle,$result);

   }
    public function emi_calculator(){
        $this->make_bread->add('emi-calculator', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = '/emi_calculator';
        load_view($middle,$result);
    }
    
}
