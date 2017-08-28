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
    public function leads_performance(){
        $input = get_session();
        $branch_id = $input['branch_id'];
        $action = 'count';
        $table = Tbl_Leads;
        $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        $select = array();

        //Walk-in
        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Self');
        $result['lead_assigned_self'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');
        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Self',
            Tbl_LeadAssign . '.status' => 'Converted');
        $result['lead_converted_self'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

        //Third Party
        $where = array(Tbl_Leads . '.branch_id' => $branch_id, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups');
        $result['lead_generated_tie_ups'] = $this->Lead->get_leads($action, $table, $select, $where, '', '', '');
        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups',
            Tbl_LeadAssign . '.status' => 'Converted');
        $result['lead_converted_tie_ups'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

        //Bank Website
        $where = array(Tbl_Leads . '.branch_id' => $branch_id, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry');
        $result['lead_generated_enquiry'] = $this->Lead->get_leads($action, $table, $select, $where, '', '', '');
        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry',
            Tbl_LeadAssign . '.status' => 'Converted');
        $result['lead_converted_enquiry'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

        //Analytics
        $where = array(Tbl_Leads . '.branch_id' => $branch_id, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics');
        $result['lead_generated_analytics'] = $this->Lead->get_leads($action, $table, $select, $where, '', '', '');
        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics',
            Tbl_LeadAssign . '.status' => 'Converted');
        $result['lead_converted_analytics'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

        $middle = "Leads/view/bm_performance";
        load_view($middle,$result);

    }
}
