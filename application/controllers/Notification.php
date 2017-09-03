<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

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
        /*Create Breadcumb*/
          $this->make_bread->add('Notification', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/


        //Get session data
        $input = get_session();
        $leads = array();

        if(isset($input['designation_name']) && !empty($input['designation_name'])){
            switch ($input['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to get_leads function.
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
               
            }

        }
        return load_view($middle,$arrData);
	}
}
