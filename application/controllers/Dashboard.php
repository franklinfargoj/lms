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
          /*Create Breadcumb*/
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/
          $login_user_id = 312;
          /*Get Leads Generated Count */
               $ytd_where = array(
                    'created_by' => $login_user_id,
                    'YEAR(created_on)' => date('Y')
               );
               $leads['leads_generated']['YTD'] = $this->master->leads_generated($ytd_where);

               $mtd_where = array(
                    'created_by' => $login_user_id,
                    'MONTH(created_on)' => date('m')
               );
               $leads['leads_generated']['MTD'] = $this->master->leads_generated($mtd_where);
          /*Get Leads Generated Count */

          /*Get Leads Converted Count */
               $ytd_where = array(
                    'employee_id' => $login_user_id,
                    'YEAR(created_on)' => date('Y')
               );
               $leads['leads_converted']['YTD'] = $this->master->leads_converted($ytd_where);

               $mtd_where = array(
                    'employee_id' => $login_user_id,
                    'MONTH(created_on)' => date('m')
               );
               $leads['leads_converted']['MTD'] = $this->master->leads_converted($mtd_where);
          /*Get Leads Generated Count */

          /*pe($leads);
          exit;*/
          
          /*Get Leads Generated Count */
          return load_view($middle = "dashboard",$arrData);
	}
}
