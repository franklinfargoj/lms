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

          $input = array(
              'hrms_id' => '100020',
              'dept_id' => '12',
              'dept_type_id' => '123',
              'dept_type_name' => 'BR',
              'branch_id' => '1234',
              'district_id' => '1234',
              'state_id' => '1234',
              'zone_id' => '1234',
              'full_name' => 'mukesh kurmi',
              'supervisor_id' => '009',
              'designation_id' => '4',
              'designation_name' => 'EM',
              'mobile' => '9975772432',
              'email_id' => 'mukesh.kurmi@wwindia.com',
          );

          /*Create Breadcumb*/
          /*$arrData['breadcrumb'] = $this->make_bread->output();*/
          $arrData['breadcrumb'] = '';
          /*Create Breadcumb*/

          if(isset($input['designation_name']) && !empty($input['designation_name'])){
               switch ($input['designation_name']) {
                    case 'EM':
                         
                              $month_generated_where = array('created_by' => $input['hrms_id'],'MONTH(created_on)' => date('m'));
                              $year_generated_where  = array('created_by' => $input['hrms_id'],'YEAR(created_on)' => date('Y'));

                              $month_converted_where = array('employee_id' => $input['hrms_id'],'MONTH(created_on)' => date('m'));
                              $year_converted_where  = array('employee_id' => $input['hrms_id'],'YEAR(created_on)' => date('Y'));

                              $month_assigned_where  = array('employee_id' => $input['hrms_id'],'YEAR(created_on)' => date('Y'));
                         
                         break;
                    /*case 'BM':
                         
                              $month_generated_where = array('branch_id' => $input['branch_id'],'MONTH(created_on)' => date('m'));
                              $year_generated_where  = array('branch_id' => $input['branch_id'],'YEAR(created_on)' => date('Y'));

                              $month_converted_where = array('branch_id' => $input['branch_id'],'MONTH(created_on)' => date('m'));
                              $year_converted_where  = array('branch_id' => $input['branch_id'],'YEAR(created_on)' => date('Y'));

                              $month_assigned_where  = array('branch_id' => $input['branch_id'],'YEAR(created_on)' => date('Y'));
                         
                         break;
                    case 'ZM':
                         
                              $month_generated_where = array('zone_id' => $input['zone_id'],'MONTH(created_on)' => date('m'));
                              $year_generated_where  = array('zone_id' => $input['zone_id'],'YEAR(created_on)' => date('Y'));

                              $month_converted_where = array('zone_id' => $input['zone_id'],'MONTH(created_on)' => date('m'));
                              $year_converted_where  = array('zone_id' => $input['zone_id'],'YEAR(created_on)' => date('Y'));

                              $month_assigned_where  = array('zone_id' => $input['zone_id'],'YEAR(created_on)' => date('Y'));   
                         
                         break;*/
               }
               $result1 = $this->master->get_generated_lead($month_generated_where,$year_generated_where,'get_count');
               $result2 = $this->master->get_converted_lead($month_converted_where,$year_converted_where,'get_count');
               $result3 = $this->master->get_assigned_leads($month_assigned_where,'get_count');
               $arrData['leads'] = array_merge($result1,$result2,$result3);
               /*pe($response);
               exit;*/
          }

          return load_view($middle = "dashboard",$arrData);
	}
}
